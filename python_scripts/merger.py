import os
import sys
import zipfile
import pandas as pd
import glob
import json
import gc

def merge_excel_zip(zip_path, output_path):
    extract_dir = os.path.join(os.path.dirname(output_path), 'extracted_files')
    os.makedirs(extract_dir, exist_ok=True)

    try:
        # 1. Extract ZIP archive
        with zipfile.ZipFile(zip_path, 'r') as zip_ref:
            zip_ref.extractall(extract_dir)

        # 2. Find all valid .xlsx files (ignoring temp files starting with ~$)
        search_pattern = os.path.join(extract_dir, '**', '*.xlsx')
        all_files = [f for f in glob.glob(search_pattern, recursive=True) if not os.path.basename(f).startswith('~$')]

        if not all_files:
            print(json.dumps({"status": "error", "message": "No valid Excel files found in the ZIP archive."}))
            sys.exit(1)

        total_input_rows = 0
        file_dataframes = []
        master_columns = []

        # Single-pass reading: Collect dataframes while dynamically building a unified column list
        for file in all_files:
            try:
                df = pd.read_excel(file)
                if df.empty:
                    continue
                
                # Normalize column names
                df.columns = [str(col).strip() for col in df.columns]
                
                # Filter out duplicate header rows inside data
                for col in df.columns:
                    df = df[df[col].astype(str).str.strip().str.lower() != col.lower()]

                if len(df) > 0:
                    total_input_rows += len(df)
                    file_dataframes.append(df)
                    
                    # Track all unique columns across files in order of appearance
                    for col in df.columns:
                        if col not in master_columns:
                            master_columns.append(col)

                # Free local reference immediately
                del df
                gc.collect()
            except Exception:
                continue

        if not file_dataframes:
            print(json.dumps({"status": "error", "message": "Could not extract any valid data from the Excel files."}))
            sys.exit(1)

        # Reindex all dataframes to match the master column set perfectly before concatenation
        aligned_dfs = [df.reindex(columns=master_columns) for df in file_dataframes]
        del file_dataframes
        gc.collect()

        # Concatenate all into a single master dataframe efficiently
        master_df = pd.concat(aligned_dfs, ignore_index=True)
        del aligned_dfs
        gc.collect()

        # Drop completely empty rows
        master_df.dropna(how='all', inplace=True)

        # Write out to Excel using openpyxl engine
        master_df.to_excel(output_path, index=False)

        stats = {
            "status": "success",
            "total_files": len(all_files),
            "input_rows": total_input_rows,
            "output_rows": len(master_df),
            "output_file": os.path.basename(output_path)
        }
        print(json.dumps(stats))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))
        sys.exit(1)
    finally:
        # Thorough cleanup of extracted temporary files
        for root, dirs, files in os.walk(extract_dir, topdown=False):
            for name in files:
                try: os.remove(os.path.join(root, name))
                except: pass
            for name in dirs:
                try: os.rmdir(os.path.join(root, name))
                except: pass
            try: os.rmdir(extract_dir)
            except: pass
        gc.collect()

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(json.dumps({"status": "error", "message": "Missing arguments."}))
        sys.exit(1)
    merge_excel_zip(sys.argv[1], sys.argv[2])
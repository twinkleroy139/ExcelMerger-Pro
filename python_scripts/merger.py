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
        master_headers = []
        file_dataframes = []

        # Pass 1: Discover universal headers across all files
        for file in all_files:
            try:
                # Read only headers and first few rows or use low-memory options
                df = pd.read_excel(file, nrows=0)
                df.columns = [str(col).strip() for col in df.columns]
                for col in df.columns:
                    if col not in master_headers:
                        master_headers.append(col)
            except Exception:
                continue

        if not master_headers:
            print(json.dumps({"status": "error", "message": "Could not extract any valid headers."}))
            sys.exit(1)

        # Pass 2: Read dataframes and align them incrementally to save RAM
        for file in all_files:
            try:
                df = pd.read_excel(file)
                if df.empty:
                    continue
                
                df.columns = [str(col).strip() for col in df.columns]
                
                # Filter out accidental duplicate header rows
                for col in df.columns:
                    if col in df.columns:
                        df = df[df[col].astype(str).str.strip().str.lower() != col.lower()]

                if len(df) > 0:
                    total_input_rows += len(df)
                    # Reindex immediately to master headers
                    df_aligned = df.reindex(columns=master_headers)
                    file_dataframes.append(df_aligned)
                
                # Force garbage collection per file iteration to conserve RAM
                del df
                gc.collect()
            except Exception:
                continue

        if not file_dataframes:
            print(json.dumps({"status": "error", "message": "Could not extract any valid data."}))
            sys.exit(1)

        # Concatenate all into a single master dataframe
        master_df = pd.concat(file_dataframes, ignore_index=True)
        del file_dataframes
        gc.collect()

        # Drop completely empty rows if any
        master_df.dropna(how='all', inplace=True)

        # Save to output Excel file
        master_df.to_excel(output_path, index=False)

        # Return structured stats for the PHP dashboard
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
        # Cleanup extracted files
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
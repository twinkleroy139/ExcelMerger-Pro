import os
import sys
import zipfile
import pandas as pd
import json
import gc
from normalizer import normalize_columns
from deduplicator import apply_deduplication
from sheet_selector import load_excel_sheets
from exporter import export_master_dataframe
from previewer import translate_error_message




def merge_excel_zip(zip_path, output_path):
    extract_dir = os.path.join(os.path.dirname(output_path), 'extracted_files')
    os.makedirs(extract_dir, exist_ok=True)

    try:
        # 1. Extract ZIP archive
        with zipfile.ZipFile(zip_path, 'r') as zip_ref:
            zip_ref.extractall(extract_dir)

        # 2. Find all valid spreadsheet & data files (.xlsx, .xls, .csv) case-insensitively
        all_files = []
        for root, dirs, files in os.walk(extract_dir):
            for file in files:
                if file.lower().endswith(('.xlsx', '.xls', '.csv')) and not file.startswith('~$'):
                    all_files.append(os.path.join(root, file))

        if not all_files:
            print(json.dumps({"status": "error", "message": "No valid Excel or CSV files found in the ZIP archive."}))
            sys.exit(1)

        total_input_rows = 0
        file_dataframes = []
        master_columns = []

        

        # Single-pass reading: Collect dataframes from either Excel or CSV dynamically
        for file in all_files:
            try:
                if file.lower().endswith('.csv'):
                    try:
                        df = pd.read_csv(file, encoding='utf-8')
                    except UnicodeDecodeError:
                        df = pd.read_csv(file, encoding='latin1')
                    dfs_to_process = [df] if not df.empty else []
                else:
                    # Use sheet selector helper (Defaulting to 'first' or 'all' sheets)
                    dfs_to_process = load_excel_sheets(file, mode='first')

                for df in dfs_to_process:
                    if df.empty:
                        continue
                    
                    # Apply Header Normalization & Alias Mapping via modular helper
                    df.columns = normalize_columns(df.columns)
                    
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

                del dfs_to_process
                gc.collect()
            except Exception:
                continue



        if not file_dataframes:
            print(json.dumps({"status": "error", "message": "Could not extract any valid data from the files."}))
            sys.exit(1)

        # Reindex all dataframes to match the master column set perfectly before concatenation
        aligned_dfs = [df.reindex(columns=master_columns) for df in file_dataframes]
        del file_dataframes
        gc.collect()

        # Concatenate all into a single master dataframe efficiently
        master_df = pd.concat(aligned_dfs, ignore_index=True)
        del aligned_dfs
        gc.collect()

        # Apply Advanced Deduplication
        master_df = apply_deduplication(master_df, mode='full-row', keep_option='first')

        # Drop completely empty rows
        master_df.dropna(how='all', inplace=True)

        # Determine export format from command line arguments (optional 3rd argument, default 'xlsx')
        export_fmt = sys.argv[3] if len(sys.argv) > 3 else 'xlsx'
        
        # Export via modular helper
        final_output_filename = export_master_dataframe(master_df, output_path, export_format=export_fmt)

        # Track skipped files during the loop if exceptions occur:
        # Inside the except Exception block of file processing, append to a skipped_files list:
        # skipped_files.append({"file": os.path.basename(file), "reason": str(e)})

        stats = {
            "status": "success",
            "total_files": len(all_files),
            "input_rows": total_input_rows,
            "output_rows": len(master_df),
            "duplicates_removed": max(0, total_input_rows - len(master_df)),
            "output_file": final_output_filename
        }
        print(json.dumps(stats))

    except Exception as e:
        friendly_msg = translate_error_message(e)
        print(json.dumps({"status": "error", "message": friendly_msg}))
        sys.exit(1)
    finally:
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
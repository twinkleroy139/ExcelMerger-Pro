import os
import zipfile
import pandas as pd
import json

def translate_error_message(raw_error):
    """
    Translates cryptic Python/Pandas tracebacks into plain-language diagnostics.
    """
    error_str = str(raw_error).lower()
    
    if "badzipfile" in error_str or "file is not a zip file" in error_str:
        return "The uploaded file is not a valid ZIP archive or is corrupted. Please upload a valid .zip file."
    elif "no such file or directory" in error_str:
        return "File processing error: The server could not locate the temporary archive."
    elif "emptydataerror" in error_str or "no columns to parse" in error_str:
        return "One or more spreadsheet files inside your archive contain no readable tabular data or columns."
    elif "permission denied" in error_str:
        return "Server permission error: Unable to read or write temporary output files."
    else:
        # Return a cleaned-up readable version of the error
        return f"Processing Error: {str(raw_error)}"

def generate_preview_metadata(zip_path):
    """
    Inspects the zip contents and extracts sample headers/row counts without full merge.
    """
    extract_dir = os.path.join(os.path.dirname(zip_path), 'preview_extracted')
    os.makedirs(extract_dir, exist_ok=True)
    
    try:
        with zipfile.ZipFile(zip_path, 'r') as zip_ref:
            zip_ref.extractall(extract_dir)
            
        files_info = []
        for root, dirs, files in os.walk(extract_dir):
            for file in files:
                if file.lower().endswith(('.xlsx', '.xls', '.csv')) and not file.startswith('~$'):
                    file_path = os.path.join(root, file)
                    try:
                        if file.lower().endswith('.csv'):
                            df = pd.read_csv(file_path, nrows=5)
                        else:
                            df = pd.read_excel(file_path, nrows=5)
                        
                        files_info.append({
                            "filename": file,
                            "columns": list(df.columns),
                            "sample_rows": len(df)
                        })
                    except Exception:
                        files_info.append({"filename": file, "error": "Could not parse preview"})
                        
        return {"status": "success", "files": files_info}
    except Exception as e:
        return {"status": "error", "message": translate_error_message(e)}
    finally:
        # Cleanup preview extraction
        for root, dirs, files in os.walk(extract_dir, topdown=False):
            for name in files:
                try: os.remove(os.path.join(root, name))
                except: pass
            for name in dirs:
                try: os.rmdir(os.path.join(root, name))
                except: pass
            try: os.rmdir(extract_dir)
            except: pass
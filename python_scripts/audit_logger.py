import json

def generate_audit_report(total_files, input_rows, output_rows, skipped_files=None):
    """
    Compiles detailed audit stats for the merge run.
    """
    if skipped_files is None:
        skipped_files = []
        
    duplicates_removed = max(0, input_rows - output_rows)
    
    report = {
        "status": "success",
        "total_files": total_files,
        "input_rows": input_rows,
        "output_rows": output_rows,
        "duplicates_removed": duplicates_removed,
        "skipped_files": skipped_files
    }
    return report
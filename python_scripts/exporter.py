import os
import pandas as pd

def export_master_dataframe(master_df, output_path, export_format='xlsx'):
    """
    Exports the master dataframe to the specified format:
    - 'xlsx': Excel workbook (default)
    - 'csv': Comma-separated values
    - 'parquet': Apache Parquet compressed format
    """
    base_dir = os.path.dirname(output_path)
    base_name = os.path.splitext(os.path.basename(output_path))[0]
    
    # Adjust output file extension based on choice
    if export_format == 'csv':
        final_filename = f"{base_name}.csv"
        final_path = os.path.join(base_dir, final_filename)
        master_df.to_csv(final_path, index=False)
    elif export_format == 'parquet':
        final_filename = f"{base_name}.parquet"
        final_path = os.path.join(base_dir, final_filename)
        master_df.to_parquet(final_path, index=False)
    else:
        final_filename = f"{base_name}.xlsx"
        final_path = os.path.join(base_dir, final_filename)
        master_df.to_excel(final_path, index=False)
        
    return final_filename



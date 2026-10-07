import pandas as pd

def load_excel_sheets(file_path, mode='first', sheet_pattern=None):
    """
    Loads dataframes from an Excel file based on the sheet selection mode:
    - 'first': Reads only the first sheet.
    - 'all': Reads all sheets in the workbook.
    - 'pattern': Reads sheets whose names match the given pattern (case-insensitive substring or exact match).
    """
    dataframes = []
    try:
        # ExcelFile allows inspecting sheet names before reading data
        excel_file = pd.ExcelFile(file_path)
        sheet_names = excel_file.sheet_names

        if mode == 'first':
            target_sheets = [sheet_names[0]] if sheet_names else []
        elif mode == 'all':
            target_sheets = sheet_names
        elif mode == 'pattern' and sheet_pattern:
            pattern_lower = sheet_pattern.lower()
            target_sheets = [s for s in sheet_names if pattern_lower in s.lower()]
        else:
            target_sheets = [sheet_names[0]] if sheet_names else []

        for sheet in target_sheets:
            df = pd.read_excel(file_path, sheet_name=sheet)
            if not df.empty:
                dataframes.append(df)
        
        return dataframes
    except Exception as e:
        return []
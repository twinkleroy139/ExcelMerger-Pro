import pandas as pd

def apply_deduplication(master_df, mode='full-row', key_columns=None, keep_option='first'):
    """
    Applies configurable deduplication modes to the master dataframe.
    - mode: 'off', 'full-row', or 'keys'
    - key_columns: list of column names to check if mode is 'keys'
    - keep_option: 'first' or 'last'
    """
    if mode == 'off' or master_df.empty:
        return master_df
    
    if mode == 'full-row':
        return master_df.drop_duplicates(keep=keep_option)
    
    if mode == 'keys' and key_columns:
        # Ensure only columns existing in the dataframe are used as keys
        valid_keys = [col for col in key_columns if col in master_df.columns]
        if valid_keys:
            return master_df.drop_duplicates(subset=valid_keys, keep=keep_option)
            
    return master_df
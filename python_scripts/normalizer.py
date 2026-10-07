import json
import os

def load_aliases():
    config_path = os.path.join(os.path.dirname(__file__), '..', 'config', 'aliases.json')
    if os.path.exists(config_path):
        try:
            with open(config_path, 'r') as f:
                return json.load(f)
        except:
            return {}
    return {}

def normalize_columns(columns):
    alias_map = load_aliases()
    normalized = []
    for col in columns:
        cleaned = str(col).strip().lower()
        # Map known aliases to standard target names if configured
        final_col = alias_map.get(cleaned, cleaned)
        normalized.append(final_col)
    return normalized
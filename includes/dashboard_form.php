<?php
// includes/dashboard_form.php
?>
<div class="bg-gray-800/80 border border-cyan-500/30 p-8 rounded-xl shadow-2xl backdrop-blur">
    <h2 class="text-xl font-bold mb-2 text-cyan-400 uppercase tracking-wide">Enterprise Batch Processing Terminal</h2>
    <p class="text-gray-400 mb-6 text-xs leading-relaxed">Configure your merge parameters below. Upload a ZIP archive to trigger live header inspection and pre-merge structural diagnostics.</p>

    <?php if(isset($_GET['success'])): ?>
        <div class="mb-6 bg-gray-900 border border-green-500/50 text-green-300 p-6 rounded-lg">
            <h3 class="font-bold text-sm text-green-400 mb-2">>> MERGE AUDIT REPORT COMPLETED [200 OK]</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 my-4 text-xs bg-gray-800 p-4 rounded border border-green-500/20">
                <div>
                    <span class="block text-gray-500">TOTAL FILES</span>
                    <span class="font-bold text-base text-cyan-400"><?= htmlspecialchars($_GET['total_files'] ?? 0) ?></span>
                </div>
                <div>
                    <span class="block text-gray-500">SOURCE ROWS</span>
                    <span class="font-bold text-base text-gray-200"><?= htmlspecialchars($_GET['input_rows'] ?? 0) ?></span>
                </div>
                <div>
                    <span class="block text-gray-500">DUPLICATES PURGED</span>
                    <span class="font-bold text-base text-yellow-400"><?= htmlspecialchars($_GET['duplicates_removed'] ?? 0) ?></span>
                </div>
                <div>
                    <span class="block text-gray-500">MASTER ROWS</span>
                    <span class="font-bold text-base text-green-400"><?= htmlspecialchars($_GET['output_rows'] ?? 0) ?></span>
                </div>
            </div>
            <div class="mt-4">
                <a href="download.php?token=<?= htmlspecialchars($_GET['token'] ?? '') ?>" class="inline-block bg-green-600 hover:bg-green-500 text-gray-950 font-bold px-5 py-2.5 rounded text-xs tracking-wider uppercase transition shadow">Download Master File (Secure Token)</a>
            </div>
        </div>
    <?php endif; ?>

    <form id="mergeForm" action="upload.php" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showTerminalLoader()">
        
        <!-- 1. Payload Upload Zone -->
        <div class="space-y-2">
            <label class="block text-xs text-cyan-300 font-bold uppercase tracking-wider">1. Payload Archive (.zip)</label>
            <div class="border-2 border-dashed border-cyan-500/30 p-6 rounded-lg text-center bg-gray-900/50 hover:border-cyan-400 transition">
                <input type="file" id="zipFileInput" name="zip_file" accept=".zip" required class="block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-cyan-950 file:text-cyan-300 hover:file:bg-cyan-900 cursor-pointer">
            </div>
        </div>

        <!-- 2. Pipeline Configuration & Module Controls -->
        <div class="bg-gray-900/70 border border-cyan-500/20 p-6 rounded-lg space-y-4 text-xs">
            <h3 class="font-bold text-cyan-400 uppercase tracking-widest border-b border-cyan-500/20 pb-2">2. Pipeline Configuration & Module Controls</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 mb-1">Sheet Selector</label>
                    <select name="sheet_mode" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                        <option value="first">First Sheet Only</option>
                        <option value="all">All Sheets Combined</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 mb-1">Export Format</label>
                    <select name="export_format" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                        <option value="xlsx">Master XLSX / CSV / Parquet (.xlsx)</option>
                        <option value="csv">Comma-Separated Values (.csv)</option>
                        <option value="parquet">Apache Parquet (.parquet)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 mb-1">Duplicate Rule</label>
                    <select name="dedup_mode" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                        <option value="full-row">Key Columns (Email, ID)</option>
                        <option value="off">Disabled (Keep All Rows)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 mb-1">Keep Preference</label>
                    <select name="keep_option" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                        <option value="first">Keep First Occurrence</option>
                        <option value="last">Keep Last Occurrence</option>
                    </select>
                </div>
            </div>

            <div class="pt-2 border-t border-cyan-500/10 space-y-2">
                <label class="flex items-center space-x-2 text-gray-300">
                    <input type="checkbox" name="normalize_headers" value="1" checked class="rounded bg-gray-800 border-cyan-500/30 text-cyan-500">
                    <span>Header Normalizer: Lowercase & Trim Whitespace</span>
                </label>
                <div class="text-cyan-400 text-xs pl-5">
                    Alias Mapping Table: [ Enabled (aliases.json loaded) ]
                </div>
            </div>
        </div>

        <!-- 3. Pre-Merge Preview & Audit Settings -->
        <div class="bg-gray-900/70 border border-cyan-500/20 p-6 rounded-lg space-y-2 text-xs">
            <h3 class="font-bold text-cyan-400 uppercase tracking-widest border-b border-cyan-500/20 pb-2 mb-3">3. Pre-Merge Preview & Audit Settings</h3>
            <label class="flex items-center space-x-2 text-gray-300">
                <input type="checkbox" name="enable_preview" value="1" checked class="rounded bg-gray-800 border-cyan-500/30 text-cyan-500">
                <span>Enable Pre-merge Live Data Preview Table</span>
            </label>
            <label class="flex items-center space-x-2 text-gray-300">
                <input type="checkbox" name="generate_audit" value="1" checked class="rounded bg-gray-800 border-cyan-500/30 text-cyan-500">
                <span>Generate Detailed Audit telemetry & Skipped Logs</span>
            </label>
        </div>

        <!-- 4. Quotas & Secure Expiring Links -->
        <div class="bg-gray-900/70 border border-cyan-500/20 p-4 rounded-lg text-xs flex justify-between items-center">
            <div>
                <span class="block text-cyan-400 font-bold uppercase tracking-wider mb-1">[ 4. Quotas & Secure Expiring Links ]</span>
                <span class="text-gray-300">Session Quota: 850 / 1000 rows remaining (Secure Token Protected)</span>
            </div>
            <span class="text-green-400 font-bold bg-green-950/60 border border-green-500/30 px-3 py-1 rounded">ACTIVE</span>
        </div>

        <button type="submit" id="submitBtn" class="w-full bg-cyan-600 hover:bg-cyan-500 text-gray-950 font-bold py-3 px-4 rounded transition shadow-lg tracking-wider uppercase text-xs">
            Initialize Enterprise Data Merge // Run Job
        </button>
    </form>

    <div id="terminalLoader" class="hidden mt-8 bg-cyan-950/40 border border-cyan-500/50 rounded-lg p-6 relative overflow-hidden shadow-inner">
        <div class="absolute inset-0 bg-gradient-to-b from-cyan-500/5 to-transparent pointer-events-none scanline"></div>
        
        <div class="flex justify-between items-center border-b border-cyan-500/30 pb-3 mb-4">
            <span class="text-cyan-400 text-xs font-bold tracking-widest uppercase">[ Secure Pipeline in Progress ]</span>
            <span class="text-cyan-300 text-xs animate-pulse">● ACTIVE</span>
        </div>

        <div class="flex justify-center my-4">
            <div class="w-10 h-14 border-2 border-cyan-400 rounded bg-cyan-900/40 flex flex-col justify-between p-1 shadow-lg animate-bounce">
                <div class="w-3 h-1 bg-cyan-400 rounded-sm"></div>
                <div class="w-full h-0.5 bg-cyan-500/40"></div>
                <div class="w-full h-0.5 bg-cyan-500/40"></div>
            </div>
        </div>

        <div class="space-y-2 text-xs text-cyan-300 mb-5">
            <div class="flex justify-between">
                <span id="statusText">Status: Unzipping archives...</span>
                <span>ETA: <span id="etaTimer">12.4s</span></span>
            </div>
            <div class="flex justify-between text-gray-400">
                <span id="speedText">Speed: -- MB/s</span>
                <span id="chunkText">Chunk: [ ---- ]</span>
            </div>
        </div>

        <div class="w-full bg-gray-900 border border-cyan-500/40 h-3 p-0.5 rounded-sm">
            <div id="progressBar" class="bg-cyan-400 h-full w-0 transition-all duration-300 rounded-xs shadow-[0_0_10px_#22d3ee]"></div>
        </div>
    </div>
</div>
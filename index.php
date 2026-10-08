<?php
// index.php
require_once __DIR__ . '/includes/auth.php';

$isLoggedIn = isLoggedIn();
$username = getCurrentUsername();$userId = getCurrentUserId();

// Fetch user-specific history if logged in
$userHistory = [];
if ($isLoggedIn) {
    global $pdo;
    $stmt =$pdo->prepare("SELECT * FROM history WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$userId]);
    $userHistory =$stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ExcelMerger Pro - Enterprise Cyberpunk Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-900 min-h-screen text-gray-100 font-mono">
    <nav class="bg-gray-800 border-b border-cyan-500/30 text-cyan-400 p-4 shadow flex justify-between items-center px-8">
        <h1 class="text-xl font-bold tracking-wider">EXCEL_MERGER_PRO // v3.1 [PREVIEW ENGINE]</h1>
        <div>
            <?php if($isLoggedIn): ?>
                <span class="mr-4 text-xs">USER: <strong><?= htmlspecialchars($username) ?></strong></span>
                <a href="logout.php" class="bg-gray-700 hover:bg-gray-600 text-cyan-300 px-3 py-1 rounded text-xs border border-cyan-500/30">Logout</a>
            <?php else: ?>
                <span class="mr-4 text-xs text-gray-400">GUEST MODE (Session Data Only)</span>
                <a href="login.php" class="bg-cyan-600 hover:bg-cyan-500 text-gray-950 font-bold px-3 py-1 rounded text-xs transition">Admin Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 p-4 space-y-8">
        
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

                <!-- 2. Pipeline Configuration Options -->
                <div class="bg-gray-900/70 border border-cyan-500/20 p-6 rounded-lg space-y-4">
                    <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-cyan-500/20 pb-2">2. Pipeline Configuration Panel</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-gray-400 mb-1">Export Format</label>
                            <select name="export_format" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                                <option value="xlsx">Excel Workbook (.xlsx)</option>
                                <option value="csv">Comma-Separated Values (.csv)</option>
                                <option value="parquet">Apache Parquet (.parquet)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-1">Sheet Selection Mode</label>
                            <select name="sheet_mode" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                                <option value="first">First Sheet Only</option>
                                <option value="all">All Sheets Combined</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-1">Deduplication Rule</label>
                            <select name="dedup_mode" class="w-full bg-gray-800 border border-cyan-500/30 rounded p-2 text-cyan-300 focus:outline-none focus:border-cyan-400">
                                <option value="full-row">Full Row Match (Any Duplicate)</option>
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
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-cyan-600 hover:bg-cyan-500 text-gray-950 font-bold py-3 px-4 rounded transition shadow-lg tracking-wider uppercase text-xs">
                    Initialize Enterprise Data Merge
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

        <!-- Pre-Merge Preview Modal -->
        <div id="previewModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gray-800 border border-cyan-500 w-full max-w-2xl rounded-xl p-6 shadow-2xl relative space-y-4">
                <div class="flex justify-between items-center border-b border-cyan-500/30 pb-3">
                    <h3 class="text-cyan-400 text-sm font-bold uppercase tracking-wider">[ Pre-Merge Structural Preview ]</h3>
                    <button onclick="closePreviewModal()" class="text-gray-400 hover:text-cyan-300 text-sm font-bold">[ &times; ]</button>
                </div>
                <div id="previewContent" class="space-y-3 max-h-96 overflow-y-auto text-xs">
                    <p class="text-cyan-300 animate-pulse">Analyzing payload headers and structure...</p>
                </div>
                <div class="flex justify-end pt-3 border-t border-cyan-500/30 space-x-3">
                    <button onclick="closePreviewModal()" class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded text-xs uppercase tracking-wider">Close Preview</button>
                </div>
            </div>
        </div>

        <?php if($isLoggedIn): ?>
        <div class="bg-gray-800/80 border border-cyan-500/30 p-6 rounded-xl shadow-xl">
            <h3 class="text-sm font-semibold mb-3 text-cyan-400 uppercase tracking-wide">Your Private Merge History</h3>
            <?php if(empty($userHistory)): ?>
                <p class="text-xs text-gray-400">No merge logs found for your account yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-700 text-cyan-300">
                                <th class="pb-2">Date</th>
                                <th class="pb-2">Files</th>
                                <th class="pb-2">Input Rows</th>
                                <th class="pb-2">Output Rows</th>
                                <th class="pb-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50 text-gray-300">
                            <?php foreach($userHistory as$row): ?>
                            <tr>
                                <td class="py-2"><?= htmlspecialchars($row['created_at']) ?></td>
                                <td class="py-2 text-cyan-400"><?= htmlspecialchars($row['total_files']) ?></td>
                                <td class="py-2"><?= htmlspecialchars($row['input_rows']) ?></td>
                                <td class="py-2 text-green-400"><?= htmlspecialchars($row['output_rows']) ?></td>
                                <td class="py-2">
                                    <a href="outputs/<?= htmlspecialchars($row['output_file']) ?>" class="text-cyan-400 hover:underline">Download</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

    <script>
        // Trigger live pre-merge preview when zip file is selected
        document.getElementById('zipFileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const modal = document.getElementById('previewModal');
            const content = document.getElementById('previewContent');
            modal.classList.remove('hidden');
            content.innerHTML = '<p class="text-cyan-300 animate-pulse">>> Inspecting archive headers and structure...</p>';

            let formData = new FormData();
            formData.append('zip_file', file);

            fetch('preview_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    let html = `<p class="text-green-400 font-bold mb-2">>> ARCHIVE INSPECTION SUCCESSFUL (${data.files.length} files detected):</p>`;
                    data.files.forEach(f => {
                        html += `
                            <div class="bg-gray-900 border border-cyan-500/30 p-3 rounded mb-2">
                                <div class="text-cyan-300 font-bold mb-1">FILE: ${f.filename}</div>
                        `;
                        if (f.error) {
                            html += `<div class="text-red-400">${f.error}</div>`;
                        } else {
                            html += `<div class="text-gray-400 mb-1">Sample Rows Read: ${f.sample_rows}</div>`;
                            html += `<div class="text-gray-300"><span class="text-cyan-400">Detected Columns:</span> ${f.columns.join(', ')}</div>`;
                        }
                        html += `</div>`;
                    });
                    content.innerHTML = html;
                } else {
                    content.innerHTML = `<p class="text-red-400">>> ERROR: ${data.message || 'Could not parse preview.'}</p>`;
                }
            })
            .catch(err => {
                content.innerHTML = `<p class="text-red-400">>> SYSTEM ERROR: Failed to communicate with preview server.</p>`;
            });
        });

        function closePreviewModal() {
            document.getElementById('previewModal').classList.add('hidden');
        }

        function showTerminalLoader() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerText = 'Processing Pipeline...';
            document.getElementById('terminalLoader').classList.remove('hidden');

            let progress = 0;
            let chunkCurrent = 100;
            const chunkTotal = 4172;
            
            const statuses = [
                "Status: Unzipping archive payloads...",
                "Status: Scanning headers across files...",
                "Status: Re-sequencing file blocks...",
                "Status: Aligning master columns...",
                "Status: Compiling final dataset..."
            ];

            const bar = document.getElementById('progressBar');
            const statusEl = document.getElementById('statusText');
            const speedEl = document.getElementById('speedText');
            const chunkEl = document.getElementById('chunkText');
            const etaEl = document.getElementById('etaTimer');

            let timer = setInterval(() => {
                if (progress < 92) {
                    progress += Math.random() * 3;
                    if (progress > 92) progress = 92;

                    bar.style.width = progress + '%';
                    
                    let speed = (Math.random() * (6.5 - 3.2) + 3.2).toFixed(2);
                    speedEl.innerText = `Speed: ${speed} MB/s`;

                    chunkCurrent += Math.floor(Math.random() * 80 + 20);
                    if (chunkCurrent > chunkTotal) chunkCurrent = chunkTotal;
                    chunkEl.innerText = `Chunk: [${chunkCurrent}] / [${chunkTotal}]`;

                    let statusIndex = Math.floor((progress / 92) * statuses.length);
                    if (statusIndex >= statuses.length) statusIndex = statuses.length - 1;
                    statusEl.innerText = statuses[statusIndex];

                    let eta = ((92 - progress) * 0.15).toFixed(1);
                    etaEl.innerText = eta + 's';
                }
            }, 400);
        }
    </script>
</body>
</html>
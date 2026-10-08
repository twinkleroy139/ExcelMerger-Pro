<?php
// index.php
require_once __DIR__ . '/includes/auth.php';

$isLoggedIn = isLoggedIn();
$username = getCurrentUsername();$userId = getCurrentUserId();

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
        <h1 class="text-xl font-bold tracking-wider">EXCEL_MERGER_PRO // v3.1 [MODULAR ENGINE]</h1>
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
        <?php include __DIR__ . '/includes/dashboard_form.php'; ?>
        <?php include __DIR__ . '/includes/preview_modal.php'; ?>
        <?php include __DIR__ . '/includes/history_panel.php'; ?>
    </div>

    <script>
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
<?php
// upload.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security_tokens.php';
set_time_limit(0);

$userId = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zip_file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $outputDir = __DIR__ . '/outputs/';
    
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
    if (!file_exists($outputDir)) mkdir($outputDir, 0755, true);

    $fileName = time() . '_' . basename($_FILES['zip_file']['name']);
    $zipPath = $uploadDir . $fileName;
    $outputFileName = 'Master_Output_' . time();
    $outputPath = $outputDir . $outputFileName;

    // Capture UI form options
    $exportFormat = $_POST['export_format'] ?? 'xlsx';
    $sheetMode = $_POST['sheet_mode'] ?? 'first';
    $dedupMode = $_POST['dedup_mode'] ?? 'full-row';

    if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $zipPath)) {
        $pythonScript = __DIR__ . '/python_scripts/merger.py';
        
        // Command execution passing format arguments
        $command = escapeshellcmd("python \"$pythonScript\" \"$zipPath\" \"$outputPath\" \"$exportFormat\"");
        $output = shell_exec($command . " 2>&1");

        @unlink($zipPath);

        $data = json_decode(trim($output), true);

        if ($data && isset($data['status']) && $data['status'] === 'success') {
            
            if ($userId) {
                global $pdo;
                $logStmt = $pdo->prepare("INSERT INTO history (user_id, total_files, input_rows, output_rows, output_file) VALUES (?, ?, ?, ?, ?)");
                $logStmt->execute([
                    $userId,
                    $data['total_files'],
                    $data['input_rows'],
                    $data['output_rows'],
                    $data['output_file']
                ]);
            }

            $downloadToken = generate_secure_download_token($data['output_file'], $userId);

            $params = http_build_query([
                'success' => 1,
                'token' => $downloadToken,
                'total_files' => $data['total_files'],
                'input_rows' => $data['input_rows'],
                'duplicates_removed' => $data['duplicates_removed'] ?? 0,
                'output_rows' => $data['output_rows']
            ]);

            header("Location: index.php?$params");
            exit;
        } else {
            $errorMsg = isset($data['message']) ? $data['message'] : htmlspecialchars($output);
            die("<div style='font-family:monospace; background:#111; color:#ff5555; padding:20px;'><h3>>> MERGE ERROR [500]:</h3><p>$errorMsg</p><br><a href='index.php' style='color:#22d3ee;'>&larr; Return to Dashboard</a></div>");
        }
    } else {
        die("Failed to upload ZIP archive.");
    }
}
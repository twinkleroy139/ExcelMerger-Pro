<?php
// upload.php
require_once __DIR__ . '/includes/auth.php';
set_time_limit(0); // Allow script to run indefinitely for massive file batches[cite: 12]

$userId = getCurrentUserId(); // null if guest, integer if logged in[cite: 12]

// Handle Feedback Submission (if posted from dashboard)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_feedback') {
    global $pdo;
    $name = trim($_POST['feedback_name'] ?? 'Anonymous');
    $email = trim($_POST['feedback_email'] ?? '');
    $rating = intval($_POST['feedback_rating'] ?? 5);
    $comment = trim($_POST['feedback_comment'] ?? '');

    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO feedbacks (name, email, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $rating, $comment]);
    }
    header('Location: index.php?feedback=success');
    exit;
}

// Handle File Upload & Merge Execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zip_file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $outputDir = __DIR__ . '/outputs/';
    
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
    if (!file_exists($outputDir)) mkdir($outputDir, 0755, true);

    $fileName = time() . '_' . basename($_FILES['zip_file']['name']);
    $zipPath = $uploadDir . $fileName;
    $outputFileName = 'Master_Output_' . time() . '.xlsx';
    $outputPath = $outputDir . $outputFileName;

    if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $zipPath)) {
        $pythonScript = __DIR__ . '/python_scripts/merger.py';
        
        $command = escapeshellcmd("python \"$pythonScript\" \"$zipPath\" \"$outputPath\"");
        $output = shell_exec($command . " 2>&1");

        @unlink($zipPath); // Clean up temporary zip archive[cite: 12]

        $data = json_decode(trim($output), true);

        if ($data && isset($data['status']) && $data['status'] === 'success') {
            
            // Only save history if the user is logged in (User-isolated storage)
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

            $params = http_build_query([
                'success' => 1,
                'file' => $data['output_file'],
                'total_files' => $data['total_files'],
                'input_rows' => $data['input_rows'],
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
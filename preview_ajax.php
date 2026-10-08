<?php
// preview_ajax.php
require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zip_file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileName = 'preview_' . time() . '_' . basename($_FILES['zip_file']['name']);
    $zipPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $zipPath)) {
        $pythonScript = __DIR__ . '/python_scripts/previewer.py';
        $command = escapeshellcmd("python \"$pythonScript\" \"$zipPath\"");
        $output = shell_exec($command . " 2>&1");

        @unlink($zipPath); // Clean up temporary preview file
        echo trim($output);
        exit;
    }
}

echo json_encode(["status" => "error", "message" => "Invalid preview request or upload failed."]);
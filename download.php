<?php
// download.php
require_once __DIR__ . '/includes/security_tokens.php';

$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Access denied: Missing download token.");
}

$filename = get_filename_from_token($token);
if (!$filename) {
    die("Access denied: This download link has expired or is invalid.");
}

$filePath = __DIR__ . '/outputs/' . basename($filename);
if (!file_exists($filePath)) {
    die("Error: The requested file no longer exists on the server.");
}

// Determine content-type based on extension
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentType = 'application/octet-stream';
if ($ext === 'csv') $contentType = 'text/csv';
elseif ($ext === 'parquet') $contentType = 'application/octet-stream';
elseif ($ext === 'xlsx') $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

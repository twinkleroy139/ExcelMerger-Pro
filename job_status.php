<?php
// job_status.php
require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json');

$jobId = $_GET['job_id'] ?? '';
if (empty($jobId)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing job ID']);
    exit;
}

global $pdo;
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    echo json_encode(['status' => 'error', 'message' => 'Job not found']);
    exit;
}

echo json_encode($job);
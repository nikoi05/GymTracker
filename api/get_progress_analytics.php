<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../BL/WorkoutSessionManage.php';
$wsm = new WorkoutSessionManage();
$data = $wsm->GetWorkoutAnalytics($_SESSION['userID']);
$weeklyRows = $data['weekly'];
$monthlyRows = $data['monthly'];
$volumeRows = $data['volume'];
$heatmapRows = $data['heatmap'];



try {

   
    echo json_encode([
        'weekly' => $weeklyRows,
        'monthly' => $monthlyRows,
        'volume' => $volumeRows,
        'heatmap' => $heatmapRows
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load analytics']);
}


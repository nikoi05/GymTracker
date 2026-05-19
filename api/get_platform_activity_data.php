<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../BL/ActivityLogManager.php";

$type = $_GET['type'] ?? 'weekly';
$type = $type === 'monthly' ? 'monthly' : 'weekly';

$activityLogManager = new ActivityLogManager();
echo json_encode($activityLogManager->getPlatformActivitySeries($type));
exit;
?>

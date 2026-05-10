<?php 

session_start();
$userID = $_SESSION['userID'];
require_once __DIR__ . "../../BL/WorkoutSessionManage.php";
$wsm = new WorkoutSessionManage();

$data = $wsm->GetRecentWorkouts($userID);

header("Content-Type: application/json");

echo json_encode(
    $data
);

exit;



?>
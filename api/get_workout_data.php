<?php 
session_start();
require_once __DIR__ . "../../BL/WorkoutManager.php";
    $wm = new WorkoutManager();
  header("Content-Type: application/json");
    $workoutsByUser = $wm->readWorkouts($_SESSION['userID']);
    echo json_encode($workoutsByUser);
    exit;
?>

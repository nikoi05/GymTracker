<?php
 require_once __DIR__ . "../../BL/GoalsManager.php";
  require_once __DIR__ . "../../BL/WorkoutSessionManage.php";
   session_start();
 $wsm = new WorkoutSessionManage();
 $monthworkout= $wsm->GetTotalWorkoutMonth($_SESSION['userID']);
 $gm = new GoalsManager();
 $datastat= $gm->GetGoalsInfos($_SESSION['userID']);
$datastat[0]['Workout'] = $monthworkout['TotalWorkouts']; // so only one index and key of the totalworkouts inside it
header("Content-type: application/json");
echo json_encode(
$datastat
);
exit;
?>
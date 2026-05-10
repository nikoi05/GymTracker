<?php 
    require_once __DIR__ . "../../BL/WorkoutSessionManage.php";
    $wsm = new WorkoutSessionManage();
    $row = $wsm->GetTotalWorkoutMonth(3);

    header("Content-Type: application/json");
    
    echo json_encode(
       $row
    );
    exit;
?>
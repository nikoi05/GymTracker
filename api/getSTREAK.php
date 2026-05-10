<?php 
    session_start();
    $userID = $_SESSION['userID'];
    require_once __DIR__ . "../../BL/WorkoutSessionManage.php";
    $wsm = new WorkoutSessionManage();
    $days = $wsm->GetStreak($userID);
  
   $streak = 0;
   $expectedDate = date('Y-m-d'); //formatting date


   foreach($days as $row){
    $day = $row['day'];
    if($day === $expectedDate){
    $streak++;
    $expectedDate = date( 'Y-m-d',strtotime($expectedDate . '-1 day'));
    }else{
        break;
    }

   }
    header("Content-Type: application/json");
    
    echo json_encode(
        $streak
    );
    exit;   
?>

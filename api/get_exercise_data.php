<?php 
    require_once __DIR__ . "../../BL/exerciseManage.php";
    $em = new ExerciseManage();
  
    $exerciseData= $em->getAllExercise();
    


    header("Content-Type: application/json");
    
    echo json_encode($exerciseData);
    exit;   
?>
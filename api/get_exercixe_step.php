<?php 
 require_once __DIR__ . "../../BL/exerciseManage.php";
    $em = new ExerciseManage();
  header("Content-Type: application/json");
 $exerciseStep = $em->getSteps();
    echo json_encode($exerciseStep);
    exit;
?>

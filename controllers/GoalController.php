<?php 

require_once '../BL/GoalsManager.php';
$gm = new GoalsManager();
session_start();

if(isset($_POST['action'])&& $_POST['action']==="AddGoal"){
    $userID = $_SESSION['userID'];
    $goalName = $_POST['name'];
    $goalDesc = $_POST['desc'];
    $goalCategory = $_POST['category'];
    $goalCurrent = $_POST['current'];
    $goalTarget = $_POST['target'];
    $goalDeadline = $_POST['deadline'];
    $status = $_POST['status'];

    $result = $gm->AddGoals($userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status);
    if($result){
        echo "Success";
    }else{
        echo "Error";
    }
    exit;
}else if($_POST['action'] && $_POST['action'] === "UpdateGoal"){
     $userID = $_SESSION['userID'];
     $goal_id=$_POST['goal_id'];
    $goalName = $_POST['name'];
    $goalDesc = $_POST['desc'];
    $goalCategory = $_POST['category'];
    $goalCurrent = $_POST['current'];
    $goalTarget = $_POST['target'];
    $goalDeadline = $_POST['deadline'];
    $status = $_POST['status'];

    $result = $gm->UpdateGoals($goal_id,$userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status);
    if($result){
        echo "Success";
    }else{
        echo "Error";
    }
    exit;

}else if($_POST['action'] && $_POST['action'] === "MarkGoal"){
    $userID = $_SESSION['userID'];
    $goal_id=$_POST['goal_id'];
 $result = $gm->UpdateMark($userID,$goal_id);
    if($result){
        echo "Success";
    }else{
        echo "Error";
    }
    exit;

}else if($_POST['action'] && $_POST['action'] === "DeleteGoal"){
    $goal_id=$_POST['goal_id'];
 $result = $gm->DeleteGoal($goal_id);
    if($result){
        echo "Success";
    }else{
        echo "Error";
    }
    exit;
}






?>
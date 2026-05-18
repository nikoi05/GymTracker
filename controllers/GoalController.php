<?php 

require_once __DIR__ . '/../BL/GoalsManager.php';
require_once __DIR__ . '/../BL/ActivityLogManager.php';
$gm = new GoalsManager();
$activityLogs = new ActivityLogManager();
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
        $activityLogs->record((int)$userID, 'Goal Created', "Created goal '{$goalName}'");
        echo "Success";
    }else{
        echo "Error";
    }
    exit;
}else if(isset($_POST['action']) && $_POST['action'] === "UpdateGoal"){
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
        $activityLogs->record((int)$userID, 'Goal Updated', "Updated goal '{$goalName}'");
        echo "Success";
    }else{
        echo "Error";
    }
    exit;

}else if(isset($_POST['action']) && $_POST['action'] === "MarkGoal"){
    $userID = $_SESSION['userID'];
     $goal_id=$_POST['goal_id'];
     $goal = $gm->getGoalById($goal_id);
 $result = $gm->UpdateMark($userID,$goal_id);
    if($result){
        $goalName = $goal['name'] ?? ('Goal #' . $goal_id);
        $activityLogs->record((int)$userID, 'Goal Completed', "Marked goal '{$goalName}' as completed");
        echo "Success";
    }else{
        echo "Error";
    }
    exit;

}else if(isset($_POST['action']) && $_POST['action'] === "DeleteGoal"){
    $userID = $_SESSION['userID'];
    $goal_id=$_POST['goal_id'];
    $goal = $gm->getGoalById($goal_id);
 $result = $gm->DeleteGoal($goal_id);
    if($result){
        $goalName = $goal['name'] ?? ('Goal #' . $goal_id);
        $activityLogs->record((int)$userID, 'Goal Deleted', "Deleted goal '{$goalName}'");
        echo "Success";
    }else{
        echo "Error";
    }
    exit;
}






?>

<?php 
session_start();
require_once '../BL/WorkoutManager.php';
require_once '../BL/WorkoutExerciseManager.php';
require_once '../BL/userManager.php';
require_once '../BL/WorkoutSessionManage.php';
// Instantation of the object

$wm = new WorkoutManager();
$wem = new WorkoutExerciseManager();
$wsm = new WorkoutSessionManage();

if(isset($_POST['action'])&& $_POST['action']==='AddWorkout'){
 $WorkoutName = $_POST['workoutName'];
 $WorkoutDescription = $_POST['workoutDescription'];
$userID = $_SESSION['userID'];
$result= $wm->addWorkouts($userID,$WorkoutName,$WorkoutDescription);
if($result){
    echo "Success";
}else{
    echo "Error";
}
exit;
}else if(isset($_POST['action']) && $_POST['action'] ==='Delete'){
$result=$wm->DeleteWorkout($_POST['workoutID']) && $wem->deleteWorkout_Exercises($_POST['workoutID']);
if($result){
    echo "success";
}else{
    echo "Error";
}
exit;
}else if(isset($_POST['action']) && $_POST['action'] ==='Edit'){
    $workoutID = $_POST['EditworkoutID'];
    $WorkoutName = $_POST['EditworkoutName'];
    $WorkoutDescription = $_POST['EditworkoutDescription'];
    $result=$wm->EditWorkout($workoutID,$WorkoutName,$WorkoutDescription);
    if($result){
        echo "success";
    }else{
        echo "Error";
    }
    exit;
}else if(isset($_POST['action']) && $_POST['action'] ==='finishWorkout'){
    $userID = $_SESSION['userID'];
    $workoutID = $_POST['workoutID'];
    $seconds = $_POST['duration'];
    $setsCompleted =$_POST['setsCompleted'];
    $result = $wsm->AddWorkoutSession($userID,$workoutID,$seconds,$setsCompleted);
    if($result){
        echo "success";
    }else{
        echo"Error";
    }
    exit;
}
?>
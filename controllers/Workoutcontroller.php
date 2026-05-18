<?php 
session_start();
require_once __DIR__ . '/../BL/WorkoutManager.php';
require_once __DIR__ . '/../BL/WorkoutExerciseManager.php';
require_once __DIR__ . '/../BL/userManager.php';
require_once __DIR__ . '/../BL/WorkoutSessionManage.php';
require_once __DIR__ . '/../BL/ActivityLogManager.php';
// Instantation of the object

$wm = new WorkoutManager();
$wem = new WorkoutExerciseManager();
$wsm = new WorkoutSessionManage();
$activityLogs = new ActivityLogManager();

if(isset($_POST['action'])&& $_POST['action']==='AddWorkout'){
 $WorkoutName = $_POST['workoutName'];
 $WorkoutDescription = $_POST['workoutDescription'];
$userID = $_SESSION['userID'];
$result= $wm->addWorkouts($userID,$WorkoutName,$WorkoutDescription);
if($result){
    $activityLogs->record((int)$userID, 'Workout Created', "Created workout '{$WorkoutName}'", 'success', (int)$result);
    echo "Success";
}else{
    echo "Error";
}
exit;
}else if(isset($_POST['action']) && $_POST['action'] ==='Delete'){
    $workoutID = $_POST['workoutID'];
    $workout = $wm->getWorkoutDetails($workoutID);
$result=$wm->DeleteWorkout($_POST['workoutID']) && $wem->deleteWorkout_Exercises($_POST['workoutID']);
if($result){
    $userID = $_SESSION['userID'] ?? ($workout['userID'] ?? 0);
    $workoutName = $workout['name'] ?? ('Workout #' . $workoutID);
    $activityLogs->record((int)$userID, 'Workout Deleted', "Deleted workout '{$workoutName}'");
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
        $userID = $_SESSION['userID'] ?? 0;
        $activityLogs->record((int)$userID, 'Workout Updated', "Updated workout '{$WorkoutName}'", 'success', (int)$workoutID);
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
        $workout = $wm->getWorkoutDetails($workoutID);
        $workoutName = $workout['name'] ?? ('Workout #' . $workoutID);
        $activityLogs->record((int)$userID, 'Workout Completed', "Completed workout '{$workoutName}'", 'success', (int)$workoutID);
        echo "success";
    }else{
        echo"Error";
    }
    exit;
}
?>

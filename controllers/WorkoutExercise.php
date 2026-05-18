<?php 
session_start();
require_once '../BL/WorkoutExerciseManager.php';
require_once '../BL/ActivityLogManager.php';
$wem = new WorkoutExerciseManager();
$activityLogs = new ActivityLogManager();
$result = null;
if(isset($_POST['action'])&& $_POST['action'] === 'add_exercise_to_workout'){
    $workoutID = $_POST['workoutId'];
    $exerciseID = $_POST['exerciseId'] ?? null;
    $typeID = $_POST['typeID'] ?? null;
    $userID = $_SESSION['userID'];
    $sets   = $_POST['sets'] ?? null;
    $reps   = $_POST['reps'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $rest   = $_POST['rest'] ?? null;
    $duration=$_POST['duration'] ?? null;
    $distance = $_POST['distance'] ?? null;
    $hold     = $_POST['hold'] ?? null;
    $muscle = $_POST['muscle'] ?? null;
    $name= $_POST['name']?? null;
    $result = $wem->addExerciseToWorkout([
        'workoutID' => $workoutID,
        'exerciseID' => $exerciseID,
        'userID' => $userID,
        'sets' => $sets,
        'reps' => $reps,
        'weight' => $weight,
        'rest' => $rest,
        'duration' => $duration,
        'distance' => $distance,
        'hold' => $hold,
        'name' => $name,
        'typeID'=>$typeID,
        'muscle'=>$muscle

    ]);
    if($result){
        $exerciseName = $name ?: ('Exercise #' . $exerciseID);
        $activityLogs->record((int)$userID, 'Workout Exercise Added', "Added '{$exerciseName}' to workout #{$workoutID}", 'success', (int)$workoutID, $exerciseID ? (int)$exerciseID : null);
        echo "success";
    }else{
        echo "Error";
    }
    exit;
}

elseif(isset($_POST['action']) && $_POST['action'] === 'updateExercise'){
    $workoutExerciseId = $_POST['workoutExerciseId'] ?? null;
    $exerciseType = $_POST['exerciseType'] ?? 'strength';
    $existingExercise = $workoutExerciseId ? $wem->getWorkoutExerciseById($workoutExerciseId) : false;

   
    $sets = $_POST['sets'] ?? null;
    $reps = $_POST['reps'] ?? null;
    $weight = $_POST['weight'] ?? 0;
    $duration = $_POST['duration'] ?? 60;
    $rest = $_POST['rest'] ?? null;
    $distance = $_POST['distance'] ?? null;
    $hold = $_POST['hold'] ?? null;

    if(!$workoutExerciseId){
        echo "Error: Missing exercise ID";
        exit;
    }
    // Strength needs sets+reps
    if($exerciseType === 'strength' && (!$sets || !$reps)){
        echo "Error: Missing sets or reps";
        exit;
    }

    // Bodyweight needs sets+reps
    if($exerciseType === 'bodyweight' && (!$sets || !$reps)){
        echo "Error: Missing sets or reps";
        exit;
    }

    // Cardio needs distance
    if($exerciseType === 'cardio' && ( !isset($distance) || $distance === '' || $distance === null)){
        echo "Error: Missing distance";
        exit;
    }

    // Flexibility needs hold
    if($exerciseType === 'flexibility' && ( !isset($hold) || $hold === '' || $hold === null)){
        echo "Error: Missing hold";
        exit;
    }

    // Update using existing model signature (sets/reps/weight/duration)
    // For non-strength types we still pass sets/reps to satisfy existing SQL; use defaults.
    $updateSets = $sets ?? 1;
    $updateReps = $reps ?? 1;
    $updateWeight = ($exerciseType === 'bodyweight' || $exerciseType === 'flexibility' || $exerciseType === 'cardio') ? 0 : $weight;

    // Map type-specific fields into DB columns
    $distanceKm = ($exerciseType === 'cardio') ? $distance : null;
    $holdTimeSec = ($exerciseType === 'flexibility') ? $hold : null;
    $restSec = ($exerciseType === 'bodyweight') ? $rest : null;

    $result = $wem->updateWorkoutExercise(
        $workoutExerciseId,
        $updateSets,
        $updateReps,
        $updateWeight,
        $duration,
        $distanceKm,
        $holdTimeSec,
        $restSec
    );

    if($result){
        $logUserID = (int)($existingExercise['userID'] ?? ($_SESSION['userID'] ?? 0));
        $logWorkoutID = isset($existingExercise['workoutId']) ? (int)$existingExercise['workoutId'] : null;
        $logExerciseID = isset($existingExercise['exerciseID']) ? (int)$existingExercise['exerciseID'] : null;
        $exerciseName = $existingExercise['exerciseName'] ?? ('Workout exercise #' . $workoutExerciseId);
        $activityLogs->record($logUserID, 'Workout Exercise Updated', "Updated '{$exerciseName}' in workout", 'success', $logWorkoutID, $logExerciseID ?: null);
        echo "success";
    }else{
        echo "Error: Failed to update exercise";
    }
    exit;
}

elseif(isset($_POST['action']) && $_POST['action'] ==='delete'){
    $workoutexerciseID = $_POST['workoutexerciseID'] ?? null;
    $existingExercise = $workoutexerciseID ? $wem->getWorkoutExerciseById($workoutexerciseID) : false;
    if(!$workoutexerciseID){
        echo "ERROR: MISSING exercise ID";
        exit;
    }else{
        $result = $wem->deleteExercise($workoutexerciseID);
    }
    if($result){
        $logUserID = (int)($existingExercise['userID'] ?? ($_SESSION['userID'] ?? 0));
        $logWorkoutID = isset($existingExercise['workoutId']) ? (int)$existingExercise['workoutId'] : null;
        $exerciseName = $existingExercise['exerciseName'] ?? ('Workout exercise #' . $workoutexerciseID);
        $activityLogs->record($logUserID, 'Workout Exercise Deleted', "Deleted '{$exerciseName}' from workout", 'success', $logWorkoutID);
        echo"success";
    }else{
        echo "Error: Failed to delete exercise";
    }
}









?>

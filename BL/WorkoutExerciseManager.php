<?php 

require_once  __DIR__ . '/../config/database.php';
require_once  __DIR__ . '../../models/WorkoutExerciseModel.php'; 

class WorkoutExerciseManager{
    private $WorkoutExerciseModel;
    public function __construct()
    {
        $database = new Workout_Database();
        $db = $database->_ConnectDB();
        $this->WorkoutExerciseModel = new WorkoutExerciseModel($db);
    }

    public function addExerciseToWorkout($data){
        return $this->WorkoutExerciseModel->addExerciseToWorkout($data);
    }
    public function getWorkoutExercisesData($id){
        return $this->WorkoutExerciseModel->getWorkoutExercisesInfo($id);
    }
    public function getTotalCard($id){
        return $this->WorkoutExerciseModel->getTotalCards($id);
    }
    public function deleteWorkout_Exercises($id){
        return $this->WorkoutExerciseModel->deleteWorkoutExcercise($id);

}
public function addCustomExercise($data){
        return $this->WorkoutExerciseModel->addCustomExercise($data);
     }
     
     
     public function updateWorkoutExercise($workoutExerciseId, $sets, $reps, $weight, $duration, $distanceKm = null, $holdTimeSec = null, $rest = null){
        return $this->WorkoutExerciseModel->updateWorkoutExercise($workoutExerciseId, $sets, $reps, $weight, $duration, $distanceKm, $holdTimeSec, $rest);
     }
    public function deleteExercise($id){
        return $this->WorkoutExerciseModel->deleteExercise($id);
    }
}



?>
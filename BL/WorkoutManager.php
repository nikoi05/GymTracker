<?php 
require_once  __DIR__ . '/../config/database.php';
require_once  __DIR__ . '../../models/WorkoutModel.php'; 

class WorkoutManager{
    private $WorkoutModel;
    public function __construct()
    {
        $database = new Workout_Database();
        $db = $database->_ConnectDB(); 
        $this->WorkoutModel = new WorkoutModel($db);
    }
    public function readWorkouts($userID){
        return $this->WorkoutModel->readWorkout($userID);
    }
    public function addWorkouts($userID, $name, $workoutDescription){
        return $this->WorkoutModel->addWorkout($userID,$name,$workoutDescription);
    }
    public function getWorkoutDetails($workoutID){
        return $this->WorkoutModel->getWorkoutDetails($workoutID);
    }
    public function DeleteWorkout($workoutID){
        return $this->WorkoutModel->DeleteWorkout($workoutID);
    }
    public function totalWorkoutsLogged(){
        return $this->WorkoutModel->totalWorkoutsLogged();
    }
    public function EditWorkout($workoutID,$name,$workoutDescription){
        return $this->WorkoutModel->EditWorkout($workoutID,$name,$workoutDescription);
}

}

?>
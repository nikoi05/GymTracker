<?php 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/WorkoutModel.php';
require_once __DIR__ . '/../models/WorkoutSessionModel.php'; 

class WorkoutSessionManage{
private $WorkoutSessionModel;
public function __construct(){
    $database = new Workout_Database();
    $db= $database->_ConnectDB();
    $this->WorkoutSessionModel = new WorkoutSessionModel($db);
}
public function AddWorkoutSession($userID,$workoutID,$minutes,$setscompleted){
    return $this->WorkoutSessionModel->addWorkoutSession($userID,$workoutID,$minutes,$setscompleted);
}
public function GetTotalDuration($userID){
    return $this->WorkoutSessionModel->getTotalDuration($userID);
}
public function GetTotalWorkoutMonth($userId){
    return $this->WorkoutSessionModel->getTotalWorkoutsMonth($userId);
}
public function GetWeeklyActivity($userID){
    return $this->WorkoutSessionModel->getWeeklyActivity($userID);
}
public function GetMonthlyActivity($userID){
    return $this->WorkoutSessionModel->getMonthlyActivity($userID);
}
public function AllGetWeeklyActivity(){
    return $this->WorkoutSessionModel->getAllWeeklyActivity();
}
public function AllGetMonthlyActivity(){
    return $this->WorkoutSessionModel->getAllMonthlyActivity();
}
public function GetTotalWorkouts($userID){
    return $this->WorkoutSessionModel->getTotalWorkouts($userID);
}
public function GetStreak($userID){
    return $this->WorkoutSessionModel->getSTREAK($userID);
}
public function GetRecentWorkouts($userID){
    return $this->WorkoutSessionModel->getRecentWorkouts($userID);
}
public function GetWorkoutAnalytics($userID){
    return $this->WorkoutSessionModel->getWorkoutAnalytics($userID);
}
}
?>
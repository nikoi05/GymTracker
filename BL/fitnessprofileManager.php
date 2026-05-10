<?php 
require_once '../config/database.php';
require_once '../models/fitnesProfModel.php';

class fitnessprofileManager{
  private  $fitnesProfModel;

public function __construct()
{
    $database = new Workout_Database();
    $db = $database->_ConnectDB();
    $this->fitnesProfModel = new FitnesProfModel($db);
}

public function insertOnboardingData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration){
   return $this->fitnesProfModel->insertData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration);
}
public function insertpreferences($userID,$preferredWorkouts,$locations,$equipment,$motivation){
    return $this->fitnesProfModel->insertPreferences($userID,$preferredWorkouts,$locations,$equipment,$motivation);
}

public function getOnboardingData($userID){
    return $this->fitnesProfModel->getData($userID);
}

public function getPreferences($userID){
    return $this->fitnesProfModel->getPreferences($userID);
}

public function updateOnboardingData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration){
    return $this->fitnesProfModel->updateData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration);
}
}
?>

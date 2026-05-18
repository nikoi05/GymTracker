<?php 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/fitnesProfModel.php';

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

public function getOnboardingData($userID){
    return $this->fitnesProfModel->getData($userID);
}

public function updateOnboardingData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration){
    return $this->fitnesProfModel->updateData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration);
}
public function getLevels(){ return $this->fitnesProfModel->getLevels(); }
public function getFitnessGoals(){ return $this->fitnesProfModel->getFitnessGoals(); }
public function getFrequencies(){ return $this->fitnesProfModel->getFrequencies(); }
public function getDurations(){ return $this->fitnesProfModel->getDurations(); }
}
?>

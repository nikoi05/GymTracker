<?php 
require_once  __DIR__ . '/../config/database.php';
require_once  __DIR__ . '/../models/GoalsModel.php'; 
class GoalsManager{
    private $goalsModel;
    public function __construct(){
        $database = new Workout_Database();
        $db = $database->_ConnectDB();
        $this->goalsModel = new GoalsModel($db);

    }

    public function AddGoals($userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status){
        return $this->goalsModel->AddGoals($userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status);
        
    }
    public function readGoals($userID){
        return $this->goalsModel->readGoals($userID);
    }
    public function getGoalById($goalID){
        return $this->goalsModel->getGoalById($goalID);
    }
    public function UpdateGoals($goalID,$userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status){
        return $this->goalsModel->UpdateGoals($goalID,$userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status);
    }
    public function UpdateMark($userID,$goal_id){
        return $this->goalsModel->UpdateMark($userID,$goal_id);
    }
    public function DeleteGoal($goal_id){
        return $this->goalsModel->DeleteGoal($goal_id);
    }
    public function GetGoalsInfos($userID){
        return $this->goalsModel->GetGoalsInfos($userID);
    }
    public function getCategories(){
        return $this->goalsModel->getCategories();
    }
}




?>

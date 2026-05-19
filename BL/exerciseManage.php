<?php 
require_once  __DIR__ . '/../config/database.php';
require_once  __DIR__ . '../../models/ExerciseModel.php'; 

class ExerciseManage{
    private $ExerciseModel;
    public function __construct(){
        $database= new Workout_Database();
        $db =$database->_ConnectDB();
        $this->ExerciseModel = new ExerciseModel($db);

    }
    public function readExercises(){
        return $this->ExerciseModel->readExercises();
    }
    public function getSteps(){
        return $this->ExerciseModel->getSteps();
}
public function readtotalHomeexercises(){
    return $this->ExerciseModel->readtotalHomeexercises();
}
public function getMuscleGroups(){
    return $this->ExerciseModel->getMuscleGroups();
}
public function getExerciseType(){
    return $this->ExerciseModel->getExerciseType();
}
public function getStepsByExerciseId($exerciseID){
    return $this->ExerciseModel->getStepsByExerciseId((int)$exerciseID);
}
public function createExerciseWithLogs(array $payload, int $actorUserId){
    return $this->ExerciseModel->createExerciseWithLogs($payload, $actorUserId);
}
public function updateExerciseWithLogs(int $exerciseID, array $payload, int $actorUserId){
    return $this->ExerciseModel->updateExerciseWithLogs($exerciseID, $payload, $actorUserId);
}
public function deleteExerciseWithLogs(int $exerciseID, int $actorUserId){
    return $this->ExerciseModel->deleteExerciseWithLogs($exerciseID, $actorUserId);
}

public function getAllExercise(){
    return $this->ExerciseModel->getAllExercise();
}
public function getDifficulty(){
    return $this->ExerciseModel->getDifficulty();
}
public function getExerciseAdmStats(){
    return $this->ExerciseModel->getExerciseAdmStats();
}
public function getChartsData(){
    return $this->ExerciseModel->getChartsData();
}
 public function getLocation(){
    return $this->ExerciseModel->getLocation();
 }
 public function getUserStats(){
    return $this->ExerciseModel->getUserStats();

 }
}
?>

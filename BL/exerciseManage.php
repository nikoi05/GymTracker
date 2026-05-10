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
}

?>
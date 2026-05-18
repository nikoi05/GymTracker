<?php 
require_once __DIR__ . '/../config/database.php';

class WorkoutModel{
    private $conn;
    public function __construct($db){
      $this->conn= $db;
    }
  
  public function readWorkout($userID){
    try{
      $sqlquery ="SELECT * FROM tbl_workouts WHERE userID = :userID  ORDER BY userID LIMIT 100";
      $stmt = $this->conn->prepare($sqlquery);
      $stmt->bindParam(":userID",$userID);
      $stmt->execute();
      return $stmt->fetchAll(PDO ::FETCH_ASSOC);
    }catch(PDOException $e){
      echo "Error" . $e->getMessage();
    }
  }
  
  public function addWorkout($userID,$workoutName,$workoutDescription){
    try{
      $sqlQuery = "INSERT INTO tbl_workouts (userID,name,workout_description,created_At,updated_At) VALUES (:userID,:name, :workout_description, NOW(), NOW())";
      $stmt =$this->conn->prepare($sqlQuery);
      $stmt->bindParam(":userID",$userID);
      $stmt->bindParam(":name",$workoutName);
       $stmt->bindParam(":workout_description",$workoutDescription);
      return $stmt->execute() ? (int)$this->conn->lastInsertId() : false;
    }catch(PDOException $e){
      echo "Error" . $e->getMessage();
      return false;
    }
  }
  public function getWorkoutDetails($workoutID){
    try{
        $sqlQuery = "SELECT * FROM tbl_workouts WHERE workoutID = :workoutID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':workoutID', $workoutID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    }

}
public function DeleteWorkout($workoutID){
  try{
    $sqlQuery ="DELETE FROM tbl_workouts WHERE workoutID = :workoutID";
    $stmt=$this->conn->prepare($sqlQuery);
    $stmt->bindParam(':workoutID',$workoutID);
    $stmt->execute();
    return true;

  }catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    return false;
  }
}
public function TotalWorkoutsLogged(){
  try{
    $sqlQuery = "SELECT COUNT(*) as total_workouts FROM tbl_workouts";
    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total_workouts'];
  }catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    return false;
  }
}
public function EditWorkout($workoutID,$name,$workoutDescription){
  try{
    $sqlQuery = "UPDATE tbl_workouts SET name = :name, workout_description = :workout_description, updated_At = NOW() WHERE workoutID = :workoutID";
    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':workout_description', $workoutDescription);
    $stmt->bindParam(':workoutID', $workoutID);
    return $stmt->execute();
  }catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    return false;
  }
}
}





?>

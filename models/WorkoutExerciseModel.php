<?php
require_once __DIR__ . '/../config/database.php';

class WorkoutExerciseModel{
    private $conn;
    public function __construct($db){
      $this->conn= $db;
    }

    public function addExerciseToWorkout($data){
        try{
            $sqlquery = "INSERT INTO tbl_workout_exercises
                (userID, workoutId, exerciseID,customExerciseName,muscle,type, sets, reps, weight, distance_km, durationInMinutes, holdTimeSec, rest, created_At, updated_At)
                VALUES
                (:userID, :workoutID, :exerciseID,:customExerciseName,:muscle,:type, :sets, :reps, :weight, :distance_km, :durationInMinutes, :holdTimeSec, :rest, NOW(), NOW())";

            $stmt = $this->conn->prepare($sqlquery);


            $distance_km = $data['distance_km'] ?? $data['distance'] ?? null;
            $durationInMinutes = $data['durationInMinutes'] ?? $data['duration'] ?? null;
            $holdTimeSec = $data['holdTimeSec'] ?? $data['hold'] ?? null;

            $stmt->bindParam(':userID', $data['userID']);
            $stmt->bindParam(':workoutID', $data['workoutID']);
            $stmt->bindParam(':exerciseID', $data['exerciseID']);
            $stmt->bindParam(':customExerciseName', $data['name']);
            $stmt->bindParam(':muscle', $data['muscle']);
            $stmt->bindParam(':type', $data['typeID']);
            $stmt->bindParam(':sets', $data['sets']);
            $stmt->bindParam(':reps', $data['reps']);
            $stmt->bindParam(':weight', $data['weight']);
            $stmt->bindParam(':distance_km', $distance_km);
            $stmt->bindParam(':durationInMinutes', $durationInMinutes);
            $stmt->bindParam(':holdTimeSec', $holdTimeSec);
            $stmt->bindParam(':rest', $data['rest']);

            return $stmt->execute();
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function addCustomExercise($data){
            try{
                $sqlquery ="INSERT INTO tbl_workout_exercises (userID,workoutId,customExerciseName,sets,reps,weight,durationInMinutes,created_At,updated_At) 
                VALUES (:userID,:workoutID,:customExerciseName,:sets,:reps,:weight,:duration,NOW(),NOW())";
                $stmt=$this->conn->prepare($sqlquery);
            return $stmt->execute();
            }catch(PDOException $e){
            echo "Error" . $e->getMessage();
            return false;
        }
        }


        // NOTE OR WORDS OF PAALALA , WE USED COALESCE HERE BECAUSE WE HAVE TWO COLUMN THAT CAN BE USED INTO A SINGLE ONE IN RENDERING
        //if first column is null then the fallback will be the next column // meron tayong manual and library exercises so to present
        //gumamit tayo coalesce ayun lang
public function getWorkoutExercisesInfo($id){
    try{
        $sqlQuery = "SELECT 
    we.workout_exercises_ID,
    we.exerciseID,
    COALESCE(e.name,we.customExerciseName) as exerciseName,
    we.sets,
    we.reps,
    we.weight,
    we.durationInMinutes,
    w.name as Workoutname,
    we.holdTimeSec,
    we.rest,
    we.distance_km,
    et.description as exerciseType,
    
    COALESCE(e.muscle, m.muscle) as muscle
FROM tbl_workout_exercises we
INNER JOIN tbl_workouts w ON we.workoutId = w.workoutID
LEFT JOIN tbl_exercise_type et ON we.type = et.typeID
LEFT JOIN tbl_exercises e ON we.exerciseID = e.exerciseID
LEFT JOIN tbl_muscles m ON we.muscle = m.muscleID
WHERE we.workoutId = :workoutId
ORDER BY we.created_At DESC;";
 $stmt = $this->conn->prepare($sqlQuery);
 $stmt->bindParam(':workoutId',$id);
 $stmt->execute();
 return $stmt->fetchALL(PDO::FETCH_ASSOC);

    }catch(PDOException $e){
        echo "Error " . $e->getMessage();
        return false;
    }
}
public function getWorkoutExerciseById($id){
    try{
        $sqlQuery = "SELECT
            we.workout_exercises_ID,
            we.userID,
            we.workoutId,
            we.exerciseID,
            COALESCE(e.name, we.customExerciseName) AS exerciseName,
            w.name AS workoutName
        FROM tbl_workout_exercises we
        LEFT JOIN tbl_workouts w ON we.workoutId = w.workoutID
        LEFT JOIN tbl_exercises e ON we.exerciseID = e.exerciseID
        WHERE we.workout_exercises_ID = :id
        LIMIT 1";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        return false;
    }
}
public function getTotalCards($id){
    try{
        $sqlQuery ="SELECT 
    SUM(we.durationInMinutes) AS total_minutes,
    SUM(we.sets) AS total_Sets,
    COUNT(we.workout_exercises_ID) AS total_exercises
    FROM tbl_workout_exercises we
    WHERE we.workoutId = :id";
    $stmt=$this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id',$id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }

}
public function totalcards(){
    try{
        $sqlQuery ="SELECT 
    SUM(we.durationInMinutes) AS total_minutes,
    SUM(we.sets) AS total_Sets,
    COUNT(we.workout_exercises_ID) AS total_exercises
    FROM tbl_workout_exercises we";
    $stmt=$this->conn->prepare($sqlQuery);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }


}
public function deleteWorkoutExcercise($id){
     try{
    $sqlQuery ="DELETE FROM tbl_workout_exercises WHERE workoutId = :workoutID";
    $stmt=$this->conn->prepare($sqlQuery);
    $stmt->bindParam(':workoutID',$id);
    $stmt->execute();
    return true;

  }catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    return false;
  }
}
public function deleteExercise($id){
    try{
        $sqlquery ="DELETE FROM tbl_workout_Exercises WHERE workout_exercises_ID = :workout_exercises_ID";
        $stmt= $this->conn->prepare($sqlquery);
        $stmt->bindParam(':workout_exercises_ID',$id);
        return $stmt->execute();
    }catch(PDOException $e){
        echo "Error". $e->getMessage();
        return false;
    }
}

public function updateWorkoutExercise($workoutExerciseId, $sets, $reps, $weight, $duration, $distanceKm = null, $holdTimeSec = null, $rest = null){
    try{
        $sqlQuery = "UPDATE tbl_workout_exercises 
                    SET sets = :sets,
                        reps = :reps,
                        weight = :weight,
                        distance_km = :distance_km,
                        durationInMinutes = :duration,
                        holdTimeSec = :holdTimeSec,
                        rest = :rest,
                        updated_At = NOW() 
                    WHERE workout_exercises_ID = :workoutExerciseId";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':workoutExerciseId', $workoutExerciseId);
        $stmt->bindParam(':sets', $sets);
        $stmt->bindParam(':reps', $reps);
        $stmt->bindParam(':weight', $weight);
        $stmt->bindParam(':distance_km', $distanceKm);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':holdTimeSec', $holdTimeSec);
        $stmt->bindParam(':rest', $rest);
        return $stmt->execute();
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    }
}


}

?>

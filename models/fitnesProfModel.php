<?php 
class FitnesProfModel{
private $conn;
public function __construct($db)
{
    $this->conn=$db;
}
public function insertData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration){
try{
    $sql_datetime = date('Y-m-d H:i:s');
    $sqlQuery = "INSERT INTO tbl_userprofile (userID, height, weight, Physical_limitations,fitnessLevel,fitnessGoal,workoutFrequency,pref_duration,created_At,updated_At)
    VALUES (:userID, :height, :weight, :Physical_limitations, :fitnessLevel, :fitnessGoal,:workoutFrequency, :pref_duration, :created_At, :updated_At) ";
    $stmt=$this->conn->prepare($sqlQuery);
    $stmt->bindParam(':userID',$userID);
    $stmt->bindParam(':height',$height);
    $stmt->bindParam(':weight',$weight);
    $stmt->bindParam(':Physical_limitations',$limitations);
    $stmt->bindParam(':fitnessLevel',$fitnessLevel);
    $stmt->bindParam(':fitnessGoal',$fitnessGoal);
    $stmt->bindParam(':workoutFrequency',$workoutFrequency);
    $stmt->bindParam(':pref_duration',$workoutDuration);
    $stmt->bindParam(':created_At',$sql_datetime);
    $stmt->bindParam(':updated_At',$sql_datetime);
    $stmt->execute();

    return $stmt->rowCount() >0;
    

}catch(PDOException $e){
    echo "ERROR: " . $e->getMessage();
    return false;
}
}
    
public function insertPreferences($userId, $preferredWorkouts, $locations, $equipment, $motivation){
     try {
        // 1. Preferred Workouts
        if (!empty($preferredWorkouts)) {
            $stmt = $this->conn->prepare("INSERT INTO tbl_userpref_workouts (userID, workout_name, created_At, updated_At) VALUES (:userID, :workout, NOW(), NOW())");
            foreach ($preferredWorkouts as $workout) {
                $stmt->execute([':userID' => $userId, ':workout' => $workout]);
            }
        }

        // 2. Locations
        if (!empty($locations)) {
            $stmt = $this->conn->prepare("INSERT INTO tbl_userpref_locations (userID, Location, created_At, updated_At) VALUES (:userID, :location, NOW(), NOW())");
            foreach ($locations as $location) {
                $stmt->execute([':userID' => $userId, ':location' => $location]);
            }
        }

        // 3. Equipment
        if (!empty($equipment)) {
            $stmt = $this->conn->prepare("INSERT INTO tbl_userpref_equipment (userID, equipment_description, created_At, updated_At) VALUES (:userID, :equipment_desc, NOW(), NOW())");
            foreach ($equipment as $equipment_desc) {
                $stmt->execute([':userID' => $userId, ':equipment_desc' => $equipment_desc]);
            }
        }

        // 4. Motivation
        if (!empty($motivation)) {
            $stmt = $this->conn->prepare("INSERT INTO tbl_userpref_motivation (userID, motivation_desc, created_At, updated_At) VALUES (:userID, :motivation_desc, NOW(), NOW())");
            foreach ($motivation as $motivation_desc) {
                $stmt->execute([':userID' => $userId, ':motivation_desc' => $motivation_desc]);
            }
        }

        // ONLY return true after EVERYTHING is done
        return true;

    } catch (PDOException $e) {
        // This will catch errors from ANY of the sections above

        error_log("Database Error: " . $e->getMessage());
        echo " DATABASE Error: " . $e->getMessage();
        return false;
    }
}

public function getData($userID){
    try{
        $sqlQuery = "SELECT * FROM tbl_userprofile WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    }
}

public function getPreferences($userID){
    $prefs = [];
    try{
        $sqlQuery = "SELECT workout_name FROM tbl_userpref_workouts WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $prefs['workouts'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $sqlQuery = "SELECT Location FROM tbl_userpref_locations WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $prefs['locations'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $sqlQuery = "SELECT equipment_description FROM tbl_userpref_equipment WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $prefs['equipment'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $sqlQuery = "SELECT motivation_desc FROM tbl_userpref_motivation WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $prefs['motivation'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $prefs;
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return $prefs;
    }
}

public function updateData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration){
    try{
        $sql_datetime = date('Y-m-d H:i:s');
        $sqlQuery = "UPDATE tbl_userprofile SET height = :height, weight = :weight, Physical_limitations = :Physical_limitations, fitnessLevel = :fitnessLevel, fitnessGoal = :fitnessGoal, workoutFrequency = :workoutFrequency, pref_duration = :pref_duration, updated_At = :updated_At WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID',$userID);
        $stmt->bindParam(':height',$height);
        $stmt->bindParam(':weight',$weight);
        $stmt->bindParam(':Physical_limitations',$limitations);
        $stmt->bindParam(':fitnessLevel',$fitnessLevel);
        $stmt->bindParam(':fitnessGoal',$fitnessGoal);
        $stmt->bindParam(':workoutFrequency',$workoutFrequency);
        $stmt->bindParam(':pref_duration',$workoutDuration);
        $stmt->bindParam(':updated_At',$sql_datetime);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }catch(PDOException $e){
        echo "ERROR: " . $e->getMessage();
        return false;
    }
}
}
?>

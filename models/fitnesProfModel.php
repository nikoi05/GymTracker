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
    return (int)$this->conn->lastInsertId() > 0;
    

}catch(PDOException $e){
    error_log("insertData error: " . $e->getMessage());
    return false;
}
}
public function getData($userID){
    try{
        $sqlQuery = "SELECT
        up.user_pref_ID,
        up.height,
        up.weight,
        up.Physical_limitations,
        up.fitnessGoal,
        up.fitnessLevel,
        up.workoutFrequency,
        up.pref_duration,
        fg.goal_label AS fitnessGoalLabel,
        el.difficulty AS fitnessLevelLabel,
        wf.freq_label AS workoutFrequencyLabel,
        ds.duration_label AS pref_durationLabel
        
        
FROM tbl_userprofile up
LEFT JOIN tbl_fitness_goals fg ON up.fitnessGoal = fg.goalID
LEFT JOIN tbl_exercise_level el ON up.fitnessLevel = el.exerciselevelID
LEFT JOIN tbl_workout_frequency wf ON up.workoutFrequency = wf.frequencyID
LEFT JOIN tbl_session_duration ds ON up.pref_duration = ds.durationID
WHERE up.userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        error_log("getData error: " . $e->getMessage());
        return false;
    }
}
public function updateData($userID,$height,$weight,$limitations,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration){
    try{
        $sql_datetime = date('Y-m-d H:i:s');
        $sqlQuery = "UPDATE tbl_userprofile SET height=:height, weight=:weight, Physical_limitations=:Physical_limitations, fitnessLevel=:fitnessLevel, fitnessGoal=:fitnessGoal, workoutFrequency=:workoutFrequency, pref_duration=:pref_duration, updated_At=:updated_At WHERE userID=:userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID',               $userID);
        $stmt->bindParam(':height',               $height);
        $stmt->bindParam(':weight',               $weight);
        $stmt->bindParam(':Physical_limitations', $limitations);
        $stmt->bindParam(':fitnessLevel',         $fitnessLevel);
        $stmt->bindParam(':fitnessGoal',          $fitnessGoal);
        $stmt->bindParam(':workoutFrequency',     $workoutFrequency);
        $stmt->bindParam(':pref_duration',        $workoutDuration);
        $stmt->bindParam(':updated_At',           $sql_datetime);
        $stmt->execute();
        return true;
    }catch(PDOException $e){
        error_log("updateData error: " . $e->getMessage());
        return false;
    }
}
public function getLevels(){
    $stmt = $this->conn->query("SELECT exerciselevelID as id, difficulty as label, exerciselevelID as value FROM tbl_exercise_level ORDER BY exerciselevelID");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getFitnessGoals(){
    $stmt = $this->conn->query("SELECT goalID as id, goal_label as label, goalID as value FROM tbl_fitness_goals ORDER BY goalID");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getFrequencies(){
    $stmt = $this->conn->query("SELECT frequencyID as id, freq_label as label, frequencyID as value FROM tbl_workout_frequency ORDER BY frequencyID");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getDurations(){
    $stmt = $this->conn->query("SELECT durationID as id, duration_label as label, durationID as value FROM tbl_session_duration ORDER BY durationID");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>

<?php 
class GoalsModel{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }

    public function AddGoals($userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status){
        try{
            $sqlquery = "INSERT INTO tbl_goals (userID,name,description,category,current_value,target_value,status,deadline,created_At,updated_At) 
            VALUES (:userID,:name,:description,:category,:current_value,:target_value,:status,:deadline,NOW(),NOW())";
            $stmt = $this->conn->prepare($sqlquery);
            $stmt->bindParam(':userID',$userID);
            $stmt->bindParam(':name',$goalName);
            $stmt->bindParam(':description',$goalDesc);
            $stmt->bindParam(':category',$goalCategory);
            $stmt->bindParam(':current_value',$goalCurrent);
            $stmt->bindParam(':target_value',$goalTarget);
            $stmt->bindParam(':status',$status);
            $stmt->bindParam(':deadline',$goalDeadline);
            return $stmt->execute();
        
        }catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }

}
public function readGoals($userID){
    try{
        $sqlquery = "SELECT * FROM tbl_goals WHERE userID = :userID ORDER BY userID LIMIT 100";
        $stmt=$this->conn->prepare($sqlquery);
        $stmt->bindParam(':userID',$userID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }
}
public function UpdateGoals($goalID,$userID,$goalName,$goalDesc,$goalCategory,$goalCurrent,$goalTarget,$goalDeadline,$status){
    try{
        $sqlquery = "UPDATE tbl_goals SET name=:name,description=:description,category=:category,current_value=:current_value,
    target_value=:target_value,status=:status,
    deadline=:deadline,updated_At=NOW() 
    WHERE userID=:userID AND goal_id =:goalID";
    $stmt=$this->conn->prepare($sqlquery);
      $stmt->bindParam(':userID',$userID);
            $stmt->bindParam(':name',$goalName);
            $stmt->bindParam(':goalID',$goalID);
            $stmt->bindParam(':description',$goalDesc);
            $stmt->bindParam(':category',$goalCategory);
            $stmt->bindParam(':current_value',$goalCurrent);
            $stmt->bindParam(':target_value',$goalTarget);
            $stmt->bindParam(':status',$status);
            $stmt->bindParam(':deadline',$goalDeadline);
            return $stmt->execute();
    }catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }
}
public function UpdateMark($userID,$goal_id){
try{
    $sqlquery=" UPDATE tbl_goals SET target_value = current_value, status ='completed' WHERE userID = :userID AND goal_id = :goal_id";
    $stmt=$this->conn->prepare($sqlquery);
    $stmt->bindParam(':userID',$userID);
    $stmt->bindParam(':goal_id',$goal_id);
    $stmt->execute();
    return true;
}catch(PDOException $e){
    echo $e->getMessage();
    return false;
}
}
public function DeleteGoal($goalID){
    try{
        $sqlquery = "DELETE FROM tbl_goals WHERE goal_id = :goalID";
        $stmt = $this->conn->prepare($sqlquery);
        $stmt->bindParam(':goalID',$goalID);
        $stmt->execute();
        return true;
    }catch(PDOException $e){
    echo $e->getMessage();
    return false;
}
}
public function GetGoalsInfos($userID){
    try{
        $sqlquery ="SELECT 
COUNT(*) AS Totalgoals,
SUM(STATUS='in-progress') AS ActiveGoals,
SUM(STATUS='completed') AS CompletedGoals,
ROUND( AVG( CASE WHEN target_value > 0 THEN (current_value/ target_value) * 100 ELSE 0 END ) ) AS AvgProgress

FROM tbl_goals
WHERE userID = :userID";
$stmt = $this->conn->prepare($sqlquery);
$stmt->bindParam(':userID',$userID);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
return $data;

    }catch(PDOException $e){
        echo $e->getMessage();
        return false;
    }
}
}






?>
<?php 
class WorkoutSessionModel{
    private $conn;

    public function __construct($db){
    $this->conn = $db;
    }


public function addWorkoutSession($userId,$workoutID,$seconds,$setsCompleted){
    $status = "completed";
try{
    $sqlquery = "INSERT INTO tbl_user_workout_sessions (userID, workoutID, date_completed, duration_seconds,
    sets_done,status,created_At,updated_At) VALUES (:userID, :workoutID, NOW(),
     :duration_seconds,:sets_done,:status,NOW(),NOW())";
    $stmt = $this->conn->prepare($sqlquery);
    $stmt->bindParam(':userID',$userId);
    $stmt->bindParam(':workoutID',$workoutID);
    $stmt->bindParam(':duration_seconds',$seconds);
    $stmt->bindParam(':sets_done',$setsCompleted);
    $stmt->bindParam(':status',$status);
    return $stmt->execute();
    
    
}catch(PDOException $e){
    echo "Error" . $e->getMessage();
    return false;
}
}
public function getTotalDuration($userID){
        try{
        $sqlQuery = "SELECT SUM(duration_seconds) as totalSeconds
FROM tbl_user_workout_sessions
WHERE userID = :userID
AND MONTH(date_completed)=MONTH(CURDATE())
	";
    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam('userID',$userID);
      $stmt->execute();
       $row =$stmt->fetch(PDO::FETCH_ASSOC);
       // to structure it into hour then minutes
       $totalseconds = $row['totalSeconds'] ?? 0;
       $totalminutes = floor($totalseconds/60); // if I use this it will return the total minutes included in the hour 
       $totalhours = floor($totalminutes/60);

       $remainingMinutes = $totalminutes % 60; // so if I get the total hour if
       // total minutes returned 80 mins  its wrong must get the remaining minutes
        //if I want to get the seconds I do modulo again
        $remainingSeconds = $totalseconds % 60;

       return $totalhours . " h " .$remainingMinutes . " m " . $remainingSeconds . " s";
    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }
}
public function getTotalWorkoutsMonth($userID){
    try{
        $sqlquery = "SELECT
COUNT(*) AS TotalWorkouts
FROM tbl_user_workout_sessions
WHERE userID = :userID
AND STATUS ='completed'
AND date_completed >= DATE_FORMAT(CURDATE(),'%Y-%m-01')
AND date_completed < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH);";
$stmt = $this->conn->prepare($sqlquery);
$stmt->bindParam(':userID',$userID);
$stmt->execute();
$row =$stmt->fetch(PDO::FETCH_ASSOC);
return $row;
    }catch(PDOException $e){
        echo "Erro" . $e->getMessage();
    }
}
public function getTotalWorkouts($userID){
    try{
        $sqlquery = "SELECT COUNT(*) as TotalWorkouts FROM tbl_user_workout_sessions WHERE userID = :userID AND STATUS='completed'";
        $stmt = $this->conn->prepare($sqlquery);
        $stmt->bindParam(':userID' ,$userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){ 
        echo "Error" . $e->getMessage();
        return false;
    }
}

public function getWeeklyActivity($userId){
    try{
        $sql = "SELECT
        DATE(date_completed) As Day, sum(duration_seconds) as total_seconds
         FROM tbl_user_workout_sessions
         WHERE userID = :userID AND status ='completed' AND date_completed >=DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(date_completed) ORDER BY DAY";
         $stmt = $this->conn->prepare($sql);
         $stmt->bindParam(':userID', $userId);
         $stmt->execute();
          $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
          return $data;
    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }

}


public function getMonthlyActivity($userId){
    try{
        $sql = "SELECT 
    DATE(date_completed) AS Day,
    SUM(duration_seconds) AS total_seconds
FROM tbl_user_workout_sessions
WHERE userID = :userID
AND status = 'completed'
AND date_completed >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
AND date_completed < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
GROUP BY DATE(date_completed)
ORDER BY day;";
  $stmt = $this->conn->prepare($sql);
         $stmt->bindParam(':userID', $userId);
         $stmt->execute();
          $data=$stmt->fetchAll(PDO::FETCH_ASSOC);


          return $data;
    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }

}
// total duration from all users

public function getAllWeeklyActivity(){
    try{
        $sql = "SELECT
        DATE(date_completed) As Day, sum(duration_seconds) as total_seconds
         FROM tbl_user_workout_sessions
         WHERE status ='completed' AND date_completed >=DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(date_completed) ORDER BY DAY";
         $stmt = $this->conn->prepare($sql);
         $stmt->execute();
          $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
          return $data;
    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }

}


public function getAllMonthlyActivity(){
    try{
        $sql = "SELECT 
    DATE(date_completed) AS Day,
    SUM(duration_seconds) AS total_seconds
FROM tbl_user_workout_sessions
WHERE status = 'completed'
AND date_completed >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
AND date_completed < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
GROUP BY DATE(date_completed)
ORDER BY day;";
  $stmt = $this->conn->prepare($sql);
         $stmt->execute();
          $data=$stmt->fetchAll(PDO::FETCH_ASSOC);


          return $data;
    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }

}
public function getSTREAK($userID){
    try{
$sqlquery="SELECT DISTINCT DATE(date_completed) AS day
FROM tbl_user_workout_sessions
WHERE userID = :userID
AND status = 'completed'
ORDER BY day DESC;";
$stmt= $this->conn->prepare($sqlquery);
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
return $data;
 }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }
}
public function getRecentWorkouts($userID){
    try{
    $sqlquery = "SELECT
ws.sessionID,
w.`name` AS WorkoutName,
ws.duration_seconds,
ws.sets_done,
ws.status,
DATE_FORMAT(ws.created_At,'%b %d, %Y') AS WorkoutDate
FROM tbl_user_workout_sessions ws
INNER JOIN tbl_workouts w ON ws.workoutID = w.workoutID
WHERE ws.userID  = :userID
ORDER BY ws.created_At DESC
LIMIT 5";
$stmt = $this->conn->prepare($sqlquery);
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$data =$stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($data as &$row){
    $seconds = $row['duration_seconds'] ?? 0;
       $totalminutes = floor($seconds/60); 
       $totalhours = floor($totalminutes/60);

       $remainingMinutes = $totalminutes % 60;
        $remainingSeconds = $seconds % 60;
        $row['formattedDuration'] = $totalhours . " h " . $remainingMinutes . " m " . $remainingSeconds . " s ";
}
return $data;

    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
    }
}
public function getWorkoutAnalytics($userID){
    try{
         // Last 7 days workout frequency (count)
    $weeklyStmt = $this->conn->prepare(
        "SELECT DATE(date_completed) AS day, COUNT(*) AS workouts
         FROM tbl_user_workout_sessions
         WHERE userID = :userID
           AND status = 'completed'
           AND DATE(date_completed) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(date_completed)
         ORDER BY day ASC"
    );
    $weeklyStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $weeklyStmt->execute();
    $weeklyRows = $weeklyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly frequency for current year (count per month)
    $monthlyStmt = $this->conn->prepare(
        "SELECT MONTH(date_completed) AS month_num, COUNT(*) AS workouts
         FROM tbl_user_workout_sessions
         WHERE userID = :userID
           AND status = 'completed'
           AND YEAR(date_completed) = YEAR(CURDATE())
         GROUP BY MONTH(date_completed)
         ORDER BY month_num ASC"
    );
    $monthlyStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $monthlyStmt->execute();
    $monthlyRows = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

    // Last 12 weeks duration trend (minutes)
    $volumeStmt = $this->conn->prepare(
        "SELECT YEARWEEK(date_completed, 3) AS yw, SUM(duration_seconds) AS total_seconds
         FROM tbl_user_workout_sessions
         WHERE userID = :userID
           AND status = 'completed'
           AND date_completed >= DATE_SUB(CURDATE(), INTERVAL 11 WEEK)
         GROUP BY YEARWEEK(date_completed, 3)
         ORDER BY yw ASC"
    );
    $volumeStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $volumeStmt->execute();
    $volumeRows = $volumeStmt->fetchAll(PDO::FETCH_ASSOC);

    // Last 91 days activity (for heatmap)

    $heatmapStmt = $this->conn->prepare(
        "SELECT DATE(date_completed) AS day, COUNT(*) AS workouts
         FROM tbl_user_workout_sessions
         WHERE userID = :userID
           AND status = 'completed'
           AND DATE(date_completed) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
         GROUP BY DATE(date_completed)
         ORDER BY day ASC"
    );
    $heatmapStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $heatmapStmt->execute();
    $heatmapRows = $heatmapStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [
        'weekly' => $weeklyRows,
        'monthly' => $monthlyRows,
        'volume' => $volumeRows,
        'heatmap' => $heatmapRows
    ];

    return  $data;

    }catch(PDOException $e){
        echo "Error" . $e->getMessage();
        return false;
    }

}
}

?>
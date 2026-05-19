<?php 

require_once __DIR__ . '/../BL/exerciseManage.php';
class ExerciseModel{
    private $conn;
    public function __construct($db){
        $this->conn=$db;
    }
    public function getAllExercise(){
        try{
            $getAll = "SELECT
        e.exerciseID,
        e.name,
        e.muscle,
        e.icon,
        e.difficulty AS difficultyID,
        el.difficulty,
        e.equipment,
        e.location AS LocationID,
        l.location,
        e.`desc`,
        e.type        AS typeID,
        e.created_At,
        et.description AS type_name,
        COUNT(DISTINCT s.exercise_stepID) AS step_count,
        GROUP_CONCAT(DISTINCT t.tagdesc ORDER BY t.tagdesc SEPARATOR ',') AS tags
    FROM tbl_exercises e
    LEFT JOIN tbl_exercise_type et ON e.type       = et.typeID
    LEFT JOIN tbl_exercise_step  s  ON e.exerciseID = s.exerciseID
    LEFT JOIN tbl_exercise_tags  t  ON e.exerciseID = t.exerciseID
    LEFT JOIN tbl_exercise_level el on e.difficulty = el.exerciselevelID
    LEFT JOIN tbl_location l on e.location = l.locationID
    GROUP BY e.exerciseID
    ORDER BY e.created_At DESC, e.exerciseID DESC";
        $stmt = $this->conn->prepare($getAll);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }
    }

    public function readExercises(){
        try{
           $sqlQuery = "SELECT 
                e.exerciseID,
                e.name,
                e.muscle,
                e.icon,
                el.difficulty,
                et.description AS type,
                e.equipment,
                e.location,
                e.desc,
                e.created_At,
                e.updated_At,
               
                GROUP_CONCAT(DISTINCT t.tagdesc) AS tag

            FROM tbl_exercises e

            LEFT JOIN tbl_exercise_tags t 
                ON e.exerciseID = t.exerciseID
            LEFT JOIN tbl_exercise_type et
                on e.type =et.typeID
            LEFT JOIN tbl_exercise_level el ON e.difficulty = el.exerciselevelID
            

            GROUP BY e.exerciseID;";
            
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }
    }
    public function getLocation(){
        try{
            $sqlQuery = "SELECT * FROM tbl_location";
            $stmt =$this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }
    }

    //get the steps
    public function getSteps(){
        try{
         $sqlquery ="SELECT exerciseID, stepOrder, step_desc 
                   FROM tbl_exercise_step 
                   ORDER BY exerciseID, stepOrder ASC";
        $stmt = $this->conn->prepare($sqlquery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }
    }
    public function readtotalHomeexercises(){
        try{
            $sqlQuery = "SELECT COUNT(*) AS total FROM tbl_exercises WHERE location = 'home'";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }
    }
    public function getMuscleGroups(){
        try{
            $sqlQuery = "SELECT  * FROM tbl_muscles";
            $stmt= $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
            
        }
    }
    public function getExerciseType(){
        try{
            $sqlQuery = "SELECT * FROM tbl_exercise_type";
            
         $stmt= $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
            
        }
    }

    public function getStepsByExerciseId($exerciseID){
        try{
            $sql = "SELECT stepOrder, step_desc FROM tbl_exercise_step WHERE exerciseID = :exerciseID ORDER BY stepOrder ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':exerciseID', (int)$exerciseID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            return [];
        }
    }

    public function createExerciseWithLogs(array $payload, int $actorUserId){
        try{
            $this->conn->beginTransaction();

            $sql = "INSERT INTO tbl_exercises (name, muscle, icon, difficulty, equipment, location, `desc`, type, created_At, updated_At)
                    VALUES (:name, :muscle, :icon, :difficulty, :equipment, :location, :desc, :typeID, NOW(), NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':name' => $payload['name'],
                ':muscle' => $payload['muscle'],
                ':icon' => $payload['icon'],
                ':difficulty' => $payload['difficulty'],
                ':equipment' => $payload['equipment'],
                ':location' => $payload['location'],
                ':desc' => $payload['desc'],
                ':typeID' => (int)$payload['typeID'],
            ]);
            $exerciseID = (int)$this->conn->lastInsertId();

            $stepInsert = $this->conn->prepare("INSERT INTO tbl_exercise_step (exerciseID, stepOrder, step_desc) VALUES (:exerciseID, :stepOrder, :step_desc)");
            foreach($payload['steps'] as $idx => $step){
                $stepInsert->execute([
                    ':exerciseID' => $exerciseID,
                    ':stepOrder' => $idx + 1,
                    ':step_desc' => $step
                ]);
            }

            $tags = array_filter(array_map('trim', explode(',', (string)$payload['tags'])));
            if(!empty($tags)){
                $tagInsert = $this->conn->prepare("INSERT INTO tbl_exercise_tags (exerciseID, tagdesc) VALUES (:exerciseID, :tagdesc)");
                foreach($tags as $tag){
                    $tagInsert->execute([':exerciseID' => $exerciseID, ':tagdesc' => $tag]);
                }
            }

            $this->insertActivityLogs($actorUserId, $exerciseID, 'Exercise Created', "Created exercise '{$payload['name']}'", 'success');
            $this->conn->commit();
            return ['ok' => true, 'exerciseID' => $exerciseID];
        }catch(PDOException $e){
            if($this->conn->inTransaction()) $this->conn->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateExerciseWithLogs(int $exerciseID, array $payload, int $actorUserId){
        try{
            $this->conn->beginTransaction();

            $sql = "UPDATE tbl_exercises
                    SET name=:name, muscle=:muscle, icon=:icon, difficulty=:difficulty, equipment=:equipment, location=:location, `desc`=:desc, type=:typeID, updated_At=NOW()
                    WHERE exerciseID=:exerciseID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':name' => $payload['name'],
                ':muscle' => $payload['muscle'],
                ':icon' => $payload['icon'],
                ':difficulty' => $payload['difficulty'],
                ':equipment' => $payload['equipment'],
                ':location' => $payload['location'],
                ':desc' => $payload['desc'],
                ':typeID' => (int)$payload['typeID'],
                ':exerciseID' => $exerciseID,
            ]);

            $this->conn->prepare("DELETE FROM tbl_exercise_step WHERE exerciseID = :exerciseID")
                ->execute([':exerciseID' => $exerciseID]);
            $stepInsert = $this->conn->prepare("INSERT INTO tbl_exercise_step (exerciseID, stepOrder, step_desc) VALUES (:exerciseID, :stepOrder, :step_desc)");
            foreach($payload['steps'] as $idx => $step){
                $stepInsert->execute([
                    ':exerciseID' => $exerciseID,
                    ':stepOrder' => $idx + 1,
                    ':step_desc' => $step
                ]);
            }

            $this->conn->prepare("DELETE FROM tbl_exercise_tags WHERE exerciseID = :exerciseID")
                ->execute([':exerciseID' => $exerciseID]);
            $tags = array_filter(array_map('trim', explode(',', (string)$payload['tags'])));
            if(!empty($tags)){
                $tagInsert = $this->conn->prepare("INSERT INTO tbl_exercise_tags (exerciseID, tagdesc) VALUES (:exerciseID, :tagdesc)");
                foreach($tags as $tag){
                    $tagInsert->execute([':exerciseID' => $exerciseID, ':tagdesc' => $tag]);
                }
            }

            $this->insertActivityLogs($actorUserId, $exerciseID, 'Exercise Updated', "Updated exercise '{$payload['name']}'", 'success');
            $this->conn->commit();
            return ['ok' => true];
        }catch(PDOException $e){
            if($this->conn->inTransaction()) $this->conn->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function deleteExerciseWithLogs(int $exerciseID, int $actorUserId){
        try{
            $this->conn->beginTransaction();

            $nameStmt = $this->conn->prepare("SELECT name FROM tbl_exercises WHERE exerciseID = :exerciseID");
            $nameStmt->execute([':exerciseID' => $exerciseID]);
            $exerciseName = (string)($nameStmt->fetchColumn() ?: 'Exercise #' . $exerciseID);

            $this->conn->prepare("DELETE FROM tbl_exercise_step WHERE exerciseID = :exerciseID")
                ->execute([':exerciseID' => $exerciseID]);
            $this->conn->prepare("DELETE FROM tbl_exercise_tags WHERE exerciseID = :exerciseID")
                ->execute([':exerciseID' => $exerciseID]);
            $this->conn->prepare("DELETE FROM tbl_exercises WHERE exerciseID = :exerciseID")
                ->execute([':exerciseID' => $exerciseID]);

            $this->insertActivityLogs($actorUserId, $exerciseID, 'Exercise Deleted', "Deleted exercise '{$exerciseName}'", 'success');
            $this->conn->commit();
            return ['ok' => true];
        }catch(PDOException $e){
            if($this->conn->inTransaction()) $this->conn->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function insertActivityLogs(int $userID, int $exerciseID, string $activityType, string $description, string $status){
        $sys = $this->conn->prepare("INSERT INTO tbl_activity_logs (userID, action, date_of_action) VALUES (:userID, :action, NOW())");
        $sys->execute([':userID' => $userID, ':action' => $activityType]);

        $usr = $this->conn->prepare("INSERT INTO tbl_user_activity_logs (userID, workoutID, exerciseID, activity_type, description, status, activity_time)
                                     VALUES (:userID, NULL, :exerciseID, :activity_type, :description, :status, NOW())");
        $usr->execute([
            ':userID' => $userID,
            ':exerciseID' => $exerciseID,
            ':activity_type' => $activityType,
            ':description' => $description,
            ':status' => $status
        ]);
    }
    public function getDifficulty(){
        try{
            $getdifficulty = "SELECT * FROM tbl_exercise_level";
            $stmt = $this->conn->prepare($getdifficulty);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }
    }
    public function getExerciseAdmStats(){
        try{
            // total exercises
            $totalsql = "SELECT COUNT(*) AS total FROM tbl_exercises";
            $stmt = $this->conn->prepare($totalsql);
            $stmt->execute();
            $total = $stmt->fetchColumn();

            //home-friendly
            $homesql ="SELECT COUNT(*) AS home FROM tbl_exercises WHERE LOWER(location) = 1";
            $stmt = $this->conn->prepare($homesql);
            $stmt->execute();
            $home = $stmt->fetchColumn();

            //advanced
            $advancedsql = "SELECT COUNT(*) AS advanced FROM tbl_exercises WHERE LOWER(difficulty) = 3";
            $stmt = $this->conn->prepare($advancedsql);
            $stmt->execute();
            $advanced = $stmt->fetchColumn();
            //muscles
            $musclesql = "SELECT COUNT(DISTINCT muscle) AS muscles FROM tbl_exercises WHERE muscle IS NOT NULL AND muscle <> ''";
            $stmt = $this->conn->prepare($musclesql);
            $stmt->execute();
            $muscles = $stmt->fetchColumn();
            $data =[
                'total' => $total,
                'home' => $home,
                'advanced' => $advanced,
                'muscles' => $muscles
            ];
            return $data;

        }catch(PDOException $e){
            echo "Error" . $e->getMessage();
        }  
}
public function getUserStats(){
    try{
          // total exercises
            $totalsql = "SELECT COUNT(*) AS total FROM tbl_exercises";
            $stmt = $this->conn->prepare($totalsql);
            $stmt->execute();
            $total = $stmt->fetchColumn();

            //home friendly
             //home-friendly
            $homesql ="SELECT COUNT(*) AS home FROM tbl_exercises WHERE LOWER(location) = 1";
            $stmt = $this->conn->prepare($homesql);
            $stmt->execute();
            $home = $stmt->fetchColumn();
               //beginner
            $beningin = "SELECT COUNT(*) AS advanced FROM tbl_exercises WHERE LOWER(difficulty) = 1";
            $stmt = $this->conn->prepare($beningin);
            $stmt->execute();
            $beginner = $stmt->fetchColumn();
            // muscle groups
             $musclesql = "SELECT COUNT(DISTINCT muscle) AS muscles FROM tbl_exercises WHERE muscle IS NOT NULL AND muscle <> ''";
            $stmt = $this->conn->prepare($musclesql);
            $stmt->execute();
            $muscles = $stmt->fetchColumn();

            $data =[
                'total' => $total,
                'home' => $home,
                'beginner' => $beginner,
                'muscles' => $muscles
            ];
            return $data;
            
     
}catch(PDOException $e){
    echo "Error" . $e->getMessage();
}
}


// get charts data for admin_exercise
public function getChartsData(){
    try{
        // for muscles chart
        $musclesql = "SELECT COALESCE(NULLIF(muscle,''),'Unassigned') AS label, COUNT(*) AS total FROM tbl_exercises GROUP BY label ORDER BY total";
        $musclestmt= $this->conn->prepare($musclesql);
        $musclestmt->execute();
        $muscles = $musclestmt->fetchAll(PDO::FETCH_ASSOC);
        // difficultyRow
        $difficultysql = "SELECT COALESCE(NULLIF(el.difficulty,''), 'Unassigned') AS label, COUNT(*) As total from tbl_exercises 
        LEFT JOIN tbl_exercise_level el ON tbl_exercises.difficulty = el.exerciselevelID
        GROUP BY label ORDER BY total";
        $difficultystmt = $this->conn->prepare($difficultysql);
        $difficultystmt->execute();
        $difficulty = $difficultystmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'muscles' => $muscles,
            'difficulty' => $difficulty
        ];
        return $data;

}catch(PDOException $e){
    echo "Error" . $e->getMessage();
}
}
}
?>

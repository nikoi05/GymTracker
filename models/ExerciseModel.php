<?php 

require_once __DIR__ . '/../BL/exerciseManage.php';
class ExerciseModel{
    private $conn;
    public function __construct($db){
        $this->conn=$db;
    }

    public function readExercises(){
        try{
           $sqlQuery = "SELECT 
                e.exerciseID,
                e.name,
                e.muscle,
                e.icon,
                e.difficulty,
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
            

            GROUP BY e.exerciseID;";
            
            $stmt = $this->conn->prepare($sqlQuery);
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
}
?>
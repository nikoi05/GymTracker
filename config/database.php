<?php
class Workout_Database{
 private $db_host = 'localhost:3309';
 private $db_user = 'root';
 private $db_pass = '';
 private $db_name = 'workouttrackerdb';
 public $conn="";

public function _ConnectDB(){
     $dsn = new PDO(
            "mysql:host=$this->db_host;dbname=$this->db_name",
            $this->db_user,
            $this->db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    try{
        $this->conn = $dsn;
        return $this->conn;

    }catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}
}
}


 




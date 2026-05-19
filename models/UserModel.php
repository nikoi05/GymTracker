<?php
require_once __DIR__ . '/../config/database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

class UserModel{
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }
    public function registerFunc($username, $email, $password, $dateofBirth, $gender){
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        if($this->emailExists($email)){
            echo "Email already exists. Please choose a different email.";
            return;
        }else if($this->usernameExists($username)){
            echo "Username already exists.Please choose a different username.";
            return;
        }   
        try{
            $sqlQuery ="INSERT INTO tbl_users (username, email, password, genderID, day_of_birth,created_At,updated_At) VALUES (:username, :email, :password, :genderID, :day_of_birth, NOW(), NOW())";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password',$hashedPassword);
            $stmt->bindParam(':genderID', $gender);
            $stmt->bindParam(':day_of_birth', $dateofBirth);
             return $stmt->execute();
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
            return false;
        }
}




public function LoginFunc($email, $password){ // NOTE: IN THE USERMODEL WE VALIDATE THE LOGIN AND WE RETURN A BOOLEAN AJAX WILL
    try{
        $user = $this->getUserByEmail($email);

        return $user;
        
    }catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    return [];
}
}



/*=========================================================================
                         LOGIN ATTEMPTS 
===========================================================================*/
//check if email is locked
public function isLocked($email){
    try{
        $stmt =$this->conn->prepare("SELECT locked_until from tbl_login_attempts WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result && $result['locked_until']){
            if(strtotime($result['locked_until']) > time()){
                return true;
            }
        }
        return false;

    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    
    }
}

public function recordAttempts($email)
{
    try {

        $stmt = $this->conn->prepare("
            SELECT attempts 
            FROM tbl_login_attempts 
            WHERE email = :email
        ");

        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Existing record
        if ($result) {

            $attempts = $result['attempts'] + 1;

            $lockeduntil = null;

            // Lock after 5 attempts
            if ($attempts >= 5) {

                $lockeduntil = date(
                    'Y-m-d H:i:s',
                    strtotime('+15 minutes')
                );
            }

            $updateState = $this->conn->prepare("
                UPDATE tbl_login_attempts 
                SET attempts = :attempts,
                    locked_until = :lockeduntil
                WHERE email = :email
            ");

            $updateState->bindParam(':attempts', $attempts);
            $updateState->bindParam(':lockeduntil', $lockeduntil);
            $updateState->bindParam(':email', $email);

            $updateState->execute();

        } else {

            // First failed attempt
            $insert = $this->conn->prepare("
                INSERT INTO tbl_login_attempts (
                    email,
                    attempts
                )
                VALUES (
                    :email,
                    :attempts
                )
            ");

            $attempt = 1;

            $insert->bindParam(':email', $email);
            $insert->bindParam(':attempts', $attempt);

            $insert->execute();
        }

        return true;

    } catch(PDOException $e) {

        error_log($e->getMessage());

        return false;
    }
}
//if successfully login
public function resetAttempts($email){
    try{
        $reset = $this->conn->prepare("DELETE FROM tbl_login_attempts WHERE email = :email");
        $reset->bindParam(':email', $email);
        $reset->execute();
        return true;

    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    }
}


public function updateProfileLevel($userID){
    try{
        $sqlUpdate ="UPDATE tbl_users SET profileLevel =1 WHERE userID = :userID";
        $stmt =$this->conn->prepare($sqlUpdate);
        $stmt->bindParam(':userID',$userID);
        return  $stmt->execute();
        
       

    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
}

}
public function getUserByEmail($email){
    try{
        $sqlQuery = "SELECT u.userID,
         u.username,
         u.email,
          u.password,
          u.profileLevel,
          last_activity,
          u.created_At,
          u.updated_At,
          g.gender
           FROM tbl_users u
           LEFT JOIN
           tbl_gender g on g.genderID = u.genderID
           WHERE email = :email;";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return null;
    }
}

public function getUserByID($userID){
    try{
        $sqlQuery = "SELECT u.userID,
         u.username,
         u.email,
          u.password,
          u.day_of_birth,
          u.profileLevel,
          u.last_activity,
          u.created_At,
          u.updated_At,
          g.gender
           FROM tbl_users u
           LEFT JOIN
           tbl_gender g on g.genderID = u.genderID
           WHERE userID = :userID;";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    }
}
// to check database if users have used the emails to protect data integrity
public function emailExists($email){
    try{
        $sqlQuery = "SELECT * FROM tbl_users WHERE email = :email";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }catch(PDOException $e){
    echo "Error: " . $e->getMessage();
}
}
public function usernameExists($username){
    try{
        $sqlQuery = "SELECT * FROM tbl_users WHERE username = :username";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}
// check if there is admin session active
public function checkifAdminSession(){
    if(session_status() == PHP_SESSION_NONE){
        session_start();
    }
    if(!isset($_SESSION['email']) || $_SESSION['profileLevel'] != 2){
        header("Location: ?page=login");
        exit();
    }
}

public function countUsers(){
    try{
        $sqlQuery = "SELECT COUNT(*) as total from tbl_users";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    } catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return 0;
    }
}
public function readUsers(){
    try{
        $sqlQuery = "SELECT * FROM tbl_users INNER JOIN tbl_roles ON tbl_users.profileLevel = tbl_roles.roleID INNER JOIN tbl_gender ON tbl_users.genderID=tbl_gender.genderID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}
public function getGender(){
    try{
        $sql = "SELECT * FROM tbl_gender";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}
public function updateUserDetails($userID, $username, $password, $email, $gender, $dateofBirth, $role){

    try {
        // Get current email
        $checkQuery = "SELECT email FROM tbl_users WHERE userID = :userID";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentEmail = $currentUser['email'];

        // Start building query
        $sqlQuery = "UPDATE tbl_users SET 
            username = :username,
            genderID = :gender,
            day_of_birth = :dateofBirth,
            profileLevel = :role,
            updated_At = NOW()";

        // Only update email if changed
        if ($email !== $currentEmail) {
            $sqlQuery .= ", email = :email";
        }

        // Only update password if not empty
        if (!empty($password)) {
            $sqlQuery .= ", password = :password";
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        }

        $sqlQuery .= " WHERE userID = :userID";

        $stmt = $this->conn->prepare($sqlQuery);

        // Bind required fields
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dateofBirth', $dateofBirth);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':userID', $userID);

        // Bind optional fields
        if ($email !== $currentEmail) {
            $stmt->bindParam(':email', $email);
        }

        if (!empty($password)) {
            $stmt->bindParam(':password', $hashedPassword);
        }

        return $stmt->execute();

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
public function deleteUser($userID){
    try{
        $sqlQuery = "DELETE FROM tbl_users WHERE userID = :userID";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':userID', $userID);
        return $stmt->execute();
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;

        }
}

public function getActiveUsers( ){
    try{
        $sqlquery ="SELECT COUNT(*) as active_today FROM tbl_users WHERE DATE(last_activity) = CURDATE()";
        $stmt =$this->conn->prepare($sqlquery);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['active_today'];
    }catch(PDOException $e){
        echo "Error:" . $e->getMessage();
    }
}

public function updateActivity($userID){
    try{
        $sqlquery = "UPDATE tbl_users SET last_activity = NOW() WHERE userID = $userID";
        $stmt =$this->conn->prepare($sqlquery);
         return $stmt->execute();
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return false;
    }
}
public function getUsersWeekly(){
try{
    $sqlquery="SELECT 
    DATE_FORMAT(all_days.date_val, '%b %d') as label, 
    COUNT(u.created_At) as total
FROM (
    -- this part creates a table with the last 7 days interval
    SELECT CURDATE() - INTERVAL 0 DAY AS date_val UNION ALL
    SELECT CURDATE() - INTERVAL 1 DAY UNION ALL
    SELECT CURDATE() - INTERVAL 2 DAY UNION ALL
    SELECT CURDATE() - INTERVAL 3 DAY UNION ALL
    SELECT CURDATE() - INTERVAL 4 DAY UNION ALL
    SELECT CURDATE() - INTERVAL 5 DAY UNION ALL
    SELECT CURDATE() - INTERVAL 6 DAY
) AS all_days
LEFT JOIN tbl_users u ON DATE(u.created_At) = all_days.date_val
GROUP BY all_days.date_val
ORDER BY all_days.date_val ASC;";
    $stmt=$this->conn->prepare($sqlquery);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    return [];
}

}

public function getuserMonthly(){
    try{
    $sql = "SELECT DATE_FORMAT(created_At,'%M') AS label, COUNT(1) AS total FROM tbl_users GROUP BY label, DATE_FORMAT(created_At, '%Y-%M') ORDER BY min(created_At) ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        return[];
    }
}
public function readRoles(){
    try{
        $sqlQuery = "SELECT * FROM tbl_roles";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}
 public function updateProfile($userID,$username,$gender,$dob,$email){
      try {
        // Get current email
        $checkQuery = "SELECT email FROM tbl_users WHERE userID = :userID";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentEmail = $currentUser['email'];

        // Start building query
        $sqlQuery = "UPDATE tbl_users SET 
            username = :username,
            genderID = :gender,
            day_of_birth = :dateofBirth,
            updated_At = NOW()";

        // Only update email if changed
        if ($email !== $currentEmail) {
            $sqlQuery .= ", email = :email";
        }

        $sqlQuery .= " WHERE userID = :userID";

        $stmt = $this->conn->prepare($sqlQuery);

        // Bind required fields
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dateofBirth', $dob);
        $stmt->bindParam(':userID', $userID);

        // Bind optional fields
        if ($email !== $currentEmail) {
            $stmt->bindParam(':email', $email);
        }
        return $stmt->execute();

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

}
?>

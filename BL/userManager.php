<?php 
require_once  __DIR__ . '/../config/database.php';
require_once  __DIR__ . '/../models/UserModel.php'; 
require_once __DIR__ . '/../helper/send.php';
class managerUser{
    private $UserModel;
public function __construct()
{
    $database = new Workout_Database();
    $db = $database->_ConnectDB();
    $this->UserModel=new UserModel($db);    
}
public function registerFunc($username, $email, $password, $dateofBirth, $gender){
      return $this->UserModel->registerFunc($username,$email,$password,$dateofBirth,$gender);
}


public function LoginFunc($email, $password){ // NOTE: IN THE USERMODEL WE VALIDATE THE LOGIN AND WE RETURN A BOOLEAN AJAX WILL
    if(session_status() ===PHP_SESSION_NONE){
        session_start();
    }
    try{
        if(empty($email) || empty($password)){
            return  false;
        }
        // checked if user is locked or nah
        if($this->UserModel->isLocked($email)){
            return "locked";

        }
        // get info to validate password
        $user = $this->UserModel->LoginFunc($email, $password);
        if($user && password_verify($password, $user['password'])){
            session_regenerate_id(true);
            //store into session
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profileLevel'] = $user['profileLevel'];
            $this->UserModel->resetAttempts($email);
            return true;
            }
        else{
            $this->UserModel->recordAttempts($email);
        return false;

        }


    }catch(InvalidArgumentException $e){
    echo "Error: " . $e->getMessage();
}
}
public function getdetailsbyEmail($email){
    try{
        return $this->UserModel->getUserByEmail($email);
    }catch(InvalidArgumentException $e){
        echo "Error: " . $e->getMessage();
    }
}

public function getUserDetails($userID){
    try{
        return $this->UserModel->getUserByID($userID);
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
        return false;
    }
}

public function updateLevelProfile($userID){
     return $this->UserModel->updateProfileLevel($userID);
}
public function checkifUserSession(){
    if(session_status() ===PHP_SESSION_NONE){
        session_start();
    }
if(!isset($_SESSION['userID'])){
    session_unset();
    header("location:?page=login");
    exit();
}
}

public function checkAdminSession(){
    $this->UserModel->checkifAdminSession();
}

public function getAllUsers(){
    return $this->UserModel->countUsers();
}
public function getAllUsersDetails(){
    return $this->UserModel->readUsers();



}
public function updateUserDetails($userID, $username, $password, $email, $gender, $dateofBirth, $role){
     return $this->UserModel->updateUserDetails($userID, $username, $password, $email, $gender, $dateofBirth, $role);
}

public function deleteUser($userID){
    return $this->UserModel->deleteUser($userID);
}
public function  updateActivity($userID){
    return $this->UserModel->updateActivity($userID);
}
public function getActiveToday(){
    return $this->UserModel->getActiveUsers();
}

public function getweeklyRegistration(){
    return $this->UserModel->getUsersWeekly();
}
public function getMonthlyRegistration(){
    return $this->UserModel->getuserMonthly();
}
public function getRoles(){
    return $this->UserModel->readRoles();
}
public function updateProfile($userID,$username,$gender,$dob,$email){
    return $this->UserModel->updateProfile($userID,$username,$gender,$dob,$email);
}
public function getGender(){
    return $this->UserModel->getGender();

}
}
?>  

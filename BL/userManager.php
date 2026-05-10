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
         return $this->UserModel->LoginFunc($email,$password);
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
public function updateUserDetails($userID, $username, $email,$password, $gender, $dateofBirth,$role){
     return $this->UserModel->updateUserDetails($userID, $username, $email,$password, $gender, $dateofBirth,$role);
}

public function deleteUser($userID){
    return $this->UserModel->deleteUser($userID);
}
public function updateActivity($userID){
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
}
?>
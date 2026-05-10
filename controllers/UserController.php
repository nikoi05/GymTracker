<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/database.php';
require_once '../BL/userManager.php';
require_once __DIR__ . '/../helper/send.php';
// Database Connecction
$userManager = new managerUser();

//register
if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['dateofBirth']) && isset($_POST['gender'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dateofBirth = $_POST['dateofBirth'];
    $gender = $_POST['gender'];
    $result =$userManager->registerFunc($username,$email,$password,$dateofBirth,$gender);
    if($result){
        $name = $username;
            $sendtoemail =filter_var($email,FILTER_VALIDATE_EMAIL);
            $message = htmlspecialchars("Welcome " . $name . "to GymTracker");
            if(!$sendtoemail){
                die("Invalid Email");
            }
            $body =$body = '
<div style="background:#f4f6f8;padding:20px; color:#fff">
    <div class="email-container"
        style="max-width:600px;margin:auto;background:linear-gradient(-45deg, #020024, #090979, #3B82F6);padding:20px;text-align:center;font-family:\'Poppins\',sans-serif;color:fff">
        
        <h2 style="font-size:24px;font-weight:700;color:#fff;">
            Welcome to 💪<span style="color: #38BDF8;">
            Gym</span>Tracker
        </h2>

        <div style="padding:30px;color:#fff;">
            <h2>Hello, <span style="color: #38BDF8;">'.$username.'</span>!</h2>
            <p>Your account is successfully created.</p>
            <p><strong>Email:</strong> '.$email.'</p>
            <p><strong>Username:</strong> '.$username.'</p>

            <p style="padding-top:40px;padding-bottom:50px;font-size:10px;">
                We are excited to be with you on your fitness journey - GymTracker Team
            </p>

            <p style="margin-top:20px;font-size:12px;color:#777">
                If this was not you, ignore this email.
            </p>
        </div>
    </div>
</div>
';
            $result = sendEmail(

            $email,
            "admin",
            "Welcome To GymTracker",
            $body
            );
            if($result===true){
                echo "Success";
            }else{
                echo "Failed: $result";
            }
    }else{
        echo "Errors";
    }
    
    //login
    }else if(isset($_POST['email'])&& isset($_POST['Logpassword'])){// NOTE: we must get a return of boolean from userMODEL if the login is VALIDATED,
    $email= $_POST['email'];                                   // if the login returned true we must return a success message
    $password = $_POST['Logpassword'];                         // to the service and vice versa if failed.
    $result = $userManager->LoginFunc($email,$password); // to check session array use var_dump($_SESSION);

    if($result){
        $userData =$userManager->getdetailsbyEmail($email);
            $_SESSION['userID'] = $userData['userID'];
            $_SESSION['profileLevel'] =(int)$userData['profileLevel'];
            $_SESSION['username'] = $userData['username'];
            if($_SESSION['userID']){
                $userManager->updateActivity($_SESSION['userID']);
            }

            if($_SESSION['profileLevel']==0){
                echo "Success_0";
            }else if($_SESSION['profileLevel']==1){
                echo "Success_1";
            }else if($_SESSION['profileLevel']==2){
                echo "Success_2";
        }else{
            echo "failed";
        }
    }else{
        echo "Invalid_Credentials";
    }
    exit;
    }
    
else if(isset($_POST['action']) && $_POST['action'] === 'update'){
    $userID   = $_POST['UpdateuserID'];
    $username = $_POST['Updateusername'];
    $email    = $_POST['Updateemail'];
    $password = $_POST['Updatepassword'] ?? '';
    $gender   = $_POST['Updategender'];
    $dob      = $_POST['UpdatedateofBirth'];
    $role     = $_POST['Updaterole'];

    $Updateresult = $userManager->updateUserDetails($userID, $username, $password, $email, $gender, $dob, $role);

    echo $Updateresult ? "Success" : "Update failed in database";
    exit;

}else if(isset($_POST['action']) && $_POST['action'] === 'getUser'){
    $userID = $_SESSION['userID'];
    $userData = $userManager->getUserDetails($userID);
    header('Content-Type: application/json');
    echo json_encode($userData ?: []);
    exit;
}else if(isset($_POST['action']) && $_POST['action'] === 'changePassword'){
    $currentPass = $_POST['currentPass'];
    $newPass = $_POST['newPass'];
    $confirmPass = $_POST['confirmPass'];
    
    if ($newPass !== $confirmPass) {
        echo "Passwords do not match";
        exit;
    }
    
    $userID = $_SESSION['userID'];
    $userData = $userManager->getUserDetails($userID);
    
    if (!$userData || !password_verify($currentPass, $userData['password'])) {
        echo "Current password incorrect";
        exit;
    }
    
    $result = $userManager->updateUserDetails($userID, $userData['username'], $newPass, $userData['email'], $userData['gender'], $userData['day_of_birth'], $userData['profileLevel']);
    echo $result ? "Success" : "Update failed";
    exit;
}else if(isset($_POST['userID'])){
    $userID = $_POST['userID'];
    $result = $userManager->deleteUser($userID);
    if($result){
        echo "Success";
    }else{
        echo "Failed to update profile level";
    }
    exit;
}

?>


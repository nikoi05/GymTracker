<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../BL/userManager.php';
require_once __DIR__ . '/../BL/ActivityLogManager.php';
require_once __DIR__ . '/../helper/send.php';
// Database Connecction
$userManager = new managerUser();
$activityLogs = new ActivityLogManager();

//register
if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['dateofBirth']) && isset($_POST['gender'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dateofBirth = $_POST['dateofBirth'];
    $gender = $_POST['gender'];
    $result =$userManager->registerFunc($username,$email,$password,$dateofBirth,$gender);
    if($result){
        $createdUser = $userManager->getdetailsbyEmail($email);
        $actorUserID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : (int)($createdUser['userID'] ?? 0);
        $activityLogs->record($actorUserID, 'User Created', "Created user '{$username}'");

        $name = $username;
            $sendtoemail =filter_var($email,FILTER_VALIDATE_EMAIL);
            $message = htmlspecialchars("Welcome " . $name . "to GymTracker");
            if(!$sendtoemail){
                die("Invalid Email");
            }
            $body = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#0D1B3E;font-family:\'Poppins\',Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0D1B3E;padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;">

          <!-- ── HEADER ── -->
          <tr>
            <td align="center" style="
              background:linear-gradient(135deg,#1e3a8a,#2563EB,#38BDF8);
              border-radius:20px 20px 0 0;
              padding:40px 32px 32px;
            ">
              <div style="font-size:44px;margin-bottom:12px;">💪</div>
              <h1 style="
                margin:0;
                font-size:28px;
                font-weight:700;
                color:#ffffff;
                letter-spacing:1px;
              ">
                Gym<span style="color:#BAE6FD;">Tracker</span>
              </h1>
              <p style="
                margin:8px 0 0;
                font-size:13px;
                color:rgba(255,255,255,0.65);
                letter-spacing:2px;
                text-transform:uppercase;
              ">Your Fitness Journey Starts Now</p>
            </td>
          </tr>

          <!-- ── WELCOME BANNER ── -->
          <tr>
            <td style="
              background:#1e2d5a;
              padding:28px 32px;
              border-left:1px solid rgba(56,189,248,0.15);
              border-right:1px solid rgba(56,189,248,0.15);
            ">
              <h2 style="
                margin:0 0 8px;
                font-size:22px;
                font-weight:700;
                color:#ffffff;
              ">
                Welcome, <span style="color:#38BDF8;">' . $username . '</span>! 🎉
              </h2>
              <p style="
                margin:0;
                font-size:14px;
                color:rgba(255,255,255,0.60);
                line-height:1.6;
              ">
                Your account has been successfully created. You\'re all set to start tracking your workouts and crushing your fitness goals.
              </p>
            </td>
          </tr>

          <!-- ── ACCOUNT DETAILS ── -->
          <tr>
            <td style="
              background:#162245;
              padding:24px 32px;
              border-left:1px solid rgba(56,189,248,0.15);
              border-right:1px solid rgba(56,189,248,0.15);
            ">
              <p style="
                margin:0 0 16px;
                font-size:11px;
                font-weight:700;
                letter-spacing:2px;
                text-transform:uppercase;
                color:rgba(255,255,255,0.35);
              ">Account Details</p>

              <!-- Email Row -->
              <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
                <tr>
                  <td style="
                    background:rgba(37,99,235,0.12);
                    border:1px solid rgba(56,189,248,0.15);
                    border-radius:10px;
                    padding:14px 18px;
                  ">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width:32px;vertical-align:middle;">
                          <div style="
                            width:28px;height:28px;
                            background:rgba(56,189,248,0.18);
                            border-radius:8px;
                            text-align:center;
                            line-height:28px;
                            font-size:14px;
                          ">📧</div>
                        </td>
                        <td style="padding-left:12px;vertical-align:middle;">
                          <p style="margin:0;font-size:10px;color:rgba(255,255,255,0.40);text-transform:uppercase;letter-spacing:1px;">Email</p>
                          <p style="margin:2px 0 0;font-size:14px;font-weight:600;color:#ffffff;">' . $email . '</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Username Row -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="
                    background:rgba(37,99,235,0.12);
                    border:1px solid rgba(56,189,248,0.15);
                    border-radius:10px;
                    padding:14px 18px;
                  ">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width:32px;vertical-align:middle;">
                          <div style="
                            width:28px;height:28px;
                            background:rgba(56,189,248,0.18);
                            border-radius:8px;
                            text-align:center;
                            line-height:28px;
                            font-size:14px;
                          ">👤</div>
                        </td>
                        <td style="padding-left:12px;vertical-align:middle;">
                          <p style="margin:0;font-size:10px;color:rgba(255,255,255,0.40);text-transform:uppercase;letter-spacing:1px;">Username</p>
                          <p style="margin:2px 0 0;font-size:14px;font-weight:600;color:#ffffff;">' . $username . '</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- ── CTA ── -->
          <tr>
            <td style="
              background:#162245;
              padding:8px 32px 32px;
              border-left:1px solid rgba(56,189,248,0.15);
              border-right:1px solid rgba(56,189,248,0.15);
              text-align:center;
            ">
              <a href="#" style="
                display:inline-block;
                padding:14px 36px;
                background:linear-gradient(135deg,#2563EB,#38BDF8);
                color:#ffffff;
                font-size:14px;
                font-weight:700;
                text-decoration:none;
                border-radius:50px;
                letter-spacing:0.5px;
                box-shadow:0 8px 24px rgba(37,99,235,0.4);
              ">Start Your First Workout →</a>
            </td>
          </tr>

          <!-- ── MOTIVATIONAL STRIP ── -->
          <tr>
            <td style="
              background:linear-gradient(135deg,#1e3a8a,#0f2060);
              padding:24px 32px;
              border-left:1px solid rgba(56,189,248,0.15);
              border-right:1px solid rgba(56,189,248,0.15);
              text-align:center;
            ">
              <p style="
                margin:0;
                font-size:14px;
                font-style:italic;
                color:rgba(255,255,255,0.55);
                line-height:1.7;
              ">
                "The secret of getting ahead is getting started."<br>
                <span style="font-size:12px;color:rgba(255,255,255,0.30);">— GymTracker Team</span>
              </p>
            </td>
          </tr>

          <!-- ── FOOTER ── -->
          <tr>
            <td style="
              background:#0f1a38;
              border-radius:0 0 20px 20px;
              border:1px solid rgba(56,189,248,0.10);
              border-top:none;
              padding:24px 32px;
              text-align:center;
            ">
              <p style="margin:0 0 6px;font-size:13px;font-weight:700;color:rgba(255,255,255,0.40);letter-spacing:1px;">
                💪 GYM<span style="color:#38BDF8;">TRACKER</span>
              </p>
              <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.25);line-height:1.6;">
                If you did not create this account, you can safely ignore this email.<br>
                © 2026 GymTracker. Built with ❤️ for fitness lovers by Niko.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
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
    
    }else if(isset($_POST['email']) && isset($_POST['Logpassword'])){

    $email = $_POST['email'];
    $password = $_POST['Logpassword'];

    $result = $userManager->LoginFunc($email, $password);
    
    if($result==="locked"){
        echo "locked";
        exit;
    }
    
    if($result){

        $userData = $userManager->getdetailsbyEmail($email);

        $_SESSION['userID'] = $userData['userID'];
        $_SESSION['profileLevel'] = (int)$userData['profileLevel'];
        $_SESSION['username'] = $userData['username'];

        if($_SESSION['userID']){
            $userManager->updateActivity($_SESSION['userID']);
        }

        if($_SESSION['profileLevel'] == 0){

            echo "Success_0";

        }else if($_SESSION['profileLevel'] == 1){

            echo "Success_1";

        }else if($_SESSION['profileLevel'] == 2){

            echo "Success_2";
        }

    } else {

        echo "failed";
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

    if($Updateresult){
        $actorUserID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : (int)$userID;
        $activityLogs->record($actorUserID, 'User Updated', "Updated user '{$username}'");
    }

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
    if($result){
        $activityLogs->record((int)$userID, 'Password Changed', "Changed password for '{$userData['username']}'");
    }
    echo $result ? "Success" : "Update failed";
    exit;
}else if(isset($_POST['userID'])){
    $userID = $_POST['userID'];
    $targetUser = $userManager->getUserDetails($userID);
    $result = $userManager->deleteUser($userID);
    if($result){
        $actorUserID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;
        $targetName = $targetUser['username'] ?? ('User #' . $userID);
        $activityLogs->record($actorUserID, 'User Deleted', "Deleted user '{$targetName}'");
        echo "Success";
    }else{
        echo "Failed to update profile level";
    }
    exit;
}else if(isset($_POST['action']) && $_POST['action'] === 'UpdateProfile'){
    $userID = $_SESSION['userID'];
    $username =$_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dateofBirth = $_POST['dateofBirth'];
    $result = $userManager->updateProfile($userID,$username,$gender,$dateofBirth,$email);

    if($result){
                $activityLogs->record((int)$userID, 'Profile Updated', "Updated profile for '{$username}'");
                echo "Success";
            }else{
                echo "Failed: $result";
            }
}


?>


<?php 

require_once '../BL/userManager.php';
require_once '../BL/fitnessprofileManager.php';
$userManager =new managerUser();
$ftpManager =new fitnessprofileManager();
session_start();

//to do pa

if(isset(
    $_POST['height'],
    $_POST['weight'],
    $_POST['limitations'],
    $_POST['fitnessLevel'],
    $_POST['fitnessGoal'],
    $_POST['workoutFrequency'],
    $_POST['workoutDuration'],
// arrayed preferences
    $_POST['preferredWorkouts'],
    $_POST['locations'],
    $_POST['equipment'],
    $_POST['motivation']
) && is_array($_POST['preferredWorkouts']) 
&& is_array($_POST['locations']) 
&& is_array($_POST['equipment']) 
&& is_array($_POST['motivation'])){

// main onboarding data
   $height= $_POST['height'];
   $weight= $_POST['weight'];
   $limitations= $_POST['limitations'];
   $fitnessLevel= $_POST['fitnessLevel'];
   $fitnessGoal=$_POST['fitnessGoal'];
   $workoutFrequency = $_POST['workoutFrequency'];
   $workoutDuration =$_POST['workoutDuration'];
   $userId = $_SESSION['userID'];
   $mainInsert= $ftpManager->insertOnboardingData($userId,$height,$weight,$limitations
   ,$fitnessLevel,$fitnessGoal,$workoutFrequency,$workoutDuration);
   if($mainInsert){
    //preferences data or in an array
    $preferredWorkouts = $_POST['preferredWorkouts'] ?? [];
    $locations = $_POST['locations'] ?? [];
    $equipment = $_POST['equipment'] ?? [];
    $motivation = $_POST['motivation'] ?? [];

    $ftpManager->insertPreferences($userId, $preferredWorkouts, $locations, 
    $equipment, $motivation);
    $updateProfileLevel = $userManager->updateLevelProfile($userId);
    if($updateProfileLevel){
        echo "working";
   }else{
    echo "update_failed";
   }
}else{
    echo "insert_failed";
}
}else{
    echo "not_working";
}





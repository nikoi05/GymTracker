<?php
session_start();
if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

require_once '../BL/fitnessprofileManager.php';
require_once '../BL/userManager.php';

$userID = $_SESSION['userID'];
$fpm = new fitnessprofileManager();
$um = new managerUser();

$profile = $fpm->getOnboardingData($userID);
$prefs = $fpm->getPreferences($userID);
$user = $um->getUserDetails($userID);

echo json_encode([
    'profile' => $profile ?: null,
    'prefs' => $prefs ?: [],
    'user' => $user ?: null
]);
?>


<?php
session_start();
require_once __DIR__ . "../../BL/GoalsManager.php";
$gm = new GoalsManager();
$userID = $_SESSION['userID'];

$data=$gm->readGoals($userID);




header("Content-type: application/json");
echo json_encode(
 $data
);
exit;

?>
<?php
require_once __DIR__ . '/../BL/exerciseManage.php';
session_start();
header("Content-Type: application/json; charset=utf-8");
$em = new ExerciseManage();
$data = $em->getChartsData();

echo json_encode($data);


?>
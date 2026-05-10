<?php 
session_start();
$userID = $_SESSION['userID'];

require_once __DIR__ . "../../BL/WorkoutSessionManage.php";
$wsm = new WorkoutSessionManage();

// type from services
$type = $_GET['type'] ?? 'weekly'; 

// get data from services
if ($type === 'monthly') {
    $data = $wsm->GetMonthlyActivity($userID);
} else {
    $data = $wsm->GetWeeklyActivity($userID);
}

$mapped = [];
$labels = [];
$values = [];

/* mapping the date to the values need for formatting */
if (is_array($data) && count($data) > 0) {
    foreach ($data as $row) {
        $mapped[$row['Day']] = (int)$row['total_seconds'];
    }
}

/* formatting para maging mon tues, plus filll yung mga days na wlaang exercise or minutes */
if ($type === 'weekly') {

    for ($i = 6; $i >= 0; $i--) {

        $date = date('Y-m-d', strtotime("-$i days"));

        // label (Mon, Tue, etc.)
        $labels[] = date('D', strtotime($date));

        // fill missing days with 0
        $values[] = floor(($mapped[$date] ?? 0) / 60);
    }

} else {

    // formatting
    $daysInMonth = date('t');
    $year = date('Y');
    $month = date('m');

    for ($i = 1; $i <= $daysInMonth; $i++) {

        $date = "$year-$month-" . str_pad($i, 2, "0", STR_PAD_LEFT);

        $labels[] = date('M d', strtotime($date));

        $values[] = floor(($mapped[$date] ?? 0) / 60);
    }
}

/*
return json data for chart js
*/
header("Content-Type: application/json");

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);

exit;
?>
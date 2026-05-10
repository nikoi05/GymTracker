<?php 
require_once 'BL/userManager.php';
$user = new managerUser();

$page = $_GET['page'] ?? 'home';

// public pages don't require session check
$public = ['home', 'login', 'register'];

// protected pages require session check
$allowed = ['dashboard', 'onboarding', 'workouts', 'exercises', 'progress', 'goals', 'settings', 'profile', 'calendar','getWorkoutDetails'];
$hidden = ['admindash','admin_users'];
$basePath = __DIR__;
$routes = [
    'home' => $basePath . '/views/home.php',
    'login' => $basePath . '/views/auth/log.php',
    'register' => $basePath . '/views/auth/reg.php',
    'dashboard' => $basePath . '/views/users/dashboard.php',
    'onboarding' => $basePath . '/views/users/onboarding.php',

    // dashboard subpages 
    'workouts' => $basePath . '/views/users/workouts.php',
    'exercises' => $basePath . '/views/users/exercises.php',
'progress' => $basePath . '/views/users/progress-goals.php',
    'goals' => $basePath . '/views/users/progress-goals.php',

    'settings' => $basePath . '/views/users/settings.php',
    'profile' => $basePath . '/views/users/profile.php',
    'calendar' => $basePath . '/views/users/calendar.php',

    //workoutdetails
    'getWorkoutDetails' => $basePath . '/views/users/getWorkoutDetails.php',

    //admin page
    'admindash' => $basePath . '/views/admin/admindashboard.php',
    'admin_users' => $basePath . '/views/admin/admin_users.php',
];
// check if page is in the hidden array and if so check for admin session
if(in_array($page, $hidden)){
    $user->checkAdminSession();
}

// check if allowed pages check if user is in session
if (in_array($page, $allowed)){
    $user->checkifUserSession();
}

// loads the file based on the page requested
if(array_key_exists($page, $routes)){
    require_once $routes[$page];
}else{
    require_once 'views/404.php';
}



?>
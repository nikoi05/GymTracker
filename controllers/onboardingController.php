<?php
require_once __DIR__ . '/../BL/userManager.php';
require_once __DIR__ . '/../BL/fitnessprofileManager.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

$userId = (int)($_SESSION['userID'] ?? 0);
if ($userId <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$ftpManager  = new fitnessprofileManager();
$userManager = new managerUser();
$action      = $_POST['action'] ?? 'insert';

// ── GET dropdown options ──────────────────────────────────────────────────────
if ($action === 'getDropdowns') {
    echo json_encode([
        'ok'          => true,
        'levels'      => $ftpManager->getLevels(),
        'goals'       => $ftpManager->getFitnessGoals(),
        'frequencies' => $ftpManager->getFrequencies(),
        'durations'   => $ftpManager->getDurations()
    ]);
    exit;
}

// ── GET profile data (used by profile page) ──────────────────────────────────
if ($action === 'getProfileData') {
    echo json_encode([
        'ok'          => true,
        'profile'     => $ftpManager->getOnboardingData($userId) ?: [],
    ]);
    exit;
}

// ── UPDATE profile data (used by profile page edit form) ─────────────────────
if ($action === 'updateProfileData') {
    $updated = $ftpManager->updateOnboardingData(
        $userId,
        $_POST['height']           ?? null,
        $_POST['weight']           ?? null,
        $_POST['limitations']      ?? 'none',
        $_POST['fitnessLevel']     ?? '',
        $_POST['fitnessGoal']      ?? '',
        $_POST['workoutFrequency'] ?? '',
        $_POST['workoutDuration']  ?? ''
    );
    echo json_encode(['ok' => $updated !== false]);
    exit;
}

// ── INSERT (onboarding first-time save) ──────────────────────────────────────
if ($action === 'insert') {
    $height    = $_POST['height']           ?? null;
    $weight    = $_POST['weight']           ?? null;
    $limits    = $_POST['limitations']      ?? 'none';
    $level     = $_POST['fitnessLevel']     ?? '';
    $goal      = $_POST['fitnessGoal']      ?? '';
    $frequency = $_POST['workoutFrequency'] ?? '';
    $duration  = $_POST['workoutDuration']  ?? '';

    if (!$height || !$weight) {
        echo json_encode(['ok' => false, 'error' => 'Height and weight are required']);
        exit;
    }

    // Try update first; if no row exists, insert
    $existing = $ftpManager->getOnboardingData($userId);
    if ($existing) {
        $result = $ftpManager->updateOnboardingData($userId, $height, $weight, $limits, $level, $goal, $frequency, $duration);
    } else {
        $result = $ftpManager->insertOnboardingData($userId, $height, $weight, $limits, $level, $goal, $frequency, $duration);
    }

    if ($result !== false) {
        $userManager->updateLevelProfile($userId);
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Save failed']);
    }
    exit;
}

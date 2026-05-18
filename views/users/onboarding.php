<?php
require_once __DIR__ . '/../../BL/fitnessprofileManager.php';
$ftpManager     = new fitnessprofileManager();
$ob_levels      = $ftpManager->getLevels();
$ob_goals       = $ftpManager->getFitnessGoals();
$ob_frequencies = $ftpManager->getFrequencies();
$ob_durations   = $ftpManager->getDurations();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker - Setup Profile</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/dashboard.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/user-enhancements.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/onboarding.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="ob-wrap">
    <div class="ob-card">

        <div class="ob-header">
            <div class="ob-brand">💪 Gym<span>Tracker</span></div>
            <h1>Set Up Your Profile</h1>
            <p>Just a few details to personalise your experience. You can update these anytime from your profile.</p>
        </div>

        <div class="ob-body">
            <form id="onboardingForm">
                <div class="ob-grid">

                    <div class="ob-group">
                        <label class="ob-label">Height (cm)</label>
                        <input type="number" id="height" class="ob-input" placeholder="e.g. 175" min="100" max="250" required>
                    </div>

                    <div class="ob-group">
                        <label class="ob-label">Weight (kg)</label>
                        <input type="number" id="weight" class="ob-input" placeholder="e.g. 70" min="30" max="300" required>
                    </div>

                    <div class="ob-group">
                        <label class="ob-label">Fitness Level</label>
                        <select id="fitnessLevel" class="browser-default">
                            <?php foreach($ob_levels as $r): ?>
                            <option value="<?= htmlspecialchars($r['id']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="ob-group">
                        <label class="ob-label">Primary Goal</label>
                        <select id="fitnessGoal" class="browser-default">
                            <?php foreach($ob_goals as $r): ?>
                            <option value="<?= htmlspecialchars($r['id']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="ob-group">
                        <label class="ob-label">Workout Frequency</label>
                        <select id="workoutFrequency" class="browser-default">
                            <?php foreach($ob_frequencies as $r): ?>
                            <option value="<?= htmlspecialchars($r['id']) ?>" <?= $r['value']==='2' ? 'selected' : '' ?>><?= htmlspecialchars($r['label']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="ob-group">
                        <label class="ob-label">Session Duration</label>
                        <select id="workoutDuration" class="browser-default">
                            <?php foreach($ob_durations as $r): ?>
                            <option value="<?= htmlspecialchars($r['id']) ?>" <?= $r['value']==='2' ? 'selected' : '' ?>><?= htmlspecialchars($r['label']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="ob-group full">
                        <label class="ob-label">Physical Limitations <span>(optional)</span></label>
                        <textarea id="limitations" class="ob-textarea" placeholder="e.g. bad knees, lower back pain — leave blank if none"></textarea>
                    </div>

                </div>

                <div class="ob-footer">
                    <button type="submit" class="ob-btn">
                        <i class="material-icons" style="font-size:18px">check</i> Save & Continue
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/onboarding.js"></script>
</body>
</html>

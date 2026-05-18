<?php 
require_once __DIR__ . '/../../BL/userManager.php';
require_once __DIR__ . '/../../BL/fitnessprofileManager.php';
$userManager = new managerUser();
$ftpManager  = new fitnessprofileManager();
$userData    = isset($_SESSION['userID']) ? $userManager->getUserDetails($_SESSION['userID']) : null;
$gender      = $userManager->getGender();
$fp_levels      = $ftpManager->getLevels();
$fp_goals       = $ftpManager->getFitnessGoals();
$fp_frequencies = $ftpManager->getFrequencies();
$fp_durations   = $ftpManager->getDurations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/workout_trackersys/assets/dashboard.css">
<link rel="stylesheet" href="/workout_trackersys/assets/profile-settings.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
    <link rel="stylesheet" href="/workout_trackersys/assets/animations-global.css">
    <title>GymTracker - Profile</title>

    <link rel="stylesheet" href="/workout_trackersys/assets/user-enhancements.css">    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body>
<script src="/workout_trackersys/assets/animations-observer.js"></script>

    <!-- SIDEBAR -->
    <div class="Sidebar" id="sidebar">
        <div class="NavLinks">
            <button class="toggle-btn" onclick="ToggleSidebar()"><i class="material-icons">menu</i></button>
        <div class="sidebar-brand"><span class="sidebar-logo" aria-hidden="true">&#128170;</span><span class="text"><span class="brand-gym">Gym</span>Tracker</span></div>
            <ul>
                <li onclick="redirectUser(1)">
                    <i class="material-icons icon">dashboard</i>
                    <span class="text">Dashboard</span>
                </li>
                <li onclick="redirectUser(2)">
                    <i class="material-icons icon">fitness_center</i>
                    <span class="text">Workouts</span>
                </li>
                <li onclick="redirectUser(3)">
                    <i class="material-icons icon">directions_run</i>
                    <span class="text">Exercises</span>
                </li>
                <li onclick="redirectUser(4)">
                    <i class="material-icons icon">show_chart</i>
                    <span class="text">Progress</span>
                </li>

            </ul>
        </div>
        <div class="Profile-Settings">
            <ul>
                <li class="active" onclick="redirectUser(8)">
                    <i class="material-icons icon">person</i>
                    <span class="text">Profile</span>
                </li>
                <li onclick="redirectUser(7)">
                    <i class="material-icons icon">settings</i>
                    <span class="text">Settings</span>
                </li>
            </ul>
        </div>
            <div class="sidebar-theme">
        <span class="toggle-label" id="theme-label">Light</span>
        <label class="toggle">
            <input type="checkbox" id="themeToggle">
            <div class="toggle-track"></div>
            <div class="toggle-thumb"><i class="material-icons">brightness_5</i></div>
        </label>
    </div>
<div class="Logout">  
            <button type="button" onclick="LogoutFunc()">
                <i class="material-icons">logout</i>
                <span class="text">Logout</span>
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="main-container">
            <section class="page-hero">
                <div>
                    <span class="section-kicker">Member Profile</span>
                    <h2>My Profile</h2>
                    <p>View and manage your personal account information.</p>
                </div>
                <div class="hero-icon"><i class="material-icons">person</i></div>
            </section>
            <section class="profile-hero">
                <div class="profile-avatar-large">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="profile-hero-copy">
                    <span class="section-kicker">Member Profile</span>
                    <h1><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></h1>
                    <p><?= htmlspecialchars($_SESSION['email'] ?? 'No email saved') ?></p>
                </div>
                <div class="profile-hero-meta">
                    <span>Member since</span>
                    <strong><?= htmlspecialchars($userData['created_At'] ?? 'Not set') ?></strong>
                </div>
            </section>
    <div class="grid-main profile-layout">
                <div class="card full-width profile-panel" style="animation-delay:0.1s">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Account Details</div>
                            <p class="card-subtitle">Your personal information and account activity.</p>
                        </div>
                        <button class="btn-primary icon-action" id="editBtn" onclick="toggleEditiz()">
                            <i class="material-icons">edit</i>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                    <div class="profile-info" id="viewMode">
                        <div class="info-row">
                            <span class="label">Username</span>
                            <span class="value" id="usernameView"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email</span>
                            <span class="value" id="emailView"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Gender</span>
                            <span class="value" id="genderView"><?= htmlspecialchars($userData['gender'] ?? '') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Date of Birth</span>
                            <span class="value" id="dobView"><?= htmlspecialchars($userData['day_of_birth'] ?? '') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Member Since</span>
                            <span class="value" id="joinedView"><?= htmlspecialchars($userData['created_At'] ?? '') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Last Active</span>
                            <span class="value" id="lastActiveView"><?= htmlspecialchars($userData['last_activity'] ?? '') ?></span>
                        </div>
                    </div>
                    <div class="profile-edit" id="editMode" style="display:none;">
                        <form id="profileForm">
                            <input type="hidden" id="userID" value="<?= $_SESSION['userID'] ?>">
                            <input type="hidden" id="role" value="<?= htmlspecialchars($userData['profileLevel'] ?? 1) ?>">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" id="username" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select id="gender"  class=browser-default>
                                    <?php foreach($gender as $g):?>
                                    <option value="<?= $g['genderID'] ?>" <?= ($userData['gender'] ?? '') == 'Male' ? 'selected' : '' ?>><?=$g['gender']?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" id="dob" value="<?= htmlspecialchars($userData['day_of_birth'] ?? '') ?>">
                            </div>
                            <div class="form-row">
                                <button type="button" onclick="toggleEditiz()" class="btn-ghost">Cancel</button>
                                <button type="button" onclick="saveChangeFuncs()" class="btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Onboarding/Fitness Profile Card -->
                <div class="card full-width fitness-card profile-panel" style="animation-delay:0.2s">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Fitness Profile</div>
                            <p class="card-subtitle">Training details used to personalize your workouts.</p>
                        </div>
                        <button class="edit-onboarding-btn icon-action" id="onboardingEditBtn" onclick="toggleOnboardingEdit()">
                            <i class="material-icons">tune</i>
                            <span>Edit Fitness Profile</span>
                        </button>
                    </div>
                    <div class="onboarding-section">
                        <div class="onboarding-view">
                            <div class="profile-info">
                                <div class="info-row">
                                    <span class="label">Height</span>
                                    <span class="value" id="heightView">Loading...</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Weight</span>
                                    <span class="value" id="weightView">Loading...</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Fitness Level</span>
                                    <span class="value" id="levelView">Loading...</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Fitness Goal</span>
                                    <span class="value" id="goalView">Loading...</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Workout Frequency</span>
                                    <span class="value" id="frequencyView">Loading...</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Preferred Duration</span>
                                    <span class="value" id="durationView">Loading...</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Physical Limitations</span>
                                    <span class="value" id="limitationsView">Loading...</span>
                                </div>
                            </div>
                        </div><!-- /.onboarding-view -->
                        <div class="onboarding-edit">
                            <form id="onboardingForm">
                                <input type="hidden" id="onboardingUserID" value="<?= $_SESSION['userID'] ?>">
                                <div class="form-row" style="gap: 20px; margin-bottom: 20px;">
                                    <div class="form-group" style="flex:1">
                                        <label>Height (cm)</label>
                                        <input type="number" id="height" placeholder="170">
                                    </div>
                                    <div class="form-group" style="flex:1">
                                        <label>Weight (kg)</label>
                                        <input type="number" id="weight" placeholder="70">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Fitness Level</label>
                                    <select id="fitnessLevel" class="browser-default">
                                        <?php foreach($fp_levels as $r): ?>
                                        <option value="<?= htmlspecialchars($r['id']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Fitness Goal</label>
                                    <select id="fitnessGoal" class="browser-default">
                                        <?php foreach($fp_goals as $r): ?>
                                        <option value="<?= htmlspecialchars($r['id']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Workout Frequency</label>
                                    <select id="workoutFrequency" class="browser-default">
                                        <?php foreach($fp_frequencies as $r): ?>
                                        <option value="<?= htmlspecialchars($r['id']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Preferred Duration</label>
                                    <select id="workoutDuration" class="browser-default">
                                        <?php foreach($fp_durations as $r): ?>
                                        <option value="<?= htmlspecialchars($r['id']) ?>"><?= htmlspecialchars($r['label']) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group full-field">
                                    <label>Physical Limitations</label>
                                    <textarea id="limitations" placeholder="No injuries, full mobility"></textarea>
                                </div>
                                <div class="form-row">
                                    <button type="button" onclick="toggleOnboardingEdit()" class="btn-ghost">Cancel</button>
                                    <button type="submit" class="btn-primary">Save Fitness Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/profile-settings.js"></script>

<script>
/**
 * Toggles the Fitness Profile card between view and edit modes.
 * Also ensures current data is shown in form fields.
 */
function toggleOnboardingEdit() {
    const viewSection = document.querySelector('.onboarding-view');
    const editSection = document.querySelector('.onboarding-edit');
    const btn = document.getElementById('onboardingEditBtn');

    if (editSection.style.display === 'none' || editSection.style.display === '') {
        viewSection.style.display = 'none';
        editSection.style.display = 'block';
        btn.innerHTML = '<i class="material-icons">close</i><span>Cancel</span>';
    } else {
        viewSection.style.display = 'block';
        editSection.style.display = 'none';
        btn.innerHTML = '<i class="material-icons">tune</i><span>Edit Fitness Profile</span>';
    }
}

$(document).ready(function() {
    // Fetch and populate the Fitness Profile data
    function loadFitnessProfile() {
        $.ajax({
            url: '/workout_trackersys/controllers/onboardingController.php',
            type: 'POST',
            data: { action: 'getProfileData' },
            dataType: 'json',
            success: function(res) {
                if (res.ok && res.profile) {
                    const p = res.profile;
                    
                    // Update View Mode Labels
                    $('#heightView').text((p.height || '--') + ' cm');
                    $('#weightView').text((p.weight || '--') + ' kg');
                    $('#levelView').text(p.fitnessLevelLabel || '--');
                    $('#goalView').text(p.fitnessGoalLabel || '--');
                    $('#frequencyView').text(p.workoutFrequencyLabel || '--');
                    $('#durationView').text(p.pref_durationLabel || '--');
                    $('#limitationsView').text(p.Physical_limitations || 'None');

                    // Pre-populate Edit Mode Form Fields
                    $('#height').val(p.height);
                    $('#weight').val(p.weight);
                    $('#fitnessLevel').val(p.fitnessLevel);
                    $('#fitnessGoal').val(p.fitnessGoal);
                    $('#workoutFrequency').val(p.workoutFrequency);
                    $('#workoutDuration').val(p.pref_duration);
                    $('#limitations').val(p.Physical_limitations === 'none' ? '' : p.Physical_limitations);
                }
            }
        });
    }

    loadFitnessProfile();
});
</script>
</html>

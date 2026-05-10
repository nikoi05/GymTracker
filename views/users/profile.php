<?php 
require_once __DIR__ . '/../../BL/userManager.php';
$userManager = new managerUser();
$userData = isset($_SESSION['userID']) ? $userManager->getUserDetails($_SESSION['userID']) : null;
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
    <title>Profile</title>
</head>
<body>
    <!-- SIDEBAR -->
    <div class="Sidebar" id="sidebar">
        <div class="NavLinks">
            <button class="toggle-btn" onclick="ToggleSidebar()">☰</button>
            <span class="text">GymTracker</span>
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
            <div class="top-bar">
                <div class="greeting">
                    <h3>My Profile</h3>
                    <h5>Manage your account information</h5>
                </div>
                <div class="toggle-wrap">
                    <span class="toggle-label" id="theme-label">Light</span>
                    <label class="toggle">
                        <input type="checkbox" id="themeToggle">
                        <div class="toggle-track"></div>
                        <div class="toggle-thumb"><i class="material-icons light-icon">brightness_5</i></div>
                    </label>
                </div>
            </div>
    <div class="grid-main">
                <div class="card full-width" style="animation-delay:0.1s">
                    <div class="card-header">
                        <div class="card-title">Account Details</div>
                        <button class="btn-primary" id="editBtn" onclick="toggleEdit()">Edit Profile</button>
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
                                <select id="gender">
                                    <option value="Male" <?= ($userData['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($userData['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= ($userData['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" id="dob" value="<?= htmlspecialchars($userData['day_of_birth'] ?? '') ?>">
                            </div>
                            <div class="form-row">
                                <button type="button" onclick="toggleEdit()" class="btn-ghost">Cancel</button>
                                <button type="submit" class="btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Onboarding/Fitness Profile Card -->
                <div class="card full-width fitness-card" style="animation-delay:0.2s">
                    <div class="card-header">
                        <div class="card-title">Fitness Profile</div>
                        <button class="edit-onboarding-btn" id="onboardingEditBtn" onclick="toggleOnboardingEdit()">Edit Fitness Profile</button>
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
                            </div>
                            <div class="preferences-section">
                                <div style="margin-top: 20px;">
                                    <strong>Preferred Workouts:</strong>
                                    <div id="workoutsList" class="preferences-list"></div>
                                </div>
                                <div>
                                    <strong>Locations:</strong>
                                    <div id="locationsList" class="preferences-list"></div>
                                </div>
                                <div>
                                    <strong>Equipment:</strong>
                                    <div id="equipmentList" class="preferences-list"></div>
                                </div>
                                <div>
                                    <strong>Motivation:</strong>
                                    <div id="motivationList" class="preferences-list"></div>
                                </div>
                            </div>
                        </div>
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
                                    <select id="fitnessLevel">
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Advanced">Advanced</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Fitness Goal</label>
                                    <input type="text" id="fitnessGoal" placeholder="Build muscle">
                                </div>
                                <div class="form-group">
                                    <label>Workout Frequency</label>
                                    <select id="workoutFrequency">
                                        <option value="3-4 times/week">3-4 times/week</option>
                                        <option value="5-6 times/week">5-6 times/week</option>
                                        <option value="Daily">Daily</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Preferred Duration</label>
                                    <select id="workoutDuration">
                                        <option value="30 min">30 min</option>
                                        <option value="45 min">45 min</option>
                                        <option value="60 min">60 min</option>
                                        <option value="90+ min">90+ min</option>
                                    </select>
                                </div>
                                <div class="form-group">
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
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/workout_trackersys/scripts/redirect.js"></script>
    <script src="/workout_trackersys/scripts/dashboard.js"></script>
<script src="/workout_trackersys/scripts/profile-settings.js"></script>

</html>


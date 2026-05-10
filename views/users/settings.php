<?php 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/workout_trackersys/assets/dashboard.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
    <title>Settings</title>
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
                <li onclick="redirectUser(8)">
                    <i class="material-icons icon">person</i>
                    <span class="text">Profile</span>
                </li>
                <li class="active" onclick="redirectUser(7)">
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
                    <h3>Settings</h3>
                    <h5>Account & Privacy</h5>
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
                        <div class="card-title">Change Password</div>
                    </div>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" id="currentPass" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" id="newPass" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" id="confirmPass" required minlength="6">
                        </div>
                        <div class="form-row">
                            <button type="submit" class="btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
                <div class="card full-width" style="animation-delay:0.2s">
                    <div class="card-header">
                        <div class="card-title">Preferences</div>
                    </div>
                    <div class="settings-toggles">
                        <div class="toggle-item">
                            <span>Email Notifications</span>
                            <label class="toggle small">
                                <input type="checkbox" id="emailNotif">
                                <div class="toggle-track"></div>
                                <div class="toggle-thumb"></div>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <span>Dark Mode</span>
                            <label class="toggle small">
                                <input type="checkbox" id="darkModePref">
                                <div class="toggle-track"></div>
                                <div class="toggle-thumb"></div>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <span>Workout Reminders</span>
                            <label class="toggle small">
                                <input type="checkbox" id="reminders">
                                <div class="toggle-track"></div>
                                <div class="toggle-thumb"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/dashboard.js"></script>
<script>
$(document).ready(function() {
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            action: 'changePassword',
            currentPass: $('#currentPass').val(),
            newPass: $('#newPass').val(),
            confirmPass: $('#confirmPass').val()
        };
        
        $.post('/workout_trackersys/controllers/UserController.php', formData, function(response) {
            if (response === 'Success') {
                Swal.fire('Success', 'Password updated!', 'success');
                $('#passwordForm')[0].reset();
            } else {
                Swal.fire('Error', response, 'error');
            }
        }).fail(() => {
            Swal.fire('Error', 'Server error', 'error');
        });
    });
});

function ToggleSidebar() {
    $('.Sidebar').toggleClass('active');
}

// Theme toggle
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.checked = true;
    }
    themeToggle.addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    });
}
</script>


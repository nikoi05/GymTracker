<?php 
require_once __DIR__ . '/../../BL/userManager.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$userManager = new managerUser();
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
    <title>GymTracker - Settings</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/user-enhancements.css">    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body>
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
                    <span class="section-kicker">Account Control</span>
                    <h2>Settings</h2>
                    <p>Keep your account secure and tailor your GymTracker experience.</p>
                </div>
                <div class="hero-icon"><i class="material-icons">settings</i></div>
            </section>
            <div class="grid-main settings-layout" style="justify-content:center; justify-items:center;">

                <!-- Change Password -->
                <div class="card full-width settings-panel password-panel" style="animation-delay:0.1s; max-width:820px; width:100%;">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Change Password</div>
                            <p class="card-subtitle">Use at least 6 characters and avoid reusing old passwords.</p>
                        </div>
                    </div>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label>Current Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="currentPass" required placeholder="Enter current password">
                                <span class="material-icons" data-target="currentPass" onclick="unhidepassFUNC(this)">visibility</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="newPass" required minlength="8" maxlength="100" placeholder="Min. 8 characters, max 100">
                                <span class="material-icons" data-target="newPass" onclick="unhidepassFUNC(this)">visibility</span>
                            </div>
                            <ul id="passRules" style="margin:6px 0 0;padding-left:18px;font-size:12px;color:var(--text-muted);">
                                <li id="rule-len">At least 8 characters</li>
                                <li id="rule-max">Maximum 100 characters</li>
                                <li id="rule-upper">One uppercase letter</li>
                                <li id="rule-num">One number</li>
                                <li id="rule-special">One special character (e.g. !@#$%/\'\")</li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirmPass" required minlength="8" maxlength="100" placeholder="Re-enter new password">
                                <span class="material-icons" data-target="confirmPass" onclick="unhidepassFUNC(this)">visibility</span>
                            </div>
                            <small id="matchHint" style="font-size:12px;margin-top:4px;display:block;"></small>
                        </div>
                        <div class="form-row" style="display:flex; justify-content:flex-end;">
                            <button type="submit" class="btn-primary icon-action">
                                <i class="material-icons">lock_reset</i>
                                <span>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Danger Zone -->
                <div class="card full-width settings-panel" style="animation-delay:0.2s; max-width:820px; width:100%; border:1px solid rgba(239,68,68,0.3);">
                    <div class="card-header">
                        <div>
                            <div class="card-title" style="color:#ef4444;">Danger Zone</div>
                            <p class="card-subtitle">Irreversible actions — proceed with caution.</p>
                        </div>
                        <i class="material-icons" style="color:#ef4444;font-size:28px;">warning</i>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;">
                        <div>
                            <div style="font-weight:600;color:var(--text);">Delete Account</div>
                            <div style="font-size:13px;color:var(--text-muted);">Permanently delete your account and all data. This cannot be undone.</div>
                        </div>
                        <button class="btn-danger icon-action" onclick="confirmDeleteAccount()" style="flex-shrink:0;margin-left:16px;background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.4);padding:10px 18px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:6px;font-weight:600;font-size:13px;white-space:nowrap;">
                            <i class="material-icons" style="font-size:18px;">delete_forever</i>
                            Delete Account
                        </button>
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
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/dashboard.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script>
$(document).ready(function () {

    // ── Live password rules ──
    const rules = {
        'rule-len':     p => p.length >= 8,
        'rule-max':     p => p.length <= 100,
        'rule-upper':   p => /[A-Z]/.test(p),
        'rule-num':     p => /\d/.test(p),
        'rule-special': p => /[^A-Za-z0-9]/.test(p)
    };

    $('#newPass').on('input', function () {
        const p = this.value;
        Object.entries(rules).forEach(([id, fn]) => {
            const el = document.getElementById(id);
            el.style.color = fn(p) ? 'var(--accent, #38BDF8)' : 'var(--text-muted)';
        });
        checkMatch();
    });

    $('#confirmPass').on('input', checkMatch);

    function checkMatch() {
        const hint = document.getElementById('matchHint');
        const np = $('#newPass').val(), cp = $('#confirmPass').val();
        if (!cp) { hint.textContent = ''; return; }
        if (np === cp) {
            hint.textContent = '✓ Passwords match';
            hint.style.color = 'var(--accent, #38BDF8)';
        } else {
            hint.textContent = '✗ Passwords do not match';
            hint.style.color = '#ef4444';
        }
    }

    function passValid(p) {
        return Object.values(rules).every(fn => fn(p));
    }

    // ── Password form submit ──
    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();
        const newPass     = $('#newPass').val();
        const confirmPass = $('#confirmPass').val();

        if (!passValid(newPass)) {
            (window.GymSwal || Swal).fire({ icon:'warning', title:'Password too weak', text:'Please meet all the password requirements.' });
            return;
        }
        if (newPass !== confirmPass) {
            (window.GymSwal || Swal).fire({ icon:'warning', title:'Passwords do not match' });
            return;
        }

        $.post('/workout_trackersys/controllers/UserController.php', {
            action:      'changePassword',
            currentPass: $('#currentPass').val(),
            newPass:     newPass,
            confirmPass: confirmPass
        }, function (res) {
            if (res.trim() === 'Success') {
                (window.GymSwal || Swal).fire({ icon:'success', title:'Password updated!', timer:1500, showConfirmButton:false });
                $('#passwordForm')[0].reset();
                document.querySelectorAll('#passRules li').forEach(li => li.style.color = '');
                document.getElementById('matchHint').textContent = '';
            } else {
                (window.GymSwal || Swal).fire({ icon:'error', title: res.trim() });
            }
        }).fail(() => (window.GymSwal || Swal).fire({ icon:'error', title:'Server error' }));
    });
});

function confirmDeleteAccount() {
    (window.GymSwal || Swal).fire({
        title: 'Delete your account?',
        text: 'This will permanently delete all your data. This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.post('/workout_trackersys/controllers/UserController.php',
            { userID: <?= $_SESSION['userID'] ?? 0 ?> },
            function (res) {
                if (res.trim() === 'Success') {
                    (window.GymSwal || Swal).fire({ icon:'success', title:'Account deleted.' })
                        .then(() => window.location.href = '?page=home');
                } else {
                    (window.GymSwal || Swal).fire({ icon:'error', title: res });
                }
            }
        ).fail(() => (window.GymSwal || Swal).fire({ icon:'error', title:'Server error' }));
    });
}
</script>


<?php 
    require_once __DIR__ . '/../../BL/userManager.php';
    require_once __DIR__ . '/../../BL/WorkoutManager.php';
    $user = new managerUser();
    $users = $user->getAllUsersDetails();
    $wm = new WorkoutManager();
    $roles = $user->getRoles();
    $totalWorkouts = $wm->totalWorkoutsLogged();
    $gender = $user->getGender();
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker Admin - Users</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/AdminUsers.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/admin-managers.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>

<body>

<div class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()"><i class="material-icons">menu</i></button>
        <div class="sidebar-brand"><span class="sidebar-logo" aria-hidden="true">&#128170;</span><span class="text"><span class="brand-gym">Gym</span>Tracker Admin</span></div>
        <ul>
            <li onclick="redirectAdmin(1)"><i class="material-icons icon">dashboard</i><span class="text">Dashboard</span></li>
            <li class="active" onclick="redirectAdmin(2)"><i class="material-icons icon">people</i><span class="text">Users</span></li>
            <li onclick="redirectAdmin(3)"><i class="material-icons icon">fitness_center</i><span class="text">Exercises</span></li>
            <li onclick="redirectAdmin(6)"><i class="material-icons icon">self_improvement</i><span class="text">Workouts</span></li>
            <li onclick="redirectAdmin(4)"><i class="material-icons icon">bar_chart</i><span class="text">Reports</span></li>
            <li onclick="redirectAdmin(5)"><i class="material-icons icon">receipt_long</i><span class="text">Activity Logs</span></li>
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
            <i class="material-icons">logout</i><span class="text">Logout</span>
        </button>
    </div>
</div>

<main class="main-content">
    <div class="main-container">
        <header class="top-bar">
            <div class="greeting">
                <h1>User Management</h1>
                <p>Manage accounts, roles, and access across GymTracker.</p>
            </div>
            <div class="toggle-thumb"><i class="material-icons light-icon">brightness_5</i></div>
                </label>
            </div>
        </header>
        <section class="admin-hero" aria-label="Admin users overview">
            <div>
                <span class="section-kicker">Admin workspace</span>
                <h2>Users Directory</h2>
                <p>Search, filter, add, and maintain user records without leaving this page.</p>
            </div>
            <button class="addbt hero-add" onclick="openAddModal()">
                <i class="material-icons left">person_add</i>Add User
            </button>
        </section>

        <div class="users-toolbar">
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"><i class="material-icons">groups</i></div>
                    <div class="stat-value"><?= count($users) ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <?php 
                $rolecounts=[];
                foreach($users as $u){
                    $role = strtolower($u['role_description']);
                    if(!isset($rolecounts[$role])){
                        $rolecounts[$role] = 0;
                    }
                    $rolecounts[$role]++;
                }
                $icons = [
                    'admin' => 'admin_panel_settings',
                    'user' => 'person',
                    'new user' => 'person_add'
                ];
                foreach($rolecounts as $role => $count):
                    $icon =$icons[$role] ?? 'person';
                ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="material-icons"><?= $icon ?></i>
                    </div>
                    <div class="stat-value"><?= $count ?></div>
                    <div class="stat-label"><?= htmlspecialchars($role) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <section class="card users-card">
            <table id="usersTable" class="display">
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Day of Birth</th>
                        <th>Role</th>
                        <th>Date Created</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($users)): foreach($users as $user): ?>
                    <tr>
                        <td data-label="UserID"><?= htmlspecialchars($user["userID"]) ?></td>
                        <td data-label="Username" class="user-name-cell"><span class="user-avatar"><?= strtoupper(substr($user["username"], 0, 1)) ?></span><span><?= htmlspecialchars($user["username"]) ?></span></td>
                        <td data-label="Email"><?= htmlspecialchars($user["email"]) ?></td>
                        <td data-label="Gender"><?= htmlspecialchars($user["gender"]) ?></td>
                        <td data-label="Date of Birth"><?= htmlspecialchars($user["day_of_birth"]) ?></td>
                        <td data-label="Role"><span class="role-pill"><?= htmlspecialchars($user["role_description"]) ?></span></td>
                        <td data-label="Date Created"><?= htmlspecialchars($user["created_At"]) ?></td>
                        <td class="actions-cell" data-label="Actions">
                            <button class="icon-btn blue" type="button" aria-label="Edit" title="Edit" onclick='openEditModal(<?= json_encode($user) ?>)'>
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="icon-btn red" type="button" aria-label="Delete" title="Delete" onclick="deleteUser(<?= (int)$user['userID'] ?>)">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</main>
<div class="modal" id="AddModal">
    <div class="modal-content">
        <button class="modal-close-btn" type="button" aria-label="Close" onclick="closeModal('AddModal')">
            <i class="material-icons">close</i>
        </button>
        <div class="modal-head">
            <div class="modal-icon"><i class="material-icons">person_add</i></div>
            <div>
                <h1 class="add-title">Add User</h1>
                <p>Create a new account and send the user their welcome email.</p>
            </div>
        </div>
        <form class="add-form">
            <div class="input-group">
                <label>Username</label>
                <input type="text" id="username" maxlength="50" minlength="3" required>
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" id="email" maxlength="100" minlength="5" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" maxlength="100" minlength="8" required>
                    <span class="material-icons" data-target="password" onclick="unhidepassFunc(this)">visibility</span>
                </div>
                <ul id="add-passRules" style="margin:6px 0 0;padding-left:18px;font-size:12px;color:var(--text-muted);">
                    <li id="add-rule-len">At least 8 characters</li>
                    <li id="add-rule-max">Maximum 100 characters</li>
                    <li id="add-rule-upper">One uppercase letter</li>
                    <li id="add-rule-num">One number</li>
                    <li id="add-rule-special">One special character</li>
                </ul>
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" maxlength="100" minlength="8" required>
                    <span class="material-icons" data-target="confirm_password" onclick="unhidepassFunc(this)">visibility</span>
                </div>
                <small id="add-matchHint" style="font-size:12px;margin-top:4px;display:block;"></small>
            </div>
            <div class="input-group">
                <label>Gender</label>
                <select id="gender" class="browser-default" onchange="toggleselectgender(this)" required>
                    <option value="" disabled selected>Select Gender</option>
                    <?php foreach($gender as $g):?>
                    <option value="<?= $g['genderID'] ?>"><?= $g['gender'] ?></option>
                    <?php endforeach?>
                </select>
                <input type="text" id="othergender" maxlength="20" minlength="2" placeholder="Please specify" style="display:none; margin-top:10px;">
            </div>
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" id="doBirth" required>
                <small id="add-dobHint" style="font-size:12px;margin-top:4px;display:block;color:var(--text-muted);">Must be 18+ years old.</small>
            </div>
        </form>
        <div class="modal-footer-btns">
                <button type="button" class="cancel-btn" onclick="closeModal('AddModal')">Cancel</button>
                <button type="button" class="register-button" onclick="addUserFunc()">Add User</button>
            </div>
    </div>
</div>

<div class="modal" id="EditModal">
    <div class="modal-content">
        <button class="modal-close-btn" type="button" aria-label="Close" onclick="closeModal('EditModal')">
            <i class="material-icons">close</i>
        </button>
        <div class="modal-head">
            <div class="modal-icon"><i class="material-icons">manage_accounts</i></div>
            <div>
                <h1 class="add-title">Edit User Details</h1>
                <p>Update profile information, access level, or set a new password.</p>
            </div>
        </div>
        <form class="add-form" id="editForm">
            <input type="hidden" id="edit_userID">
            <div class="input-group">
                <label>Username</label>
                <input type="text" id="edit_username" maxlength="50" minlength="3" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="edit_email" maxlength="100" minlength="5" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" id="edit_password" maxlength="100" minlength="8">
                    <span class="material-icons" data-target="edit_password" onclick="unhidepassFunc(this)">visibility</span>
                </div>
                <small style="font-size:12px;color:var(--text-muted);margin-top:4px;display:block;">Leave blank to keep current password. Min 8, max 100 chars.</small>
                <ul id="edit-passRules" style="margin:4px 0 0;padding-left:18px;font-size:12px;color:var(--text-muted);display:none;">
                    <li id="edit-rule-len">At least 8 characters</li>
                    <li id="edit-rule-max">Maximum 100 characters</li>
                    <li id="edit-rule-upper">One uppercase letter</li>
                    <li id="edit-rule-num">One number</li>
                    <li id="edit-rule-special">One special character</li>
                </ul>
            </div>
            <div class="input-group">
                <label>Gender</label>
                <select id="edit_gender" class="browser-default" required>
                    <?php foreach($gender as $g):?>
                    <option value="<?= $g['genderID'] ?>"><?= $g['gender'] ?></option>
                    <?php endforeach?>
                </select>
            </div>
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" id="edit_doBirth" required>
                <small id="edit-dobHint" style="font-size:12px;margin-top:4px;display:block;color:var(--text-muted);">Must be 18+ years old.</small>
            </div>
            <div class="input-group">
                <label>Role</label>
                <select id="edit_role" class="browser-default" required>
                   <?php foreach($roles as $role): ?>
                    <option value="<?= (int)$role['roleID'] ?>"><?= htmlspecialchars($role['role_description']) ?></option>
                   <?php endforeach;?>
                </select>
            </div>
           
        </form>
         <div class="modal-footer-btns">
                <button type="button" class="cancel-btn" onclick="closeModal('EditModal')">Cancel</button>
                <button type="button" class="register-button" onclick="updateUserFunc()">Update User</button>
            </div>
    </div>
</div>

<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script src="/workout_trackersys/scripts/AdminUser.js"></script>

<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span>Tracker</div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
</body>
</html>

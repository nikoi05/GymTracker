<?php 
    require_once __DIR__ . '/../../BL/userManager.php';
    require_once __DIR__ . '/../../BL/WorkoutManager.php';
    $user = new managerUser();
    $users = $user->getAllUsersDetails();
    $wm = new WorkoutManager();
    $totalWorkouts = $wm->totalWorkoutsLogged();
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/AdminUsers.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<aside class="Sidebar" id="sidebar">
    <div class="NavLinks">
        <button class="toggle-btn" onclick="ToggleSidebar()">☰</button>
        <span class="text">Admin Panel</span>
        <ul>
            <li onclick="redirectAdmin(1)"><i class="material-icons icon">dashboard</i><span class="text">Dashboard</span></li>
            <li class="active" onclick="redirectAdmin(2)"><i class="material-icons icon">people</i><span class="text">Users</span></li>
            <li onclick="redirectAdmin(3)"><i class="material-icons icon">fitness_center</i><span class="text">Workouts</span></li>
            <li onclick="redirectAdmin(4)"><i class="material-icons icon">bar_chart</i><span class="text">Reports</span></li>
            <li onclick="redirectAdmin(5)"><i class="material-icons icon">settings</i><span class="text">Settings</span></li>
        </ul>
    </div>
    <div class="Logout">
        <button type="button" onclick="LogoutFunc()">
            <i class="material-icons">logout</i><span class="text">Logout</span>
        </button>
    </div>
</aside>

<main class="main-content">
    <div class="main-container">
        <header class="top-bar">
            <div class="greeting">
                <h1>User Management</h1>
                <p>View, add, edit, and manage users</p>
            </div>
            <div class="toggle-wrap">
                <span class="toggle-label" id="theme-label">Light</span>
                <label class="toggle">
                    <input type="checkbox" id="themeToggle">
                    <div class="toggle-track"></div>
                    <div class="toggle-thumb"><i class="material-icons light-icon">brightness_5</i></div>
                </label>
            </div>
        </header>
        <div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
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

        <section class="add-btn">
            <button class="addbt" onclick="openAddModal()">
                <i class="material-icons left">person_add</i>Add User
            </button>
        </section>

        <section class="card">
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($users)): foreach($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user["userID"]) ?></td>
                        <td><?= htmlspecialchars($user["username"]) ?></td>
                        <td><?= htmlspecialchars($user["email"]) ?></td>
                        <td><?= htmlspecialchars($user["gender"]) ?></td>
                        <td><?= htmlspecialchars($user["day_of_birth"]) ?></td>
                        <td><?= htmlspecialchars($user["role_description"]) ?></td>
                        <td><?= htmlspecialchars($user["created_At"]) ?></td>
                        <td>
                            <button class="btn-small blue" onclick='openEditModal(<?= json_encode($user) ?>)'>
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="btn-small red" onclick="deleteUser(<?= $user['userID'] ?>)">
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
        <button class="modal-close-btn" onclick="closeModal('AddModal')">×</button>
        <h1 class="add-title">Add User</h1>
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
<input type="password" id="password" maxlength="255" minlength="6" required>
                    <span class="material-icons" data-target="password" onclick="unhidepassFUNC(this)">visibility</span>
                </div>
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
<input type="password" id="confirm_password" maxlength="255" minlength="6" required>
                    <span class="material-icons" data-target="confirm_password" onclick="unhidepassFUNC(this)">visibility</span>
                </div>
            </div>
            <div class="input-group">
                <label>Gender</label>
                <select id="gender" class="browser-default" onchange="toggleselectgender(this)" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
<input type="text" id="othergender" maxlength="20" minlength="2" placeholder="Please specify" style="display:none; margin-top:10px;">
            </div>
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" id="doBirth" required>
            </div>
            <button type="button" class="register-button" onclick="addUserFunc()">Add User</button>
        </form>
    </div>
</div>

<div class="modal" id="EditModal">
    <div class="modal-content">
        <button class="modal-close-btn" onclick="closeModal('EditModal')">×</button>
        <h1 class="add-title">Edit User Details</h1>
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
<input type="password" id="edit_password" maxlength="255" minlength="6">
                visibility
            </span></input>
            </div>
            <div class="input-group">
                <label>Gender</label>
                <select id="edit_gender" class="browser-default" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" id="edit_doBirth" required>
            </div>
            <div class="input-group">
                <label>Role</label>
                <select id="edit_role" class="browser-default" required>
                    <option value="2">Admin</option>
                    <option value="1">User</option>
                    <option value="0">New User</option>
                </select>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="register-button" onclick="updateUserFunc()">Update User</button>
            </div>
        </form>
    </div>
</div>

<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script src="/workout_trackersys/scripts/AdminUser.js"></script>
<script src="/workout_trackersys/scripts/authservices.js"></script>

</body>
</html>
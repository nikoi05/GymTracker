<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
   
     <link rel="stylesheet" href="/workout_trackersys/assets/register.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified JavaScript -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <title>GymTracker - Register</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body class="auth-page">
    <div class="intro-loader">
    <h1 class="siteName">💪<span>Gym</span>Tracker</h1>
    </div>
<!-- Particles -->
<div class="particles" id="particles">
</div>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-brand">
        <a href="#" onclick="redirectFUNC(3)">💪 <span>Gym</span>Tracker</a> 
    </div>
    <div class="nav-links">
        <a href="#" onclick="redirectFUNC(1)">Login</a>
        <a href="#" onclick="redirectFUNC(3)">Home</a>
    </div>
</nav>

<!-- MAIN -->
<main class="register-main">
    <div class="register-container">
        
        <h1 class="register-title">Create Account</h1>
        <p class="register-subtitle">Start your fitness journey <i class="material-icons" style="font-size:16px;vertical-align:middle">fitness_center</i></p>

        <form class="register-form">

            <div class="input-group">
                <label>Username <span class="required-mark">*</span></label>
<input type="text" id="username" required maxlength="50" minlength="3" autocomplete="username">
            </div>

            <div class="input-group">
                <label>Email <span class="required-mark">*</span></label>
<input type="email" id="email" placeholder="e.g juandelacruz@email.com" required maxlength="50" minlength="5" autocomplete="email">
            </div>

            <div class="input-group">
                <label>Password <span class="required-mark">*</span></label>
                <div class="input-wrapper">
<input type="password" id="password" required maxlength="50" minlength="8" autocomplete="new-password">
                    <span class="material-icons" data-target="password" onclick="unhidepassFUNC(this)">visibility</span>
                </div>
                <ul class="password-rules" id="passwordRules" aria-live="polite">
                    <li data-rule="length">At least 8 characters</li>
                    <li data-rule="uppercase">One uppercase letter</li>
                    <li data-rule="lowercase">One lowercase letter</li>
                    <li data-rule="number">One number</li>
                    <li data-rule="special">One special character</li>
                </ul>
            </div>

            <div class="input-group">
                <label>Confirm Password <span class="required-mark">*</span></label>
                <div class="input-wrapper">
<input type="password" id="confirm_password" required maxlength="50" minlength="8" autocomplete="new-password">
                    <span class="material-icons" data-target="confirm_password" onclick="unhidepassFUNC(this)">visibility</span>
                </div>
                <small class="field-hint" id="passwordMatchHint" aria-live="polite">Passwords must match.</small>
            </div>

            <div class="input-group">
                <label>Gender <span class="required-mark">*</span></label>
                <select id="gender" onchange="toggleselectgender(this)" required>
                    <option value="" disabled selected>Select Gender</option>
                    <?php 
                    $genders = $user->getGender();
                    foreach($genders as $g): ?>
                        <option value="<?= $g['genderID'] ?>"><?= htmlspecialchars($g['gender']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="text" id="othergender" placeholder="Please specify" style="display:none;" maxlength="50">
            </div>

            <div class="input-group">
                <label>Date of Birth <span class="required-mark">*</span></label>
                <input type="date" id="doBirth" required>
                <small class="field-hint" id="dobHint" aria-live="polite">You must be 18 years old or older. Future dates are not allowed.</small>
            </div>

            <button type="button" class="register-button" onclick="registerUserFunc()">
                Register
            </button>

        </form>

        <div class="register-footer">
            <p>Already have an account?
                <a href="#" onclick="redirectFUNC(1)">Login</a>
            </p>
        </div>

    </div>
</main>
<footer>
    <div class="footer-container">
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span><span style="color:white;">Tracker</span></div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
</body>

<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/authservices.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script></script>

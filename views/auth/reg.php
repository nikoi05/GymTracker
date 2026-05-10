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
    <title>Gym Tracker - Register</title>
</head>
<body>
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
        <p class="register-subtitle">Start your fitness journey 💪</p>

        <form class="register-form">

            <div class="input-group">
                <label>Username</label>
                <input type="text" id="username" required maxlength="50">
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" id="email" placeholder="e.g juandelacruz@email.com" required maxlength="50">
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" required maxlength="50">
                    <span class="material-icons" data-target="password" onclick="unhidepassFUNC(this)">visibility</span>
                </div>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" required maxlength="50">
                    <span class="material-icons" data-target="confirm_password" onclick="unhidepassFUNC(this)">visibility</span>
                </div>
            </div>

            <div class="input-group">
                <label>Gender</label>
                <select id="gender" onchange="toggleselectgender(this)" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <input type="text" id="othergender" placeholder="Please specify" style="display:none;" maxlength="50">
            </div>

            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" id="doBirth" required>
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
            <div class="footer-brand">💪 GymTracker</div>
            <div class="footer-links">
                <a href="#">Features</a>
                <a href="#">Pricing</a>
                <a href="#">Contact</a>
            </div>
        </div>
        <div class="footer-copyright">
            © 2026 Gym Tracker. Built with ❤️ for fitness lovers by Niko
        </div>
    </footer>
</body>

<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/authservices.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script></script>
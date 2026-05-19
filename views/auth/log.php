
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Materialized CSS framework-->
    <!-- Compiled and minified CSS -->
     <link rel="stylesheet" href="/workout_trackersys/assets/logincss.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified JavaScript -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <title>GymTracker - Login</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
</head>
<body class="auth-page">
    <!-- intro Loader -->
   <div class="intro-loader">
    <h1 class="siteName">💪<span>Gym</span>Tracker</h1>
    </div>
    <!-- Particles -->
    <div class="particles" id="particles">
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-brand">
           <a href="?page=home">💪 <span>Gym</span>Tracker</a> 
        </div>
        <div class="nav-links">
            <a href="#" onclick="redirectFUNC(3)">Home</a>
            <a href="#" onclick="redirectFUNC(2)">Register</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="login-main">
        <div class="login-container">
            <div class="login-header">
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to your account</p>
            </div>

            <form class="login-form" id="loginForm">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
<input type="email" id="email" name="email" required minlength="5" maxlength="100">
                        <span class="material-icons email">email</span>
                    </div>
                    <span class="input-label">Enter your email</span>
                </div>

                <div class="input-group">
                    <label for="Logpassword">Password</label>
                    <div class="input-wrapper">
<input type="password" id="Logpassword" name="Logpassword" required minlength="6" maxlength="100">
                        <span class="material-icons password-"  data-target="Logpassword" onclick="unhidepassFUNC(this)">
                visibility
            </span>
                    </div>
                    <span class="input-label">Enter your password</span>
                </div>

                <button type="button" class="login-button" onclick="loginUserFunc()">
                    <span>Sign In</span>
                    <i class="material-icons">login</i>
                </button>
            </form>

            <div class="login-footer">
                <p>Don't have an account? 
                    <a href="#" onclick="redirectFUNC(2)" class="signup-link">Create Account</a>
                </p>
            </div>
        </div>

        <!-- Demo Illustration -->
        <div class="login-illustration">
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Bench Press</div>
                        <div class="stat-value">3 × 10 @ 80kg</div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Lat Pulldown</div>
                        <div class="stat-value">2 × 10 @ 100kg</div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Tricep Dips</div>
                        <div class="stat-value">2 × 10 @ 67kg</div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Chest Flys</div>
                        <div class="stat-value">2 × 10 @ 50kg</div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Magbeg</div>
                        <div class="stat-value">2 × 10 to failure</div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">I miss you</div>
                        <div class="stat-value">2 × 10 to failure</div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Pull-Ups</div>
                        <div class="stat-value">2 × 10 </div>
                    </div>
                </div>
            </div>
            <div class="floating-card">
                <div class="workout-demo">
                    <div class="workout-icon"><i class="material-icons">fitness_center</i></div>
                    <div class="workout-stats">
                        <div class="stat">Lateral Raise</div>
                        <div class="stat-value">2 × 10 @ 25kg</div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </main>

    <!-- Footer -->
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

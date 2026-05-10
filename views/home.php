<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Tracker - Track Your Progress 🚀</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="intro-loader">
    <h1 class="siteName">💪<span>Gym</span>Tracker</h1>
    </div>
    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle" style="--delay: 0s; --duration: 20s;"></div>
        <div class="particle" style="--delay: 2s; --duration: 25s;"></div>
        <div class="particle" style="--delay: 4s; --duration: 18s;"></div>
        <div class="particle" style="--delay: 6s; --duration: 22s;"></div>
    </div>

    <!-- Main Hero Section -->
    <header class="hero">
        <nav class="navbar">
            <div class="nav-brand">
                💪 <span>Gym</span>Tracker
            </div>
            <div class="nav-links">
                <a href="#" onclick="redirectFUNC(1)">Login</a>
                <a href="#" onclick="redirectFUNC(2)">Register</a>
            </div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>

        <div class="hero-content">
            <div class="hero-text">
                <h1 class="main-title">
                    Track Your <span class="gradient-text">Gym Progress</span>
                    <br>Like Never Before
                </h1>
                <p class="hero-subtitle">
                    Log workouts, track progress, and crush your fitness goals 
                    with our intuitive gym tracker app.
                </p>
                <div class="hero-buttons">
                    <button class="cta-primary" onclick="redirectFUNC(1)">
                        <span>Start Tracking</span>
                        <i class="material-icons">rocket_launch</i>
                    </button>
                    <button class="cta-secondary" onclick="redirectFUNC(2)">
                        Create Free Account
                    </button>
                </div>
                <div class="features-preview">
                    <div class="feature-item">
                        <div class="feature-icon">📊</div>
                        <span>Progress Charts</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⏱️</div>
                        <span>Workout Timer</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">📱</div>
                        <span>Mobile Ready</span>
                    </div>
                </div>
            </div>
            
            <div class="hero-image">
                <div class="phone-mockup">
                    <div class="phone-screen">
                        <div class="app-demo">
                            <div class="chart-demo"></div>
                            <div class="workout-card">Bench Press 3x10</div>
                            <div class="workout-card">Bench Press 3x10</div>
                            <div class="workout-card">Bench Press 3x10</div>
                            <div class="workout-card">Bench Press 3x10</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-brand">💪 GymTracker</div>
        </div>
        <div class="footer-copyright">
            © 2026 Gym Tracker. Built with ❤️ for fitness lovers by Niko
        </div>
    </footer>
</body>

<!-- Scripts -->
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script>
    window.addEventListener("load", () => {
    const name = document.querySelector(".siteName");
    const intro = document.querySelector(".intro-loader");

    if(!name || !intro) return;

    setTimeout(() => {
        name.style.opacity = '1';
        name.style.transform = "translateY(0)";
    }, 300);

    setTimeout(() => {
        intro.style.top = "-100%";
   }, 2000);
});
// Mobile menu toggle
document.querySelector('.hamburger').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});
</script>
</html>
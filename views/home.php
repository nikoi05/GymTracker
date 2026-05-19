<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker - Home</title>
    <link rel="stylesheet" href="/workout_trackersys/assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/workout_trackersys/assets/footer.css">
    <link rel="stylesheet" href="/workout_trackersys/assets/animations-global.css">
</head>
<body>
<script src="/workout_trackersys/assets/animations-global-inject.js"></script>
<script src="/workout_trackersys/assets/animations-observer.js"></script>

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
    <nav class="navbar" aria-label="Primary">
        <div class="nav-brand" role="button" tabindex="0" onclick="redirectFUNC(1)" onkeydown="if(event.key==='Enter') redirectFUNC(1)">
            💪 <span>Gym</span>Tracker
        </div>
        <div class="nav-links" id="landingNavLinks">
            <a href="#" onclick="event.preventDefault(); redirectFUNC(1); closeLandingNav();">Login</a>
            <a href="#" onclick="event.preventDefault(); redirectFUNC(2); closeLandingNav();">Register</a>
        </div>
        <button class="hamburger" type="button" aria-label="Open navigation" aria-controls="landingNavLinks" aria-expanded="false" onclick="toggleLandingNav()">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <header class="hero">
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
                        <div class="feature-icon"><i class="material-icons">bar_chart</i></div>
                        <span>Progress Charts</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="material-icons">timer</i></div>
                        <span>Workout Timer</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="material-icons">smartphone</i></div>
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
        <div class="footer-brand">&#128170;<span class="brand-gym">Gym</span><span style="color:white">Tracker</span></div>
    </div>
    <div class="footer-copyright">&copy; 2026 Gym Tracker. Built with &hearts; for fitness lovers by Niko</div>
</footer>
</body>

<!-- Scripts -->
<script src="/workout_trackersys/scripts/redirect.js"></script>
<script src="/workout_trackersys/scripts/magic.js"></script>
<script>
    window.addEventListener("load", () => {
        const name = document.querySelector(".siteName");
        const intro = document.querySelector(".intro-loader");

        if (!name || !intro) return;

        setTimeout(() => {
            name.style.opacity = '1';
            name.style.transform = "translateY(0)";
        }, 300);

        setTimeout(() => {
            intro.style.top = "-100%";
        }, 2000);
    });

    function closeLandingNav() {
        const navLinks = document.getElementById('landingNavLinks');
        const hamburgerBtn = document.querySelector('.hamburger');
        if (!navLinks) return;

        navLinks.classList.remove('active');
        if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', 'false');
    }

    function toggleLandingNav() {
        const navLinks = document.getElementById('landingNavLinks');
        const hamburgerBtn = document.querySelector('.hamburger');
        if (!navLinks) return;

        const isActive = navLinks.classList.toggle('active');
        if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', String(isActive));
    }


  function ToggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
  }
const themeToggle = document.getElementById('themeToggle');
const themeLabel = document.getElementById('theme-label');
const thumb = document.querySelector('.toggle-thumb i');

// APPLY SAVED THEME ON LOAD
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.checked = true;
        themeLabel.textContent = 'Dark';
        thumb.textContent = 'brightness_2';
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        themeToggle.checked = false;
        themeLabel.textContent = 'Light';
        thumb.textContent = 'brightness_5';
    }
});

// TOGGLE CHANGE EVENT
themeToggle.addEventListener('change', () => {
    if (themeToggle.checked) {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeLabel.textContent = 'Dark';
        thumb.textContent = 'brightness_2';
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        themeLabel.textContent = 'Light';
        thumb.textContent = 'brightness_5';
        localStorage.setItem('theme', 'light');
    }
});
let charts = {};

/* ============================================================
   UNIVERSAL CHART LOADER
============================================================ */
async function getChart(canvasId, type = 'weekly', apiUrl,label) {

    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    try {
        const response = await fetch(`${apiUrl}?type=${type}`);
        if (!response.ok) throw new Error('API failed');

        const data = await response.json();

        // destroy previous instance (prevents overlap)
        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }

        // theme variables
        const root = getComputedStyle(document.documentElement);
        const accent = root.getPropertyValue('--accent').trim();
        const accentLight = root.getPropertyValue('--accent-light').trim();
        const border = root.getPropertyValue('--border').trim();
        const textMuted = root.getPropertyValue('--text-muted').trim();

        const ctx = canvas.getContext('2d');

        charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    label: label,

                    borderColor: accent,
                    backgroundColor: accentLight,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,

                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: accent,
                    pointBorderWidth: 2
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    mode: 'index',
                    intersect: false
                },

                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(2,0,36,0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 10
                    }
                },

                scales: {
                    x: {
                        grid: { color: border, drawBorder: false },
                        ticks: { color: textMuted }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: border, drawBorder: false },
                        ticks: { color: textMuted }
                    }
                },

                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });

    } catch (err) {
        console.error(`Chart ${canvasId} error:`, err);
    }
}
// universal listner for charts  // I learned that this entire logic is scalable for when we
document.addEventListener('DOMContentLoaded', () => {
const activitylabel = "Total Duration";
 const userlabel ="User Registration";
    // INITIAL LOAD (NO OVERLAP)
    getChart('ActivityChartsAll', 'weekly', '/workout_trackersys/api/get_ALL_ACTIVITY_DATA.php',activitylabel);
    getChart('UserReg', 'weekly', '/workout_trackersys/api/get_user_registrationData.php',userlabel);

    /* ========================================================
       ACTIVITY CHART TABS
    ======================================================== */
    const activityCard = document.querySelector('#ActivityChartsAll').closest('.card');
    
    activityCard.querySelectorAll('.chart-tab').forEach(tab => {
        tab.addEventListener('click', function () {

            activityCard.querySelector('.chart-tab.active')?.classList.remove('active');
            this.classList.add('active');

            const type = this.textContent.trim().toLowerCase();

            getChart(
                'ActivityChartsAll',
                type,
                '/workout_trackersys/api/get_ALL_ACTIVITY_DATA.php',activitylabel);
        });
    });

    /* ========================================================
       USER CHART TABS
    ======================================================== */
    const userCard = document.querySelector('#UserReg').closest('.card');
   
    userCard.querySelectorAll('.chart-tab').forEach(tab => {
        tab.addEventListener('click', function () {

            userCard.querySelector('.chart-tab.active')?.classList.remove('active');
            this.classList.add('active');

            const type =this.textContent.trim().toLowerCase();

            getChart(
                'UserReg',
                type,
                '/workout_trackersys/api/get_user_registrationData.php'
           ,userlabel );
        });
    });

});

function LogoutFunc() {
    GymSwal.fire({
        title: 'Leaving so soon?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Stay'
    }).then(r => {
        if (r.isConfirmed) {
            $.ajax({
                url: '/workout_trackersys/controllers/logout.php',
                type: 'POST',
                success: () => window.location.href = '?page=login'
            });
        }
    });
}

/* ============================================================
   GYMSWAL & GYMTOAST
============================================================ */
const GymSwal = Swal.mixin({
    customClass:{ container:'swal-on-top' }, backdrop:'rgba(0,0,0,0.5)',
    didOpen:(p)=>{
        p.style.background='rgba(9,9,121,0.95)'; p.style.backdropFilter='blur(20px)';
        p.style.border='1px solid rgba(255,255,255,0.2)'; p.style.borderRadius='20px';
        p.style.color='#fff'; p.style.fontFamily='Poppins,sans-serif';
        const t=p.querySelector('.swal2-title'); if(t)t.style.color='#fff';
        const tx=p.querySelector('.swal2-html-container'); if(tx)tx.style.color='rgba(255,255,255,0.8)';
        const c=p.querySelector('.swal2-confirm'); if(c){c.style.background='linear-gradient(45deg,#38BDF8,#3B82F6)';c.style.border='none';c.style.borderRadius='50px';c.style.fontFamily='Poppins,sans-serif';c.style.fontWeight='600';}
        const x=p.querySelector('.swal2-cancel'); if(x){x.style.background='rgba(255,255,255,0.15)';x.style.color='white';x.style.border='1px solid rgba(255,255,255,0.2)';x.style.borderRadius='50px';x.style.fontFamily='Poppins,sans-serif';x.style.fontWeight='600';}
    }
});

const GymToast = Swal.mixin({
    toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true,
    customClass:{ container:'swal-on-top' },
    didOpen:(t)=>{ t.style.background='rgba(9,9,121,0.95)'; t.style.backdropFilter='blur(20px)'; t.style.border='1px solid rgba(255,255,255,0.2)'; t.style.borderRadius='12px'; t.style.color='#fff'; t.addEventListener('mouseenter',Swal.stopTimer); t.addEventListener('mouseleave',Swal.resumeTimer); }
});


    function ToggleSidebar() {
     const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
  // Save the state based on the current class list
  const isActive = sidebar.classList.contains('active');
  localStorage.setItem('sidebar-state', isActive ? 'active' : 'inactive');
  }
const themeToggle = document.getElementById('themeToggle');
const themeLabel = document.getElementById('theme-label');
const thumb = document.querySelector('.toggle-thumb i');

// APPLY SAVED THEME ON LOAD
window.addEventListener('DOMContentLoaded', () => {
const savedStateToggle = localStorage.getItem('sidebar-state');
  const sidebar = document.getElementById("sidebar");

  // Only add the class if it was saved as 'active'
  if (savedStateToggle === 'active') {
      sidebar.classList.add('active');
  } else {
      sidebar.classList.remove('active');
  }


    // theme 
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
    
    // Re-render all charts with new theme colors
    Object.keys(charts).forEach(canvasId => {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const card = canvas.closest('.card');
        if (!card) return;
        const activeTab = card.querySelector('.chart-tab.active');
        const type = activeTab ? activeTab.textContent.trim().toLowerCase() : 'weekly';
        
        if (canvasId === 'activityChart') {
            getChart(canvasId, type, '/workout_trackersys/api/get_platform_activity_data.php', 'Platform Activity');
        } else if (canvasId === 'ActivityChartsAll') {
            getChart(canvasId, type, '/workout_trackersys/api/get_ALL_ACTIVITY_DATA.php', 'Total Duration');
        } else if (canvasId === 'UserReg') {
            getChart(canvasId, type, '/workout_trackersys/api/get_user_registrationData.php', 'User Registration');
        }
    });
});
let charts = {};

/* ============================================================
   UNIVERSAL CHART LOADER
============================================================ */
async function getChart(canvasId, type = 'weekly', apiUrl, label) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    try {
        const response = await fetch(`${apiUrl}?type=${type}`);
        if (!response.ok) throw new Error('API failed');
        const data = await response.json();

        if (charts[canvasId]) charts[canvasId].destroy();

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textMuted = isDark ? 'rgba(255,255,255,0.45)' : '#94a3b8';
        const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';

        const ctx = canvas.getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, canvas.offsetHeight || 220);
        grad.addColorStop(0, 'rgba(59,130,246,0.35)');
        grad.addColorStop(0.5, 'rgba(56,189,248,0.18)');
        grad.addColorStop(1, 'rgba(59,130,246,0)');

        charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    label,
                    borderColor: '#3B82F6',
                    backgroundColor: grad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 9,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3B82F6',
                    pointBorderWidth: 3,
                    pointHoverBorderWidth: 4,
                    pointHoverBackgroundColor: '#fff',
                    shadowOffsetX: 0,
                    shadowOffsetY: 4,
                    shadowBlur: 12,
                    shadowColor: 'rgba(59,130,246,0.4)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? 'rgba(15,23,42,0.96)' : 'rgba(2,0,36,0.92)',
                        titleColor: '#f1f5f9',
                        bodyColor: '#cbd5e1',
                        borderColor: '#3B82F6',
                        borderWidth: 2,
                        cornerRadius: 12,
                        padding: 14,
                        titleFont: { weight: '700', size: 14 },
                        bodyFont: { size: 13 },
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { color: textMuted, font: { size: 11, weight: '500' } },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { color: textMuted, font: { size: 11, weight: '500' } },
                        border: { display: false }
                    }
                },
                animation: { duration: 1000, easing: 'easeOutCubic' }
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
const platformLabel = "Platform Activity";
    // INITIAL LOAD (NO OVERLAP)
    getChart('activityChart', 'weekly', '/workout_trackersys/api/get_platform_activity_data.php', platformLabel);
    getChart('ActivityChartsAll', 'weekly', '/workout_trackersys/api/get_ALL_ACTIVITY_DATA.php',activitylabel);
    getChart('UserReg', 'weekly', '/workout_trackersys/api/get_user_registrationData.php',userlabel);

    /* ========================================================
       PLATFORM ANALYTICS CHART TABS
    ======================================================== */
    const platformCard = document.querySelector('#activityChart').closest('.card');

    platformCard.querySelectorAll('.chart-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            platformCard.querySelector('.chart-tab.active')?.classList.remove('active');
            this.classList.add('active');

            const type = this.textContent.trim().toLowerCase();
            getChart(
                'activityChart',
                type,
                '/workout_trackersys/api/get_platform_activity_data.php',
                platformLabel
            );
        });
    });

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

window.LogoutFunc = window.LogoutFunc || function() {
    (window.GymSwal || Swal).fire({
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
};

/* ============================================================
   GYMSWAL & GYMTOAST
============================================================ */
const GymSwal = window.GymSwal || Swal.mixin({
    customClass: {
        container: 'swal-on-top',
        popup: 'gym-swal-popup',
        title: 'gym-swal-title',
        htmlContainer: 'gym-swal-text',
        confirmButton: 'gym-swal-confirm',
        cancelButton: 'gym-swal-cancel'
    },
    background: 'var(--surface)',
    color: 'var(--text)',
    backdrop: 'rgba(2, 6, 23, 0.62)',
    buttonsStyling: false,
    didOpen: (p) => {
        p.style.borderRadius = '18px';
        p.style.border = '1px solid var(--border)';
        p.style.boxShadow = '0 20px 48px rgba(2,6,23,.28)';
        const ok = p.querySelector('.swal2-confirm');
        if (ok) { ok.style.background = 'linear-gradient(45deg,#38BDF8,#3B82F6)'; ok.style.color = '#fff'; ok.style.border = 'none'; ok.style.borderRadius = '999px'; ok.style.padding = '10px 20px'; ok.style.fontWeight = '600'; }
        const no = p.querySelector('.swal2-cancel');
        if (no) { no.style.background = 'var(--surface-2)'; no.style.color = 'var(--text)'; no.style.border = '1px solid var(--border)'; no.style.borderRadius = '999px'; no.style.padding = '10px 20px'; no.style.fontWeight = '600'; }
    }
});

const GymToast = window.GymToast || Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
        container: 'swal-on-top',
        popup: 'gym-toast-popup',
        title: 'gym-toast-title',
        htmlContainer: 'gym-toast-text'
    },
    background: 'var(--surface)',
    color: 'var(--text)',
    didOpen: (t) => {
        t.style.borderRadius = '12px';
        t.style.border = '1px solid var(--border)';
        t.style.boxShadow = '0 20px 48px rgba(2,6,23,.28)';
        t.addEventListener('mouseenter', Swal.stopTimer);
        t.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

window.GymSwal = window.GymSwal || GymSwal;
window.GymToast = window.GymToast || GymToast;

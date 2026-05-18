(() => {
  function safeNumber(v) {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  }

  window._activityRenderChart = function renderChart(canvasId, rows, chartType) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textMuted = isDark ? 'rgba(255,255,255,0.45)' : '#94a3b8';
    const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const tooltipBg = isDark ? 'rgba(15,23,42,0.96)' : 'rgba(2,0,36,0.92)';
    const PALETTE = ['#3B82F6','#38BDF8','#22C55E','#F59E0B','#EF4444','#A855F7','#10B981','#F97316'];

    const ctx = canvas.getContext('2d');
    const h = canvas.offsetHeight || 200;

    let bgColor, borderColor, borderWidth, borderRadius, pointRadius, pointHoverRadius;

    if (chartType === 'line') {
      const grad = ctx.createLinearGradient(0, 0, 0, h);
      grad.addColorStop(0, 'rgba(59,130,246,0.35)');
      grad.addColorStop(0.5, 'rgba(56,189,248,0.18)');
      grad.addColorStop(1, 'rgba(59,130,246,0)');
      bgColor = grad;
      borderColor = '#3B82F6';
      borderWidth = 3;
      pointRadius = 5;
      pointHoverRadius = 9;
    } else {
      bgColor = (rows || []).map((_, i) => {
        const g = ctx.createLinearGradient(0, 0, 0, h);
        const c = PALETTE[i % PALETTE.length];
        g.addColorStop(0, c);
        g.addColorStop(1, c + '88');
        return g;
      });
      borderColor = 'transparent';
      borderWidth = 0;
      borderRadius = 8;
      pointRadius = 0;
      pointHoverRadius = 0;
    }

    new Chart(canvas, {
      type: chartType,
      data: {
        labels: (rows || []).map(r => r.label || 'Unassigned'),
        datasets: [{
          data: (rows || []).map(r => safeNumber(r.total)),
          backgroundColor: bgColor,
          borderColor,
          borderWidth,
          borderRadius: borderRadius || 0,
          tension: 0.4,
          fill: chartType === 'line',
          pointRadius,
          pointHoverRadius,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#3B82F6',
          pointBorderWidth: 3,
          pointHoverBorderWidth: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: tooltipBg,
            titleColor: '#f1f5f9',
            bodyColor: '#cbd5e1',
            borderColor: '#3B82F6',
            borderWidth: 2,
            cornerRadius: 12,
            padding: 14,
            titleFont: { weight: '700', size: 14 },
            bodyFont: { size: 13 }
          }
        },
        scales: {
          x: { grid: { color: gridColor, drawBorder: false }, ticks: { color: textMuted, font: { size: 11, weight: '500' } }, border: { display: false } },
          y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: textMuted, font: { size: 11, weight: '500' } }, border: { display: false } }
        },
        animation: { duration: 1000, easing: 'easeOutCubic' }
      }
    });
  }

  function boot() {
    const dailyRows = window.__ADMIN_ACTIVITY_DAILY_ROWS__ || [];
    const typeRows = window.__ADMIN_ACTIVITY_TYPE_ROWS__ || [];
    window._activityRenderChart('dailyChart', dailyRows, 'line');
    window._activityRenderChart('typeChart', typeRows, 'bar');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();


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
    
    // Re-render charts with new theme
    setTimeout(() => {
        const dailyRows = window.__ADMIN_ACTIVITY_DAILY_ROWS__ || [];
        const typeRows = window.__ADMIN_ACTIVITY_TYPE_ROWS__ || [];
        Object.values(Chart.instances).forEach(c => c.destroy());
        window._activityRenderChart('dailyChart', dailyRows, 'line');
        window._activityRenderChart('typeChart', typeRows, 'bar');
    }, 50);
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
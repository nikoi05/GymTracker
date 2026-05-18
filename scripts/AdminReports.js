

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
    
    // Re-render all charts with new theme
    setTimeout(() => {
        const datasets = window.__ADMIN_REPORTS_DATASETS__ || {};
        Object.values(Chart.instances).forEach(c => c.destroy());
        window._reportsRenderChart('activityChart', datasets.activity || [], 'line', 'Minutes');
        window._reportsRenderChart('registrationChart', datasets.registration || [], 'bar', 'Users');
        window._reportsRenderChart('goalChart', datasets.goals || [], 'doughnut', 'Goals');
        window._reportsRenderChart('muscleChart', datasets.muscles || [], 'bar', 'Exercises');
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



(() => {
  function safeNumber(v) {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  }

  window._reportsRenderChart = function renderChart(canvasId, rows, chartType, label) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textMuted = isDark ? 'rgba(255,255,255,0.45)' : '#94a3b8';
    const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const tooltipBg = isDark ? 'rgba(15,23,42,0.96)' : 'rgba(2,0,36,0.92)';

    const PALETTE = ['#3B82F6','#38BDF8','#22C55E','#F59E0B','#EF4444','#A855F7','#10B981','#F97316','#64748B'];

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
    } else if (chartType === 'bar') {
        // Per-bar gradient
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
    } else {
        bgColor = PALETTE;
        borderColor = isDark ? 'rgba(255,255,255,0.08)' : '#fff';
        borderWidth = 3;
    }

    const isLinear = chartType === 'line' || chartType === 'bar';

    new Chart(canvas, {
      type: chartType,
      data: {
        labels: (rows || []).map(r => r.label),
        datasets: [{
          label,
          data: (rows || []).map(r => safeNumber(r.total)),
          borderColor,
          backgroundColor: bgColor,
          borderWidth,
          borderRadius: borderRadius || 0,
          tension: 0.4,
          fill: chartType === 'line',
          pointRadius: pointRadius || 0,
          pointHoverRadius: pointHoverRadius || 0,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#3B82F6',
          pointBorderWidth: 3,
          pointHoverBorderWidth: 4,
          hoverOffset: chartType === 'doughnut' ? 10 : 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: chartType === 'doughnut' ? '65%' : undefined,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            display: chartType === 'doughnut',
            position: 'bottom',
            labels: { color: textMuted, padding: 16, font: { size: 12, weight: '500' }, boxWidth: 12, borderRadius: 4 }
          },
          tooltip: {
            backgroundColor: tooltipBg,
            titleColor: '#f1f5f9',
            bodyColor: '#cbd5e1',
            borderColor: '#3B82F6',
            borderWidth: 2,
            cornerRadius: 12,
            padding: 14,
            titleFont: { weight: '700', size: 14 },
            bodyFont: { size: 13 },
            displayColors: chartType !== 'line'
          }
        },
        scales: isLinear ? {
          x: { grid: { color: gridColor, drawBorder: false }, ticks: { color: textMuted, font: { size: 11, weight: '500' } }, border: { display: false } },
          y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: textMuted, font: { size: 11, weight: '500' } }, border: { display: false } }
        } : {},
        animation: { duration: 1000, easing: 'easeOutCubic' }
      }
    });
  }

  function boot() {
    const datasets = window.__ADMIN_REPORTS_DATASETS__ || {};
    window._reportsRenderChart('activityChart', datasets.activity || [], 'line', 'Minutes');
    window._reportsRenderChart('registrationChart', datasets.registration || [], 'bar', 'Users');
    window._reportsRenderChart('goalChart', datasets.goals || [], 'doughnut', 'Goals');
    window._reportsRenderChart('muscleChart', datasets.muscles || [], 'bar', 'Exercises');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();


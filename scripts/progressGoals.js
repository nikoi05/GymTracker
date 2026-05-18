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

const themeToggle = document.getElementById('themeToggle');
const themeLabel = document.getElementById('theme-label');
const thumb = document.querySelector('.toggle-thumb i');

let fetchedGoals = [];
let weeklyFreq = [1, 2, 0, 2, 1, 3, 2];
let monthlyFreq = [10, 14, 12, 18, 16, 20, 17, 22, 19, 25, 23, 28];
let weeklyVolume = new Array(12).fill(0);
let heatmapSeries = [];
let freqChart = null;
let volumeChart = null;
let isEditing = false;
let editingId = null;
// loads and fetches data first 
window.addEventListener('DOMContentLoaded', async () => {
const savedStateToggle = localStorage.getItem('sidebar-state');
  const sidebar = document.getElementById("sidebar");

  // Only add the class if it was saved as 'active'
  if (savedStateToggle === 'active') {
      sidebar.classList.add('active');
  } else {
      sidebar.classList.remove('active');
  }

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('change', () => setTheme(themeToggle.checked ? 'dark' : 'light'));
    }

    await fetchLatestGoalsAndStats();
    buildFreqChart(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], weeklyFreq);
    buildVolumeChart(weeklyVolume);
    buildHeatmap(heatmapSeries);
});

function setTheme(theme) {
    const dark = theme === 'dark';
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    if (themeToggle) themeToggle.checked = dark;
    if (themeLabel) themeLabel.textContent = dark ? 'Dark' : 'Light';
    if (thumb) thumb.textContent = dark ? 'brightness_2' : 'brightness_5';
    if (freqChart) updateChartColors(freqChart);
    if (volumeChart) updateChartColors(volumeChart);
}

function ToggleSidebar() {
     const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
  // Save the state based on the current class list
  const isActive = sidebar.classList.contains('active');
  localStorage.setItem('sidebar-state', isActive ? 'active' : 'inactive');
  }

async function getGoals() {
    const response = await fetch('/workout_trackersys/api/get_goals.php');
    if (!response.ok) throw new Error('Failed to fetch goals');
    return response.json();
}

async function fetchStats() {
    const response = await fetch('/workout_trackersys/api/get_goals_stats.php');
    if (!response.ok) throw new Error('Failed to fetch stats');
    return response.json();
}

async function fetchAnalytics() {
    const response = await fetch('/workout_trackersys/api/get_progress_analytics.php');
    if (!response.ok) throw new Error('Failed to fetch analytics');
    return response.json();
}
// this is the function to get all the datas from all fetching
async function fetchLatestGoalsAndStats() {
    try {
        // put each data into an array specifics for goals,stats, and progress analytics
        const [goalsData, statsData, analytics] = await Promise.all([getGoals(), fetchStats(), fetchAnalytics()]);
        // get the fetchedGoals then call renderGoals() to render the goals into cards
        fetchedGoals = Array.isArray(goalsData) ? goalsData : [];
        renderGoals();
        // render stats card
        if (Array.isArray(statsData) && statsData.length > 0) {
            renderStats(statsData[0]);
        }
        // basically fill the datasets with data from fetched
        hydrateAnalytics(analytics || {});
    } catch (err) {
        console.error(err);
        GymToast.fire({ icon: 'error', title: 'Failed to load goals data.' });
    }
}

function hydrateAnalytics(analytics) {
    // gets  data based on the fetch data and stores it to its corresponding variable
    weeklyFreq = mapWeeklyCounts(Array.isArray(analytics.weekly) ? analytics.weekly : []);
    monthlyFreq = mapMonthlyCounts(Array.isArray(analytics.monthly) ? analytics.monthly : []);
    //volume
    weeklyVolume = mapWeeklyVolume(Array.isArray(analytics.volume) ? analytics.volume : []);
    //for heat map
    heatmapSeries = Array.isArray(analytics.heatmap) ? analytics.heatmap : [];
    // renders it
    renderConsistencyMetrics(weeklyFreq, monthlyFreq, heatmapSeries);
}

function mapWeeklyCounts(rows) {
    const out = new Array(7).fill(0); // this is for the 7 days in the chart
    const keyMap = new Map(); // then mapping it 
    // rows is the data then a foreach then paramaeter to map out the days 
    rows.forEach((r) => keyMap.set(String(r.day).slice(0, 10), Number(r.workouts || 0)));// this one maps the workouts to the data

    //this for loop stores the mapped weekly counts to the array out that will be used to make the chart
    for (let i = 6; i >= 0; i -= 1) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        const key = d.toISOString().slice(0, 10);
        out[6 - i] = keyMap.get(key) || 0;
    }
    return out;
}

function mapMonthlyCounts(rows) {
    const out = new Array(12).fill(0);
    rows.forEach((r) => {
        const idx = Number(r.month_num) - 1;
        if (idx >= 0 && idx < 12) out[idx] = Number(r.workouts || 0);
    });
    return out;
}

function mapWeeklyVolume(rows) {
    const out = new Array(12).fill(0);
    const vals = rows.map((r) => Math.round((Number(r.total_seconds || 0) / 60)));
    const start = Math.max(0, 12 - vals.length);
    vals.forEach((v, i) => { out[start + i] = v; });
    return out;
}

function renderStats(s) {
    document.getElementById('statActive').textContent = s.ActiveGoals ?? '0';
    document.getElementById('statCompleted').textContent = s.CompletedGoals ?? '0';
    document.getElementById('statWorkout').textContent = s.Workout ?? '0';
    document.getElementById('statAvgProgress').textContent = (s.AvgProgress || s.AvgProgress === 0) ? `${s.AvgProgress}%` : '0%';
}

function getPercent(g) {
    if (g.status === 'completed') return 100;
    const target = parseFloat(g.target_value) || 0;
    const current = parseFloat(g.current_value) || 0;
    if (target <= 0) return 0;
    return Math.min(100, Math.round((current / target) * 100));
}

function renderGoals() {
    const grid = document.getElementById('goalsGrid');
    const colors = [
        'linear-gradient(45deg,#38BDF8,#3B82F6)',
        'linear-gradient(45deg,#F59E0B,#EF4444)',
        'linear-gradient(45deg,#22C55E,#38BDF8)',
        'linear-gradient(45deg,#6366F1,#8B5CF6)',
        'linear-gradient(45deg,#EC4899,#8B5CF6)'
    ];

    if (!fetchedGoals.length) {
        grid.innerHTML = `<div class="empty-goals" style="grid-column:1/-1"><div class="material-icons">flag</div><div>No goals yet.</div></div>`;
        return;
    }

    grid.innerHTML = '';
    fetchedGoals.forEach((g, i) => {
        const pct = getPercent(g);
        const color = colors[i % colors.length];
        const deadlineStr = g.deadline ? new Date(g.deadline).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-';
        const categoryIcon = getCategoryIcon(g.category);

        const card = document.createElement('div');
        card.className = 'goal-card';
        card.style.animationDelay = `${i * 0.06}s`;
        card.innerHTML = `
            <div class="goal-card-top">
                <div class="goal-emoji"><span class="material-icons">${categoryIcon}</span></div>
                <span class="goal-status-badge status-${g.status}">${(g.status || '').replace('-', ' ')}</span>
            </div>
            <div class="goal-name">${g.name}</div>
            <div class="goal-desc">${g.description || ''}</div>
            <div class="goal-progress-meta">
                <span class="goal-progress-label">${g.category || 'Goal'}</span>
                <span class="goal-progress-val">${pct}%</span>
            </div>
            <div class="goal-bar-bg"><div class="goal-bar-fill" style="width:0%;background:${color}" data-target="${pct}%"></div></div>
            <div class="goal-values"><span>Current: <strong style="color:var(--text)">${g.current_value}</strong></span><span>Target: ${g.target_value}</span></div>
            <div class="goal-deadline"><span class="material-icons">event</span> Deadline: ${deadlineStr}</div>
            <div class="goal-actions">
                <button class="goal-action-btn" onclick="openUpdateGoal(${g.goal_id})"><span class="material-icons">edit</span> Update</button>
                <button class="goal-action-btn" onclick="markComplete(${g.goal_id})"><span class="material-icons">check_circle</span> Complete</button>
                <button class="goal-action-btn del" onclick="deleteGoal(${g.goal_id})"><span class="material-icons">delete</span></button>
            </div>`;
        grid.appendChild(card);
    });

    setTimeout(() => document.querySelectorAll('.goal-bar-fill').forEach((b) => { b.style.width = b.dataset.target; }), 150);
}

function getCategoryIcon(category) {
    const k = String(category || '').toLowerCase();
    if (k.includes('strength')) return 'fitness_center';
    if (k.includes('weight')) return 'monitor_weight';
    if (k.includes('cardio')) return 'directions_run';
    if (k.includes('flex')) return 'self_improvement';
    if (k.includes('hiit')) return 'bolt';
    return 'flag';
}

function statusFromValues(current, target) {
    if (current <= 0) return 'not-started';
    if (current < target) return 'in-progress';
    return 'completed';
}

function postGoalAction(data) {
    return $.ajax({
        type: 'POST',
        url: '/workout_trackersys/controllers/GoalController.php',
        data
    });
}

function markComplete(id) {
    const g = fetchedGoals.find((x) => Number(x.goal_id) === Number(id));
    if (!g) return;
    if (g.status === 'completed') {
        GymToast.fire({ icon: 'info', title: 'Already completed.' });
        return;
    }

    GymSwal.fire({
        title: `Complete "${g.name}"?`,
        text: 'Mark this goal as achieved.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, complete!'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            const result = await postGoalAction({ action: 'MarkGoal', goal_id: id });
            if (String(result).trim().toLowerCase() === 'success') {
                await fetchLatestGoalsAndStats();
                GymToast.fire({ icon: 'success', title: 'Goal completed.' });
            } else {
                GymSwal.fire({ icon: 'error', title: 'Update failed', text: result });
            }
        } catch (err) {
            GymSwal.fire({ icon: 'error', title: 'Server Error', text: String(err) });
        }
    });
}

function deleteGoal(id) {
    const g = fetchedGoals.find((x) => Number(x.goal_id) === Number(id));
    if (!g) return;

    GymSwal.fire({
        title: `Delete "${g.name}"?`,
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            const result = await postGoalAction({ action: 'DeleteGoal', goal_id: id });
            if (String(result).trim().toLowerCase() === 'success') {
                await fetchLatestGoalsAndStats();
                GymToast.fire({ icon: 'success', title: 'Goal deleted.' });
            } else {
                GymSwal.fire({ icon: 'error', title: 'Delete failed', text: result });
            }
        } catch (err) {
            GymSwal.fire({ icon: 'error', title: 'Server Error', text: String(err) });
        }
    });
}

function openAddGoal() {
    isEditing = false;
    editingId = null;
    document.getElementById('modalTitle').textContent = 'Add New Goal';
    document.getElementById('goalName').value = '';
    document.getElementById('goalDesc').value = '';
    const categorySelect = document.getElementById('goalCategory');
    categorySelect.value = categorySelect.options[0]?.value;
    document.getElementById('goalDeadline').value = '';
    document.getElementById('goalCurrent').value = '';
    document.getElementById('goalTarget').value = '';
    document.getElementById('goalModal').classList.add('visible');
}

function openUpdateGoal(id) {
    const g = fetchedGoals.find((x) => Number(x.goal_id) === Number(id));
    if (!g) return;
    isEditing = true;
    editingId = Number(id);
    document.getElementById('modalTitle').textContent = 'Update Goal';
    document.getElementById('goalName').value = g.name || '';
    document.getElementById('goalDesc').value = g.description || '';
    const categorySelect = document.getElementById('goalCategory');
    categorySelect.value = g.goalID || categorySelect.options[0]?.value;
    document.getElementById('goalDeadline').value = g.deadline || '';
    document.getElementById('goalCurrent').value = g.current_value ?? 0;
    document.getElementById('goalTarget').value = g.target_value ?? 0;
    document.getElementById('goalModal').classList.add('visible');
}

function closeGoalModal() {
    document.getElementById('goalModal').classList.remove('visible');
}

function closeModalOutside(e) {
    if (e.target === document.getElementById('goalModal')) closeGoalModal();
}

async function saveGoal() {
    const name = document.getElementById('goalName').value.trim();
    const desc = document.getElementById('goalDesc').value.trim();
    const category = document.getElementById('goalCategory').value;
    const deadline = document.getElementById('goalDeadline').value;
    const current = parseFloat(document.getElementById('goalCurrent').value) || 0;
    const target = parseFloat(document.getElementById('goalTarget').value) || 0;

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const deadlineDate = new Date(deadline);

    if (!name) return GymSwal.fire({ icon: 'warning', title: 'Please enter a goal name.' });
    if (!deadline) return GymSwal.fire({ icon: 'warning', title: 'Please set a deadline.' });
    if (deadlineDate < today) return GymSwal.fire({ icon: 'warning', title: 'Please set a valid deadline.' });
    if (target <= 0) return GymSwal.fire({ icon: 'warning', title: 'Target value must be greater than 0.' });
   
    const status = statusFromValues(current, target);
    const payload = {
        action: isEditing ? 'UpdateGoal' : 'AddGoal',
        name,
        desc,
        category,
        current,
        target,
        deadline,
        status
    };

    if (isEditing) payload.goal_id = editingId;

    try {
        const result = await postGoalAction(payload);
        if (String(result).trim().toLowerCase() === 'success') {
            closeGoalModal();
            await fetchLatestGoalsAndStats();
            GymToast.fire({ icon: 'success', title: isEditing ? 'Goal updated.' : 'Goal added.' });
        } else {
            GymSwal.fire({ icon: 'error', title: 'Save failed', text: result });
        }
    } catch (err) {
        GymSwal.fire({ icon: 'error', title: 'Server Error', text: String(err) });
    }
}

function getChartColors() {
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    return { gc: dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)', tc: dark ? '#94A3B8' : '#5B7BAF' };
}

function buildFreqChart(labels, data) {
    const ctx = document.getElementById('freqChart').getContext('2d');
    if (freqChart) freqChart.destroy();
    const { gc, tc } = getChartColors();
    freqChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Workouts', data, backgroundColor: 'rgba(59,130,246,0.18)', borderColor: '#3B82F6', borderWidth: 2, borderRadius: 8, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { color: gc }, ticks: { color: tc, font: { family: 'Poppins', size: 11 } } }, y: { grid: { color: gc }, ticks: { color: tc, font: { family: 'Poppins', size: 11 }, stepSize: 1 }, beginAtZero: true } } }
    });
}

function buildVolumeChart(data) {
    const ctx = document.getElementById('volumeChart').getContext('2d');
    if (volumeChart) volumeChart.destroy();
    const { gc, tc } = getChartColors();
    const labels = ['W1', 'W2', 'W3', 'W4', 'W5', 'W6', 'W7', 'W8', 'W9', 'W10', 'W11', 'W12'];
    volumeChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label: 'Minutes', data, borderColor: '#38BDF8', backgroundColor: 'rgba(56,189,248,0.08)', tension: 0.4, fill: true, pointBackgroundColor: '#38BDF8', pointRadius: 4, borderWidth: 2.5 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { color: gc }, ticks: { color: tc, font: { family: 'Poppins', size: 11 } } }, y: { grid: { color: gc }, ticks: { color: tc, font: { family: 'Poppins', size: 11 } }, beginAtZero: false } } }
    });
}

function switchFreqChart(type, btn) {
    document.querySelectorAll('.chart-tab').forEach((t) => t.classList.remove('active'));
    if (btn) btn.classList.add('active');
    const labels = type === 'weekly'
        ? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    buildFreqChart(labels, type === 'weekly' ? weeklyFreq : monthlyFreq);
}

function updateChartColors(chart) {
    const { gc, tc } = getChartColors();
    ['x', 'y'].forEach((ax) => {
        chart.options.scales[ax].grid.color = gc;
        chart.options.scales[ax].ticks.color = tc;
    });
    chart.update();
}

function buildHeatmap(rows) {
    const map = new Map();
    (rows || []).forEach((r) => map.set(String(r.day).slice(0, 10), Number(r.workouts || 0)));
    const grid = document.getElementById('heatmapGrid');
    grid.innerHTML = '';
    for (let i = 0; i < 91; i += 1) {
        const cell = document.createElement('div');
        const d = new Date();
        d.setDate(d.getDate() - (90 - i));
        const key = d.toISOString().slice(0, 10);
        const raw = map.get(key) || 0;
        const lvl = raw >= 4 ? 4 : raw;
        cell.className = `heatmap-cell${lvl > 0 ? ` level-${Math.min(lvl, 4)}` : ''}`;
        cell.setAttribute('data-tip', raw === 0 ? 'No workout' : `${raw} workout${raw > 1 ? 's' : ''}`);
        grid.appendChild(cell);
    }
}

function renderConsistencyMetrics(weekly, monthly, heatmap) {
    const metricList = document.getElementById('metricList');
    if (!metricList) return;

    const weeklyTotal = weekly.reduce((a, b) => a + b, 0);
    const monthlyTotal = monthly.reduce((a, b) => a + b, 0);
    const activeDays = new Set((heatmap || []).map((r) => String(r.day).slice(0, 10))).size;
    const avgPerWeek = Math.round((monthlyTotal / 12) * 10) / 10;

    const items = [
        { icon: 'calendar_view_week', label: 'This Week', hint: 'Completed sessions in last 7 days', val: weeklyTotal },
        { icon: 'calendar_month', label: 'This Year', hint: 'Completed sessions in current year', val: monthlyTotal },
        { icon: 'event_available', label: 'Active Days', hint: 'Days with at least one workout in last 3 months', val: activeDays },
        { icon: 'query_stats', label: 'Avg / Month', hint: 'Average completed sessions per month this year', val: avgPerWeek }
    ];

    metricList.innerHTML = items.map((m) => `
        <div class="metric-item">
            <div class="metric-icon"><span class="material-icons">${m.icon}</span></div>
            <div class="metric-info">
                <div class="metric-label">${m.label}</div>
                <div class="metric-hint">${m.hint}</div>
            </div>
            <div class="metric-val">${m.val}</div>
        </div>
    `).join('');
}

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

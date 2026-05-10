
        // Theme toggle handling
        const themeToggle = document.getElementById('themeToggle');
        const themeLabel = document.getElementById('theme-label');
        
        themeToggle?.addEventListener('change', function() {
            const theme = this.checked ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            themeLabel.textContent = theme === 'dark' ? 'Dark' : 'Light';
        });
        
        // Initialize theme
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (themeToggle) {
                themeToggle.checked = savedTheme === 'dark';
            }
            if (themeLabel) {
                themeLabel.textContent = savedTheme === 'dark' ? 'Dark' : 'Light';
            }
        })();
    /* ── Sidebar Toggle ── */
    function ToggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

// Global variables
let currentGoals = [];
let currentProgressData = {};


// Initialize on page load
$(document).ready(function() {
    
    // Load initial data
    loadProgressData();
    loadGoals();
    
    // Setup tab navigation
    setupTabs();
    
    // Setup modal controls
    setupModals();
});

// ── Load Progress Data ──
function loadProgressData() {
    $.ajax({
        url: '/workout_trackersys/controllers/ProgressController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'getProgressData', userID: userID },
        success: function(data) {
            renderProgressStats(data.stats);

            // Update training intensity card if backend provided it
            if (data?.stats?.intensity !== undefined) {
                const intensityEl = document.getElementById('statIntensity');
                const intensityChangeEl = document.getElementById('statIntensityChange');
                if (intensityEl) intensityEl.textContent = data.stats.intensity;

                if (intensityChangeEl) {
                    const pct = data.stats.intensityPercent;
                    intensityChangeEl.textContent = pct !== undefined ? `+${pct}%` : '';
                }
            }
            renderProgressChart(data.weeklyActivity);
            renderRecentActivity(data.recentActivity);
            currentProgressData = data;
        },
        error: function() {
            // Fallback demo data
            renderProgressStats({
                totalWorkouts: 24,
                totalHours: '18.5h',
                intensity: 'High',
                intensityPercent: 75,
                streak: 7
            });
            renderProgressChart([2, 1, 3, 0, 2, 4, 1]);
            renderRecentActivity([
                { type: 'workout', name: 'Chest Day', date: 'Today', duration: '45 min' },
                { type: 'goal', name: 'Bench Press 80kg', date: 'Yesterday', status: 'completed' },
                { type: 'workout', name: 'Leg Day', date: '2 days ago', duration: '60 min' }
            ]);
        }
    });
}

// ── Load Goals ──
function loadGoals() {
    $.ajax({
        url: '/workout_trackersys/controllers/GoalController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'getGoals', userID: userID },
        success: function(data) {
            currentGoals = data.goals || [];
            renderGoals(currentGoals);
        },
        error: function() {
            // Fallback demo data - remove when backend connected
            currentGoals = [
                { id: 1, name: 'Bench Press 100kg', category: 'strength', current: 80, target: 100, unit: 'kg', deadline: '2024-03-15', color: 'linear-gradient(45deg,#EF4444,#F97316)' },
                { id: 2, name: 'Lose 5kg', category: 'weight', current: 2, target: 5, unit: 'kg', deadline: '2024-04-01', color: 'linear-gradient(45deg,#F59E0B,#EAB308)' },
                { id: 3, name: 'Run 5km', category: 'cardio', current: 3.5, target: 5, unit: 'km', deadline: '2024-02-28', color: 'linear-gradient(45deg,#38BDF8,#0EA5E9)' },
                { id: 4, name: '20 Pull-ups', category: 'strength', current: 15, target: 20, unit: 'reps', deadline: '2024-03-20', color: 'linear-gradient(45deg,#A855F7,#8B5CF6)' }
            ];
            renderGoals(currentGoals);
        }
    });
}
let Streak ="";
// fetch and render streak
async function getStreak(){
    try{
        const response = await fetch(`/workout_trackersys/api/getSTREAK.php`);
        if(!response){
            throw new Error("API FAILED");
        }
        const data = response.json();
        return data;
    }catch(err){
        console.error(err);
    }
}

async function initStreak(){
    Streak = await getStreak();
    //render the streak
document.getElementById('statStreak').innerText = Streak;

}

initStreak();

// ── Render Chart ──
let progressChart = null;
function renderProgressChart(data) {
    const ctx = document.getElementById('progressChart');
    if (!ctx) return;
    
    if (progressChart) progressChart.destroy();
    
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
    const textColor = dark ? '#94A3B8' : '#5B7BAF';
    
    progressChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Workouts',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: '#3B82F6',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { family: 'Poppins', size: 11 } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { family: 'Poppins', size: 11 } },
                    beginAtZero: true
                }
            }
        }
    });
}

// ── Render Recent Activity ──
function renderRecentActivity(activities) {
    const list = document.getElementById('activityList');
    if (!list) return;
    
    if (!activities || activities.length === 0) {
        list.innerHTML = '<div class="pg-empty-text">No recent activity yet. Start your first workout!</div>';
        return;
    }
    
    const icons = { workout: '💪', goal: '🎯', milestone: '🏆' };
    list.innerHTML = activities.map(a => `
        <div class="timeline-item">
            <div class="timeline-date">${a.date}</div>
            <div class="timeline-content">
                <span style="margin-right:8px">${icons[a.type] || '📊'}</span>
                <strong>${a.name}</strong>
                ${a.duration ? `<span style="color:var(--text-muted)"> — ${a.duration}</span>` : ''}
                ${a.status ? `<span class="badge badge-success" style="margin-left:8px">${a.status}</span>` : ''}
            </div>
        </div>
    `).join('');
}

// ── Render Goals ──
function renderGoals(goals) {
    const container = document.getElementById('goalsContainer');
    if (!container) return;
    
    if (!goals || goals.length === 0) {
        container.innerHTML = `
            <div class="pg-empty">
                <div class="pg-empty-icon">🎯</div>
                <div class="pg-empty-title">No goals yet</div>
                <div class="pg-empty-text">Set your first fitness goal to start tracking progress!</div>
                <button class="btn-primary" onclick="openGoalModal()">
                    <i class="material-icons">add</i> Create Goal
                </button>
            </div>
        `;
        return;
    }
    
    const categoryIcons = {
        strength: '💪',
        weight: '⚖️',
        endurance: '🏃',
        cardio: '❤️',
        flexibility: '🧘'
    };
    
    container.innerHTML = goals.map(goal => {
        const percent = Math.round((goal.current / goal.target) * 100);
        const isCompleted = percent >= 100;
        return `
            <div class="goal-card">
                <div class="goal-header">
                    <div style="display:flex;align-items:center">
                        <div class="goal-icon" style="background:${goal.color || 'var(--accent-light)'}">${categoryIcons[goal.category] || '🎯'}</div>
                        <div class="goal-info">
                            <div class="goal-title">${goal.name}</div>
                            <div class="goal-meta">Target: ${goal.target} ${goal.unit}</div>
                        </div>
                    </div>
                    <span class="goal-category ${goal.category}">${goal.category}</span>
                </div>
                <div class="goal-progress-wrap">
                    <div class="goal-progress-header">
                        <span class="goal-progress-label">Progress</span>
                        <span class="goal-progress-value">${goal.current} / ${goal.target} ${goal.unit} (${percent}%)</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:0%;background:${goal.color || 'var(--accent-grad)'}" data-target="${percent}%"></div>
                    </div>
                </div>
                <div class="goal-actions">
                    <button class="goal-btn" onclick="updateGoalProgress(${goal.id})">
                        <i class="material-icons">update</i> Update
                    </button>
                    <button class="goal-btn" onclick="editGoal(${goal.id})">
                        <i class="material-icons">edit</i> Edit
                    </button>
                    <button class="goal-btn" onclick="deleteGoal(${goal.id})">
                        <i class="material-icons">delete</i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    // Animate progress bars
    setTimeout(() => {
        document.querySelectorAll('.progress-bar-fill').forEach(bar => {
            bar.style.width = bar.dataset.target;
        });
    }, 300);
}

// ── Tab Navigation ──
function setupTabs() {
    $('.pg-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.pg-tab').removeClass('active');
        $(this).addClass('active');
        
        $('.pg-section').hide();
        $('#section-' + tab).fadeIn(300);
    });
}

// ── Modals ──
function setupModals() {
    // Close modal on overlay click
    $('.modal-overlay').on('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Close on escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
}

function openGoalModal(goalId = null) {
    const modal = $('#goalModal');
    modal.addClass('visible');
    
    if (goalId) {
        const goal = currentGoals.find(g => g.id === goalId);
        if (goal) {
            $('#goalName').val(goal.name);
            $('#goalCategory').val(goal.category);
            $('#goalTarget').val(goal.target);
            $('#goalUnit').val(goal.unit);
            $('#goalDeadline').val(goal.deadline);
            $('#goalModalTitle').text('Edit Goal');
            $('#goalAction').val('update');
            $('#goalId').val(goal.id);
        }
    } else {
        $('#goalForm')[0].reset();
        $('#goalModalTitle').text('Create New Goal');
        $('#goalAction').val('create');
        $('#goalId').val('');
    }
}

function closeModal() {
    $('.modal-overlay').removeClass('visible');
}

function saveGoal() {
    const formData = {
        action: $('#goalAction').val(),
        id: $('#goalId').val(),
        name: $('#goalName').val(),
        category: $('#goalCategory').val(),
        target: $('#goalTarget').val(),
        unit: $('#goalUnit').val(),
        deadline: $('#goalDeadline').val(),
        userID: userID
    };
    
    if (!formData.name || !formData.target || !formData.category) {
        GymSwal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all required fields'
        });
        return;
    }
    
    $.ajax({
        url: '/workout_trackersys/controllers/GoalController.php',
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function(response) {
            closeModal();
            loadGoals();
            GymSwal.fire({
                icon: 'success',
                title: 'Success!',
                text: formData.action === 'create' ? 'Goal created successfully!' : 'Goal updated successfully!'
            });
        },
        error: function() {
            // Demo mode - add locally
            if (formData.action === 'create') {
                currentGoals.push({
                    id: Date.now(),
                    ...formData,
                    current: 0,
                    color: getCategoryColor(formData.category)
                });
            } else {
                const idx = currentGoals.findIndex(g => g.id == formData.id);
                if (idx >= 0) {
                    currentGoals[idx] = { ...currentGoals[idx], ...formData };
                }
            }
            renderGoals(currentGoals);
            closeModal();
            GymSwal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Goal saved! (Demo Mode)'
            });
        }
    });
}

function updateGoalProgress(goalId) {
    const goal = currentGoals.find(g => g.id === goalId);
    if (!goal) return;
    
    GymSwal.fire({
        title: 'Update Progress',
        html: `
            <div style="text-align:left;margin-bottom:16px">
                <label style="font-size:12px;font-weight:600;color:var(--text-muted)">Current: ${goal.current} ${goal.unit}</label>
                <input type="number" id="newProgress" class="swal2-input" 
                    placeholder="Enter new value" value="${goal.current}" 
                    min="0" max="${goal.target}" step="0.1">
            </div>
        `,
        preConfirm: () => {
            const newValue = $('#newProgress').val();
            if (!newValue || newValue < 0) {
                Swal.showValidationMessage('Please enter a valid value');
                return false;
            }
            return newValue;
        },
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (result.isConfirmed) {
            const newValue = parseFloat(result.value);
            updateGoalValue(goalId, newValue);
        }
    });
}

function updateGoalValue(goalId, newValue) {
    const goal = currentGoals.find(g => g.id === goalId);
    if (!goal) return;
    
    $.ajax({
        url: '/workout_trackersys/controllers/GoalController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'updateProgress', id: goalId, current: newValue, userID: userID },
        success: function() {
            loadGoals();
            GymSwal.fire({ icon: 'success', title: 'Progress Updated!' });
        },
        error: function() {
            // Demo mode
            goal.current = newValue;
            renderGoals(currentGoals);
            GymSwal.fire({ icon: 'success', title: 'Progress Updated! (Demo)' });
        }
    });
}

function editGoal(goalId) {
    openGoalModal(goalId);
}

function deleteGoal(goalId) {
    GymSwal.fire({
        title: 'Delete Goal?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/workout_trackersys/controllers/GoalController.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'delete', id: goalId, userID: userID },
                success: function() {
                    loadGoals();
                    GymSwal.fire({ icon: 'success', title: 'Goal Deleted!' });
                },
                error: function() {
                    // Demo mode
                    currentGoals = currentGoals.filter(g => g.id !== goalId);
                    renderGoals(currentGoals);
                    GymSwal.fire({ icon: 'success', title: 'Goal Deleted! (Demo)' });
                }
            });
        }
    });
}

// Helper function for category colors
function getCategoryColor(category) {
    const colors = {
        strength: 'linear-gradient(45deg,#EF4444,#F97316)',
        weight: 'linear-gradient(45deg,#F59E0B,#EAB308)',
        endurance: 'linear-gradient(45deg,#22C55E,#10B981)',
        cardio: 'linear-gradient(45deg,#38BDF8,#0EA5E9)',
        flexibility: 'linear-gradient(45deg,#A855F7,#8B5CF6)'
    };
    return colors[category] || 'linear-gradient(45deg,#38BDF8,#3B82F6)';
}

// Tab switching (wired from HTML onclick, but implemented here)
function switchTab(tab) {
    $('.pg-tab').removeClass('active');
    $('.pg-tab[data-tab="' + tab + '"]').addClass('active');

    $('.pg-section').hide();
    $('#section-' + tab).fadeIn(300);

    if (tab === 'combined' && progressChart) {
        setTimeout(() => progressChart.resize(), 350);
    }
}

// Progress chart type switching
let currentChartType = 'weekly';
function switchProgressChart(type, btn) {
    currentChartType = type;
    $('.chart-tab').removeClass('active');
    if (btn) $(btn).addClass('active');

    // If backend doesn't provide monthly yet, fall back to weekly data we already have.
    if (type === 'weekly') {
        if (currentProgressData.weeklyActivity) renderProgressChart(currentProgressData.weeklyActivity);
        return;
    }

    // Try backend monthly if present; otherwise fall back.
    if (currentProgressData.monthlyActivity) {
        renderProgressChart(currentProgressData.monthlyActivity);
        return;
    }

    $.ajax({
        url: '/workout_trackersys/controllers/ProgressController.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'getProgressData', userID: userID, chartType: type },
        success: function(data) {
            if (data && data[type + 'Activity']) {
                currentProgressData = { ...currentProgressData, ...data };
                renderProgressChart(data[type + 'Activity']);
            } else if (data && data.weeklyActivity) {
                renderProgressChart(data.weeklyActivity);
            }
        },
        error: function() {
            if (currentProgressData.weeklyActivity) renderProgressChart(currentProgressData.weeklyActivity);
        }
    });
}

// Sidebar toggle
function ToggleSidebar() {
    document.getElementById('sidebar')?.classList.toggle('active');
}

// Logout function
function LogoutFunc() {
    GymSwal.fire({
        title: 'Leaving so soon?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Stay'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = '?page=login';
        }
    });
}

// Theme toggle handling
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    themeToggle.addEventListener('change', () => {
        const t = themeToggle.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', t);
        localStorage.setItem('theme', t);

        if (currentProgressData.weeklyActivity) {
            renderProgressChart(currentProgressData.weeklyActivity);
        }
    });
}

// Initialize theme from localStorage
const savedTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', savedTheme);
if (themeToggle) {
    themeToggle.checked = savedTheme === 'dark';
}


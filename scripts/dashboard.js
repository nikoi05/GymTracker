
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


let goalssnapshot =[];

async function fetchGoals(){
    try{
        const response = await fetch(`/workout_trackersys/api/get_goals.php`);
        if(!response.ok){
            throw new Error("API FAILED");
        }
        const data = await response.json();
        return data;
    }catch(err){
        console.error(err);
    }
}
async function initGoals(){
    goalssnapshot = await fetchGoals();
    renderGoals();
}
initGoals();

// rendering goals (Goals Snapshot)
function getGoalProgressPercent(g) {
    const status = (g?.status || '').toLowerCase();
    if (status === 'completed') return 100;

    const current = Number(g?.current_value ?? 0);
    const target = Number(g?.target_value ?? 0);

    if (!target || target <= 0) return 0;
    const pct = Math.round((current / target) * 100);
    return Math.max(0, Math.min(100, pct));
}

function renderGoals(){
    const container = document.getElementById('goalSnapshotList');
    if(!container) return;

    // If redirectUser is not available, avoid runtime errors
    if (typeof window.redirectUser !== 'function') {
        console.warn('redirectUser() is not available; snapshot action will be disabled.');
    }


    if(!Array.isArray(goalssnapshot) || goalssnapshot.length === 0){
        container.innerHTML = `<div class="Loading-state" style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px">No goals yet</div>`;
        return;
    }

    // Keep it compact for the dashboard card
    const maxItems = 3;
    const items = goalssnapshot.slice(0, maxItems);


    container.innerHTML = '';

    items.forEach((g, i) => {
        const pct = getGoalProgressPercent(g);

        // Map status to CSS classes used by dashboard styles we will add
        const status = (g.status || 'not-started').toLowerCase();
        const statusClass =
            status === 'completed' ? 'status-completed' :
            status === 'in-progress' ? 'status-in-progress' :
            'status-not-started';

        const deadlineStr = g.deadline
            ? new Date(g.deadline).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'})
            : '—';

        // Fallbacks to avoid breaking markup if API returns unexpected fields
        const name = (g.name ?? 'Untitled goal');
        const desc = (g.description ?? '').trim();
        const category = (g.category ?? '').trim();

        const badgeText = (g.status || 'not-started').replace('-', ' ');
        const emoji = category ? category.split(' ')[0] : '🎯';

        const card = document.createElement('div');
        card.className = 'goal-snapshot-card';
        card.style.animationDelay = (i * 0.05) + 's';

        card.innerHTML = `
            <div class="goal-snapshot-top">
                <div class="goal-snapshot-emoji">${emoji}</div>
                <span class="goal-snapshot-status-badge ${statusClass}">${badgeText}</span>
            </div>

            <div class="goal-snapshot-name" title="${String(name).replace(/"/g,'"')}">${name}</div>

            ${desc ? `<div class="goal-snapshot-desc">${desc}</div>` : ''}

            <div class="goal-snapshot-meta">
                <span class="goal-snapshot-category">${category || 'Goal'}</span>
                <span class="goal-snapshot-pct">${pct}%</span>
            </div>

            <div class="goal-snapshot-bar-bg">
                <div class="goal-snapshot-bar-fill" style="width:0%" data-target="${pct}%"></div>
            </div>

            <div class="goal-snapshot-footer">
                <span class="goal-snapshot-deadline"><span class="material-icons">event</span> ${deadlineStr}</span>
                <button class="goal-snapshot-view" type="button" data-goal-id="${g.goal_id}">
                    <span class="material-icons">open_in_new</span>
                </button>
            </div>
        `;

        // When clicking the small button, go to the Goals page (same as View All)
        const btn = card.querySelector('.goal-snapshot-view');
        if(btn){
            btn.addEventListener('click', () => redirectUser(5));
        }

        container.appendChild(card);
    });

    // Animate bars
    setTimeout(() => {
        container.querySelectorAll('.goal-snapshot-bar-fill').forEach(bar => {
            bar.style.width = bar.dataset.target;
        });
    }, 150);

}
//fetching chart data and rendering
let charts = {};

async function getChart(canvasId, type = 'weekly') {

    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    try {
        const response = await fetch(`/workout_trackersys/api/get_activity_data.php?type=${type}`);
        if (!response.ok) throw new Error('API failed');

        const data = await response.json();

        // destroy previous instance
        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }

        // get your theme variables
        const root = getComputedStyle(document.documentElement);

        const accent = root.getPropertyValue('--accent').trim();
        const accentSky = root.getPropertyValue('--accent-sky').trim();
        const accentLight = root.getPropertyValue('--accent-light').trim();
        const border = root.getPropertyValue('--border').trim();
        const textMuted = root.getPropertyValue('--text-muted').trim();

        const ctx = canvas.getContext('2d');

        charts[canvasId] = new Chart(ctx, {
            type: 'line',

            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Workout Duration Minutes',

                    data: data.values || [],

                    // 🎨 THEME MATCHED COLORS
                    borderColor: accent,
                    backgroundColor: accentLight,

                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,

                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: accent,
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: accent,
                    pointHoverBorderColor: '#FFFFFF'
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                plugins: {
                    legend: { display: false },

                    tooltip: {
                        backgroundColor: 'rgba(2, 0, 36, 0.9)', 
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 10,
                        cornerRadius: 12,
                        borderColor: accent,
                        borderWidth: 1
                    }
                },

                scales: {

                    x: {
                        grid: {
                            color: border,
                            drawBorder: false
                        },
                        ticks: {
                            color: textMuted,
                            font: {
                                family: 'Poppins'
                            }
                        }
                    },

                    y: {
                        beginAtZero: true,
                        grid: {
                            color: border,
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            color: textMuted,
                            font: {
                                family: 'Poppins'
                            }
                        }
                    }
                },

                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },

                elements: {
                    line: {
                        borderJoinStyle: 'round'
                    },
                    point: {
                        hoverBorderWidth: 3
                    }
                }
            }
        });

    } catch (err) {
        console.error(`Chart ${canvasId} failed:`, err);
    }
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
        console.errpr(err);
    }
}
async function initStreak(){
    Streak = await getStreak();

    //render the streak
    
document.getElementById('statPR').innerText = Streak;

}


initStreak();

// Fetch and render Recent Workouts

async function loadRecentActivities(){
    const container = document.getElementById('recentWorkoutList'); // get the container of the recentworkoutList
    container.innerHTML = `<div class=loading-State> Loading...</div>`;
    try{
        const response = await fetch(`/workout_trackersys/api/get_recent_workouts.php`);
    if(!response.ok){
        throw new Error("API FAILED");
    }
    const data = await response.json();
    if(!data.length){
        container.innerHTML =`<div class=loading-State> No Workouts done yet</div>`;
        return;
    }
    container.innerHTML="";
    data.forEach( w=> {
        const div = document.createElement("div");
        div.classList.add("workout-item");
        div.innerHTML =`<div class="workout-left">
                            <div class="workout-name">${w.WorkoutName}</div>
                            <div class = "workout-date">${w.WorkoutDate}</div>
                              <div class="workout-meta">
                        ${w.formattedDuration} • ${w.sets_done} sets
                    </div>
                    </div>
                `;
                container.appendChild(div);
    });

    }catch(err){
        console.error(err);
    };
}

loadRecentActivities();
document.addEventListener('DOMContentLoaded', () =>{
    //init chart
    getChart('activityChart','weekly');
    // switching charts

    document.querySelectorAll('.chart-tab').forEach(tab =>{
        tab.addEventListener('click',function(){ // listens
            const parent =this.closest('.card'); // gets the parent div
            if(parent.querySelector('#activityChart')){
                parent.querySelector('.chart-tab.active').classList.remove('active'); // when button is clicked
                this.classList.add('active');
                getChart('activityChart',this.innerText.toLowerCase()); // gets the chart 
            }

        });
    });
});

/* ============================================================
   LOGOUT FUNCTION
============================================================ */
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

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
function ToggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }


/* ============================================================
   LOAD ALL DATA
============================================================ */
let goals = [], weeklyFreq = [], monthlyFreq = []; let fetchedGoals =[]; let stats = [];



//fetch Goals
async function getGoals(){
    try{
        const response = await fetch(`/workout_trackersys/api/get_goals.php`);
        const data = await response.json();
        return data;
    }catch(err){
        console.error(err);
    }
}
 async function initGoals(){
    fetchedGoals = await getGoals();
    console.log(fetchedGoals);
    loadAll();
}
initGoals();
function loadAll() {
    renderGoals();
    initStats();
    renderPBs([
        { icon:'🏋️‍♀️', name:'Bench Press', val:'100kg', date:'Apr 10, 2026', isNew:true },
        { icon:'🤎',    name:'Squat',        val:'140kg', date:'Apr 18, 2026', isNew:false },
        { icon:'💪',    name:'Pull-Ups',     val:'18 reps', date:'Apr 20, 2026', isNew:true },
        { icon:'🏋️‍♂️', name:'Deadlift',     val:'180kg', date:'Mar 28, 2026', isNew:false },
        { icon:'🏃‍♂️', name:'5km Run',      val:'24:30',  date:'Apr 5, 2026',  isNew:false },
    ]);

    weeklyFreq  = [1,2,0,2,1,3,2];
    monthlyFreq = [10,14,12,18,16,20,17,22,19,25,23,28];

    buildFreqChart(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], weeklyFreq);
    buildVolumeChart([2400,2800,3100,2700,3400,3200,3800,4100,3900,4500,4300,4800]);
}

//get the stats
async function FetchStats() {
    try{
        const response = await fetch('/workout_trackersys/api/get_goals_stats.php');
        if(!response.ok){
            throw new Error('Network response was not ok');
            return;
        }
        const data = await response.json();
        return data;
    
    }catch(err){
        console.error(err);
    }  
}
async function initStats() {
    const data = await FetchStats();
    if (data && data.length > 0) {
        stats = data[0]; 
        console.log(stats);
        renderStats(stats);
    }
}
/* ── Stats ── */
function renderStats(s) {
    document.getElementById('statActive').textContent = s.ActiveGoals?? '—';
    document.getElementById('statCompleted').textContent = s.CompletedGoals ?? '—';
    document.getElementById('statWorkout').textContent=s.Workout ?? '—';
    document.getElementById('statAvgProgress').textContent = (s.AvgProgress || s.AvgProgress === 0) ? (s.AvgProgress + '%') : '—';

}




/* ============================================================
   GOALS
============================================================ */

function getPercent(g) {
    if (g.status === 'completed') return 100;
    if (!g.target_value || g.target_value === 0) return 0;
    return Math.min(100, Math.round((g.current_value / g.target_value) * 100));
}

function renderGoals() {
    const grid = document.getElementById('goalsGrid');
    // COLORS FOR THE PROGRESSS BARS
const colors = [
    'linear-gradient(45deg,#38BDF8,#3B82F6)', // blue
    'linear-gradient(45deg,#F59E0B,#EF4444)', // orange-red
    'linear-gradient(45deg,#22C55E,#38BDF8)', // green-blue
    'linear-gradient(45deg,#6366F1,#8B5CF6)', // indigo-purple
    'linear-gradient(45deg,#F97316,#EF4444)', // orange-red

    'linear-gradient(45deg,#EC4899,#8B5CF6)', // pink-purple
    'linear-gradient(45deg,#14B8A6,#06B6D4)', // teal-cyan
    'linear-gradient(45deg,#84CC16,#22C55E)', // lime-green
    'linear-gradient(45deg,#F43F5E,#EC4899)', // rose-pink
    'linear-gradient(45deg,#0EA5E9,#6366F1)', // sky-indigo

    'linear-gradient(45deg,#A855F7,#EC4899)', // violet-pink
    'linear-gradient(45deg,#10B981,#059669)', // emerald
    'linear-gradient(45deg,#FACC15,#F97316)', // yellow-orange
    'linear-gradient(45deg,#06B6D4,#3B82F6)', // cyan-blue
    'linear-gradient(45deg,#EF4444,#DC2626)', // red

    'linear-gradient(45deg,#8B5CF6,#3B82F6)', // purple-blue
    'linear-gradient(45deg,#E879F9,#C084FC)', // soft violet
    'linear-gradient(45deg,#FB7185,#F43F5E)', // soft rose
    'linear-gradient(45deg,#2DD4BF,#14B8A6)', // aqua-teal
    'linear-gradient(45deg,#4ADE80,#22C55E)'  // fresh green
];

    // Update stat cards
    document.getElementById('statActive').textContent    = fetchedGoals.filter(g=>g.status!=='completed').length;
    document.getElementById('statCompleted').textContent = fetchedGoals.filter(g=>g.status==='completed').length;
    const total = fetchedGoals.length;
    const avgPct = total ? Math.round(fetchedGoals.reduce((s,g)=>s+getPercent(g),0)/total) : 0;
    document.getElementById('statAvgProgress').textContent = avgPct + '%';

    if (!fetchedGoals.length) {
        grid.innerHTML = `
            <div class="empty-goals" style="grid-column:1/-1">
                <div class="material-icons">flag</div>
                <div>No goals yet.</div>
                <div style="margin-top:6px;font-size:13px">Click <strong>Add New Goal</strong> to set your first target!</div>
            </div>`;
        return;
    }

    grid.innerHTML = '';
    fetchedGoals.forEach((g, i) => {
        const pct = getPercent(g);
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        const card = document.createElement('div');
        card.className = 'goal-card';
        card.style.animationDelay = (i * 0.06) + 's';
        card.style.animation = 'fadeUp 0.45s ease both';
        const deadlineStr = g.deadline ? new Date(g.deadline).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—';
        card.innerHTML = `
            <div class="goal-card-top">
                <div class="goal-emoji">${g.category ? g.category.split(' ')[0] : '🎯'}</div>
                <span class="goal-status-badge status-${g.status}">${g.status.replace('-',' ')}</span>
            </div>
            <div class="goal-name">${g.name}</div>
            <div class="goal-desc">${g.description || ''}</div>
            <div class="goal-progress-meta">
                <span class="goal-progress-label">${g.category || 'Goal'}</span>
                <span class="goal-progress-val">${pct}%</span>
            </div>
            <div class="goal-bar-bg">
                <div class="goal-bar-fill" style="width:0%;background:${randomColor||'var(--accent-grad)'}" data-target="${pct}%"></div>
            </div>
            <div class="goal-values">
                <span>Current: <strong style="color:var(--text)">${g.current_value}</strong></span>
                <span>Target: ${g.target_value}</span>
            </div>
            <div class="goal-deadline"><span class="material-icons">event</span> Deadline: ${deadlineStr}</div>
            <div class="goal-actions">
                <button class="goal-action-btn" onclick="openUpdateGoal(${g.goal_id})"><span class="material-icons">edit</span> Update</button>
                <button class="goal-action-btn" onclick="markComplete(${g.goal_id})"><span class="material-icons">check_circle</span> Complete</button>
                <button class="goal-action-btn del" onclick="deleteGoal(${g.goal_id})"><span class="material-icons">delete</span></button>
            </div>`;
        grid.appendChild(card);
    });

    // Animate bars
    setTimeout(() => document.querySelectorAll('.goal-bar-fill').forEach(b => b.style.width = b.dataset.target), 200);
}

/* ── Mark Complete ── */
function markComplete(id) {
    const g = fetchedGoals.find(x => x.goal_id === id); if(!g) return;
     const goalID = id;
    if (g.status === 'completed') { GymToast.fire({ icon:'info', title:'Already completed!' }); return; }

    GymSwal.fire({ title:`Complete "${g.name}"?`, text:'Mark this goal as achieved! 🎉', icon:'question', showCancelButton:true, confirmButtonText:'Yes, complete!', cancelButtonText:'Cancel' })
    .then(r => {
        if (!r.isConfirmed) return;
        g.status  = 'completed';
        g.current_value = g.target_value;
        $.ajax({
            type:"POST",
            url:"/workout_trackersys/controllers/GoalController.php",
            data:{
                action:"MarkGoal",
                goal_id:goalID
            }, success:function(result){
                if(result.trim().toLowerCase()==="success"){
                    GymSwal.fire({
                        icon:"success",
                        title:"Goal Saved!",
                        text:"Your goal has been updated.",
                        timer:1500,
                        showConfirmButton:false
                    });

            }else{
                GymSwal.fire({
                    icon:"error",
                    title:"Something went wrong",
                    text:result
                });
            }
            }
        });
        renderGoals();
        GymSwal.fire({ icon:'success', title:'Goal completed! 🎉', text:`"${g.name}" — you crushed it!`, timer:2500, showConfirmButton:false });
    });
}
function deleteGoal(id) {

    const g = fetchedGoals.find(x => x.goal_id === id);

    if (!g) return;

    GymSwal.fire({
        title: `Delete "${g.name}"?`,
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    })

    .then(r => {

        if (!r.isConfirmed) return;

        $.ajax({
            type: "POST",
            url: "/workout_trackersys/controllers/GoalController.php",
            data: {
                action: "DeleteGoal",
                goal_id: id
            },

            success: function(result) {

                if (result.trim().toLowerCase() === "success") {

                    fetchedGoals = fetchedGoals.filter(x => x.goal_id !== id);

                    renderGoals();

                    GymToast.fire({
                        icon: 'success',
                        title: 'Goal deleted.'
                    });

                } else {

                    GymSwal.fire({
                        icon: 'error',
                        title: 'Delete failed',
                        text: result
                    });
                }
            },

            error: function(xhr, status, error) {

                GymSwal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: error
                });
            }
        });

    });
}

/* ── Modal ── */
let isEditing = false, editingId = null;

function openAddGoal() {
    isEditing = false; editingId = null;
    document.getElementById('modalTitle').textContent = 'Add New Goal';
    document.getElementById('goalEditId').value   = '';
    document.getElementById('goalName').value     = '';
    document.getElementById('goalDesc').value     = '';
    document.getElementById('goalCategory').value = '💪 Strength';
    document.getElementById('goalDeadline').value = '';
    document.getElementById('goalCurrent').value  = '';
    document.getElementById('goalTarget').value   = '';
    document.getElementById('goalModal').classList.add('visible');
}

function openUpdateGoal(id) {
    const g = fetchedGoals.find(x => x.goal_id === id); if(!g) return;
    isEditing = true; editingId = id;
    document.getElementById('modalTitle').textContent    = 'Update Goal';
    document.getElementById('goalEditId').value          = id;
    document.getElementById('goalName').value            = g.name;
    document.getElementById('goalDesc').value            = g.description || '';
    document.getElementById('goalCategory').value        = g.category || '💪 Strength';
    document.getElementById('goalDeadline').value        = g.deadline || '';
    document.getElementById('goalCurrent').value         = g.current_value;
    document.getElementById('goalTarget').value          = g.target_value;
    document.getElementById('goalModal').classList.add('visible');
}

function closeGoalModal() { document.getElementById('goalModal').classList.remove('visible'); }
function closeModalOutside(e) { if(e.target === document.getElementById('goalModal')) closeGoalModal(); }

function saveGoal() {
    const name     = document.getElementById('goalName').value.trim();
    const desc     = document.getElementById('goalDesc').value.trim();
    const category = document.getElementById('goalCategory').value;
    const deadline = document.getElementById('goalDeadline').value;
    const current  = parseFloat(document.getElementById('goalCurrent').value) || 0;
    const target   = parseFloat(document.getElementById('goalTarget').value)  || 100;
    const today = new Date()
    today.setHours(0,0,0,0);
    const deadlinecheck = new Date(deadline);


    if (!name)     { GymSwal.fire({ icon:'warning', title:'Please enter a goal name.' }); return; }
    if (!deadline) { GymSwal.fire({ icon:'warning', title:'Please set a deadline.' });    return; }
    if(deadlinecheck <today){
        GymSwal.fire({ icon :'warning',title:'Please set a Valid Deadline.'}); return
    }
    if (isEditing) {
        if(current ==0){
        updateGoal(editingId,name,desc,category,current,target,deadline,status="not-started");

        }else if(current < target && current >0){
            updateGoal(editingId,name,desc,category,current,target,deadline,status="in-progress");
        }else{
             updateGoal(editingId,name,desc,category,current,target,deadline,status="completed");
        }
       
    } else {
        isEditing=false;
        if(current ==0){
          addGoal(name,desc,category,current,target,deadline,status="not-started");

        }else if(current < target && current >0){
              addGoal(name,desc,category,current,target,deadline,status="in-progress");
        }else{
             addGoal(name,desc,category,current,target,deadline,status="completed");
        }
      
    }
    closeGoalModal();
    renderGoals();
    GymToast.fire({ icon:'success', title: isEditing ? 'Goal updated!' : 'Goal added! 🎯' });
}
/* ── Update goal to backend ── */
function updateGoal(editingid,name,desc,category,current,target,deadline,status){
    const goalid = editingid;
    const goalname = name;
    const goaldesc = desc;
     const goalcategory = category;
     const goalcurrent = current;
     const goaltarget = target;
     const goaldeadline = deadline;
     const goalstatus = status;
      $.ajax({
    type: "POST",
    url: "/workout_trackersys/controllers/GoalController.php",
    data: {
        action: "UpdateGoal",
        goal_id: goalid,
        name: goalname,
        desc: goaldesc,
        category: goalcategory,
        current: goalcurrent,
        target: goaltarget,
        deadline: goaldeadline,
        status: goalstatus,
    },
    success: function (result) {

        if (result.trim().toLowerCase() === "success") {

            GymSwal.fire({
                icon: "success",
                title: "Goal Saved!",
                text: isEditing ? "Your goal has been updated." : "New goal has been added.",
                timer: 1500,
                showConfirmButton:true
            });
            setInterval(()=>{
            
            },1500);
            renderGoals();
        } else {

            GymSwal.fire({
                icon: "error",
                title: "Something went wrong",
                text: result
            });

        }
    },
    error: function (xhr, status, error) {

        GymSwal.fire({
            icon: "error",
            title: "Server Error",
            text: error
        });

    }
});
renderGoals();   
}
/* ── Save goal to backend ── */
function addGoal(name, desc, category, current, target, deadline,status) {
     const goalname = name;
     const goaldesc = desc;
     const goalcategory = category;
     const goalcurrent = current;
     const goaltarget = target;
     const goaldeadline = deadline;
     const goalstatus = status;

  $.ajax({
    type: "POST",
    url: "/workout_trackersys/controllers/GoalController.php",
    data: {
        action: "AddGoal",
        name: goalname,
        desc: goaldesc,
        category: goalcategory,
        current: goalcurrent,
        target: goaltarget,
        deadline: goaldeadline,
        status: goalstatus,
    },
    success: function (result) {

        if (result.trim().toLowerCase() === "success") {

            GymSwal.fire({
                icon: "success",
                title: "Goal Saved!",
                text: isEditing ? "Your goal has been updated." : "New goal has been added.",
                timer: 1500,
                showConfirmButton: false
            });
            setInterval(()=>{
                location.reload(result);
            },1500);
            loadGoals();
            renderGoals(); // or renderGoals()

        } else {

            GymSwal.fire({
                icon: "error",
                title: "Something went wrong",
                text: result
            });

        }
    },
    error: function (xhr, status, error) {

        GymSwal.fire({
            icon: "error",
            title: "Server Error",
            text: error
        });

    }
});
    

}


/* ============================================================
   PERSONAL BESTS
============================================================ */
function renderPBs(pbs) {
    const list = document.getElementById('pbList');
    if (!pbs.length) { list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">No personal bests logged yet.</div>'; return; }
    list.innerHTML = pbs.map(pb => `
        <div class="pb-item">
            <div class="pb-icon">${pb.icon}</div>
            <div class="pb-info"><div class="pb-name">${pb.name}</div><div class="pb-date">${pb.date}</div></div>
            <div class="pb-val${pb.isNew?' new':''}">${pb.val} ${pb.isNew?'🆕':''}</div>
        </div>`).join('');
}

/* ============================================================
   CHARTS
============================================================ */
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
        data: { labels, datasets: [{ label:'Workouts', data, backgroundColor:'rgba(59,130,246,0.18)', borderColor:'#3B82F6', borderWidth:2, borderRadius:8, borderSkipped:false }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ x:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 } } }, y:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 }, stepSize:1 }, beginAtZero:true } } }
    });
}

function buildVolumeChart(data) {
    const ctx = document.getElementById('volumeChart').getContext('2d');
    if (volumeChart) volumeChart.destroy();
    const { gc, tc } = getChartColors();
    const labels = ['W1','W2','W3','W4','W5','W6','W7','W8','W9','W10','W11','W12'];
    volumeChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label:'Volume (kg)', data, borderColor:'#38BDF8', backgroundColor:'rgba(56,189,248,0.08)', tension:0.4, fill:true, pointBackgroundColor:'#38BDF8', pointRadius:4, borderWidth:2.5 }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ x:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 } } }, y:{ grid:{ color:gc }, ticks:{ color:tc, font:{ family:'Poppins', size:11 } }, beginAtZero:false } } }
    });
}

function switchFreqChart(type, btn) {
    document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const labels = type === 'weekly' ? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    buildFreqChart(labels, type === 'weekly' ? weeklyFreq : monthlyFreq);
}

function updateChartColors(chart) {
    const { gc, tc } = getChartColors();
    ['x','y'].forEach(ax => { chart.options.scales[ax].grid.color = gc; chart.options.scales[ax].ticks.color = tc; });
    chart.update();
}

/* ============================================================
   HEATMAP
============================================================ */
function buildHeatmap() {
    // JS-only sample data (no backend)
    const levels = [0,0,1,0,2,0,1,3,0,2,1,0,4,2,0,1,3,0,2,1,0,1,0,2,3,1,0,0,2,1,4,0,1,2,0,3,1,0,2,0,1,3,2,0,1,0,2,1,3,0,4,1,2,0,1,3,0,2,1,0,4,1,2,3,0,1,2,0,3,1,4,0,2,1,3,0,1,2,4,0,1,3,2,1,0,2,3,1,0,4,2];
    renderHeatmap(levels);
}


function renderHeatmap(levels) {
    const grid = document.getElementById('heatmapGrid');
    grid.innerHTML = '';
    for (let i = 0; i < 91; i++) {
        const cell = document.createElement('div');
        const lvl  = levels[i % levels.length] || 0;
        cell.className = `heatmap-cell${lvl > 0 ? ' level-' + Math.min(lvl,4) : ''}`;
        cell.setAttribute('data-tip', lvl === 0 ? 'No workout' : `${lvl} workout${lvl > 1 ? 's' : ''}`);
        grid.appendChild(cell);
    }
}

loadAll();

function LogoutFunc() {
    GymSwal.fire({ title:'Leaving so soon?', icon:'question', showCancelButton:true, confirmButtonText:'Yes, log out', cancelButtonText:'Stay' })
    .then(r => { if(r.isConfirmed) $.ajax({ url:'/workout_trackersys/controllers/logout.php', type:'POST', success:()=>window.location.href='?page=login' }); });
}
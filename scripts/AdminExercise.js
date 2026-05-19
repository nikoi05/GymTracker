
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

/* ==============================================
   CHART INIT
============================================== */

/* get charts data */

let muscleRows;
let difficultyRows;
let defaultTypeId;

async function getChartsData(){
    try{
        const response = await fetch(`/workout_trackersys/api/get_exercise_admin_chart.php`);
        if(!response.ok){
            throw new Error("Failed to fetch charts data");
        }
        return response.json();
    }catch(err){
        console.error(err);
    }
}

// init data
async function initCharts(){
    const data = await getChartsData();

    muscleRows = data.muscles;
    difficultyRows = data.difficulty;
    console.log("MuscleRows",muscleRows);
    console.log(" DifficultuRows",difficultyRows);
    //render thy chart
    makeChart('muscleChart',     muscleRows,     'bar');
    makeChart('difficultyChart', difficultyRows, 'doughnut');

}




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


// make thy char
function makeChart(id, rows, type = 'bar') {
    const ctx  = document.getElementById(id);
    if (!ctx) return;
    const root   = getComputedStyle(document.documentElement);
    const colors = ['#3B82F6','#38BDF8','#22C55E','#F59E0B','#EF4444','#A855F7','#64748B','#F97316'];
    new Chart(ctx, {
        type,
        data: {
            labels:   rows.map(r => r.label),
            datasets: [{
                data:            rows.map(r => Number(r.total)),
                backgroundColor: colors,
                borderRadius:    type === 'bar' ? 6 : 0,
                borderWidth:     type === 'bar' ? 0 : 2,
                borderColor:     '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display:  type !== 'bar',
                    position: 'right',
                    labels: { color: root.getPropertyValue('--text').trim(), font: { family: 'Poppins', size: 12 } }
                }
            },
            scales: type === 'bar' ? {
                y: { beginAtZero: true, ticks: { color: root.getPropertyValue('--text-muted').trim() }, grid: { color: root.getPropertyValue('--border').trim() } },
                x: { ticks: { color: root.getPropertyValue('--text-muted').trim() }, grid: { display: false } }
            } : {}
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
   initCharts();
});

/* ==============================================
   CLIENT-SIDE TABLE FILTER
============================================== */
function filterTable() {
    const q    = document.getElementById('exSearch').value.toLowerCase();
    const mus  = document.getElementById('exMuscleFilter').value.toLowerCase();
    const diff = document.getElementById('exDiffFilter').value.toLowerCase();

    document.querySelectorAll('#exerciseTableBody tr').forEach(row => {
        const name  = row.dataset.name.toLowerCase() || '';
        const muscle= row.dataset.muscle.toLowerCase()|| '';
        const drow  = row.dataset.diff.toLowerCase() || '';
        const equip = row.dataset.equip.toLowerCase() || '';
        const locat = row.dataset.locat.toLowerCase() || '';


        const matchQ   = !q    || name.includes(q)   || muscle.includes(q) || equip.includes(q) || locat.includes(q);
        const matchM   = !mus  || muscle === mus;
        const matchD   = !diff || drow   === diff;

        row.style.display = (matchQ && matchM && matchD) ? '' : 'none';
    });
}

/* ==============================================
   TAG STATE (separate for add / edit)
============================================== */
const tagState = { add: [], edit: [] };

function renderTags(mode) {
    const wrap = document.getElementById(mode === 'add' ? 'addTagsWrap' : 'editTagsWrap');
    wrap.innerHTML = tagState[mode].map((t, i) => `
        <span class="tag-chip">${escHtml(t)}
            <button type="button" onclick="removeTag('${mode}',${i})">&times;</button>
        </span>`).join('');
}

function addTag(mode) {
    const input = document.getElementById(mode === 'add' ? 'addTagInput' : 'editTagInput');
    const val   = input.value.trim();
    if (val && !tagState[mode].includes(val)) {
        tagState[mode].push(val);
        renderTags(mode);
    }
    input.value = '';
    input.focus();
}

function removeTag(mode, i) {
    tagState[mode].splice(i, 1);
    renderTags(mode);
}

/* ==============================================
   STEPS BUILDER (shared, pass list ID)
============================================== */
function renderSteps(listId, steps = []) {
    const list = document.getElementById(listId);
    list.innerHTML = '';
    (steps.length ? steps : ['']).forEach((s, i) => addStepRowTo(list, s, i + 1));
}

function addStep(listId) {
    const list = document.getElementById(listId);
    addStepRowTo(list, '', list.children.length + 1);
}

function addStepRowTo(list, val, num) {
    const row = document.createElement('div');
    row.className = 'step-row';
    row.innerHTML = `
        <div class="step-num">${num}</div>
        <input type="text" class="step-input" placeholder="Step ${num}..." value="${escHtml(val)}">
        <button class="step-del" type="button" onclick="removeStepRow(this)">
            <i class="material-icons">close</i>
        </button>`;
    list.appendChild(row);
}

function removeStepRow(btn) {
    const list = btn.closest('.steps-list');
    btn.closest('.step-row').remove();
    list.querySelectorAll('.step-num').forEach((b, i) => b.textContent = i + 1);
}

function getStepsFrom(listId) {
    return [...document.querySelectorAll(`#${listId} .step-input`)]
        .map(i => i.value.trim()).filter(Boolean);
}

/* ==============================================
   OPEN / CLOSE MODALS
============================================== */
function openAddExModal() {
    // Reset all fields
    ['add_name','add_icon','add_equipment','add_desc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('add_muscle').value     = 'chest';
    document.getElementById('add_type').value       = defaultTypeId;
    document.getElementById('add_difficulty').value = 'Beginner';
    document.getElementById('add_location').value   = 'Gym';
    tagState.add = [];
    renderTags('add');
    renderSteps('addStepsList', ['']);
    document.getElementById('AddExModal').classList.add('active');
}

function openEditExModal(ex) {
    document.getElementById('edit_exerciseID').value = ex.exerciseID;
    document.getElementById('edit_name').value       = ex.name       || '';
    document.getElementById('edit_icon').value       = ex.icon       || '';
    document.getElementById('edit_muscle').value     = ex.muscle;
    document.getElementById('edit_type').value       = ex.typeID;
    document.getElementById('edit_difficulty').value = ex.difficultyID;
    document.getElementById('edit_location').value   = ex.LocationID;
    document.getElementById('edit_equipment').value  = ex.equipment  || '';
    document.getElementById('edit_desc').value       = ex.desc       || '';

    // Parse tags from comma string
    tagState.edit = ex.tags ? ex.tags.split(',').map(t => t.trim()).filter(Boolean) : [];
    renderTags('edit');

    // Load steps from DB via AJAX (tbl_exercise_step ordered by stepOrder)
    $.ajax({
        url:      '/workout_trackersys/controllers/AdminExerciseController.php',
        type:     'POST',
        dataType: 'json',
        data:     { action: 'getSteps', exerciseID: ex.exerciseID },
        success:  function(data) {
            const steps = Array.isArray(data) ? data.map(s => s.step_desc) : [];
            renderSteps('editStepsList', steps.length ? steps : ['']);
        },
        error: function() { renderSteps('editStepsList', ['']); }
    });

    document.getElementById('EditExModal').classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Close on backdrop click
['AddExModal','EditExModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

/* ==============================================
   SAVE EXERCISE (ADD / EDIT)
   Sends to AdminExerciseController.php
   which handles tbl_exercises, tbl_exercise_step, tbl_exercise_tags
============================================== */
function saveExercise(mode) {
    const isEdit = mode === 'edit';
    const prefix = isEdit ? 'edit' : 'add';

    const name  = document.getElementById(`${prefix}_name`).value.trim();
    const steps = getStepsFrom(isEdit ? 'editStepsList' : 'addStepsList');

    if (!name)         { GymSwal.fire({ icon:'warning', title:'Exercise name is required.' }); return; }
    if (!steps.length) { GymSwal.fire({ icon:'warning', title:'Add at least one step.' });     return; }

    const iconText = document.getElementById(`${prefix}_icon`).value.trim();
    const iconFileInput = document.getElementById(`${prefix}_icon_file`);
    const iconFile = iconFileInput && iconFileInput.files && iconFileInput.files.length ? iconFileInput.files[0] : null;

    const formData = new FormData();
    formData.append('action', isEdit ? 'edit' : 'add');
    formData.append('id', isEdit ? document.getElementById('edit_exerciseID').value : '');
    formData.append('name', name);
    formData.append('icon', iconText); // fallback if no file uploaded
    formData.append('muscle', document.getElementById(`${prefix}_muscle`).value);
    formData.append('typeID', document.getElementById(`${prefix}_type`).value);
    formData.append('difficulty', document.getElementById(`${prefix}_difficulty`).value);
    formData.append('location', document.getElementById(`${prefix}_location`).value);
    formData.append('equipment', document.getElementById(`${prefix}_equipment`).value.trim());
    formData.append('desc', document.getElementById(`${prefix}_desc`).value.trim());
    formData.append('steps', JSON.stringify(steps));
    formData.append('tags', tagState[mode].join(','));

    if (iconFile) {
        formData.append('icon_file', iconFile);
    }

    $.ajax({
        url:      '/workout_trackersys/controllers/AdminExerciseController.php',
        type:     'POST',
        dataType: 'json',
        data:     formData,
        processData: false,
        contentType: false,
        success: function(d) {
            if (d.error) { GymSwal.fire({ icon:'error', title:'Error', text:d.error }); return; }
            closeModal(isEdit ? 'EditExModal' : 'AddExModal');
            GymToast.fire({ icon:'success', title: isEdit ? 'Exercise updated!' : 'Exercise added!' });
            setTimeout(() => location.reload(), 1400);
        },
        error: function(xhr) {
            GymSwal.fire({ icon:'error', title:'Server Error', text:'Status ' + xhr.status });
        }
    });
}

/* ==============================================
   DELETE EXERCISE
   Removes from tbl_exercises + cascades steps & tags
============================================== */
function deleteExercise(id, name) {
    GymSwal.fire({
        icon:              'warning',
        title:             `Delete "${name}"?`,
        text:              'This permanently removes the exercise, all its steps and tags.',
        showCancelButton:  true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText:  'Cancel'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.ajax({
            url:      '/workout_trackersys/controllers/AdminExerciseController.php',
            type:     'POST',
            dataType: 'json',
            data:     { action: 'delete', id },
            success: function(d) {
                if (d.error) { GymSwal.fire({ icon:'error', title:'Error', text:d.error }); return; }
                GymToast.fire({ icon:'success', title:'Exercise deleted!' });
                setTimeout(() => location.reload(), 1400);
            },
            error: function(xhr) {
                GymSwal.fire({ icon:'error', title:'Server Error', text:'Status ' + xhr.status });
            }
        });
    });
}

/* -- HTML escape helper -- */
function escHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
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

/* STAT CARD RENDERISM */

async function fetchStats() {
    try{
        const response = await fetch(`/workout_trackersys/api/get_exercise_admin_stats.php`);
        if(!response.ok){
            throw new Error("Failed to fetch stats");
        }
        return response.json();
    
    }catch(err){
            console.error(err);
            GymToast.fire({ icon: 'error', title: 'Failed to load goals data.' });
    }

}
    let stats = [];
    async function renderStats() {
        stats = await fetchStats();
        if (!stats) return;

        const statcards = document.querySelectorAll('.stat-card');
        // Convert the object values into an array to use the numeric index
        const dataValues = Object.values(stats);

        statcards.forEach((card, index)=>{
            const statvalue = card.querySelector('.stat-value');
            if(statvalue && dataValues[index] !== undefined){
                statvalue.textContent = dataValues[index];
            }
        
        });
    }
    renderStats();

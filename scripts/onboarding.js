const GymSwal = window.GymSwal || Swal.mixin({
    customClass: { container:'swal-on-top', popup:'gym-swal-popup', title:'gym-swal-title', confirmButton:'gym-swal-confirm', cancelButton:'gym-swal-cancel' },
    buttonsStyling: false
});

document.getElementById('onboardingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const height = $('#height').val().trim();
    const weight = $('#weight').val().trim();
    if (!height || !weight) {
        GymSwal.fire({ icon: 'warning', title: 'Height and weight are required.' });
        return;
    }
    $.ajax({
        url: '/workout_trackersys/controllers/onboardingController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            height,
            weight,
            limitations:      $('#limitations').val() || 'none',
            fitnessLevel:     $('#fitnessLevel').val(),
            fitnessGoal:      $('#fitnessGoal').val(),
            workoutFrequency: $('#workoutFrequency').val(),
            workoutDuration:  $('#workoutDuration').val()
        },
        success: function(res) {
            if (res.ok) {
                GymSwal.fire({ icon:'success', title:'Profile saved!', showConfirmButton:false, timer:1500 })
                    .then(() => window.location.href = '?page=dashboard');
            } else {
                GymSwal.fire({ icon:'error', title:'Something went wrong', text: res.error ?? '' });
            }
        },
        error: () => GymSwal.fire({ icon:'error', title:'Server error' })
    });
});

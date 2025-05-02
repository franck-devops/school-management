<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['principal']);
?>

<h2>Principal Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <h3>Total Students</h3>
        <p><?= count(getStudents()) ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Staff</h3>
        <p><?= count(getStaff()) ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Classes</h3>
        <p><?= count(getClasses()) ?></p>
    </div>
</div>

<div class="chart-container">
    <canvas id="passRateChart"></canvas>
</div>

<script>
$(document).ready(function() {
    // Sample data for pass rate chart
    const ctx = document.getElementById('passRateChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5'],
            datasets: [{
                label: 'Pass Rate (%)',
                data: [85, 78, 92, 88, 95],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>

<?php
require_once '../../includes/footer.php';
?>
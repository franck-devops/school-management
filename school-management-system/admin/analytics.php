<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['principal']);

// Get analytics data
$totalStudents = count(getStudents());
$totalStaff = count(getStaff());
$totalClasses = count(getClasses());
?>

<h2>System Analytics</h2>

<div class="stats">
    <div class="stat-card">
        <h3>Students by Section</h3>
        <canvas id="studentsBySectionChart"></canvas>
    </div>
    <div class="stat-card">
        <h3>Staff by Role</h3>
        <canvas id="staffByRoleChart"></canvas>
    </div>
</div>

<div class="chart-container">
    <h3>Student Performance Trend</h3>
    <canvas id="performanceTrendChart"></canvas>
</div>

<script>
$(document).ready(function() {
    // Students by section chart
    const sectionCtx = document.getElementById('studentsBySectionChart').getContext('2d');
    new Chart(sectionCtx, {
        type: 'pie',
        data: {
            labels: ['General', 'Commercial', 'Technical'],
            datasets: [{
                data: [120, 85, 65],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        }
    });
    
    // Staff by role chart
    const roleCtx = document.getElementById('staffByRoleChart').getContext('2d');
    new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: ['Teachers', 'HODs', 'Class Masters', 'Both'],
            datasets: [{
                data: [25, 5, 8, 3],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
            }]
        }
    });
    
    // Performance trend chart
    const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['2018', '2019', '2020', '2021', '2022', '2023'],
            datasets: [{
                label: 'Overall Pass Rate (%)',
                data: [75, 78, 82, 85, 88, 90],
                borderColor: '#36A2EB',
                fill: false
            }]
        }
    });
});
</script>

<?php
require_once '../../includes/footer.php';
?>
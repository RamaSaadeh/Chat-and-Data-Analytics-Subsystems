<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<?php
//starting session
session_start();
if (!isset($_SESSION["valid"])) {
    //redirect if session is not valid
    header("Location: login.php");
    exit();
}

require_once("../../includes/dbh.inc.php");
require_once("../functions.php");
// printing count for flagged
$flaggedCount = intval(countFlagged($conn));
$pendingCount = intval(countAll($conn));
$todayCount = intval(countToday($conn));
$completedCount = intval(countCompleted($conn));

$totalCount = $flaggedCount + $pendingCount + $todayCount + $completedCount;
if ($totalCount == !0) {


    $flaggedPercentage = ($flaggedCount / $totalCount) * 100;
    $pendingPercentage = ($pendingCount / $totalCount) * 100;
    $todayPercentage = ($todayCount / $totalCount) * 100;
    $completedPercentage = ($completedCount / $totalCount) * 100;

    $labels = array(
        array("Flagged"),
        array("Pending"),
        array("Today"),
        array("Completed")
    );

    $data = array(
        array($flaggedPercentage),
        array($pendingPercentage),
        array($todayPercentage),
        array($completedPercentage)
    );

?>
    <style>
        .flexbox {
            display: block;
            align-items: top;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
    </style>

    <div class="flexbox">
        <div class="chartbox">
            <canvas id="donut-chart" style="width: 100%; height: 75%"></canvas>
            <script>
                labels = <?php echo json_encode($labels) ?>;
                task_data = <?php echo json_encode($data) ?>;
                backgroundColor = [
                    '#FFD966',
                    '#E97777',
                    '#98A8F8',
                    '#D0E7D2'
                ];

                ctx = document.getElementById('donut-chart');

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: task_data,
                            backgroundColor: backgroundColor
                        }]
                    },
                    options: {
                        cutoutPercentage: 30,
                        maintainAspectRatio: false
                    }
                });
            </script>
        </div>
    </div>
<?php
}
?>
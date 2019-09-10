<div class="col-md-6">
    <div class="panel panel-default">
    <!-- Default panel contents -->
        <div class="panel-heading">
            <?php echo html_escape(element('board_name', element('board', $view))); ?>
            <div class="view-all pull-right">
                <a href="<?php echo board_url(element('brd_key', element('board', $view))); ?>" title="<?php echo html_escape(element('board_name', element('board', $view))); ?>">더보기 <i class="fa fa-angle-right"></i></a>
            </div>
        </div>
        
        <div id="chart_div"></div>
        
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function drawChart() {

    var data = new google.visualization.arrayToDataTable([
        ['도메인', '비율'],
        <?php
        $sum = 0;
        if (element('list', $view)) {
            $i=0;
            foreach (element('list', $view) as $result) {
        ?>
        ['<?php echo html_escape(element('key', $result)); ?>',<?php echo element('count', $result, 0); ?>],
        <?php
                $i++;
                $sum += element('count', $result, 0);
                if ($i > 8) break;
            }
        }
        if (element('sum_count', $view) && $sum && $sum < element('sum_count', $view)) {
        ?>
            ['기타',<?php echo element('sum_count', $view, 0) - $sum; ?>],
        <?php
        }
        ?>
    ]);

    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));

    chart.draw(data, {
        width: '100%', height: '400',
    });
}


</script>

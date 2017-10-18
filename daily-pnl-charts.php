<?php 
require('lib/common.php');
require('cfg/db.portfolios.php');

?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Daily PnL Charts | Huatai Capital Investment Limited</title>     
   
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.1/highcharts.js"></script> 
    <script>
    var chart; // global
    
    /**
     * Request data from the server, add it to the graph and set a timeout 
     * to request again
     */
    function requestData() {
        $.ajax({
            url: 'lib/portfolio-netpnl.php',
            success: function(point) {
                var today = new Date();
                var start = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 20, 0, 0);
                var end = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 16, 30, 0, 0);
                //var hour = today.getHours();
                //var minutes = today.getMinutes();
                //if (hour >= 9 && hour <= 17) {
                if (today >= start && today <= end) {
                    var series = chart.series[0],
                        shift = series.data.length > 100; // shift if the series is 
                                                         // longer than 100
                    // add the point
                    chart.series[0].addPoint(point, true, shift);
                }
                // call it again after one second
                setTimeout(requestData, 5000);    
            },
            cache: false
        });
    }    
    
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                defaultSeriesType: 'spline',
                events: {
                    load: requestData
                }
            },
            title: {
                text: 'Real-time PnL'
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 150,
                maxZoom: 20 * 1000,
            },
            yAxis: {
                minPadding: 0.2,
                maxPadding: 0.2,
                title: {
                    text: 'PnL',
                    margin: 10
                }
            },
            series: [{
                name: 'Net PnL',
                data: []
            }]
        });        
    });    
    </script>
</head>

<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Daily PnL Charts</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
    
            </div>
            <!-- /.row -->
            
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <div id = "container" style = "width: 100%; height: 400px; margin: 0 auto"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->            
            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->            
</body>

</html>
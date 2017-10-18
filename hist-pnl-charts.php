<?php 
require('lib/common.php');
require('cfg/db.portfolios.php');

?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Historical PnL Charts | Huatai Capital Investment Limited</title>     
 
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.1/highcharts.js"></script> 
    <script>
    var chart; // global
    
    /**
     * Request data from the server, add it to the graph and set a timeout 
     * to request again
     */
    function requestData() {
        $.ajax({
            url: 'lib/portfolio-histpnl.php',
            success: function(csv) {
                var lines = csv.split('\n');
                $.each(lines, function(lineNo, line) {
                    var items = line.split(',');
                    // header line containes categories
                    if (lineNo != 0) {
                        var xPoint = Date.parse(items[0]);
                        var yPoint = parseFloat(Number(parseFloat(items[1])).toFixed(2));
                        chart.series[0].addPoint({ x: xPoint, y: yPoint}, true);
                    }
                });
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
                text: 'Historical PnL'
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
                    <h1 class="page-header">Historical PnL Charts</h1>
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
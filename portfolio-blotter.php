<?php 
require('lib/common.php');
?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Portfolio Blotter | Huatai Capital Investment Limited</title>    
    <style>  
    .positive {
        color: blue;
    }
    .negative {
        color: red;
    }
    .custom {
        font-size: 12px;
    }    
    table.dataTable.custom td {
        padding-bottom: 0px;
    }       
    </style>
    <script type="text/javascript" src="//code.highcharts.com/highcharts.js"></script>
    <script type="text/javascript" src="//code.highcharts.com/modules/exporting.js"></script>
    <script type="text/javascript" class="init">
    
    function escapeDoubleQuotes(str) {
        return str.replace(/\\([\s\S])|(")/g,"\\$1$2"); // thanks @slevithan!
    }
    
    function displayNumber(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    function round(value, precision) {
        var multiplier = Math.pow(10, precision || 0);
        return Math.round(value * multiplier) / multiplier;
    }    
    
    $(document).ready(function() {
        var portdettable = $('#dtDetail').DataTable({
            "lengthChange": false,              
            "order": [[ 0, "asc" ]],
            "pageLength": 50,            
            "processing": true,
            "serverSide": false,
            "ajax": "lib/portfolio-detail.php",
            "buttons": [
                'pageLength', 
                'colvis',                
                'copy', 
                'csv', 
                'excel', 
                {
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'LEGAL'
                },
                'print'
            ],
            "columnDefs": [
                { targets: '_all', visible: true },
                { targets: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], className: "text-right" },
                { targets: 0, render: function ( data, type, row ) {
                    return "<a href='ticker.php?name="+data+"'>"+data+"</a>";
                }},                
                { targets: [10,11,12,13], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return '<p class="positive">'+round(data,3).toLocaleString('en-US')+'</p>';
                      } else {
                        return '<p class="negative">'+round(data,3).toLocaleString('en-US')+'</p>';
                      }
                }},                  
                { targets: [1,2,3,4,5,6,7,8,9,14,15,16,17,18,19], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return '<p class="positive">'+round(data,0).toLocaleString('en-US')+'</p>';
                      } else {
                        return '<p class="negative">'+round(data,0).toLocaleString('en-US')+'</p>';
                      }
                }},                            
            ],
            responsive: true
        });
                
        var portsumtable = $('#dtSummary').DataTable({
            "lengthChange": false,              
            "paging":   false,
            "searching": false,
            "order": [[ 0, "asc" ]],        
            "processing": true,
            "serverSide": false,
            "ajax": "lib/portfolio-summary.php",
            "columnDefs": [
                { targets: '_all', visible: true },
                { targets: [0,1,2,3,4,5,6,7,8,9,10,11,12], className: "text-right" },       
                { targets: [0,1,2,3,4,5,6,7,8,9,10,11,12], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return '<p class="positive">'+round(data,0).toLocaleString('en-US')+'</p>';
                      } else {
                        return '<p class="negative">'+round(data,0).toLocaleString('en-US')+'</p>';
                      }
                }},                            
            ],
            responsive: true
        });

        setInterval( function () {
            portdettable.buttons().container().appendTo( $('.col-sm-6:eq(0)', portdettable.table().container() ) );
            portdettable.ajax.reload(null, false);
            portsumtable.ajax.reload(null, false);
        }, 5000 );        
    } );
    
    var chartNetPnL; // global
    var chartYtdPnL; // global
    var chartDelta; // global
    
    /**
     * Request data from the server, add it to the graph and set a timeout 
     * to request again
     */
    function requestNetPnL() {
        $.ajax({
            url: 'lib/portfolio-netpnl.php',
            success: function(point) {
                var today = new Date();
                var start = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 20, 0, 0);
                var end = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 16, 30, 0, 0);  
                if (today >= start && today <= end) {
                    var series = chartNetPnL.series[0];
                    chartNetPnL.series[0].addPoint(point, true, false);
                }
                // call it again after 5 seconds
                setTimeout(requestNetPnL, 5000);    
            },
            cache: false
        });
    }    
    
    function requestDelta() {
        $.ajax({
            url: 'lib/portfolio-delta.php',
            success: function(point) {
                var today = new Date();
                var start = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 20, 0, 0);
                var end = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 16, 30, 0, 0);                
                if (today >= start && today <= end) {
                    var series = chartDelta.series[0];
                    chartDelta.series[0].addPoint(point, true, false);
                }
                // call it again after 5 seconds
                setTimeout(requestDelta, 5000);    
            },
            cache: false
        });
    }   

    function requestYtdPnL() {
        $.ajax({
            url: 'lib/portfolio-ytdpnl.php',
            success: function(point) {
                var today = new Date();
                var start = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 9, 20, 0, 0);
                var end = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 16, 30, 0, 0);   
                if (today >= start && today <= end) {
                    var series = chartYtdPnL.series[0];
                    chartYtdPnL.series[0].addPoint(point, true, false);
                }
                // call it again after 5 seconds
                setTimeout(requestYtdPnL, 5000);    
            },
            cache: false
        });
    }       
    
    $(document).ready(function() {
        chartNetPnL = new Highcharts.Chart({
            chart: {
                renderTo: 'chartNetPnL',
                defaultSeriesType: 'spline',
                events: {
                    load: requestNetPnL
                }
            },
            title: {
                text: ''
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
                    text: '$',
                    margin: 10
                }
            },
            series: [{
                name: 'Net PnL',
                data: []
            }]
        });        
    });        

    $(document).ready(function() {
        chartDelta = new Highcharts.Chart({
            chart: {
                renderTo: 'chartDelta',
                defaultSeriesType: 'spline',
                events: {
                    load: requestDelta
                }
            },
            title: {
                text: ''
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
                    text: '$',
                    margin: 10
                }
            },
            series: [{
                name: 'Delta',
                data: []
            }]
        });        
    });          

    $(document).ready(function() {
        chartYtdPnL = new Highcharts.Chart({
            chart: {
                renderTo: 'chartYtdPnL',
                defaultSeriesType: 'spline',
                events: {
                    load: requestYtdPnL
                }
            },
            title: {
                text: ''
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
                    text: '$',
                    margin: 10
                }
            },
            series: [{
                name: 'Year-To-Date PnL',
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
                    <h1 class="page-header">Portfolio Blotter</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Summary
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover custom" id="dtSummary">
                                <thead>
                                    <tr>
                                        <th>Long</th>
                                        <th>Short</th>                                    
                                        <th>Delta</th>
                                        <th>Comms</th>
                                        <th>Divs</th>                            
                                        <th>Gross PnL</th>
                                        <th>Net PnL</th>
                                        <th>Mtd PnL</th>
                                        <th>Mtd Comms</th>
                                        <th>Mtd Divs</th>                                    
                                        <th>Ytd PnL</th>
                                        <th>Ytd Comms</th>
                                        <th>Ytd Divs</th>
                                   </tr>
                               </thead>              
                            </table>	
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->                
            
            <div class="row">
            
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Daily Net PnL
                        </div>
                        <div class="panel-body">
                            <div id="chartNetPnL" style="width: 100%; height: 200px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Delta
                        </div>
                        <div class="panel-body">
                            <div id="chartDelta" style="width: 100%; height: 200px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Year-To-Date PnL
                        </div>
                        <div class="panel-body">
                            <div id="chartYtdPnL" style="width: 100%; height: 200px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>                
            </div>
            <!-- /.row -->   
            
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Details
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover custom" id="dtDetail">
                                <thead>
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Ovn Qty</th>
                                        <th>Buy Qty</th>
                                        <th>Sell Qty</th>                 
                                        <th>Net Qty</th>
                                        <th>Delta</th>
                                        <th>Comms</th>
                                        <th>Divs</th>                            
                                        <th>Gross PnL</th>
                                        <th>Net PnL</th>
                                        <th>1D Px</th>
                                        <th>Last Px</th>
                                        <th>Net Chg</th>
                                        <th>Pct Chg</th>
                                        <th>Mtd PnL</th>
                                        <th>Mtd Comms</th>
                                        <th>Mtd Divs</th>                                    
                                        <th>Ytd PnL</th>
                                        <th>Ytd Comms</th>
                                        <th>Ytd Divs</th>                            
                                   </tr>
                               </thead>
                                <tfoot>
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Ovn Qty</th>
                                        <th>Buy Qty</th>
                                        <th>Sell Qty</th>                 
                                        <th>Net Qty</th>
                                        <th>Delta</th>
                                        <th>Comms</th>
                                        <th>Divs</th>                            
                                        <th>Gross PnL</th>
                                        <th>Net PnL</th>
                                        <th>1D Px</th>
                                        <th>Last Px</th>
                                        <th>Net Chg</th>
                                        <th>Pct Chg</th>
                                        <th>Mtd PnL</th>
                                        <th>Mtd Comms</th>
                                        <th>Mtd Divs</th>                                    
                                        <th>Ytd PnL</th>
                                        <th>Ytd Comms</th>
                                        <th>Ytd Divs</th>                                  
                                   </tr>
                               </tfoot>                
                            </table>	
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
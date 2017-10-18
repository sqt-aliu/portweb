<?php 
require('lib/common.php');
?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Portfolio Cost Basis | Huatai Capital Investment Limited</title>    
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
        var portcosttable = $('#dtCost').DataTable({
            "lengthChange": false,              
            "order": [[ 0, "asc" ]],
            "pageLength": 50,            
            "processing": true,
            "serverSide": false,
            "ajax": "lib/portfolio-cost.php",
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
                { targets: [1,2,3,4,5,6], className: "text-right" },
                { targets: 0, render: function ( data, type, row ) {
                    return "<a href='ticker.php?name="+data+"'>"+data+"</a>";
                }},                
                { targets: [2,3,4], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return '<p class="positive">'+round(data,3).toLocaleString('en-US')+'</p>';
                      } else {
                        return '<p class="negative">'+round(data,3).toLocaleString('en-US')+'</p>';
                      }
                }},                  
                { targets: [1,5,6], render: function ( data, type, row )  {
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
            portcosttable.buttons().container().appendTo( $('.col-sm-6:eq(0)', portcosttable.table().container() ) );
            portcosttable.ajax.reload(null, false);
        }, 5000 );        
    } );

    </script>          
</head>

<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Portfolio Cost Basis</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
           
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Cost Basis
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover custom" id="dtCost">
                                <thead>
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Net Qty</th>
                                        <th>Avg Px</th>
                                        <th>Last Px</th>                 
                                        <th>Pct Chg</th>
                                        <th>Cost</th>
                                        <th>Delta</th>                       
                                   </tr>
                               </thead>
                                <tfoot>
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Net Qty</th>
                                        <th>Avg Px</th>
                                        <th>Last Px</th>                 
                                        <th>Pct Chg</th>
                                        <th>Cost</th>
                                        <th>Delta</th>                                  
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
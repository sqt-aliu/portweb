<?php 
require('lib/common.php');
require('cfg/db.portfolios.php');

$proc = <<<EOD
select * from trades where date = CURRENT_DATE() and portfolio like 'prod%'
EOD;
$results = "";
try {
    $db = @new PDO(
        "mysql:host=".DB_PORT_HOST.";dbname=".DB_PORT_DATABASE,
        DB_PORT_USER,
        DB_PORT_PASSWORD,
        array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
    );
    $stmt = $db->prepare( $proc );

    try {
        $stmt->execute(); // Execute
    }
    catch (PDOException $e) {
        print_r( "An SQL error occurred: ".$e->getMessage() );
    }
    $results = $stmt->fetchAll( PDO::FETCH_ASSOC ); // Return all
}
catch (PDOException $e) {
    print_r( "An SQL error occurred: ".$e->getMessage() );
}

?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Trades Report | Huatai Capital Investment Limited</title>
    <script>
    $(document).ready(function() {
        var table = $('#dataTables-example').DataTable({ 
            "order": [[ 0, "asc" ]],
            "lengthChange": false,            
            "pageLength": 50,
            "processing": false,
            "serverSide": false,
            "columnDefs": [              
                { targets: [4,5,6], className: "dt-right" },
                { targets: [4], render: $.fn.dataTable.render.number( ',', '.', 0 ) },   
                      
            ],                 
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
            
            responsive: true
        });
        
        table.buttons().container().appendTo( '#dataTables-example_wrapper .col-sm-6:eq(0)' );        
    });
    </script>
</head>

<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Trades Report</h1>
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
                        <div class="panel-heading">
                        Today's Trades
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th align='left'>Portfolio</th>
                                        <th align='center'>Date</th>
                                        <th align='right'>Ticker</th>
                                        <th align='right'>Side</th>
                                        <th align='right'>Exec Qty</th>
                                        <th align='right'>Avg Px</th>
                                        <th align='right'>Comms</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach( $results as $row ){
                                    echo "<tr><td>";
                                    echo $row['portfolio'];
                                    echo "</td><td>";
                                    echo $row['date'];
                                    echo "</td><td>";
                                    echo "<a href='ticker.php?name=".$row['ticker']."'>".$row['ticker']."</a>";
                                    echo "</td><td>";                                    
                                    echo $row['side'];
                                    echo "</td><td align='right'>";
                                    echo $row['execqty'];
                                    echo "</td><td align='right'>";
                                    echo $row['avgpx'];
                                    echo "</td><td align='right'>";
                                    echo $row['comms'];
                                    echo "</td>";                              
                                    echo "</tr>";
                                }                                
                                ?>
                                </tbody>
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
<?php 
require('lib/common.php');
require('cfg/db.portfolios.php');
$proc = <<<EOD
select ticker, eodqty, eodnot from report where portfolio like 'prod%' and date = CURRENT_DATE() and eodqty < 0
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

function readMarketCap( ) {
    $mktCap = array();
    $files = glob("/home/sqtdata/dfs/raw/live/blmg.day/*/xhkg.univ.*.csv");
    if (($handle = fopen( end($files), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($data[0] <> 'ticker') {
                $ticker = bloombergTicker($data[0]);
                $mktCap[$ticker] = $data[6];
            }
        }
        fclose($handle);
    }
    return ($mktCap);
}

$mktCapMap = readMarketCap();
?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Short Sale Report | Huatai Capital Investment Limited</title>
    <script>
    $(document).ready(function() {
        var table = $('#dataTables-example').DataTable({ 
            "order": [[ 0, "asc" ]],
            "lengthChange": false,
            "pageLength": 50,
            "processing": false,
            "serverSide": false,
            "columnDefs": [              
                { targets: [1,2,3,4,5], className: "dt-right" },
                { targets: [1,2,3], render: $.fn.dataTable.render.number( ',', '.', 0 ) },   
                      
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
                    <h1 class="page-header">Short Sale Report</h1>
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
                        Today's Short Positions
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead class="custom">
                                    <tr>
                                        <th align='right'>Ticker</th>
                                        <th align='right'>Net Qty</th>
                                        <th align='right'>Net Exposure</th>
                                        <th align='right'>Market Cap</th>
                                        <th align='right'>% of Market Cap</th>
                                        <th align='right'>Exceeds Threshold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach( $results as $row ){
                                    $mktCap = $mktCapMap[$row['ticker']];
                                    $pctOfMktCap = round(100*(abs($row['eodnot'])/$mktCap), 5);
                                    echo "<tr><td>";
                                    echo "<a href='ticker.php?name=".$row['ticker']."'>".$row['ticker']."</a>";
                                    echo "</td><td align='right'>";
                                    echo abs($row['eodqty']);
                                    echo "</td><td align='right'>";
                                    echo abs($row['eodnot']);
                                    echo "</td><td align='right'>";
                                    echo $mktCap;
                                    echo "</td><td align='right'>";
                                    echo $pctOfMktCap;
                                    echo "</td><td align='right'>";
                                    if ((abs($row['eodnot']) >= 30000000) || ($pctOfMktCap >= 0.02)) {
                                        echo "True";
                                    } else {
                                        echo "False";
                                    }
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
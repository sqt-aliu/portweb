<?php 
require('lib/common.php');
require('cfg/db.portfolios.php');

$sectorTotals = array();
$industryTotals = array();

$sectorMap = array();
$industryMap = array();
$files = glob('/bca/scores/*.bca.*.asof.*.cap.csv');
if (($handle = fopen( end($files), "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        if (($data[0] <> 'Ticker') && ($data[2] == 'SEHK')) {
            $ticker = bcaTicker($data[0]);
            $sectorMap[$ticker] = $data[3];
            $industryMap[$ticker] = $data[4];
        }
    }
}

$name = "prod_hk";
$proc = <<<EOD
(select COALESCE(c.ticker,c.ticker,d.ticker) as ticker, c.ytdpnl, c.ytdfees, c.ytddivs, c.mtdpnl, c.mtdfees, c.mtddivs, d.sodqty, d.buyqty, d.sellqty, d.eodqty as netqty, d.eodnot as delta, d.comms, d.divs, d.grosspnl, d.netpnl
	 from (select COALESCE(a.ticker,a.ticker,b.ticker) as ticker, a.ytdpnl, a.ytdfees, a.ytddivs, b.mtdpnl, b.mtdfees, b.mtddivs from 
		(select ticker, sum(netpnl) as ytdpnl, sum(comms) as ytdfees, sum(divs) as ytddivs from report where date >= DATE_FORMAT(CURRENT_DATE(), '%Y-01-01') and date <= CURRENT_DATE() and portfolio = '$name' group by ticker) a left outer join
			(select ticker, sum(netpnl) as mtdpnl, sum(comms) as mtdfees, sum(divs) as mtddivs from report where date >= DATE_FORMAT(CURRENT_DATE(),'%Y-%m-01') and date <= CURRENT_DATE() and portfolio = '$name' group by ticker) b on a.ticker = b.ticker) c left outer join
				(select * from report where portfolio = '$name' and date = CURRENT_DATE()) d on c.ticker = d.ticker) 
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


foreach( $results as $row ){
    if (array_key_exists($row['ticker'], $sectorMap)) {
        $sector = $sectorMap[$row['ticker']];
        if (!array_key_exists($sector, $sectorTotals)) {
            $sectorTotals[$sector] = array();
            $sectorTotals[$sector]['YTDPNL'] = 0;
            $sectorTotals[$sector]['MTDPNL'] = 0;
            $sectorTotals[$sector]['NETPNL'] = 0;
            $sectorTotals[$sector]['GROSSPNL'] = 0;
            $sectorTotals[$sector]['DELTA'] = 0;
        }
        $sectorTotals[$sector]['YTDPNL'] += $row['ytdpnl'];
        $sectorTotals[$sector]['MTDPNL'] += $row['mtdpnl'];
        $sectorTotals[$sector]['NETPNL'] += $row['netpnl'];
        $sectorTotals[$sector]['GROSSPNL'] += $row['grosspnl'];
        $sectorTotals[$sector]['DELTA'] += $row['delta'];            
    }
    if (array_key_exists($row['ticker'], $industryMap)) {
        $industry = $industryMap[$row['ticker']];
        if (!array_key_exists($industry, $industryTotals)) {
            $industryTotals[$industry] = array();
            $industryTotals[$industry]['YTDPNL'] = 0;
            $industryTotals[$industry]['MTDPNL'] = 0;
            $industryTotals[$industry]['NETPNL'] = 0;
            $industryTotals[$industry]['GROSSPNL'] = 0;
            $industryTotals[$industry]['DELTA'] = 0;
        }
        $industryTotals[$industry]['YTDPNL'] += $row['ytdpnl'];
        $industryTotals[$industry]['MTDPNL'] += $row['mtdpnl'];
        $industryTotals[$industry]['NETPNL'] += $row['netpnl'];
        $industryTotals[$industry]['GROSSPNL'] += $row['grosspnl'];
        $industryTotals[$industry]['DELTA'] += $row['delta'];            
    }        
}

?>
<html>
<head>
    <?php include('inc/header.php') ?>
    <title>Risk Report | Huatai Capital Investment Limited</title>     
    <style>  
    .positive {
        color: blue;
    }
    .negative {
        color: red;
    }
    </style>    
    <script>
    function round(value, precision) {
        var multiplier = Math.pow(10, precision || 0);
        return Math.round(value * multiplier) / multiplier;
    }    
    
    $(document).ready(function() {
        $('#dataTables-sector').DataTable({ 
            "paging":   false,
            "searching": false,            
            "order": [[ 0, "asc" ]],
            "pageLength": 50,
            "processing": false,
            "serverSide": false,
            "columnDefs": [              
                { targets: [1,2,3,4,5], className: "text-right" },
                { targets: [1,2,3,4,5], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return '<p class="positive">'+round(data,0).toLocaleString('en-US')+'</p>';
                      } else {
                        return '<p class="negative">'+round(data,0).toLocaleString('en-US')+'</p>';
                      }
                }}, 
            ],                 
            responsive: true
        });
    });
    
    $(document).ready(function() {
        $('#dataTables-industry').DataTable({ 
            "paging":   false,
            "searching": false,            
            "order": [[ 0, "asc" ]],
            "pageLength": 50,
            "processing": false,
            "serverSide": false,
            "columnDefs": [              
                { targets: [1,2,3,4,5], className: "text-right" },
                { targets: [1,2,3,4,5], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return '<p class="positive">'+round(data,0).toLocaleString('en-US')+'</p>';
                      } else {
                        return '<p class="negative">'+round(data,0).toLocaleString('en-US')+'</p>';
                      }
                }},   
            ],                 
            responsive: true
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
                    <h1 class="page-header">Risk Report</h1>
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
                        Sector Report
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-sector">
                                <thead class="custom">
                                    <tr>
                                    <th align='left' width='400'>Sector</th>
                                    <th align='right'>Mkt Value</th>
                                    <th align='right'>Net PnL</th>
                                    <th align='right'>Gross PnL</th>
                                    <th align='right'>MTD PnL</th>
                                    <th align='right'>YTD PnL</th>
                                   </tr>
                               </thead>                           
                               <tbody>
                               <?php 
                               foreach ($sectorTotals as $sectorKey => $sectorVal) {
                                    echo "<tr>";
                                    echo "<td>";
                                    echo $sectorKey;
                                    echo "</td><td>";
                                    echo $sectorVal['DELTA'];
                                    echo "</td><td>";
                                    echo $sectorVal['NETPNL'];
                                    echo "</td><td>";
                                    echo $sectorVal['GROSSPNL'];
                                    echo "</td><td>";      
                                    echo $sectorVal['MTDPNL'];
                                    echo "</td><td>";     
                                    echo $sectorVal['YTDPNL'];
                                    echo "</td>";                                        
                                    echo "</tr>";
                               }
                               ?>
                               <tbody>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Industry Report
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-industry">
                                <thead class="custom">
                                    <tr>
                                    <th align='left'  width='400'>Industry</th>
                                    <th align='right'>Mkt Value</th>
                                    <th align='right'>Net PnL</th>
                                    <th align='right'>Gross PnL</th>
                                    <th align='right'>MTD PnL</th>
                                    <th align='right'>YTD PnL</th>
                                   </tr>
                               </thead>                           
                               <tbody>
                               <?php 
                               foreach ($industryTotals as $industryKey => $industryVal) {
                                    echo "<tr>";
                                    echo "<td>";
                                    echo $industryKey;
                                    echo "</td><td>";
                                    echo $industryVal['DELTA'];
                                    echo "</td><td>";
                                    echo $industryVal['NETPNL'];
                                    echo "</td><td>";
                                    echo $industryVal['GROSSPNL'];
                                    echo "</td><td>";      
                                    echo $industryVal['MTDPNL'];
                                    echo "</td><td>";     
                                    echo $industryVal['YTDPNL'];
                                    echo "</td>";                                        
                                    echo "</tr>";
                               }
                               ?>
                               <tbody>                               
                               <tbody>
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
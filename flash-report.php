<?php 
require('lib/common.php');
require('cfg/db.portfolios.php');
function loadFX() {
    $map = array();
    $files = glob('/home/sqtdata/dfs/raw/live/xefx.day/*/*.fx.csv');    
    if (($handle = fopen( end($files), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($data[0] <> 'ccy') {
                $map[$data[0]] = $data[1];
            }
        }
        fclose($handle);
    }
    return $map;
}
$fxmap = loadFX();


$name = "prod_hk";
$proc = <<<EOD
select e.*, f.prevpx, f.lastpx, f.lastpx-f.prevpx as netchg, 100*((f.lastpx/f.prevpx)-1) as pctchg
  from (select COALESCE(c.ticker,c.ticker,d.ticker) as ticker, c.ytdpnl, c.ytdfees, c.ytddivs, c.mtdpnl, c.mtdfees, c.mtddivs, d.sodqty, d.buyqty, d.sellqty, d.eodqty as netqty, d.eodnot as delta, d.comms, d.divs, d.grosspnl, d.netpnl
	 from (select COALESCE(a.ticker,a.ticker,b.ticker) as ticker, a.ytdpnl, a.ytdfees, a.ytddivs, b.mtdpnl, b.mtdfees, b.mtddivs from 
		(select ticker, sum(netpnl) as ytdpnl, sum(comms) as ytdfees, sum(divs) as ytddivs from report where date >= DATE_FORMAT(CURRENT_DATE(), '%Y-01-01') and date <= CURRENT_DATE() and portfolio = '$name' group by ticker) a left outer join
			(select ticker, sum(netpnl) as mtdpnl, sum(comms) as mtdfees, sum(divs) as mtddivs from report where date >= DATE_FORMAT(CURRENT_DATE(),'%Y-%m-01') and date <= CURRENT_DATE() and portfolio = '$name' group by ticker) b on a.ticker = b.ticker) c left outer join
				(select * from report where portfolio = '$name' and date = CURRENT_DATE()) d on c.ticker = d.ticker) e left outer join 
					(select * from prices where date = CURRENT_DATE()) f on e.ticker = f.ticker
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
    <title>Flash Report | Huatai Capital Investment Limited</title>     
</head>

<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Flash Report</h1>
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
                        Flash Report
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead class="custom">
                                    <tr>
                                    <th align='left'>Name</th>
                                    <th align='center'>Currency</th>
                                    <th align='right'>Position</th>
                                    <th align='right'>Mkt Value</th>
                                    <th align='right'>Price</th>
                                    <th align='right'>Daily P&L</th>
                                    <th align='right'>MTD P&L</th>
                                    <th align='right'>YTD P&L</th>
                                    <th align='right'>P&L (LCY)</th>
                                    <th align='right'>Fees (LCY)</th>
                                    <th align='right'>Broker</th>
                                   </tr>
                               </thead>                           
                               <tbody>
                                </tr>
                                <?php 
                                    $delta_usd = 0;
                                    $dailypnl_usd = 0;
                                    $mtdpnl_usd = 0;
                                    $ytdpnl_usd = 0;
                                    $netpnl_lcy = 0;
                                    $comms_lcy = 0;
                                    foreach( $results as $row ){
                                        $fxrate = $fxmap[tickerCurrency($row['ticker'])];
                                        $delta_usd += ($row['delta']*$fxrate);
                                        $dailypnl_usd += ($row['netpnl']*$fxrate);
                                        $mtdpnl_usd += ($row['mtdpnl']*$fxrate);
                                        $ytdpnl_usd += ($row['ytdpnl']*$fxrate);
                                        $netpnl_lcy += $row['netpnl'];
                                        $comms_lcy += $row['comms'];
                                    }
                                    echo "<tr><td>";
                                    echo "Top-Level";
                                    echo "</td><td>";
                                    echo "</td><td align='right'>";
                                    echo "</td><td align='right'>";                                    
                                    echo round($delta_usd);
                                    echo "</td><td align='right'>";
                                    echo "</td><td align='right'>";
                                    echo round($dailypnl_usd);
                                    echo "</td><td align='right'>";
                                    echo round($mtdpnl_usd);
                                    echo "</td><td align='right'>";
                                    echo round($ytdpnl_usd);
                                    echo "</td><td align='right'>";
                                    echo round($netpnl_lcy);
                                    echo "</td><td align='right'>";
                                    echo round($comms_lcy);
                                    echo "</td><td>";
                                    echo "</td>";                                    
                                    echo "</tr>";
                                ?>
                                <?php foreach( $results as $row ){
                                    $fxrate = $fxmap[tickerCurrency($row['ticker'])];
                                    echo "<tr><td><a href='ticker.php?name=".$row['ticker']."'>";
                                    echo tickerBloomberg($row['ticker']);
                                    echo "</a></td><td>";
                                    echo tickerCurrency($row['ticker']);
                                    echo "</td><td align='right'>";
                                    echo $row['netqty'];
                                    echo "</td><td align='right'>";                                    
                                    echo round($row['delta']*$fxrate);
                                    echo "</td><td align='right'>";
                                    echo $row['lastpx'];
                                    echo "</td><td align='right'>";
                                    echo round($row['netpnl']*$fxrate);
                                    echo "</td><td align='right'>";
                                    echo round($row['mtdpnl']*$fxrate);
                                    echo "</td><td align='right'>";
                                    echo round($row['ytdpnl']*$fxrate);
                                    echo "</td><td align='right'>";
                                    echo round($row['netpnl']);
                                    echo "</td><td align='right'>";
                                    echo round($row['comms']);
                                    echo "</td><td>";
                                    echo "HK-INCA";
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
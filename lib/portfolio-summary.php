<?php
require('ssp.php');
require('../cfg/db.portfolios.php');
$name = "prod_hk";
// DB table to use
$proc = <<<EOD
select sum(ytdpnl) as ytdpnl, sum(ytdfees) as ytdfees, sum(ytddivs) as ytddivs, sum(mtdpnl) as mtdpnl, sum(mtdfees) as mtdfees, sum(mtddivs) as mtddivs, sum(delta) as delta, sum(longposn) as longposn, sum(shortposn) as shortposn, sum(comms) as comms, sum(divs) as divs, sum(grosspnl) as grosspnl, sum(netpnl) as netpnl
  from (select COALESCE(c.ticker,c.ticker,d.ticker) as ticker, c.ytdpnl, c.ytdfees, c.ytddivs, c.mtdpnl, c.mtdfees, c.mtddivs, d.sodqty, d.buyqty, d.sellqty, d.eodqty as netqty, d.eodnot as delta, IF(d.eodnot>0,d.eodnot,0) as longposn, IF(d.eodnot<0,d.eodnot,0) as shortposn, d.comms, d.divs, d.grosspnl, d.netpnl
	 from (select COALESCE(a.ticker,a.ticker,b.ticker) as ticker, a.ytdpnl, a.ytdfees, a.ytddivs, b.mtdpnl, b.mtdfees, b.mtddivs from 
		(select ticker, sum(netpnl) as ytdpnl, sum(comms) as ytdfees, sum(divs) as ytddivs from report where date >= DATE_FORMAT(CURRENT_DATE(), '%Y-01-01') and date <= CURRENT_DATE() and portfolio = '$name' group by ticker) a left outer join
			(select ticker, sum(netpnl) as mtdpnl, sum(comms) as mtdfees, sum(divs) as mtddivs from report where (date >= DATE_FORMAT(CURRENT_DATE(),'%Y-%m-01') and date <= CURRENT_DATE()) and portfolio = '$name' group by ticker) b on a.ticker = b.ticker) c left outer join
				(select * from report where portfolio = '$name' and date = CURRENT_DATE()) d on c.ticker = d.ticker) e 
					
EOD;

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'longposn', 'dt' => 0 ),
    array( 'db' => 'shortposn', 'dt' => 1 ),
    array( 'db' => 'delta', 'dt' => 2 ),
    array( 'db' => 'comms', 'dt' => 3 ),    
    array( 'db' => 'divs', 'dt' => 4 ),
    array( 'db' => 'grosspnl', 'dt' => 5 ),
    array( 'db' => 'netpnl', 'dt' => 6 ),
    array( 'db' => 'mtdpnl', 'dt' => 7 ),
    array( 'db' => 'mtdfees', 'dt' => 8 ),
    array( 'db' => 'mtddivs', 'dt' => 9 ),       
    array( 'db' => 'ytdpnl', 'dt' => 10 ),
    array( 'db' => 'ytdfees', 'dt' => 11 ),
    array( 'db' => 'ytddivs', 'dt' => 12 ),
);

// SQL server connection information
$sql_details = array(
    'user' => DB_PORT_USER,
    'pass' => DB_PORT_PASSWORD,
    'db'   => DB_PORT_DATABASE,
    'host' => DB_PORT_HOST
);

echo json_encode(
    SSP::simple_query( $_GET, $sql_details, $proc, $columns )
);

?>

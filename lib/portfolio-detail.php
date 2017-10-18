<?php
require('ssp.php');
require('../cfg/db.portfolios.php');
$name = "prod_hk";
// DB table to use
$proc = <<<EOD
select e.*, f.prevpx, f.lastpx, f.lastpx-f.prevpx as netchg, 100*((f.lastpx/f.prevpx)-1) as pctchg
  from (select COALESCE(c.ticker,c.ticker,d.ticker) as ticker, c.ytdpnl, c.ytdfees, c.ytddivs, c.mtdpnl, c.mtdfees, c.mtddivs, d.sodqty, d.buyqty, d.sellqty, d.eodqty as netqty, d.eodnot as delta, d.comms, d.divs, d.grosspnl, d.netpnl
	 from (select COALESCE(a.ticker,a.ticker,b.ticker) as ticker, a.ytdpnl, a.ytdfees, a.ytddivs, b.mtdpnl, b.mtdfees, b.mtddivs from 
		(select ticker, sum(netpnl) as ytdpnl, sum(comms) as ytdfees, sum(divs) as ytddivs from report where date >= DATE_FORMAT(CURRENT_DATE(), '%Y-01-01') and date <= CURRENT_DATE() and portfolio = '$name' group by ticker) a left outer join
			(select ticker, sum(netpnl) as mtdpnl, sum(comms) as mtdfees, sum(divs) as mtddivs from report where (date >= DATE_FORMAT(CURRENT_DATE(),'%Y-%m-01') and date <= CURRENT_DATE()) and portfolio = '$name' group by ticker) b on a.ticker = b.ticker) c left outer join
				(select * from report where portfolio = '$name' and date = CURRENT_DATE()) d on c.ticker = d.ticker) e left outer join 
					(select * from prices where date = CURRENT_DATE()) f on e.ticker = f.ticker
EOD;

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'ticker', 'dt' => 0 ),
    array( 'db' => 'sodqty', 'dt' => 1 ),
    array( 'db' => 'buyqty', 'dt' => 2 ),
    array( 'db' => 'sellqty', 'dt' => 3 ),
    array( 'db' => 'netqty', 'dt' => 4 ),
    array( 'db' => 'delta', 'dt' => 5 ),
    array( 'db' => 'comms', 'dt' => 6 ),    
    array( 'db' => 'divs', 'dt' => 7 ),
    array( 'db' => 'grosspnl', 'dt' => 8 ),
    array( 'db' => 'netpnl', 'dt' => 9 ),
    array( 'db' => 'prevpx', 'dt' => 10 ),
    array( 'db' => 'lastpx', 'dt' => 11 ),
    array( 'db' => 'netchg', 'dt' => 12 ),
    array( 'db' => 'pctchg', 'dt' => 13 ),
    array( 'db' => 'mtdpnl', 'dt' => 14 ),
    array( 'db' => 'mtdfees', 'dt' => 15 ),
    array( 'db' => 'mtddivs', 'dt' => 16 ),        
    array( 'db' => 'ytdpnl', 'dt' => 17 ),
    array( 'db' => 'ytdfees', 'dt' => 18 ),
    array( 'db' => 'ytddivs', 'dt' => 19 ),

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

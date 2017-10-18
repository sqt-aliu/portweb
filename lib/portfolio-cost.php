<?php
require('ssp.php');
require('../cfg/db.portfolios.php');
$name = "prod_hk";
// DB table to use
$proc = <<<EOD
select c.ticker, c.eodqty as netqty, coalesce(c.avgpx, 0) as avgpx, coalesce(d.lastpx, 0) as lastpx, IF (c.eodqty>0, coalesce(100*((d.lastpx/c.avgpx)-1), 0), coalesce(100*((c.avgpx/d.lastpx)-1), 0)) as pctchg, c.cost, c.eodnot as delta from 
	(select a.ticker, b.cost/a.eodqty as avgpx, b.cost, a.eodqty, a.eodnot from  (select ticker, eodqty, eodnot from report where date = CURRENT_DATE() and portfolio = '$name') a left outer join 
		(select ticker,  sum(buynot) - sum(sellnot) as cost from report where portfolio = '$name' group by ticker) b on a.ticker = b.ticker) c left outer join 
			(select * from prices where date = CURRENT_DATE()) d on c.ticker = d.ticker
EOD;

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'ticker', 'dt' => 0 ),
    array( 'db' => 'netqty', 'dt' => 1 ),    
    array( 'db' => 'avgpx', 'dt' => 2 ),
    array( 'db' => 'lastpx', 'dt' => 3 ),
    array( 'db' => 'pctchg', 'dt' => 4 ),
    array( 'db' => 'cost', 'dt' => 5 ),
    array( 'db' => 'delta', 'dt' => 6 )
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

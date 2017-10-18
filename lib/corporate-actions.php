<?php
require('ssp.php');
require('../cfg/db.equities.php');
$name = $_GET['name'];

// DB table to use
$proc = <<<EOD
select * from dvd where ticker = '$name'		
EOD;

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'ticker', 'dt' => 0 ),
    array( 'db' => 'date', 'dt' => 1 ),
    array( 'db' => 'dividend', 'dt' => 2 ),
    array( 'db' => 'split', 'dt' => 3 ),
    array( 'db' => 'source', 'dt' => 4 ),
);

// SQL server connection information
$sql_details = array(
    'user' => DB_EQTY_USER,
    'pass' => DB_EQTY_PASSWORD,
    'db'   => DB_EQTY_DATABASE,
    'host' => DB_EQTY_HOST
);

echo json_encode(
    SSP::simple_query( $_GET, $sql_details, $proc, $columns )
);

?>

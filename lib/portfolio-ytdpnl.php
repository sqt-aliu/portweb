<?php
// Set the JSON header
header("Content-type: text/json");

require('../cfg/db.portfolios.php');

$proc = <<<EOD
select sum(netpnl) as ytdpnl from report where portfolio like 'prod%'
EOD;
$result = 0;
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
    $pnl_stmt = $stmt->fetch();
    $result = (float)$pnl_stmt['ytdpnl'];
}
catch (PDOException $e) {
    print_r( "An SQL error occurred: ".$e->getMessage() );
}

// The x value is the current JavaScript time, which is the Unix time multiplied 
// by 1000.
$x = (time() + (8 * 60 * 60)) * 1000;
// The y value is a random number
$y = $result;

// Create a PHP array and echo it as JSON
$ret = array($x, $y);
echo json_encode($ret);
?>
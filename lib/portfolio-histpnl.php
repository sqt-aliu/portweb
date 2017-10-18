<?php
// Set the CSV header
#header("Content-type: text/csv");

require('../cfg/db.portfolios.php');
echo "date,netpnl\n";
$proc = <<<EOD
select date, sum(totpnl) as netpnl from totals where portfolio like 'prod%' group by date 
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
    $results = $stmt->fetchAll( PDO::FETCH_ASSOC ); // Return all
    
    foreach( $results as $row ){
        echo $row['date'].",".$row['netpnl']."\n";
    }    
}
catch (PDOException $e) {
    print_r( "An SQL error occurred: ".$e->getMessage() );
}

?>
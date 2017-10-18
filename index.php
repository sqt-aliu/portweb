<!DOCTYPE html>
<?php
require('lib/common.php');
require('cfg/db.portfolios.php');
require('cfg/db.equities.php');

function getUpcomingDvd() {
    $proc = "select * from dvd where date >= CURRENT_DATE() and date <= (DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY))";
    $results = "";
    $dvds = array();
    try {
        $db = @new PDO(
            "mysql:host=".DB_EQTY_HOST.";dbname=".DB_EQTY_DATABASE,
            DB_EQTY_USER,
            DB_EQTY_PASSWORD,
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
            $dvdrow = array();
            $dvdrow['DATE'] = $row['date'];
            $dvdrow['DIVIDEND'] = $row['dividend'];
            $dvdrow['SPLIT'] = $row['split'];
            $dvds[$row['ticker']] = $dvdrow;
        }
    }
    catch (PDOException $e) {
        print_r( "An SQL error occurred: ".$e->getMessage() );
    }     
    return($dvds);
}

function getPositions() {
    $proc = "select ticker, eodqty from portfolios.report where portfolio = 'prod_hk' and date = (select max(date) from portfolios.report)";
    $results = "";
    $positions = array();
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
            $positions[$row['ticker']] = $row['eodqty'];
        }
    }
    catch (PDOException $e) {
        print_r( "An SQL error occurred: ".$e->getMessage() );
    }     
    return($positions);
}

function getAdjustmentsCount() {
    $proc = "select count(*) as adjustments from corpactions where date = CURRENT_DATE() and portfolio like 'prod%'";
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
        $adj_stmt = $stmt->fetch();
        $result = (int)$adj_stmt['adjustments'];
    }
    catch (PDOException $e) {
        print_r( "An SQL error occurred: ".$e->getMessage() );
    }
    return ($result);
}

function getTradeCount() {
    $proc = "select count(*) as trades from trades where date = CURRENT_DATE() and portfolio like 'prod%'";
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
        $trades_stmt = $stmt->fetch();
        $result = (int)$trades_stmt['trades'];
    }
    catch (PDOException $e) {
        print_r( "An SQL error occurred: ".$e->getMessage() );
    }
    return ($result);
}

$trades = getTradeCount();
$adjustments = getAdjustmentsCount();
$corpactions = getUpcomingDvd();
$positions = getPositions();
$anrs = 0;

?>
<html lang="en">
<head>
    <?php include('inc/header.php') ?>
    <title>Home | Huatai Capital Investment Limited</title>
</head>
<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $anrs ?></div>
                                    <div>New Analyst Revisions!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $adjustments ?></div>
                                    <div>New Corporate Actions!</div>
                                </div>
                            </div>
                        </div>
                        <a href="corp-actions-report.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-support fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $trades ?></div>
                                    <div>New Trades!</div>
                                </div>
                            </div>
                        </div>
                        <a href="trades-report.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Upcoming Corporate Actions
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                           <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead class="custom">
                                    <tr>
                                        <th align='right'>Ticker</th>
                                        <th align='right'>Net Qty</th>
                                        <th align='right'>Ex-Date</th>
                                        <th align='right'>Dividend</th>
                                        <th align='right'>Split</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach( $corpactions as $key => $value ){
                                    if (array_key_exists($key, $positions)) {
                                        echo "<tr>";
                                        echo "<td>";
                                        echo "<a href='ticker.php?name=".$key."'>".$key."</a>";
                                        echo "</td><td>";
                                        echo $positions[$key];
                                        echo "</td><td>";
                                        echo $value['DATE'];
                                        echo "</td><td>";
                                        echo $value['DIVIDEND'];
                                        echo "</td><td>";
                                        echo $value['SPLIT'];
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }                                
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
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

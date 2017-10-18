<!DOCTYPE html>
<?php 
require('lib/common.php');
$name = $_GET['name'];

$ticker_sedol = "";
$ticker_name = "";
$ticker_ccy = "";
$ticker_lotsz = "";
$ticker_mktcap = "";
$ticker_sectype = "";
$ticker_sector = "";
$ticker_indgroup = "";
$ticker_industry = "";
$ticker_subind = "";
$ticker_restricted = FALSE;

function readRestrictedList( )  {
    $tickers = array();
    $files = glob('/home/sqtdata/dfs/raw/live/rest.day/*/*.restricted.csv'); 
    if (($handle = fopen( end($files), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($data[0] <> 'code') {
                array_push($tickers, restrictedListTicker($data[0], $data[1]));
            }
        }
        fclose($handle);
    }
    return ($tickers);
}
function readUniverse( $mic, $ticker ) {
    $tickerUniv = array();
    $bbgTicker = tickerBloomberg($ticker);
    $files = glob("/home/sqtdata/dfs/raw/live/blmg.day/*/".$mic.".univ.*.csv");
    if (($handle = fopen( end($files), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($data[0] <> 'ticker') {
                if ($data[0] == $bbgTicker) {
                    $tickerUniv['sedol'] = $data[1];
                    $tickerUniv['name'] = $data[2];
                    $tickerUniv['currency'] = $data[4];
                    $tickerUniv['lotsize'] = $data[5];
                    $tickerUniv['marketcap'] = $data[6];
                    $tickerUniv['sectype'] = $data[7];
                    $tickerUniv['sector'] = $data[8];
                    $tickerUniv['indgroup'] = $data[9];
                    $tickerUniv['industry'] = $data[10];
                    $tickerUniv['subind'] = $data[11];
                }
            }
        }
        fclose($handle);
    }
    return ($tickerUniv);
}

if (strlen($name) > 0) {
    $restricted = readRestrictedList();
    if ( endsWith( $name, ".HK" )) {
        $hk_ticker_info = readUniverse("xhkg", $name);
        $ticker_sedol = $hk_ticker_info['sedol'];
        $ticker_name = $hk_ticker_info['name'];
        $ticker_ccy = $hk_ticker_info['currency'];
        $ticker_lotsz = $hk_ticker_info['lotsize'];
        $ticker_mktcap = $hk_ticker_info['marketcap'];
        $ticker_sectype = $hk_ticker_info['sectype'];
        $ticker_sector = $hk_ticker_info['sector'];
        $ticker_indgroup = $hk_ticker_info['indgroup'];
        $ticker_industry = $hk_ticker_info['industry'];
        $ticker_subind = $hk_ticker_info['subind']; 
    }
    if ( in_array( $name, $restricted )) {
        $ticker_restricted = TRUE;
    }
}

?>
<html lang="en">
<head>
    <?php include('inc/header.php') ?>
    <title><?php echo $name ?> | Huatai Capital Investment Limited</title>
    <script type="text/javascript" class="init">
    $(document).ready(function() {
        var anrtable = $('#dataTables-anr').DataTable({ 
            "order": [[ 7, "desc" ]],
            "processing": true,
            "serverSide": false,
            "ajax": "lib/analyst-revisions.php?name=<?php echo $name; ?>",  
            "columnDefs": [
                { targets: [0,2], visible: false },                    
                { targets: [1,3,4,5,6,7,8], visible: true },   
                { targets: [6,8], render: function ( data, type, row )  {
                      if (data >= 0) {
                        return data;
                      } else {
                        return 'N/A';
                      }
                }},                   
            ],            
            responsive: true
        });
        
        var corpactionstable = $('#dataTables-corpactions').DataTable({ 
            "order": [[ 1, "desc" ]],
            "processing": true,
            "serverSide": false,
            "ajax": "lib/corporate-actions.php?name=<?php echo $name; ?>",  
            "columnDefs": [
                { targets: [0,4], visible: false },                                       
            ],            
            responsive: true
        });        
        
        setInterval( function () {
            anrtable.ajax.reload(null, false);
            corpactionstable.ajax.reload(null, false);
        }, 60000 );         
    });
    </script>    
</head>
<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Ticker: 
                    <?php 
                    echo $name; 
                    if ($ticker_restricted) {
                        echo "<small style='color:#ff0000'> (Restricted)</small>";
                    }                    
                    ?>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">

            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Description
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td width="20%">Name:</td>
                                    <td width="30%"><?php echo $ticker_name ?></td>
                                    <td width="20%">Sedol:</td>
                                    <td width="30%"><?php echo $ticker_sedol ?></td>      
                                </tr>
                                <tr>
                                    <td width="20%">Market Cap:</td>
                                    <td width="30%"><?php echo $ticker_mktcap ?></td>
                                    <td width="20%">Sec Type:</td>
                                    <td width="30%"><?php echo $ticker_sectype ?></td>                                          
                                </tr>
                                </tr>
                                <tr>
                                    <td width="20%">Lot Size:</td>
                                    <td width="30%"><?php echo $ticker_lotsz ?></td>
                                    <td width="20%">Currency:</td>
                                    <td width="30%"><?php echo $ticker_ccy ?></td>                                          
                                </tr>        
                                <tr>
                                    <td width="20%">Sector:</td>
                                    <td width="30%"><?php echo $ticker_sector ?></td>
                                    <td width="20%">Industry:</td>
                                    <td width="30%"><?php echo $ticker_industry ?></td>                                          
                                </tr>    
                                <tr>
                                    <td width="20%">Industry Group:</td>
                                    <td width="30%"><?php echo $ticker_indgroup ?></td>
                                    <td width="20%">Sub Industry:</td>
                                    <td width="30%"><?php echo $ticker_subind ?></td>                                          
                                </tr>                                       
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Corporate Actions
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-corpactions">
                                <thead>
                                    <tr>
                                        <th align='right'>Ticker</th>
                                        <th align='right'>Date</th>
                                        <th align='right'>Dividend</th>
                                        <th align='right'>Split</th>
                                        <th align='right'>Source</th>
                                    </tr>
                                </thead>
                            </table>                           
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Analyst Ratings
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-anr">
                                <thead>
                                    <tr>
                                        <th align='right'>Ticker</th>
                                        <th align='right'>Company</th>
                                        <th align='right'>Analyst</th>
                                        <th align='right'>Recommendation</th>
                                        <th align='right'>Rating</th>
                                        <th align='right'>Action</th>
                                        <th align='right'>Target</th>
                                        <th align='right'>Date</th>
                                        <th align='right'>BARR</th>
                                    </tr>
                                </thead>
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

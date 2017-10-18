<!DOCTYPE html>
<?php 
require('lib/common.php'); 
?>
<html lang="en">
<head>
    <?php include('inc/header.php') ?>
    <title>HKG Top/Bottom Scores | Huatai Capital Investment Limited</title>
    <script>
    $(document).ready(function() {
        var table = $('#dataTables-example').DataTable({ 
            "order": [[ 0, "asc" ]],
            "lengthChange": false,                
            "pageLength": 50,
            "processing": false,
            "serverSide": false,
            "columnDefs": [
                { targets: [2,4,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33], visible: false },
                { targets: [5,6,34,35], className: "dt-right" },
                { targets: [6], render: $.fn.dataTable.render.number( ',', '.', 0 ) },   
                { targets: [34,35], render: $.fn.dataTable.render.number( ',', '.', 3 ) },       
            ],                 
            "buttons": [
                'pageLength', 
                'colvis',
                'copy', 
                'csv', 
                'excel', 
                {
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'LEGAL'
                },
                'print'
            ],
            
            responsive: true
        });
        
        table.buttons().container().appendTo( '#dataTables-example_wrapper .col-sm-6:eq(0)' );              
    });
    </script>    
</head>
<body>
    <div id="wrapper">
        <?php include('inc/navbar.php') ?>    

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">HKG Top/Bottom Scores</h1>
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
                        <?php
                            $files = glob('/bca/scores/*.bca.*.asof.*.cap.csv');
                            echo "Most Recent BCA Scores:  <small>".basename(end($files), '.csv')."</small>";
                        ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Name</th>
                                        <th>Exchange</th>
                                        <th>Sector</th>
                                        <th>Industry Grp.</th>
                                        <th>Last Price</th>
                                        <th>Market Cap. (USD)</th>
                                        <th>P/E (Score)</th>
                                        <th>Fwd. P/E (Score)</th>
                                        <th>P/Book (Score)</th>
                                        <th>P/Sales (Score)</th>
                                        <th>P/Cash Flow (Score)</th>
                                        <th>Debt/Assets (Score)</th>
                                        <th>Altman-Z (Score)</th>
                                        <th>Size (Score)</th>
                                        <th>Volatility (Score)</th>
                                        <th>Beta (Score)</th>
                                        <th>Liquidity (Score)</th>
                                        <th>Div. Yield (Score)</th>
                                        <th>Chg. Shrs. Outsd. (Score)</th>
                                        <th>Accruals (Score)</th>
                                        <th>Asset Growth (Score)</th>
                                        <th>ROE (Score)</th>
                                        <th>1M Mean-Rev. (Score)</th>
                                        <th>1Y Momentum (Score)</th>
                                        <th>3Y Mean-Rev. (Score)</th>
                                        <th>Earn. Momentum (Score)</th>
                                        <th>Insider Buying (Score)</th>
                                        <th>Insider Selling (Score)</th>
                                        <th>Chg. Analyst Rec. (Score)</th>
                                        <th>% Chg. Inst. Inv (Score)</th>
                                        <th>Visibility (Score)</th>
                                        <th>BCA Sector (Score)</th>
                                        <th>BCA Style (Score)</th>
                                        <th>BCA Score</th>
                                        <th>BCA Scr. Chg.</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Name</th>
                                        <th>Exchange</th>
                                        <th>Sector</th>
                                        <th>Industry Grp.</th>
                                        <th>Last Price</th>
                                        <th>Market Cap. (USD)</th>
                                        <th>P/E (Score)</th>
                                        <th>Fwd. P/E (Score)</th>
                                        <th>P/Book (Score)</th>
                                        <th>P/Sales (Score)</th>
                                        <th>P/Cash Flow (Score)</th>
                                        <th>Debt/Assets (Score)</th>
                                        <th>Altman-Z (Score)</th>
                                        <th>Size (Score)</th>
                                        <th>Volatility (Score)</th>
                                        <th>Beta (Score)</th>
                                        <th>Liquidity (Score)</th>
                                        <th>Div. Yield (Score)</th>
                                        <th>Chg. Shrs. Outsd. (Score)</th>
                                        <th>Accruals (Score)</th>
                                        <th>Asset Growth (Score)</th>
                                        <th>ROE (Score)</th>
                                        <th>1M Mean-Rev. (Score)</th>
                                        <th>1Y Momentum (Score)</th>
                                        <th>3Y Mean-Rev. (Score)</th>
                                        <th>Earn. Momentum (Score)</th>
                                        <th>Insider Buying (Score)</th>
                                        <th>Insider Selling (Score)</th>
                                        <th>Chg. Analyst Rec. (Score)</th>
                                        <th>% Chg. Inst. Inv (Score)</th>
                                        <th>Visibility (Score)</th>
                                        <th>BCA Sector (Score)</th>
                                        <th>BCA Style (Score)</th>
                                        <th>BCA Score</th>
                                        <th>BCA Scr. Chg.</th>
                                    </tr>
                                </tfoot>  
                                <tbody>
                                <?php
                                    if (($handle = fopen( end($files), "r")) !== FALSE) {
                                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                            if (($data[0] <> 'Ticker') && ($data[2] == 'SEHK') && ($data[34] >= .80 || $data[34] <= .20)) {
                                                echo "<tr>\n";
                                                echo "<td><a href='ticker.php?name=".bcaTicker($data[0])."'>".$data[0]."</a></td>\n";
                                                echo "<td>".$data[1]."</td>\n";
                                                echo "<td>".$data[2]."</td>\n";
                                                echo "<td>".$data[3]."</td>\n";
                                                echo "<td>".$data[4]."</td>\n";
                                                echo "<td>".$data[5]."</td>\n";
                                                echo "<td>".$data[6]."</td>\n";
                                                echo "<td>".$data[7]."</td>\n";
                                                echo "<td>".$data[8]."</td>\n";
                                                echo "<td>".$data[9]."</td>\n";
                                                echo "<td>".$data[10]."</td>\n";
                                                echo "<td>".$data[11]."</td>\n";
                                                echo "<td>".$data[12]."</td>\n";
                                                echo "<td>".$data[13]."</td>\n";
                                                echo "<td>".$data[14]."</td>\n";
                                                echo "<td>".$data[15]."</td>\n";
                                                echo "<td>".$data[16]."</td>\n";
                                                echo "<td>".$data[17]."</td>\n";
                                                echo "<td>".$data[18]."</td>\n";
                                                echo "<td>".$data[19]."</td>\n";
                                                echo "<td>".$data[20]."</td>\n";
                                                echo "<td>".$data[21]."</td>\n";
                                                echo "<td>".$data[22]."</td>\n";
                                                echo "<td>".$data[23]."</td>\n";
                                                echo "<td>".$data[24]."</td>\n";
                                                echo "<td>".$data[25]."</td>\n";
                                                echo "<td>".$data[26]."</td>\n";
                                                echo "<td>".$data[27]."</td>\n";
                                                echo "<td>".$data[28]."</td>\n";
                                                echo "<td>".$data[29]."</td>\n";
                                                echo "<td>".$data[30]."</td>\n";
                                                echo "<td>".$data[31]."</td>\n";
                                                echo "<td>".$data[32]."</td>\n";
                                                echo "<td>".$data[33]."</td>\n";
                                                echo "<td>".$data[34]."</td>\n";
                                                echo "<td>".$data[35]."</td>\n";
                                                echo "</tr>";       
                                            }
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

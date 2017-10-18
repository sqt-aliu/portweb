<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('inc/header.php') ?>
    <title>Stock Borrow Availability | Huatai Capital Investment Limited</title>
    <script>
    $(document).ready(function() {
        var table = $('#dataTables-example').DataTable({
            "order": [[ 0, "asc" ]],
            "lengthChange": false,
            "pageLength": 50,
            "processing": false,
            "serverSide": false,
            "buttons": [
                'pageLength', 
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
                    <h1 class="page-header">Stock Borrow Availability</h1>
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
                            $files_jpy = glob('/home/sqtdata/dfs/raw/live/nomu.day/*/*.jpy.csv');
                            $files_hkd = glob('/home/sqtdata/dfs/raw/live/nomu.day/*/*.hkd.csv');
                            echo "Stock Borrows:  <small>".basename(end($files_jpy), '.csv')." , ".basename(end($files_hkd), '.csv')."</small>";
                         ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead class="custom">
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Fee</th>
                                   </tr>
                               </thead>
                                <tfoot class="custom">
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Fee</th>
                                   </tr>
                               </tfoot>               
                               <tbody>
                                <?php
                                    function translateRestrictedList($code, $exch) {
                                        $ticker = $code.".".$exch;
                                        if ($exch == "HK") {
                                            $ticker = str_pad($code,4,"0",STR_PAD_LEFT).".".$exch;
                                        }
                                        return($ticker);
                                    }

                                    if (($handle = fopen( end($files_hkd), "r")) !== FALSE) {
                                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                            if ($data[0] <> 'Market') {
                                                echo "<tr>\n";
                                                echo "<td>".$data[3]."</td>\n";
                                                echo "<td>".$data[4]."</td>\n";
                                                echo "<td>".$data[5]."</td>\n";
                                                echo "<td>".$data[6]."</td>\n";
                                                echo "</tr>";     
                                            }
                                        }
                                        fclose($handle);
                                    }                                          
                                
                                    if (($handle = fopen( end($files_jpy), "r")) !== FALSE) {
                                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                            if ($data[0] <> 'Code') {
                                                echo "<tr>\n";
                                                echo "<td>".$data[0]."</td>\n";
                                                echo "<td>".$data[1]."</td>\n";
                                                echo "<td>".$data[5]."</td>\n";
                                                echo "<td>".$data[6]."</td>\n";
                                                echo "</tr>";                                                    
                                            }
                                        }
                                        fclose($handle);
                                    }
                                ?>
                                </tbody> 
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

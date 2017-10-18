<!DOCTYPE html>
<?php 
require('lib/common.php');
?>
<html lang="en">
<head>
    <?php 
    include('inc/header.php') 
    ?>
    <title>Restricted List | Huatai Capital Investment Limited</title>
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
                    <h1 class="page-header">Restricted List</h1>
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
                            $files = glob('/home/sqtdata/dfs/raw/live/rest.day/*/*.restricted.csv');
                            echo "Restricted List:  <small>".basename(end($files), '.csv')."</small>";
                         ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead class="custom">
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Code</th>
                                        <th>Exchange</th>
                                   </tr>
                               </thead>
                                <tfoot class="custom">
                                    <tr>
                                        <th>Ticker</th>
                                        <th>Code</th>
                                        <th>Exchange</th>
                                   </tr>
                               </tfoot>               
                               <tbody>
                                <?php
                                    if (($handle = fopen( end($files), "r")) !== FALSE) {
                                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                                            if ($data[0] <> 'code') {
                                                $ticker = restrictedListTicker($data[0], $data[1]);
                                                echo "<tr>\n";
                                                echo "<td><a href='ticker.php?name=".$ticker."'>".$ticker."</td>\n";
                                                echo "<td>".$data[0]."</td>\n";
                                                echo "<td>".$data[1]."</td>\n";
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

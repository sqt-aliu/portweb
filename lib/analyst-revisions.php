<?php
require('common.php');
$name = $_GET['name'];
$row = 0;
$records = array();
if (!empty($name)) {
    $bbg = tickerBloomberg($name);
    $mic = "xhkg";
    if ( endsWith( $name, ".HK" )) {
        $mic = "xhkg";
    } elseif ( endsWith( $name, ".JP" )) {
        $mic = "xtks";
    }
    $files = glob("/home/sqtdata/dfs/raw/live/blmg.day/*/".$mic.".anr.*.csv");
    if (($handle = fopen( end($files), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if (($data[0] <> 'Code') && ($bbg == $data[0])) {
                array_push($records, $data);  
                $row++;
            }
        }
        fclose($handle);
    }            
}

echo json_encode(array(
    "draw"              => 0,
    "recordsTotal"      => $row,
    "recordsFiltered"   => $row,
    "data"              => $records
));

?>
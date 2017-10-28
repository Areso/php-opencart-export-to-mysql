<?php
if (is_file('config.php')) {
        require_once('config.php');
}
/* vars for export */
// database record to be exported
$db_record = 'oc_product';
// optional where query
$where = 'WHERE 1 ORDER BY 1';
// filename for export
$csv_filename = 'db_export_'.$db_record.'_'.date('Y-m-d').'.csv';
// database variables
//$DB_HOSTNAME = "localhost";
//$DB_USERNAME = "XXXXXXXXX";
//$DB_PASSWORD = "XXXXXXXXX";
//$DB_DATABASE = "XXXXXXXXX";
// Database connecten voor alle services
mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD)
or die('Could not connect: ' . mysql_error());
					
mysql_select_db(DB_DATABASE)
or die ('Could not select database ' . mysql_error());
// create empty variable to be filled with export data
$csv_export = '';
// query to get data from database
$query = mysql_query("SELECT * FROM ".$db_record." ".$where);
$field = mysql_num_fields($query);
//echo 'test';
//create array of skipped columns
$skip_columns = array(2, 3, 4, 5, 6, 7, 8);
//echo $skip_columns;
//echo 'test2'; 
//if (in_array("uk", $os))
// create line with field names
for($i = 0; $i < $field; $i++) {
  if (in_array($i, $skip_columns)) {
    $i = $i;
  } else {
    $csv_export.= mysql_field_name($query,$i).';';
  }
}
// newline (seems to work both on Linux & Windows servers)
$csv_export.= '
';
// loop through database query and fill export variable
while($row = mysql_fetch_array($query)) {
  // create line with field values
  for($i = 0; $i < $field; $i++) {
    if (in_array($i, $skip_columns)) {
      $i = $i;
      // echo '"'.$row[mysql_field_name($query,$i)].'";';
      //echo $i; //debug purposes;
      //nothing to do 
    } else {
      if ($i != 11 ) {
        $csv_export.= '"'.$row[mysql_field_name($query,$i)].'";';
      } else {
        $csv_export.= 'http://ayumi-land.com/image/cache/';
        $tmp = ''.$row[mysql_field_name($query,$i)].';';
        $tmp = str_ireplace(".jpg","-500x500.jpg",$tmp); 
        //$csv_export.= ''.$row[mysql_field_name($query,$i)].';';
        $csv_export.= $tmp;
      }
    }
  }	
  $csv_export.= '
';	
}
// Export the data and prompt a csv file for download
header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");
echo($csv_export);
?>

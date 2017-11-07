<?php
if (is_file('config.php')) {
        require_once('config.php');
}
ini_set("default_charset",'utf-8');
/* vars for export */
// database record to be exported
$db_record = 'oc_product';
// optional where query
$where = 'WHERE oc_product.id < 66 ORDER BY oc_product.product_id';
// filename for export
$csv_filename = 'db_export_'.$db_record.'_'.date('Y-m-d').'.csv';
// database variables
/*
$hostname = "XXX_HOSTNAME_XXX";
$user     = "XXX_USER_XXX";
$password = "XXX_PASS_XXX";
$database = "XXX_DATABASE_XXX";
$port = 3306;
*/
$conn = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
//mysqli_set_charset("utf8", $conn);
//printf("Initial character set: %s\n", mysqli_character_set_name($conn));

/* change character set to utf8 */
if (!mysqli_set_charset($conn, "utf8")) {
  //  printf("Error loading character set utf8: %s\n", mysqli_error($conn));
    exit();
} else {
  //  printf("Current character set: %s\n", mysqli_character_set_name($conn));
}
// create empty variable to be filled with export data
$csv_export = '';
$query_line = "SELECT 
oc_product.product_id, model, oc_product.quantity, image, oc_product.price as MSRP, shipping, 
name, description,
oc_product_discount.price as bulk_price,  
oc_product_special.price as discount_price, oc_product_special.date_start as discount_date_start, oc_product_special.date_end as discount_date_end 
FROM oc_product
LEFT OUTER JOIN oc_product_description
ON oc_product.product_id = oc_product_description.product_id
LEFT JOIN oc_product_discount
ON oc_product.product_id = oc_product_discount.product_id
LEFT OUTER JOIN oc_product_special
ON oc_product.product_id = oc_product_special.product_id
WHERE oc_product.quantity > 30
ORDER BY oc_product.product_id";
// query it is object to get data from database and to store it
$query = mysqli_query($conn, $query_line); 
//".$db_record." ".$where);
$field = mysqli_field_count($conn);
// create line with field names
for($i = 0; $i < $field; $i++) {
    $csv_export.= mysqli_fetch_field_direct($query, $i)->name.';';
}
// newline (seems to work both on Linux & Windows servers)
$csv_export.= '
';
// loop through database query and fill export variable
while($row = mysqli_fetch_array($query)) {
    // create line with field values
    for($i = 0; $i < $field; $i++) {
		$flag = 0;
		
		if ($i==0) {
			$product_id	= $row[mysqli_fetch_field_direct($query, $i)->name];	
		}
		
		if ($i==3) {
			$csv_export.= 'http://ayumi-land.com/image/cache/';
        	$tmp = ''.$row[mysqli_fetch_field_direct($query, $i)->name].';';
        	$tmp = str_ireplace(".jpg","-500x500.jpg",$tmp);
        	$csv_export.= $tmp;
			$flag = 1;
		}

		if ($i==5) {
			$csv_export.="http://ayumi-land.com/index.php?route=product/product&product_id=".$product_id.';';
			$flag = 1;
		}
		
		if ($flag==0) {
		
			$csv_export.= '"'.$row[mysqli_fetch_field_direct($query, $i)->name].'";';
		}
    }
    $csv_export.= '
';
}
// Export the data and prompt a csv file for download
header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");
echo($csv_export);

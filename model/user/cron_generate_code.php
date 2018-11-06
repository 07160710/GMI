<?php
require_once("include/conn.php");
require_once("function.php");

$sql = "SELECT id FROM company WHERE code IS NULL";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$company_id = $row[0];
		$code = generate_code($company_id);
		echo "Generate binding code [$code] for company [$company_id] successfully!<br/>";
	}
}
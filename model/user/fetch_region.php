<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$region = $doc->createElement("region");
$doc->appendChild($region);

$province_id = $_REQUEST['province'];
$city_id = $_REQUEST['city'];

if($_REQUEST['action']=="get_city"){
	$sql = "SELECT DISTINCT id,name 
			FROM region 
			WHERE parent_id='$province_id'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){		
			$city = $doc->createElement("city");
			$region->appendChild($city);
			
			$id = $doc->createElement("id",$row[0]);
			$city->appendChild($id);
			
			$name = $doc->createElement("name",htmlspecialchars($row[1]));
			$city->appendChild($name);
		}
	}
}
if($_REQUEST['action']=="get_district"){
	if($city_id!=""){
		$sql = "SELECT DISTINCT id,name 
				FROM region 
				WHERE parent_id='$city_id'";
		$stmt = mysql_query($sql);
		if(mysql_num_rows($stmt)>0){
			while($row = mysql_fetch_array($stmt)){
				$district = $doc->createElement("district");
				$region->appendChild($district);
				
				$id = $doc->createElement("id",$row[0]);
				$district->appendChild($id);
				
				$name = $doc->createElement("name",htmlspecialchars($row[1]));
				$district->appendChild($name);
			}
		}
	}
}

echo $doc->saveXML();
?>
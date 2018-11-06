<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$platform = $doc->createElement("platform");
$doc->appendChild($platform);

$platform_id = $_REQUEST['platform_id'];

$select_field_str = "";
foreach($GLOBALS['platform_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT $select_field_str 
		FROM platform 
		WHERE id='$platform_id'";
$get_platform = mysql_query($sql);
if(mysql_num_rows($get_platform)>0){
	$row = mysql_fetch_array($get_platform);
	
	foreach($GLOBALS['platform_fields'] as $key){
		${$key} = $row[$key];
		
		${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
		$platform->appendChild(${'r_'.$key});
	}
}

echo $doc->saveXML();
?>
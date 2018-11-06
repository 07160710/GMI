<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$object = mysql_escape_string($_REQUEST['object']);
$keyword = mysql_escape_string($_REQUEST['keyword']);

$results = $doc->createElement("results");
$doc->appendChild($results);

if($object=="project"){
	$object = "pbase";
	$cond_str = "AND type='p'";
}
if($object=="agreement"){
	$object = "pbase";
	$cond_str = "AND type='a'";
}

$sql = "SELECT name  
		FROM ".$object." 
		WHERE name LIKE '%".$keyword."%' $cond_str 
		ORDER BY CONVERT(name USING GBK)";
$get_result = mysql_query($sql);
if(mysql_num_rows($get_result)>0){
	while($row = mysql_fetch_array($get_result)){		
		$result = $doc->createElement("result",htmlspecialchars($row[0]));
		$results->appendChild($result);;
	}
}

echo $doc->saveXML();
?>
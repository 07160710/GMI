<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = $_REQUEST['per_page'];

$doc = new DOMDocument('1.0', 'UTF-8');

$platforms = $doc->createElement("platforms");
$doc->appendChild($platforms);

$cond_str = "";
include_once("fetch_platform_cond.php");

$sql = "SELECT COUNT(*) 
		FROM platform 
		$cond_str";
$platform_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("platform_num",$platform_num);
$platforms->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$platforms->appendChild($curr_p);

$page_num = ceil($platform_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$platforms->appendChild($p_num);

$sort_str = "";
if($_REQUEST['sort']){
	foreach($_REQUEST['sort'] as $sort){
		$sort_arr = explode("|",$sort);
		if($sort_str!="")$sort_str .= ",";		
		$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
	}
}
if($sort_str!=""){	
	$sort_str = "ORDER BY ".$sort_prefix.$sort_str;
}
else{
	$sort_str = "ORDER BY CONVERT(name USING GBK)";
}

$select_field_str = "";
foreach($GLOBALS['platform_list_fields'] as $key=>$val){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT 
			id,
			$select_field_str 
		FROM platform 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_platform = mysql_query($sql);
if(mysql_num_rows($get_platform)>0){
	while($row = mysql_fetch_array($get_platform)){		
		$platform = $doc->createElement("platform");
		$platforms->appendChild($platform);
		
		$platform_id = $doc->createElement("platform_id",$row['id']);
		$platform->appendChild($platform_id);
		
		foreach($GLOBALS['platform_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			if($key=="url")$url = "<a href=\"$url\" target=\"_blank\">$url</a>";			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$platform->appendChild(${'r_'.$key});
		}
	}
}

echo $doc->saveXML();
?>
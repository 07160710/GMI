<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = 20;

$doc = new DOMDocument('1.0', 'UTF-8');

$cbases = $doc->createElement("cbases");
$doc->appendChild($cbases);

$cond_str = "";
include_once("fetch_cbase_cond.php");

$sql = "SELECT COUNT(*) 
		FROM cbase 
		$cond_str";
$cbase_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("cbase_num",$cbase_num);
$cbases->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$cbases->appendChild($curr_p);

$page_num = ceil($cbase_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$cbases->appendChild($p_num);

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
	$sort_str = "ORDER BY year DESC, type, CONVERT(city USING GBK), CONVERT(company USING GBK)";
}

$select_field_str = "";
foreach($GLOBALS['cbase_list_fields'] as $key=>$val){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT 
			id,
			$select_field_str 
		FROM cbase 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_cbase = mysql_query($sql);
if(mysql_num_rows($get_cbase)>0){
	while($row = mysql_fetch_array($get_cbase)){		
		$cbase = $doc->createElement("cbase");
		$cbases->appendChild($cbase);
		
		$id = $row['id'];
		$c_id = $doc->createElement('c_id',$id);
		$cbase->appendChild($c_id);
		
		foreach($GLOBALS['cbase_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			
			if($key=="bonus")$bonus = ($bonus>0)?$bonus:"-";
			
			if($key=="imported_by"){
				$sql = "SELECT user.name FROM cbase LEFT JOIN user ON cbase.imported_by=user.id WHERE cbase.id='$id'";
				$imported_by = mysql_result(mysql_query($sql),0);
			}
			if($key=="imported_time")$imported_time = date('m/d H:i',$imported_time);
			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$cbase->appendChild(${'r_'.$key});
		}		
	}
}

echo $doc->saveXML();
?>
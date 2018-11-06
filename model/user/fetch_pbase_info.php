<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$pbase = $doc->createElement("pbase");
$doc->appendChild($pbase);

$pbase_id = $_REQUEST['pbase_id'];

$select_field_str = "";
foreach($GLOBALS['pbase_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT $select_field_str 
		FROM pbase 
		WHERE id='$pbase_id'";
$get_pbase = mysql_query($sql);
if(mysql_num_rows($get_pbase)>0){
	$row = mysql_fetch_array($get_pbase);
	
	foreach($GLOBALS['pbase_fields'] as $key){
		${$key} = $row[$key];
		
		${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
		$pbase->appendChild(${'r_'.$key});
	}
	
	$sql = "SELECT * FROM pbase_info WHERE pbase_id='$pbase_id' ORDER BY year_apply DESC, level, apply_deadline_e DESC, apply_deadline_p DESC";
	$get_info = mysql_query($sql);
	if(mysql_num_rows($get_info)>0){
		while($i_row = mysql_fetch_array($get_info)){
			$year_apply = $i_row['year_apply'];
			$level= $i_row['level'];
			$region = $i_row['region'];
			$apply_deadline_e = $i_row['apply_deadline_e'];
			$apply_deadline_p = $i_row['apply_deadline_p'];
			
			$info = $doc->createElement("info");
			$pbase->appendChild($info);
			
			$i_tmp_id = $doc->createElement("tmp_id",md5($year_apply.$level.$region));
			$info->appendChild($i_tmp_id);
			
			$i_year_apply = $doc->createElement("year_apply",$year_apply);
			$info->appendChild($i_year_apply);
			
			$i_level = $doc->createElement("level",$level);
			$info->appendChild($i_level);
			
			$region_arr = explode(",",$region);
			$province = $region_arr[0];
			$r_province = $doc->createElement("province",$province);
			$info->appendChild($r_province);
			
			$city = $region_arr[1];
			$r_city = $doc->createElement("city",$city);
			$info->appendChild($r_city);
			
			$district = $region_arr[2];
			$r_district = $doc->createElement("district",$district);
			$info->appendChild($r_district);
			
			$i_apply_deadline_e = $doc->createElement("apply_deadline_e",$apply_deadline_e);
			$info->appendChild($i_apply_deadline_e);
			
			$i_apply_deadline_p = $doc->createElement("apply_deadline_p",$apply_deadline_p);
			$info->appendChild($i_apply_deadline_p);
		}
	}
}

echo $doc->saveXML();
?>
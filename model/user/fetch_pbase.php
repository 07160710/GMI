<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = $_REQUEST['per_page'];

$doc = new DOMDocument('1.0', 'UTF-8');

$pbases = $doc->createElement("pbases");
$doc->appendChild($pbases);

$cond_str = "";
include_once("fetch_pbase_cond.php");

$sql = "SELECT COUNT(*) 
		FROM pbase 
		$cond_str";
$pbase_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("pbase_num",$pbase_num);
$pbases->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$pbases->appendChild($curr_p);

$page_num = ceil($pbase_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$pbases->appendChild($p_num);

$sort_str = "";
if($_REQUEST['sort']){
	foreach($_REQUEST['sort'] as $sort){
		$sort_arr = explode("|",$sort);
		if($sort_str!="")$sort_str .= ",";
		else $sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
	}
}
if($sort_str!=""){	
	$sort_str = "ORDER BY ".$sort_prefix.$sort_str;
}
else{
	$sort_str = "ORDER BY CONVERT(name USING GBK)";
}

$select_field_str = "";
foreach($GLOBALS['pbase_list_fields'] as $key=>$val){
	if(	$key!="year_apply" && 
		$key!="count" && 
		$key!="level" && 
		$key!="region" && 
		$key!="apply_deadline_e" && 
		$key!="apply_deadline_p"
	){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= $key;
	}
}

$sql = "SELECT 
			id,
			$select_field_str 
		FROM pbase 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_pbase = mysql_query($sql);
if(mysql_num_rows($get_pbase)>0){
	while($row = mysql_fetch_array($get_pbase)){		
		$pbase = $doc->createElement("pbase");
		$pbases->appendChild($pbase);
		
		$pbase_id = $row['id'];
		$r_pbase_id = $doc->createElement("pbase_id",$pbase_id);
		$pbase->appendChild($r_pbase_id);
		
		foreach($GLOBALS['pbase_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			
			if($key=="type")$type = $GLOBALS['pbase_type_opt'][$type];
			if($key=="count"){
				if($type=="项目"){
					$sql = "SELECT COUNT(*) FROM project WHERE name='$name'";
				}
				if($type=="协议"){
					$sql = "SELECT COUNT(*) FROM agreement WHERE name='$name'";
				}
				$count = mysql_result(mysql_query($sql),0);
			}
			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$pbase->appendChild(${'r_'.$key});
		}
		
		$sql = "SELECT * 
				FROM pbase_info 
				WHERE pbase_id='$pbase_id' 
					AND year_apply<='".date('Y')."' 
				ORDER BY year_apply DESC,apply_deadline_e,apply_deadline_p LIMIT 1";
		$get_info = mysql_query($sql);
		if(mysql_num_rows($get_info)>0){
			$i_row = mysql_fetch_array($get_info);
			$year_apply = $i_row['year_apply'];
			$level = $i_row['level'];
			$region = $i_row['region'];
			$apply_deadline_e = $i_row['apply_deadline_e'];
			$apply_deadline_e = ($apply_deadline_e!="0000-00-00")?$apply_deadline_e:"-";
			$apply_deadline_p = $i_row['apply_deadline_p'];
			$apply_deadline_p = ($apply_deadline_p!="0000-00-00")?$apply_deadline_p:"-";
			
			$i_year_apply = $doc->createElement("year_apply",$year_apply);
			$pbase->appendChild($i_year_apply);
			
			$level = ($level>0)?$GLOBALS['project_level_opt'][$level]:"-";
			$i_level = $doc->createElement("level",$level);
			$pbase->appendChild($i_level);
			
			$region_str = "";
			if($region!=""){
				$region_arr = explode(",",$region);
				$province = $region_arr[0];
				$sql = "SELECT name FROM region WHERE id='$province'";
				$province = mysql_result(mysql_query($sql),0);
				$region_str .= $province;
				
				$city = $region_arr[1];
				$sql = "SELECT name FROM region WHERE id='$city'";
				$city = mysql_result(mysql_query($sql),0);
				if($region_str!="" && $city!="")$region_str .= "-";
				$region_str .= $city;
				
				$district = $region_arr[2];
				$sql = "SELECT name FROM region WHERE id='$district'";
				$district = mysql_result(mysql_query($sql),0);
				if($region_str!="" && $district!="")$region_str .= "-";
				$region_str .= $district;
				
				$region = $region_str;
			}
			else $region = "-";
			$i_region = $doc->createElement("region",$region);
			$pbase->appendChild($i_region);
			
			$i_apply_deadline_e = $doc->createElement("apply_deadline_e",$apply_deadline_e);
			$pbase->appendChild($i_apply_deadline_e);
			
			$i_apply_deadline_p = $doc->createElement("apply_deadline_p",$apply_deadline_p);
			$pbase->appendChild($i_apply_deadline_p);
		}
	}
}

echo $doc->saveXML();
?>
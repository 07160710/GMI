<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = 20;

$doc = new DOMDocument('1.0', 'UTF-8');

$nbases = $doc->createElement("nbases");
$doc->appendChild($nbases);

$cond_str = "";
include_once("fetch_nbase_cond.php");

$sql = "SELECT COUNT(*) 
		FROM nbase 
		$cond_str";
$nbase_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("nbase_num",$nbase_num);
$nbases->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$nbases->appendChild($curr_p);

$page_num = ceil($nbase_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$nbases->appendChild($p_num);

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
	$sort_str = "ORDER BY created_time DESC";
}

$select_field_str = "";
foreach($GLOBALS['nbase_list_fields'] as $key=>$val){
	if($key!="file_count"){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= $key;
	}
}

$sql = "SELECT 
			id,
			$select_field_str,
			published_time,
			(SELECT COUNT(*) FROM nbase_file WHERE n_id=nbase.id) AS file_count 
		FROM nbase 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_nbase = mysql_query($sql);
if(mysql_num_rows($get_nbase)>0){
	while($row = mysql_fetch_array($get_nbase)){		
		$nbase = $doc->createElement("nbase");
		$nbases->appendChild($nbase);
		
		$id = $row['id'];
		$n_id = $doc->createElement('n_id',$id);
		$nbase->appendChild($n_id);
		
		foreach($GLOBALS['nbase_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			
			if($key=="region"){
				$region_arr = explode(",",$region);
				$province = $region_arr[0];
				$sql = "SELECT name FROM region WHERE id='$province'";
				$province = mysql_result(mysql_query($sql),0);
				$region = $province;
				
				$city = $region_arr[1];
				if($city!=""){
					$sql = "SELECT name FROM region WHERE id='$city'";
					$city = mysql_result(mysql_query($sql),0);
					$region .= "-".$city;
				}
				
				$district = $region_arr[2];
				if($district!=""){
					$sql = "SELECT name FROM region WHERE id='$district'";
					$district = mysql_result(mysql_query($sql),0);
					$region .= "-".$district;
				}
			}
			if($key=="file_count")$file_count = ($file_count>0)?$file_count:"-";
			if($key=="created_by"){
				build_conn("zkwf");
				$sql = "SELECT name FROM user WHERE id='$created_by'";
				$created_by = mysql_result(mysql_query($sql),0);
				close_conn("zkwf");
			}
			if($key=="published_by"){
				build_conn("zkwf");
				$sql = "SELECT name FROM user WHERE id='$published_by'";
				$published_by = mysql_result(mysql_query($sql),0);
				close_conn("zkwf");
			}
			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$nbase->appendChild(${'r_'.$key});
		}
		
		$published_time = $row['published_time'];
		$publish = 0;
		if($published_time>0)$publish = 1;
		$r_publish = $doc->createElement("publish", $publish);
		$nbase->appendChild($r_publish);
	}
}

echo $doc->saveXML();
?>
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$filters = $doc->createElement("filters");
$doc->appendChild($filters);

$cond_str = "";
include_once("fetch_nbase_cond.php");

$select_key_str = "";
foreach($GLOBALS['nbase_filter'] as $val){
	if($select_key_str!="")$select_key_str .= ",";
	$select_key_str .= $val;
}

$sql = "SELECT 
			$select_key_str 
		FROM nbase 
		$cond_str";
$filter_info = mysql_query($sql);
if(mysql_num_rows($filter_info)>0){
	foreach($GLOBALS['nbase_filter'] as $val){
		if($val=="region"){
			$provinces = $doc->createElement("provinces");
			$filters->appendChild($provinces);
			
			$citys = $doc->createElement("citys");
			$filters->appendChild($citys);
			
			$districts = $doc->createElement("districts");
			$filters->appendChild($districts);
		}
		else{
			${$val."s"} = $doc->createElement($val."s");
			$filters->appendChild(${$val."s"});
		}
	}
	
	foreach($GLOBALS['nbase_filter'] as $val){
		if($val=="region"){
			$province_arr = array();
			$city_arr = array();
			$district_arr = array();
		}
		else{
			${$val."_arr"} = array();
		}
	}
	while($row = mysql_fetch_array($filter_info)){
		foreach($GLOBALS['nbase_filter'] as $val){
			if($val=="region"){
				$region = $row['region'];
				$region_arr = explode(",",$region);
				$province_arr[] = $region_arr[0];
				$city_arr[] = $region_arr[1];
				$district_arr[] = $region_arr[2];
			}
			else{
				${$val."_arr"}[] = $row[$val];
			}
		}
	}
	
	foreach($GLOBALS['nbase_filter'] as $val){
		if($val=="region"){
			build_conn("zkkb");
			$province_arr = array_unique($province_arr);
			asort($province_arr);
			foreach($province_arr as $arr_val){
				$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ")."region LIKE '".$arr_val."%'";
				$sql = "SELECT COUNT(*) FROM nbase $tmp_cond_str";
				$count = mysql_result(mysql_query($sql),0);
				
				$sql = "SELECT name FROM region WHERE id='$arr_val'";
				$arr_val_title = mysql_result(mysql_query($sql),0);
				
				$province = $doc->createElement("province",$arr_val."|".htmlspecialchars($arr_val_title)." (".$count.")");
				$provinces->appendChild($province);
			}
			
			$city_arr = array_filter(array_unique($city_arr));
			asort($city_arr);
			foreach($city_arr as $arr_val){
				$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ")."region LIKE '%,".$arr_val.",%'";
				$sql = "SELECT COUNT(*) FROM nbase $tmp_cond_str";
				$count = mysql_result(mysql_query($sql),0);
				
				$sql = "SELECT name FROM region WHERE id='$arr_val'";
				$arr_val_title = mysql_result(mysql_query($sql),0);
				
				$city = $doc->createElement("city",$arr_val."|".htmlspecialchars($arr_val_title)." (".$count.")");
				$citys->appendChild($city);
			}
			
			$district_arr = array_filter(array_unique($district_arr));
			asort($district_arr);
			foreach($district_arr as $arr_val){
				$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ")."region LIKE '%,".$arr_val."'";
				$sql = "SELECT COUNT(*) FROM nbase $tmp_cond_str";
				$count = mysql_result(mysql_query($sql),0);
				
				$sql = "SELECT name FROM region WHERE id='$arr_val'";
				$arr_val_title = mysql_result(mysql_query($sql),0);
				
				$district = $doc->createElement("district",$arr_val."|".htmlspecialchars($arr_val_title)." (".$count.")");
				$districts->appendChild($district);
			}
			build_conn("zkkb");
		}
		else{
			${$val."_arr"} = array_unique(${$val."_arr"});
			asort(${$val."_arr"});
			
			foreach(${$val."_arr"} as $arr_val){
				build_conn("zkkb");
				$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ")."$val='$arr_val'";
				$sql = "SELECT COUNT(*) FROM nbase $tmp_cond_str";
				$count = mysql_result(mysql_query($sql),0);
				build_conn("zkkb");
				
				if($val=="type")$arr_val_title = $GLOBALS['nbase_type'][$arr_val];
				else if($val=="created_by"){
					build_conn("zkwf");
					$sql = "SELECT name FROM user WHERE id='$arr_val'";
					$arr_val_title = mysql_result(mysql_query($sql),0);
					close_conn("zkwf");
				}
				else $arr_val_title = $arr_val;
				
				${$val} = $doc->createElement($val,$arr_val."|".htmlspecialchars($arr_val_title)." (".$count.")");
				${$val."s"}->appendChild(${$val});
			}
		}
	}
}

$sql = "SELECT COUNT(id) FROM nbase WHERE is_top=1";
$is_top_num = mysql_result(mysql_query($sql),0);
$is_top_num = $doc->createElement("is_top",$is_top_num);
$filters->appendChild($is_top_num);

$sql = "SELECT COUNT(id) FROM nbase WHERE is_hot=1";
$is_hot_num = mysql_result(mysql_query($sql),0);
$is_hot_num = $doc->createElement("is_hot",$is_hot_num);
$filters->appendChild($is_hot_num);

$sql = "SELECT COUNT(id) FROM nbase WHERE is_recommend=1";
$is_recommend_num = mysql_result(mysql_query($sql),0);
$is_recommend_num = $doc->createElement("is_recommend",$is_recommend_num);
$filters->appendChild($is_recommend_num);

echo $doc->saveXML();
?>
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$filters = $doc->createElement("filters");
$doc->appendChild($filters);

$cond_str = "";
include_once("fetch_pbase_cond.php");

$select_key_str = "";
foreach($GLOBALS['pbase_filter'] as $val){
	if($select_key_str!="")$select_key_str .= ",";
	$select_key_str .= $val;
}

$sql = "SELECT $select_key_str 
		FROM pbase 
		$cond_str";
$filter_info = mysql_query($sql);
if(mysql_num_rows($filter_info)>0){
	foreach($GLOBALS['pbase_filter'] as $val){
		${$val."s"} = $doc->createElement($val."s");
		$filters->appendChild(${$val."s"});
	}
	
	foreach($GLOBALS['pbase_filter'] as $val){
		${$val."_arr"} = [];
	}
	while($row = mysql_fetch_array($filter_info)){
		foreach($GLOBALS['pbase_filter'] as $val){
			${$val."_arr"}[] = $row[$val];
		}
	}
	
	foreach($GLOBALS['pbase_filter'] as $val){
		${$val."_arr"} = array_unique(${$val."_arr"});
		
		asort(${$val."_arr"});
		foreach(${$val."_arr"} as $arr_val){
			$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ")."$val='$arr_val'";
			$sql = "SELECT COUNT(*) 
					FROM pbase 
					$tmp_cond_str";
			$count = mysql_result(mysql_query($sql),0);
			
			if($val=="type"){
				$arr_val_title = $GLOBALS['pbase_type_opt'][$arr_val];
			}
			else{
				$arr_val_title = $arr_val;
			}
			
			${$val} = $doc->createElement($val,$arr_val."|".htmlspecialchars($arr_val_title)." (".$count.")");
			${$val."s"}->appendChild(${$val});
		}
	}
}

echo $doc->saveXML();
?>
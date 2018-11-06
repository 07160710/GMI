<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$filters = $doc->createElement("filters");
$doc->appendChild($filters);

$cond_str = build_responsible_cond();
include_once("fetch_agreement_cond.php");

$select_key_str = "";
foreach($GLOBALS['agreement_filter'] as $key){
	if($select_key_str!="")$select_key_str .= ",";
	$select_key_str .= "agreement.".$key;
}

$sql = "SELECT 
			$select_key_str 
		FROM agreement 
			LEFT JOIN company ON agreement.company_id=company.id 
		$cond_str";
$filter_info = mysql_query($sql);
if(mysql_num_rows($filter_info)>0){
	foreach($GLOBALS['agreement_filter'] as $key){
		${$key."s"} = $doc->createElement($key."s");
		$filters->appendChild(${$key."s"});
	}
	
	foreach($GLOBALS['agreement_filter'] as $key){
		${$key."_arr"} = [];
	}
	$user_arr = [];
	while($row = mysql_fetch_array($filter_info)){
		foreach($GLOBALS['agreement_filter'] as $key){
			${$key."_arr"}[] = $row[$key];
		}
	}
	$user_arr = array_filter(array_unique($user_arr));
	
	foreach($GLOBALS['agreement_filter'] as $key){
		${$key."_arr"} = array_unique(${$key."_arr"});
		asort(${$key."_arr"});		
		
		foreach(${$key."_arr"} as $arr_val){
			$tmp_cond_str = "agreement.$key='$arr_val'";
			$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ").$tmp_cond_str;
			$sql = "SELECT COUNT(*) 
					FROM agreement 
						LEFT JOIN company ON agreement.company_id=company.id 
					$tmp_cond_str";
			$count = mysql_result(mysql_query($sql),0);
			
			if($key=="branch"){
				$branch_arr = explode(",",$arr_val);
				$branch_str = "";
				foreach($branch_arr as $b_id){
					$sql = "SELECT name FROM branch WHERE id='$b_id'";
					$b_name = mysql_result(mysql_query($sql),0);
					
					if($branch_str!="")$branch_str .= "+";
					$branch_str .= $b_name;
				}
				$arr_val_title = $branch_str;
				$arr_val_title = ($arr_val_title!="")?$arr_val_title:"无";
			}
			else if($key=="sales_name"){
				$arr_val_title = ($arr_val!="")?$arr_val:"无";
			}
			else{
				$arr_val_title = $arr_val;
			}
			
			${$key} = $doc->createElement($key,$arr_val."|".htmlspecialchars($arr_val_title)." (".$count.")");
			${$key."s"}->appendChild(${$key});
		}
	}
}

echo $doc->saveXML();
?>
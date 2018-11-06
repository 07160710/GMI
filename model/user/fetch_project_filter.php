<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$filters = $doc->createElement("filters");
$doc->appendChild($filters);

$object = $_REQUEST['object'];

$cond_str = build_responsible_cond();
include_once("fetch_project_cond.php");

$select_key_str = "";
foreach($GLOBALS[$object.'_filter'] as $key){
	if($select_key_str!="")$select_key_str .= ",";
	if(	$key=="sales" || 
		$key=="technology" || 
		$key=="finance"
	){
		$select_key_str .= "a_".$key.".u_id AS ".$key;
		$select_key_str .= ",a_".$key.".name AS ".$key."_name";
	}
	else{
		$select_key_str .= "project.".$key;
	}
}

$join_str = "";
switch($object){
	case "sales": 
		$join_str = "LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='s' AND is_curr=1) AS a_sales ON project.id=a_sales.project_id"; 
		break;
	case "technology": 
		$join_str = "LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='t' AND is_curr=1) AS a_technology ON project.id=a_technology.project_id";
		break;
	case "finance":
		$join_str = "LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='f' AND is_curr=1) AS a_finance ON project.id=a_finance.project_id";
		break;
}

$sql = "SELECT 
			$select_key_str 
		FROM project 
			LEFT JOIN company ON project.company_id=company.id 
			$join_str 
		$cond_str";
$filter_info = mysql_query($sql);
if(mysql_num_rows($filter_info)>0){
	foreach($GLOBALS[$object.'_filter'] as $key){
		${$key."s"} = $doc->createElement($key."s");
		$filters->appendChild(${$key."s"});
	}
	
	foreach($GLOBALS[$object.'_filter'] as $key){
		${$key."_arr"} = [];
	}
	$user_arr = [];
	while($row = mysql_fetch_array($filter_info)){
		foreach($GLOBALS[$object.'_filter'] as $key){
			${$key."_arr"}[] = $row[$key];
			if(	$key=="sales" || 
				$key=="technology" || 
				$key=="finance"
			){
				$user_arr[$row[$key]] = $row[$key.'_name'];
			}
		}
	}
	$user_arr = array_filter(array_unique($user_arr));
	
	foreach($GLOBALS[$object.'_filter'] as $key){
		${$key."_arr"} = array_unique(${$key."_arr"});
		asort(${$key."_arr"});
		if(	$key=="sales" || 
			$key=="technology" || 
			$key=="finance"
		){
			${$key."_arr"} = array_filter(${$key."_arr"});
		}
		else{
			if($key=="year_apply")${$key."_arr"} = array_reverse(${$key."_arr"});
		}
		
		foreach(${$key."_arr"} as $arr_val){
			if(	$key=="sales" || 
				$key=="technology" || 
				$key=="finance"
			){
				$tmp_cond_str = "a_".$key.".u_id='$arr_val'";
			}
			else{
				$tmp_cond_str = "project.$key='$arr_val'";
			}
			$tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ").$tmp_cond_str;
			$sql = "SELECT COUNT(*) 
					FROM project 
						LEFT JOIN company ON project.company_id=company.id 
						$join_str 
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
			else if($key=="year_apply"){
				$arr_val_title = ($arr_val>0)?$arr_val."年":"无";
			}
			else if($key=="category"){
				$arr_val_title = $GLOBALS['project_category_opt'][$arr_val];
			}
			else if($key=="level"){
				$arr_val_title = ($arr_val>0)?$GLOBALS['project_level_opt'][$arr_val]:"无";
			}
			else if($key=="progress"){
				$arr_val_title = $GLOBALS['project_progress_opt'][$arr_val];
			}
			else if($key=="status_assign"){
				$arr_val_title = $GLOBALS['status_assign_opt'][$arr_val];
			}
			else if($key=="sales" || 
					$key=="technology" || 
					$key=="finance"
			){
				$arr_val_title = $user_arr[$arr_val];
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
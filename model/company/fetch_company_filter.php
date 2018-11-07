<?php
require_once("../../public/check_login.php");
require_once("../../public/include/conn.php");
require_once("../../public/function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$filters = $doc->createElement("filters");
$doc->appendChild($filters);

$cond_str = "";
include_once("fetch_company_cond.php");

$select_key_str = "";
foreach($GLOBALS['company_filter'] as $val){
    if($select_key_str!="")$select_key_str .= ",";
    $select_key_str .= $val;
}

$sql = "SELECT 
			$select_key_str 
		FROM gmi_company 
		$cond_str";
$filter_info = mysql_query($sql);
if(mysql_num_rows($filter_info)>0){
    foreach($GLOBALS['company_filter'] as $val){
        ${$val."s"} = $doc->createElement($val."s");
        $filters->appendChild(${$val."s"});
    }

    foreach($GLOBALS['company_filter'] as $val){
        ${$val."_arr"} = [];
    }
    while($row = mysql_fetch_array($filter_info)){
        foreach($GLOBALS['company_filter'] as $val){
            ${$val."_arr"}[] = $row[$val];
        }
    }

    foreach($GLOBALS['company_filter'] as $val){
        ${$val."_arr"} = array_unique(${$val."_arr"});

        asort(${$val."_arr"});
        foreach(${$val."_arr"} as $arr_val){
            $tmp_cond_str = (($cond_str!="")?$cond_str." AND ":" WHERE ")."$val='$arr_val'";
            $sql = "SELECT COUNT(*) 
					FROM gmi_company 
					$tmp_cond_str";
            $count = mysql_result(mysql_query($sql),0);

            if(	$val=="province" ||
                $val=="city" ||
                $val=="district"
            ){
                $sql = "SELECT name FROM region WHERE id='$arr_val'";
                $arr_val_title = mysql_result(mysql_query($sql),0);
                $arr_val_title = ($arr_val_title!="")?$arr_val_title:"无";
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
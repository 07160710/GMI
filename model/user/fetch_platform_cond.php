<?php
$filter_name = "";
if(isset($_REQUEST['name'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['name'] as $val){
		if($filter_name!="")$filter_name .= " OR ";
		$filter_name .= "name LIKE '%".$val."%'";
	}
	$cond_str .= "($filter_name)";
}

if($cond_str!="")$cond_str = " WHERE ".$cond_str;
?>
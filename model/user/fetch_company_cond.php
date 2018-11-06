<?php
$filter_province = "";
if(isset($_REQUEST['province'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['province'] as $val){
		if($filter_province!="")$filter_province .= " OR ";
		$filter_province .= "province='".$val."'";
	}
	$cond_str .= "($filter_province)";
}
$filter_city = "";
if(isset($_REQUEST['city'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['city'] as $val){
		if($filter_city!="")$filter_city .= " OR ";
		$filter_city .= "city='".$val."'";
	}
	$cond_str .= "($filter_city)";
}
$filter_district = "";
if(isset($_REQUEST['district'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['district'] as $val){
		if($filter_district!="")$filter_district .= " OR ";
		$filter_district .= "district='".$val."'";
	}
	$cond_str .= "($filter_district)";
}

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
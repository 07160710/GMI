<?php
$filter_type = "";
if(isset($_REQUEST['type'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['type'] as $val){
		if($filter_type!="")$filter_type .= " OR ";
		$filter_type .= "type='".$val."'";
	}
	$cond_str .= "($filter_type)";
}
$filter_year = "";
if(isset($_REQUEST['year'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['year'] as $val){
		if($filter_year!="")$filter_year .= " OR ";
		$filter_year .= "year='".$val."'";
	}
	$cond_str .= "($filter_year)";
}
$filter_batch = "";
if(isset($_REQUEST['batch'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['batch'] as $val){
		if($filter_batch!="")$filter_batch .= " OR ";
		$filter_batch .= "batch='".$val."'";
	}
	$cond_str .= "($filter_batch)";
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

$filter_company = "";
if(isset($_REQUEST['company'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['company'] as $val){
		if($filter_company!="")$filter_company .= " OR ";
		$filter_company .= "company LIKE '%".$val."%'";
	}
	$cond_str .= "($filter_company)";
}

if($cond_str!="")$cond_str = " WHERE ".$cond_str;
?>
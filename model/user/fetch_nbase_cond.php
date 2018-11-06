<?php
$filter_province = "";
if(isset($_REQUEST['province'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['province'] as $val){
		if($filter_province!="")$filter_province .= " OR ";
		$filter_province .= "region LIKE '".$val.",%'";
	}
	$cond_str .= "($filter_province)";
}
$filter_city = "";
if(isset($_REQUEST['city'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['city'] as $val){
		if($filter_city!="")$filter_city .= " OR ";
		$filter_city .= "region LIKE '%,".$val.",%'";
	}
	$cond_str .= "($filter_city)";
}
$filter_district = "";
if(isset($_REQUEST['district'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['district'] as $val){
		if($filter_district!="")$filter_district .= " OR ";
		$filter_district .= "region LIKE '%,".$val."'";
	}
	$cond_str .= "($filter_district)";
}
$filter_bureau = "";
if(isset($_REQUEST['bureau'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['bureau'] as $val){
		if($filter_bureau!="")$filter_bureau .= " OR ";
		$filter_bureau .= "bureau='".$val."'";
	}
	$cond_str .= "($filter_bureau)";
}
$filter_policy_type = "";
if(isset($_REQUEST['policy_type'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['policy_type'] as $val){
		if($filter_policy_type!="")$filter_policy_type .= " OR ";
		$filter_policy_type .= "policy_type='".$val."'";
	}
	$cond_str .= "($filter_policy_type)";
}
$filter_created_by = "";
if(isset($_REQUEST['created_by'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['created_by'] as $val){
		if($filter_created_by!="")$filter_created_by .= " OR ";
		$filter_created_by .= "created_by='".$val."'";
	}
	$cond_str .= "($filter_created_by)";
}
$filter_notice_type = "";
if(isset($_REQUEST['notice_type'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['notice_type'] as $val){
		if($filter_notice_type!="")$filter_notice_type .= " OR ";
		$filter_notice_type .= $val."=1";
	}
	$cond_str .= "($filter_notice_type)";
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
$filter_support_industry = "";
if(isset($_REQUEST['support_industry'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['support_industry'] as $val){
		if($filter_support_industry!="")$filter_support_industry .= " OR ";
		$filter_support_industry .= "support_industry LIKE '%".$val."%'";
	}
	$cond_str .= "($filter_support_industry)";
}

$start_date_cond_str = "";
if(isset($_REQUEST['release_start_date'])){
	$rec_start_date = mysql_real_escape_string($_REQUEST['release_start_date']);
	$rec_start_date = date('Y-m-d',strtotime($rec_start_date));
	$start_date_cond_str = "UNIX_TIMESTAMP(release_date)>=".strtotime($rec_start_date." 00:00:00");
}
if($cond_str!="" && $start_date_cond_str!="")$cond_str .= " AND ";
$cond_str .= $start_date_cond_str;

$end_date_cond_str = "";
if(isset($_REQUEST['release_end_date'])){
	$rec_end_date = mysql_real_escape_string($_REQUEST['release_end_date']);
	$rec_end_date = date('Y-m-d',strtotime($rec_end_date));
	$end_date_cond_str = "UNIX_TIMESTAMP(release_date)<=".strtotime($rec_end_date." 23:59:59");
}
if($cond_str!="" && $end_date_cond_str!="")$cond_str .= " AND ";
$cond_str .= $end_date_cond_str;

if($cond_str!="")$cond_str = " WHERE ".$cond_str;
?>
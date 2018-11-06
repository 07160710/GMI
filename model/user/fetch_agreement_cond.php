<?php
$filter_branch = "";
if(isset($_REQUEST['branch'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['branch'] as $val){
		if($filter_branch!="")$filter_branch .= " OR ";
		$filter_branch .= "branch='".$val."'";
	}
	$cond_str .= "($filter_branch)";
}

$filter_name = "";
if(isset($_REQUEST['name'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['name'] as $val){
		if($filter_name!="")$filter_name .= " AND ";
		$filter_name .= "agreement.name LIKE '%".$val."%'";
	}
	$cond_str .= "($filter_name)";
}
$filter_company = "";
if(isset($_REQUEST['company'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['company'] as $val){
		if($filter_company!="")$filter_company .= " OR ";
		$filter_company .= "company.name LIKE '%".$val."%'";
	}
	$cond_str .= "($filter_company)";
}

$sign_start_date_cond_str = "";
if(isset($_REQUEST['sign_start_date'])){
	$rec_start_date = mysql_real_escape_string($_REQUEST['sign_start_date']);
	$rec_start_date = date('Y-m-d',strtotime($rec_start_date));
	$sign_start_date_cond_str = "UNIX_TIMESTAMP(date_sign)>=".strtotime($rec_start_date." 00:00:00");
}
if($cond_str!="" && $sign_start_date_cond_str!="")$cond_str .= " AND ";
$cond_str .= $sign_start_date_cond_str;

$sign_end_date_cond_str = "";
if(isset($_REQUEST['sign_end_date'])){
	$rec_end_date = mysql_real_escape_string($_REQUEST['sign_end_date']);
	$rec_end_date = date('Y-m-d',strtotime($rec_end_date));
	$sign_end_date_cond_str = "UNIX_TIMESTAMP(date_sign)<=".strtotime($rec_end_date." 23:59:59");
}
if($cond_str!="" && $sign_end_date_cond_str!="")$cond_str .= " AND ";
$cond_str .= $sign_end_date_cond_str;

$expire_start_date_cond_str = "";
if(isset($_REQUEST['expire_start_date'])){
	$rec_start_date = mysql_real_escape_string($_REQUEST['expire_start_date']);
	$rec_start_date = date('Y-m-d',strtotime($rec_start_date));
	$expire_start_date_cond_str = "date_expire!='0000-00-00' AND UNIX_TIMESTAMP(date_expire)>=".strtotime($rec_start_date." 00:00:00");
}
if($cond_str!="" && $expire_start_date_cond_str!="")$cond_str .= " AND ";
$cond_str .= $expire_start_date_cond_str;

$expire_end_date_cond_str = "";
if(isset($_REQUEST['expire_end_date'])){
	$rec_end_date = mysql_real_escape_string($_REQUEST['expire_end_date']);
	$rec_end_date = date('Y-m-d',strtotime($rec_end_date));
	$expire_end_date_cond_str = "date_expire!='0000-00-00' AND UNIX_TIMESTAMP(date_expire)<=".strtotime($rec_end_date." 23:59:59");
}
if($cond_str!="" && $expire_end_date_cond_str!="")$cond_str .= " AND ";
$cond_str .= $expire_end_date_cond_str;

$filter_sales_name = "";
if(isset($_REQUEST['sales_name'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['sales_name'] as $val){
		if($filter_sales_name!="")$filter_sales_name .= " OR ";
		$filter_sales_name .= "sales_name='".$val."'";
	}
	$cond_str .= "($filter_sales_name)";
}

$filter_receive = "";
if(isset($_REQUEST['receive'])){
	if($cond_str!="")$cond_str .= " AND ";
	$cond_str .= "agreement.receive=1";
}

if($cond_str!="")$cond_str = " WHERE ".$cond_str;
?>
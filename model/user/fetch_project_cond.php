<?php
$filter_level = "";
if(isset($_REQUEST['level'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['level'] as $val){
		if($filter_level!="")$filter_level .= " OR ";
		$filter_level .= "level='".$val."'";
	}
	$cond_str .= "($filter_level)";
}
$filter_branch = "";
if(isset($_REQUEST['branch'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['branch'] as $val){
		if($filter_branch!="")$filter_branch .= " OR ";
		$filter_branch .= "branch='".$val."'";
	}
	$cond_str .= "($filter_branch)";
}
$filter_category = "";
if(isset($_REQUEST['category'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['category'] as $val){
		if($filter_category!="")$filter_category .= " OR ";
		$filter_category .= "category='".$val."'";
	}
	$cond_str .= "($filter_category)";
}
$filter_year_apply = "";
if(isset($_REQUEST['year_apply'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['year_apply'] as $val){
		if($filter_year_apply!="")$filter_year_apply .= " OR ";
		$filter_year_apply .= "year_apply='".$val."'";
	}
	$cond_str .= "($filter_year_apply)";
}
$filter_progress = "";
if(isset($_REQUEST['progress'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['progress'] as $val){
		if($filter_progress!="")$filter_progress .= " OR ";
		$filter_progress .= "progress='".$val."'";
	}
	$cond_str .= "($filter_progress)";
}

$filter_id = "";
if(isset($_REQUEST['id'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['id'] as $val){
		if($filter_id!="")$filter_id .= " AND ";
		$filter_id .= "project.id='".$val."'";
	}
	$cond_str .= "($filter_id)";
}
$filter_name = "";
if(isset($_REQUEST['name'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['name'] as $val){
		if($filter_name!="")$filter_name .= " AND ";
		$filter_name .= "project.name LIKE '%".$val."%'";
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

$filter_status_assign = "";
if(isset($_REQUEST['status_assign'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['status_assign'] as $val){
		if($filter_status_assign!="")$filter_status_assign .= " OR ";
		$filter_status_assign .= "status_assign='".$val."'";
	}
	$cond_str .= "($filter_status_assign)";
}
$filter_sales = "";
if(isset($_REQUEST['sales'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['sales'] as $val){
		if($filter_sales!="")$filter_sales .= " OR ";
		$filter_sales .= "a_sales.u_id='".$val."'";
	}
	$cond_str .= "($filter_sales)";
}
$filter_technology = "";
if(isset($_REQUEST['technology'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['technology'] as $val){
		if($filter_technology!="")$filter_technology .= " OR ";
		$filter_technology .= "a_technology.u_id='".$val."'";
	}
	$cond_str .= "($filter_technology)";
}
$filter_finance = "";
if(isset($_REQUEST['finance'])){
	if($cond_str!="")$cond_str .= " AND ";
	foreach($_REQUEST['finance'] as $val){
		if($filter_finance!="")$filter_finance .= " OR ";
		$filter_finance .= "a_finance.u_id='".$val."'";
	}
	$cond_str .= "($filter_finance)";
}

$filter_confirm = "";
if(isset($_REQUEST['confirm'])){
	if($cond_str!="")$cond_str .= " AND ";
	if($object=="technology"){
		$filter_confirm = "	progress=4 AND 
							(status_apply LIKE '%\"t\":{%\"e\":%1%,\"p\":%1%,\"c\":%0%},%' OR 
							status_apply LIKE '%\"t\":{%\"e\":%2%,\"p\":%1%,\"c\":%0%},%' OR 
							status_apply LIKE '%\"t\":{%\"e\":%1%,\"p\":%2%,\"c\":%0%},%' OR 
							status_apply LIKE '%\"t\":{%\"e\":%2%,\"p\":%2%,\"c\":%0%},%')";
	}
	if($object=="finance"){
		$filter_confirm = "	progress=4 AND 
							(status_apply LIKE '%\"f\":{%\"e\":%1%,\"p\":%1%,\"c\":%0%}%' OR 
							status_apply LIKE '%\"f\":{%\"e\":%2%,\"p\":%1%,\"c\":%0%}%' OR 
							status_apply LIKE '%\"f\":{%\"e\":%1%,\"p\":%2%,\"c\":%0%}%' OR 
							status_apply LIKE '%\"f\":{%\"e\":%2%,\"p\":%2%,\"c\":%0%}%')";
	}
	$cond_str .= $filter_confirm;
}

$filter_receive = "";
if(isset($_REQUEST['receive'])){
	if($cond_str!="")$cond_str .= " AND ";
	$cond_str .= "project.receive=1";
}

if($cond_str!="")$cond_str = " WHERE ".$cond_str;
?>
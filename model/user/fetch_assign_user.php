<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$users = $doc->createElement("users");
$doc->appendChild($users);

$project_id = $_REQUEST['project_id'];
$object = $_REQUEST['object'];

$role = "";
$u_type = "";
if($object=="sales"){
	$role = "ss";
	$u_type = "s";
}
if($object=="technology"){
	$role = "ts";
	$u_type = "t";
}
if($object=="finance"){
	$role = "fs";
	$u_type = "f";
}

$user_arr = array();
$sql = "SELECT id,name FROM user WHERE role LIKE '%".$role."%' ORDER BY CONVERT(name USING GBK)";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$user_arr[$row[1]] = array(
			'id'=>$row[0],
		);
	}
}

$curr_arr = array();
$sql = "SELECT 
			pa.u_id,
			pa.name,
			pa.start_date,
			ut.name,			
			pa.assigned_time 
		FROM project_assign pa 
			LEFT JOIN user ut ON pa.assigned_by=ut.id 
		WHERE project_id='$project_id' AND u_type='$u_type' AND is_curr=1 
		ORDER BY assigned_time DESC";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$id = $row[0];
		$name = $row[1];
		$start_date = $row[2];
		$assigned_by = $row[3];
		$assigned_time = $row[4];
		
		$curr_arr[$name] = array(
			'id'=>$id,
			'start_date'=>$start_date,
			'assigned_by'=>$assigned_by,
			'assigned_time'=>$assigned_time,
		);
	}
	foreach($curr_arr as $user_name=>$info){
		$curr = $doc->createElement("curr");
		$users->appendChild($curr);
		
		$u_id = $doc->createElement("id",$info['id']);
		$curr->appendChild($u_id);
		
		$u_name = $doc->createElement("name",$user_name);
		$curr->appendChild($u_name);
		
		$u_start_date = $doc->createElement("start_date",$info['start_date']);
		$curr->appendChild($u_start_date);
		
		$u_assigned_by = $doc->createElement("assigned_by",$info['assigned_by']);
		$curr->appendChild($u_assigned_by);
		
		$assigned_time = $info['assigned_time'];
		$assigned_time = ($assigned_time>0)?date('Y-m-d H:i',$assigned_time):"";
		$u_assigned_time = $doc->createElement("assigned_time",$assigned_time);
		$curr->appendChild($u_assigned_time);
		
		$sql = "SELECT COUNT(*) FROM user WHERE id='".$info['id']."' AND status=1";
		$is_active = mysql_result(mysql_query($sql),0);
		$u_active = $doc->createElement("active",$is_active);
		$curr->appendChild($u_active);
	}
}

$pass_arr = array();
$sql = "SELECT 
			pa.u_id,
			pa.name,
			pa.start_date,
			ut.name,
			pa.assigned_time 
		FROM project_assign pa 
			LEFT JOIN user ut ON pa.assigned_by=ut.id 
		WHERE project_id='$project_id' AND u_type='$u_type' AND is_curr=0 
		ORDER BY assigned_time DESC";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$id = $row[0];
		$name = $row[1];
		$start_date = $row[2];
		$assigned_by = $row[3];
		$assigned_time = $row[4];
		
		$pass_arr[$name] = array(
			'id'=>$id,
			'start_date'=>$start_date,
			'assigned_by'=>$assigned_by,
			'assigned_time'=>$assigned_time,
		);
	}
	foreach($pass_arr as $user_name=>$info){
		$pass = $doc->createElement("pass");
		$users->appendChild($pass);
		
		$u_id = $doc->createElement("id",$info['id']);
		$pass->appendChild($u_id);
		
		$u_name = $doc->createElement("name",$user_name);
		$pass->appendChild($u_name);
		
		$u_start_date = $doc->createElement("start_date",$info['start_date']);
		$pass->appendChild($u_start_date);
		
		$u_assigned_by = $doc->createElement("assigned_by",$info['assigned_by']);
		$pass->appendChild($u_assigned_by);
		
		$assigned_time = $info['assigned_time'];
		$assigned_time = ($assigned_time>0)?date('Y-m-d H:i',$assigned_time):"";
		$u_assigned_time = $doc->createElement("assigned_time",$assigned_time);
		$pass->appendChild($u_assigned_time);
		
		$sql = "SELECT COUNT(*) FROM user WHERE id='".$info['id']."' AND status=1";
		$is_active = mysql_result(mysql_query($sql),0);
		$u_active = $doc->createElement("active",$is_active);
		$pass->appendChild($u_active);
	}
}

$assign_arr = array_merge($curr_arr,$pass_arr);
$user_arr = array_diff_assoc($user_arr,$assign_arr);
foreach($user_arr as $user_name=>$info){
	$user = $doc->createElement("user");
	$users->appendChild($user);
	
	$u_id = $doc->createElement("id",$info['id']);
	$user->appendChild($u_id);
	
	$u_name = $doc->createElement("name",$user_name);
	$user->appendChild($u_name);
}

echo $doc->saveXML();
?>
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

function deep_in_array($value, $array) { 
	foreach($array as $item) { 
		if(!is_array($item)) { 
			if ($item == $value) {
				return true;
			} else {
				continue; 
			}
		} 
		if(in_array($value, $item)) {
			return true; 
		} else if(deep_in_array($value, $item)) {
			return true; 
		}
	} 
	return false; 
}

function build_parent_name($p_id){
	$sql = "SELECT parent_id,name FROM user_group WHERE id='$p_id'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
		$parent_id = $row[0];
		$name = $row[1];
		
		if($parent_id!=""){
			$p_name = build_parent_name($parent_id).$name."-";
		}
		else{
			$p_name = $name."-";
		}
	}
	
	return $p_name;
}

$doc = new DOMDocument('1.0', 'UTF-8');

$auth = $doc->createElement("auth");
$doc->appendChild($auth);

$media_id = $_REQUEST['media_id'];

$user_list = [];
$sql = "SELECT id,name FROM user WHERE id!='999' ORDER BY CONVERT(name USING GBK)";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$user_list[$row[0]] = $row[1];
	}
}

$dept_list = [];
$sql = "SELECT id,name,parent_id FROM user_group ORDER BY parent_id, sort_order DESC";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$id = $row[0];
		$parent_id = $row[2];
		$name = $row[1];
		
		$parent_name = build_parent_name($parent_id).$name;
		$dept_list[$id] = $parent_name;
	}
}

$sql = "SELECT wu_list,wd_list,bu_list,bd_list 
		FROM media_table 
		WHERE id='$media_id'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	$row = mysql_fetch_array($stmt);
	
	$wsu_list = $row[0];
	if($wsu_list!="")$wsu_list = explode(",",$wsu_list);
	
	$wsd_list = $row[1];
	if($wsd_list!="")$wsd_list = explode(",",$wsd_list);
	
	$bsu_list = $row[2];
	if($bsu_list!="")$bsu_list = explode(",",$bsu_list);
	
	$bsd_list = $row[3];
	if($bsd_list!="")$bsd_list = explode(",",$bsd_list);
	
	foreach($user_list as $au_id=>$au_name){
		if(!in_array($au_id,$wsu_list)){
			$wau = $doc->createElement("wau");
			$auth->appendChild($wau);
			
			$u_id = $doc->createElement("id",$au_id);
			$wau->appendChild($u_id);
			
			$u_name = $doc->createElement("name",$au_name);
			$wau->appendChild($u_name);
		}
	}
	
	if(is_array($wsu_list) && count($wsu_list)>0){
		foreach($wsu_list as $wsu_id){
			$wsu = $doc->createElement("wsu");
			$auth->appendChild($wsu);
			
			$u_id = $doc->createElement("id",$wsu_id);
			$wsu->appendChild($u_id);
			
			$sql = "SELECT name FROM user WHERE id='$wsu_id'";
			$wsu_name = mysql_result(mysql_query($sql),0);
			$u_name = $doc->createElement("name",$wsu_name);
			$wsu->appendChild($u_name);
		}
	}
	
	foreach($dept_list as $ad_id=>$ad_name){
		if(!in_array($ad_id,$wsd_list)){
			$wad = $doc->createElement("wad");
			$auth->appendChild($wad);
			
			$d_id = $doc->createElement("id",$ad_id);
			$wad->appendChild($d_id);
			
			$d_name = $doc->createElement("name",$ad_name);
			$wad->appendChild($d_name);
		}
	}
	
	if(is_array($wsd_list) && count($wsd_list)>0){
		foreach($wsd_list as $wsd_id){
			$wsd = $doc->createElement("wsd");
			$auth->appendChild($wsd);
			
			$d_id = $doc->createElement("id",$wsd_id);
			$wsd->appendChild($d_id);
			
			$sql = "SELECT parent_id,name FROM user_group WHERE id='$wsd_id'";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				$row = mysql_fetch_array($stmt);
				$parent_id = $row[0];
				$name = $row[1];
				
				$wsd_name = build_parent_name($parent_id).$name;
			}
			$d_name = $doc->createElement("name",$wsd_name);
			$wsd->appendChild($d_name);
		}
	}
	
	foreach($user_list as $au_id=>$au_name){
		if(!in_array($au_id,$bsu_list)){
			$bau = $doc->createElement("bau");
			$auth->appendChild($bau);
			
			$u_id = $doc->createElement("id",$au_id);
			$bau->appendChild($u_id);
			
			$u_name = $doc->createElement("name",$au_name);
			$bau->appendChild($u_name);
		}
	}
	
	if(is_array($bsu_list) && count($bsu_list)>0){
		foreach($bsu_list as $bsu_id){
			$bsu = $doc->createElement("bsu");
			$auth->appendChild($bsu);
			
			$u_id = $doc->createElement("id",$bsu_id);
			$bsu->appendChild($u_id);
			
			$sql = "SELECT name FROM user WHERE id='$bsu_id'";
			$bsu_name = mysql_result(mysql_query($sql),0);
			$u_name = $doc->createElement("name",$bsu_name);
			$bsu->appendChild($u_name);
		}
	}
	
	foreach($dept_list as $ad_id=>$ad_name){
		if(!in_array($ad_id,$bsd_list)){
			$bad = $doc->createElement("bad");
			$auth->appendChild($bad);
			
			$d_id = $doc->createElement("id",$ad_id);
			$bad->appendChild($d_id);
			
			$d_name = $doc->createElement("name",$ad_name);
			$bad->appendChild($d_name);
		}
	}
	
	if(is_array($bsd_list) && count($bsd_list)>0){
		foreach($bsd_list as $bsd_id){
			$bsd = $doc->createElement("bsd");
			$auth->appendChild($bsd);
			
			$d_id = $doc->createElement("id",$bsd_id);
			$bsd->appendChild($d_id);
			
			$sql = "SELECT parent_id,name FROM user_group WHERE id='$bsd_id'";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				$row = mysql_fetch_array($stmt);
				$parent_id = $row[0];
				$name = $row[1];
				
				$bsd_name = build_parent_name($parent_id).$name;
			}
			$d_name = $doc->createElement("name",$bsd_name);
			$bsd->appendChild($d_name);
		}
	}
}

echo $doc->saveXML();
?>
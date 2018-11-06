<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$nbase = $doc->createElement("nbase");
$doc->appendChild($nbase);

$n_id = $_REQUEST['n_id'];

$select_field_str = "";
foreach($GLOBALS['nbase_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT 
			$select_field_str,
			img_url,
			created_by,
			created_time,
			updated_by,
			updated_time,
			published_by,
			published_time 
		FROM nbase 
		WHERE id='$n_id'";
$get_nbase = mysql_query($sql);
if(mysql_num_rows($get_nbase)>0){
	$row = mysql_fetch_array($get_nbase);		
	
	foreach($GLOBALS['nbase_fields'] as $key){
		${$key} = $row[$key];
		
		$release_date = ($release_date!="0000-00-00")?$release_date:"";
		$apply_deadline = ($apply_deadline!="0000-00-00")?$apply_deadline:"";
		
		if($key=="region"){
			$region_arr = explode(",",$region);
			$province = $region_arr[0];
			$r_province = $doc->createElement("province",$province);
			$nbase->appendChild($r_province);
			
			$city = $region_arr[1];
			$r_city = $doc->createElement("city",$city);
			$nbase->appendChild($r_city);
			
			$district = $region_arr[2];
			$r_district = $doc->createElement("district",$district);
			$nbase->appendChild($r_district);
		}
		else{
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$nbase->appendChild(${'r_'.$key});
		}
	}
	$img_url = _BASE_URL_.$row['img_url'];
	// $img_url = str_replace(_THUMB_FOLDER_,"",$img_url);
	$r_img_url = $doc->createElement("img_url",$img_url);
	$nbase->appendChild($r_img_url);

	$created_by = $row['created_by'];
	$sql = "SELECT name FROM user WHERE id='$created_by'";
	$created_by = mysql_result(mysql_query($sql),0);
	$created_by = ($created_by!="")?$created_by:"-";
	$r_created_by = $doc->createElement("created_by",$created_by);
	$nbase->appendChild($r_created_by);
	
	$updated_by = $row['updated_by'];
	$sql = "SELECT name FROM user WHERE id='$updated_by'";
	$updated_by = mysql_result(mysql_query($sql),0);
	$updated_by = ($updated_by!="")?$updated_by:"-";
	$r_updated_by = $doc->createElement("updated_by",$updated_by);
	$nbase->appendChild($r_updated_by);
	
	$published_by = $row['published_by'];
	$sql = "SELECT name FROM user WHERE id='$published_by'";
	$published_by = mysql_result(mysql_query($sql),0);
	$published_by = ($published_by!="")?$published_by:"-";
	$r_published_by = $doc->createElement("published_by",$published_by);
	$nbase->appendChild($r_published_by);
	
	$created_time = $row['created_time'];
	$created_time = ($created_time>0)?date('Y/m/d H:i',$created_time):"-";
	$r_created_time = $doc->createElement("created_time",$created_time);
	$nbase->appendChild($r_created_time);
	
	$updated_time = $row['updated_time'];
	$updated_time = ($updated_time>0)?date('Y/m/d H:i',$updated_time):"-";
	$r_updated_time = $doc->createElement("updated_time",$updated_time);
	$nbase->appendChild($r_updated_time);
	
	$published_time = $row['published_time'];
	$published_time = ($published_time>0)?date('Y/m/d H:i',$published_time):"-";
	$r_published_time = $doc->createElement("published_time",$published_time);
	$nbase->appendChild($r_published_time);
	
	$sql = "SELECT 
				nf.m_id AS id,
				mt.name,
				mt.ext,
				mt.file_url 
			FROM nbase_file nf 
				LEFT JOIN media_table mt ON nf.m_id=mt.id 
			WHERE n_id='$n_id'";
	$get_file = mysql_query($sql);
	if(mysql_num_rows($get_file)>0){		
		while($row = mysql_fetch_array($get_file)){
			$file_id = $row['id'];
			$file_name = $row['name'];
			$file_ext = $row['ext'];
			$file_url = $row['file_url'];
			
			$file = $doc->createElement("file");
			$nbase->appendChild($file);
			
			$r_file_id = $doc->createElement("file_id",htmlspecialchars($file_id));
			$file->appendChild($r_file_id);
			
			$r_file_name = $doc->createElement("file_name",htmlspecialchars($file_name));
			$file->appendChild($r_file_name);
			
			$r_file_ext = $doc->createElement("file_ext",htmlspecialchars($file_ext));
			$file->appendChild($r_file_ext);
			
			$r_file_url = $doc->createElement("file_url",htmlspecialchars($file_url));
			$file->appendChild($r_file_url);
		}
	}
}

echo $doc->saveXML();
?>
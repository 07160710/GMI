<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

if($_REQUEST['m_id']!=""){
	$m_id = mysql_escape_string($_REQUEST['m_id']);
	
	$media = $doc->createElement("media");
	$doc->appendChild($media);
	
	$sql = "SELECT name,file_url FROM media_table WHERE id='$m_id'";
	$get_media = mysql_query($sql);
	if(mysql_num_rows($get_media)>0){
		$m_row = mysql_fetch_array($get_media);
		$m_name = $m_row['name'];
		$m_file_url = $m_row['file_url'];
		
		$id = $doc->createElement("id",$m_id);
		$media->appendChild($id);
		
		$name = $doc->createElement("name",$m_name);
		$media->appendChild($name);		
	}
}

if($_REQUEST['parent_id']!=""){
	$parent_id = mysql_escape_string($_REQUEST['parent_id']);
	
	$media = $doc->createElement("media");
	$doc->appendChild($media);
	
	$sort_str = "";
	if($_REQUEST['sort']){
		foreach($_REQUEST['sort'] as $sort){
			$sort_arr = explode("|",$sort);
			if($sort_str!="")$sort_str .= ",";
			
			if(	strpos($sort_arr[0],"size")!==false
			){
				$sort_str .= $sort_arr[0]." ".strtoupper($sort_arr[1]);
			}
			else{
				$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
			}
		}
	}
	if($sort_str!=""){
		$sort_str = "ORDER BY ".$sort_str;
	}
	else{
		$sort_str = "ORDER BY sort_order";
	}
	
	$sql = "SELECT * 
			FROM media_table 
			WHERE parent_id='$parent_id' AND type='file' 
			$sort_str";
	$get_media = mysql_query($sql);
	if(mysql_num_rows($get_media)>0){
		while($m_row = mysql_fetch_array($get_media)){
			$file = $doc->createElement("file");
			$media->appendChild($file);
			
			$m_id = $m_row['id'];
			$id = $doc->createElement("id",$m_id);
			$file->appendChild($id);
			
			$m_file_url = $m_row['file_url'];
			$file_url = $doc->createElement("file_url",$m_file_url);
			$file->appendChild($file_url);
			
			foreach($file_fields as $key=>$val){
				${$key} = $m_row[$key];
				if(	$key=="created_by" || 
					$key=="uploaded_by"
				){
					$sql = "SELECT ut.name 
							FROM media_table mt
								LEFT JOIN user ut ON mt.".$key."=ut.id 
							WHERE mt.id='".$m_id."'";
					${$key} = mysql_result(mysql_query($sql),0);
					
					${$key} = (${$key}!="")?${$key}:"-";
				}
				if(	$key=="created_time" || 
					$key=="uploaded_time"
				){
					${$key} = (${$key}>0)?date('y/m/d H:i',${$key}):"-";
				}
				if($key=="size"){
					${$key} = ceil(${$key}/1024);
					if(${$key}<1000){
						${$key} = ${$key}." KB";
					}
					else{
						${$key} = number_format(${$key}/1024,2)." MB";
					}
				}
				
				${'m_'.$key} = $doc->createElement($key,${$key});
				$file->appendChild(${'m_'.$key});
			}
			
			$m_publish = $m_row['publish'];
			$publish = $doc->createElement("publish",$m_publish);
			$file->appendChild($publish);
		}		
	}
}

echo $doc->saveXML();
?>
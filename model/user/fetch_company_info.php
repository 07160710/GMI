<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$company = $doc->createElement("company");
$doc->appendChild($company);

$company_id = $_REQUEST['company_id'];

$select_field_str = "";
foreach($GLOBALS['company_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT $select_field_str 
		FROM company 
		WHERE id='$company_id'";
$get_company = mysql_query($sql);
if(mysql_num_rows($get_company)>0){
	$row = mysql_fetch_array($get_company);
	
	foreach($GLOBALS['company_fields'] as $key){
		${$key} = $row[$key];
		
		${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
		$company->appendChild(${'r_'.$key});
	}
	
	$sql = "SELECT id,name FROM project WHERE company_id='$company_id' ORDER BY date_sign DESC";
	$get_project = mysql_query($sql);
	if(mysql_num_rows($get_project)>0){
		while($p_row = mysql_fetch_array($get_project)){
			$project = $doc->createElement('project');
			$company->appendChild($project);
			
			$project_id = $doc->createElement('project_id',$p_row[0]);
			$project->appendChild($project_id);
			
			$project_name = $doc->createElement('project_name',$p_row[1]);
			$project->appendChild($project_name);
		}
	}
	
	$sql = "SELECT 
				cf.media_id AS id,
				mt.name,
				mt.ext,
				mt.file_url 
			FROM company_file cf 
				LEFT JOIN media_table mt ON cf.media_id=mt.id 
			WHERE company_id='$company_id'";
	$get_file = mysql_query($sql);
	if(mysql_num_rows($get_file)>0){
		while($f_row = mysql_fetch_array($get_file)){
			$file_id = $f_row['id'];
			$file_name = $f_row['name'];
			$file_ext = strtolower($f_row['ext']);
			$file_url = $f_row['file_url'];
			
			$file = $doc->createElement("file");
			$company->appendChild($file);
			
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
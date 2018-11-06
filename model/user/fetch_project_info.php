<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$project = $doc->createElement("project");
$doc->appendChild($project);

$project_id = $_REQUEST['project_id'];

$select_field_str = "";
foreach($GLOBALS['project_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT $select_field_str 
		FROM project 
		WHERE id='$project_id'";
$get_project = mysql_query($sql);
if(mysql_num_rows($get_project)>0){
	$row = mysql_fetch_array($get_project);
	
	foreach($GLOBALS['project_fields'] as $key){
		${$key} = $row[$key];
		
		if($key=="company_id"){
			$sql = "SELECT name FROM company WHERE id='$company_id'";
			$company = mysql_result(mysql_query($sql),0);
			
			$r_company = $doc->createElement("company",htmlspecialchars($company));
			$project->appendChild($r_company);
		}
		
		${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
		$project->appendChild(${'r_'.$key});
	}
	
	$sql = "SELECT * FROM project_finance WHERE project_id='$project_id'";
	$get_finance = mysql_query($sql);
	if(mysql_num_rows($get_finance)>0){
		$f_row = mysql_fetch_array($get_finance);
		foreach($GLOBALS['project_finance_fields'] as $key){
			${$key} = $f_row[$key];				
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$project->appendChild(${'r_'.$key});
		}
	}
}

echo $doc->saveXML();
?>
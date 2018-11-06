<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$agreement = $doc->createElement("agreement");
$doc->appendChild($agreement);

$agreement_id = $_REQUEST['agreement_id'];

$select_field_str = "";
foreach($GLOBALS['agreement_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $key;
}

$sql = "SELECT $select_field_str 
		FROM agreement 
		WHERE id='$agreement_id'";
$get_agreement = mysql_query($sql);
if(mysql_num_rows($get_agreement)>0){
	$row = mysql_fetch_array($get_agreement);
	
	foreach($GLOBALS['agreement_fields'] as $key){
		${$key} = $row[$key];
		
		if($key=="company_id"){
			$sql = "SELECT name FROM company WHERE id='$company_id'";
			$company = mysql_result(mysql_query($sql),0);
			
			$r_company = $doc->createElement("company",htmlspecialchars($company));
			$agreement->appendChild($r_company);
		}
		
		${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
		$agreement->appendChild(${'r_'.$key});
	}
	
	$sql = "SELECT * FROM agreement_finance WHERE agreement_id='$agreement_id'";
	$get_finance = mysql_query($sql);
	if(mysql_num_rows($get_finance)>0){
		$f_row = mysql_fetch_array($get_finance);
		foreach($GLOBALS['project_finance_fields'] as $key){
			${$key} = $f_row[$key];				
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$agreement->appendChild(${'r_'.$key});
		}
	}
}

echo $doc->saveXML();
?>
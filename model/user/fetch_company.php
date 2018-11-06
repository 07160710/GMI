<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = $_REQUEST['per_page'];

$doc = new DOMDocument('1.0', 'UTF-8');

$companys = $doc->createElement("companys");
$doc->appendChild($companys);

$cond_str = "";
include_once("fetch_company_cond.php");

$sql = "SELECT COUNT(*) 
		FROM company 
		$cond_str";
$company_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("company_num",$company_num);
$companys->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$companys->appendChild($curr_p);

$page_num = ceil($company_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$companys->appendChild($p_num);

$sort_str = "";
if($_REQUEST['sort']){
	foreach($_REQUEST['sort'] as $sort){
		$sort_arr = explode("|",$sort);
		if($sort_str!="")$sort_str .= ",";		
		$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
	}
}
if($sort_str!=""){	
	$sort_str = "ORDER BY ".$sort_prefix.$sort_str;
}
else{
	$sort_str = "ORDER BY CONVERT(name USING GBK)";
}

$select_field_str = "";
foreach($GLOBALS['company_list_fields'] as $key=>$val){
	if($key!="file"){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= $key;
	}
}

$sql = "SELECT 
			id,
			$select_field_str 
		FROM company 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_company = mysql_query($sql);
if(mysql_num_rows($get_company)>0){
	while($row = mysql_fetch_array($get_company)){		
		$company = $doc->createElement("company");
		$companys->appendChild($company);
		
		$company_id = $row['id'];
		$r_company_id = $doc->createElement("company_id",$company_id);
		$company->appendChild($r_company_id);
		
		foreach($GLOBALS['company_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			
			if(	$key=="province" || 
				$key=="city" || 
				$key=="district"
			){
				$sql = "SELECT name FROM region WHERE id='".${$key}."'";
				${$key} = mysql_result(mysql_query($sql),0);
			}
			if($key=="file"){
				$file_list = "";
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
						$file_url = _ROOT_URL_.$f_row['file_url'];
						
						$file_list .= "<a class=\"file $file_ext\" href=\"$file_url\" target=\"_blank\" title=\"$file_name\"></a>";
					}
				}
				$file = $file_list;
			}
			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$company->appendChild(${'r_'.$key});
		}
	}
}

echo $doc->saveXML();
?>
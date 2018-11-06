<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = $_REQUEST['per_page'];

$doc = new DOMDocument('1.0', 'UTF-8');

$agreements = $doc->createElement("agreements");
$doc->appendChild($agreements);

$cond_str = build_responsible_cond();
include_once("fetch_agreement_cond.php");

$sql = "SELECT COUNT(*) 
		FROM agreement 
			LEFT JOIN company ON agreement.company_id=company.id 
			LEFT JOIN agreement_finance finance ON agreement.id=finance.agreement_id 
		$cond_str";
$agreement_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("agreement_num",$agreement_num);
$agreements->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$agreements->appendChild($curr_p);

$page_num = ceil($agreement_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$agreements->appendChild($p_num);

$sort_str = "";
if($_REQUEST['sort']){
	foreach($_REQUEST['sort'] as $sort){
		$sort_arr = explode("|",$sort);
		if($sort_str!="")$sort_str .= ",";
		if($sort_arr[0]=="company_id")$sort_arr[0] = "company.name";
		else if($sort_arr[0]=="sales_id")$sort_arr[0] = "sales_name";
		else $sort_arr[0] = "agreement.".$sort_arr[0];
		$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
	}
}
if($sort_str!=""){
	$sort_str = "ORDER BY ".$sort_str;
}
else{
	$sort_str = "ORDER BY date_sign DESC";
}

$select_key_str = "";
foreach($GLOBALS['agreement_list_fields'] as $key=>$val){
	if($select_key_str!="")$select_key_str .= ",";
	
	if($key=="company_id")$select_key_str .= "company.name AS company_name";
	else if($key=="pay_type" || 
			$key=="amt_contract" || 
			$key=="amt_prepay" || 
			$key=="amt_actual" || 
			$key=="amt_commission" || 
			$key=="agent"
	)$select_key_str .= "finance.".$key;
	else if($key=="sales_id")$select_key_str .= "(SELECT name FROM user WHERE id=agreement.sales_id) AS sales_name";
	else $select_key_str .= "agreement.".$key;
}

$sql = "SELECT 
			agreement.id,
			agreement.receive,
			$select_key_str 
		FROM agreement 
			LEFT JOIN company ON agreement.company_id=company.id 
			LEFT JOIN agreement_finance finance ON agreement.id=finance.agreement_id 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_agreement = mysql_query($sql);
if(mysql_num_rows($get_agreement)>0){
	while($row = mysql_fetch_array($get_agreement)){		
		$agreement = $doc->createElement("agreement");
		$agreements->appendChild($agreement);
		
		$agreement_id = $row['id'];
		$r_agreement_id = $doc->createElement("agreement_id",$agreement_id);
		$agreement->appendChild($r_agreement_id);
		
		$receive = $row['receive'];
		$r_receive = $doc->createElement("receive",$receive);
		$agreement->appendChild($r_receive);
		
		$raw_status_assign = 0;
		foreach($GLOBALS['agreement_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			
			if($key=="company_id")$company_id = $row['company_name'];
			if($key=="name"){
				$sql = "SELECT user.name,log_time 
						FROM log LEFT JOIN user ON log.u_id=user.id 
						WHERE object='a' AND object_id='$agreement_id' AND content='审核'";
				$get_verify = mysql_query($sql);
				if(mysql_num_rows($get_verify)>0){
					$v_row = mysql_fetch_array($get_verify);
					$r_verify = $doc->createElement('verify',htmlspecialchars($v_row[0]."于".date('Y/m/d H:i',$v_row[1])."审核"));
					$agreement->appendChild($r_verify);
				}
			}
			if($key=="branch"){
				$branch_arr = explode(",",$branch);
				$branch_str = "";
				foreach($branch_arr as $b_id){
					$sql = "SELECT name FROM branch WHERE id='$b_id'";
					$b_name = mysql_result(mysql_query($sql),0);
					
					if($branch_str!="")$branch_str .= "+";
					$branch_str .= $b_name;
				}
				$branch = $branch_str;
			}
			if($key=="sales_id"){
				$sales_name = $row['sales_name'];
				$sales_id = ($sales_name!="")?$sales_name:"-";
			}
			if($key=="pay_type")$pay_type = $GLOBALS['project_pay_type_opt'][$pay_type];
			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$agreement->appendChild(${'r_'.$key});
		}
	}
}

echo $doc->saveXML();
?>
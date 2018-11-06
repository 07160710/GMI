<?php 
header('Content-type: text/html; charset=utf-8');

require_once("../include/conn.php");
require_once("../public_param.php");
require_once("../function.php");

$project_arr = [];
$sql = "SELECT id,company_id,name,date_sign FROM project_bk WHERE name LIKE '%协议%' ORDER BY company_id";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$company_id = $row[1];
		$name = $row[2];
		$date_sign = $row[3];
		$project_arr[] = $project_id;
		
		$sql = "SELECT * FROM project_finance WHERE project_id='$project_id'";
		$get_finance = mysql_query($sql);
		if(mysql_num_rows($get_finance)>0){
			$f_row = mysql_fetch_array($get_finance);
			$pay_type = $f_row[1];
			$amt_contract = $f_row[2];
			$amt_prepay = $f_row[3];
			$amt_actual = $f_row[4];
			$amt_commission = $f_row[5];
			$agent = $f_row[6];
			$finance_remark = $f_row[7];
			
			$sql = "SELECT id FROM agreement WHERE company_id='$company_id' AND name='$name' AND date_sign='$date_sign'";
			$agreement_id = mysql_result(mysql_query($sql),0);
			if($agreement_id!=""){
				$sql = "INSERT INTO agreement_finance(
							agreement_id,
							pay_type,
							amt_contract,
							amt_prepay,
							amt_actual,
							amt_commission,
							agent,
							finance_remark
						) VALUES(
							'$agreement_id',
							'$pay_type',
							'$amt_contract',
							'$amt_prepay',
							'$amt_actual',
							'$amt_commission',
							'$agent',
							'$finance_remark'
						)";
				if(mysql_query($sql)){
					echo "成功插入协议财务信息 [".$agreement_id."]<br/>";
				}
			}
		}
	}
	echo "SELECT * FROM project_finance WHERE project_id IN (".implode(",",$project_arr).");";
}
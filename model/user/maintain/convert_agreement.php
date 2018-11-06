<?php 
header('Content-type: text/html; charset=utf-8');

require_once("../include/conn.php");
require_once("../public_param.php");
require_once("../function.php");

$sql = "SELECT * FROM project_bk WHERE name LIKE '%客户开拓协议%' ORDER BY id";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$insert_key_str = "";
		$insert_val_str = "";
		foreach($GLOBALS['project_fields'] as $key){
			${'p_'.$key} = $row[$key];
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";
			$insert_val_str .= "'".${'p_'.$key}."'";
		}
		$update_by = $row['update_by'];
		$update_time = $row['update_time'];
		
		$sql = "INSERT INTO project(
					$insert_key_str,
					update_by,
					update_time
				) VALUES(
					$insert_val_str,
					'$update_by',
					'$update_time'
				)";
		if(mysql_query($sql)){
			$sql = "SELECT * FROM project_assign_bk WHERE project_id='$p_id'";
			$get_assign = mysql_query($sql);
			if(mysql_num_rows($get_assign)>0){
				while($a_row = mysql_fetch_array($get_assign)){
					$a_u_type = $a_row['u_type'];
					$a_u_id = $a_row['u_id'];
					$a_name = $a_row['name'];
					$a_is_curr = $a_row['is_curr'];
					$a_start_date = $a_row['start_date'];
					$a_assigned_by = $a_row['assigned_by'];
					$a_assigned_time = $a_row['assigned_time'];
					$a_accepted_time = $a_row['accepted_time'];
					
					$sql = "INSERT INTO project_assign(
								project_id,
								u_type,
								u_id,
								name,
								is_curr,
								start_date,
								assigned_by,
								assigned_time,
								accepted_time
							) VALUES(
								'$p_id',
								'$a_u_type',
								'$a_u_id',
								'$a_name',
								'$a_is_curr',
								'$a_start_date',
								'$a_assigned_by',
								'$a_assigned_time',
								'$a_accepted_time'
							)";
					if(!mysql_query($sql)){
						echo "Fail to insert project assign [$p_id]<br/>";
					}
				}
			}
			
			$sql = "SELECT * FROM project_apply_bk WHERE project_id='$p_id'";
			$get_apply = mysql_query($sql);
			if(mysql_num_rows($get_apply)>0){
				while($a_row = mysql_fetch_array($get_apply)){
					$a_u_type = $a_row['u_type'];
					$a_u_id = $a_row['u_id'];
					$a_name = $a_row['name'];
					$a_type = $a_row['type'];
					$a_process_time = $a_row['process_time'];
					
					$sql = "INSERT INTO project_apply(
								project_id,
								u_type,
								u_id,
								name,
								type,
								process_time
							) VALUES(
								'$p_id',
								'$a_u_type',
								'$a_u_id',
								'$a_name',
								'$a_type',
								'$a_process_time'
							)";
					if(!mysql_query($sql)){
						echo "Fail to insert project apply [$p_id]<br/>";
					}
				}
			}
			
			$sql = "SELECT * FROM project_finance_bk WHERE project_id='$p_id'";
			$get_finance = mysql_query($sql);
			if(mysql_num_rows($get_finance)>0){
				while($f_row = mysql_fetch_array($get_finance)){
					$f_pay_type = $f_row['pay_type'];
					$f_amt_contract = $f_row['amt_contract'];
					$f_amt_prepay = $f_row['amt_prepay'];
					$f_amt_actual = $f_row['amt_actual'];
					$f_amt_commission = $f_row['amt_commission'];
					$f_agent = $f_row['agent'];
					$f_finance_remark = $f_row['finance_remark'];
					
					$sql = "INSERT INTO project_finance(
								project_id,
								pay_type,
								amt_contract,
								amt_prepay,
								amt_actual,
								amt_commission,
								agent,
								finance_remark
							) VALUES(
								'$p_id',
								'$f_pay_type',
								'$f_amt_contract',
								'$f_amt_prepay',
								'$f_amt_actual',
								'$f_amt_commission',
								'$f_agent',
								'$f_finance_remark'
							)";
					if(!mysql_query($sql)){
						echo "Fail to insert project finance [$p_id]<br/>";
					}
				}
			}
			
			$sql = "SELECT * FROM log_bk WHERE (object='p' OR object='s' OR object='t' OR object='f') AND object_id='$p_id'";
			$get_log = mysql_query($sql);
			if(mysql_num_rows($get_log)>0){
				while($l_row = mysql_fetch_array($get_log)){
					$l_object = $l_row['object'];
					$l_u_id = $l_row['u_id'];
					$l_content = $l_row['content'];
					$l_log_time = $l_row['log_time'];
					
					$sql = "INSERT INTO log(
								object,
								object_id,
								u_id,
								content,
								log_time
							) VALUES(
								'$l_object',
								'$p_id',
								'$l_u_id',
								'$l_content',
								'$l_log_time'
							)";
					if(!mysql_query($sql)){
						echo "Fail to insert log [$p_id]<br/>";
					}
				}
			}
		}
		else{
			echo "Fail to insert project [$p_id]<br/>";
		}
	}
}
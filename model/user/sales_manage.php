<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="assign_sales"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = build_responsible_cond();
		include_once("fetch_project_cond.php");
		$sql = "SELECT project.id 
				FROM project 
					LEFT JOIN company ON project.company_id=company.id 
				$cond_str";		
		$get_project = mysql_query($sql);
		if(mysql_num_rows($get_project)>0){
			while($row = mysql_fetch_array($get_project)){
				$id = $row[0];
				
				$sql = "UPDATE project_assign SET is_curr=0 WHERE project_id='$id' AND u_type='s'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"设置销售派单过往记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				if(isset($_POST['curr_assign'])){
					foreach($_POST['curr_assign'] as $u_id){
						$start_date = $_POST['curr_start_date_'.$u_id];
						$user_info = get_user_info($u_id);
						
						$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
						$has_rec = mysql_result(mysql_query($sql),0);
						if($has_rec==0){
							$sql = "INSERT INTO project_assign(
										project_id,
										u_type,
										u_id,
										name,
										is_curr,
										start_date,
										assigned_by,
										assigned_time
									) VALUES(
										'$id',
										's',
										'$u_id',
										'".$user_info['name']."',
										'1',
										'$start_date',
										'".$_SESSION['u_id']."',
										'".time()."'
									)";
							if(mysql_query($sql)){
								wxmsg_assign_task("s", $id, $u_id, $start_date);
								save_log("s",$id,"派单");
							}
							else{
								$arr = array(
									'success'=>0,
									'error'=>"保存销售派单当前记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
						}
						else{
							$sql = "UPDATE project_assign 
									SET is_curr='1',start_date='$start_date' 
									WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
							if(!mysql_query($sql)){
								$arr = array(
									'success'=>0,
									'error'=>"保存销售派单当前记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
						}
					}
				}
				if(isset($_POST['pass_assign'])){
					foreach($_POST['pass_assign'] as $u_id){
						$start_date = $_POST['pass_start_date_'.$u_id];
						$user_info = get_user_info($u_id);
						
						$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
						$has_rec = mysql_result(mysql_query($sql),0);
						if($has_rec==0){
							$sql = "INSERT INTO project_assign(
										project_id,
										u_type,
										u_id,
										name,
										is_curr,
										start_date,
										assigned_by,
										assigned_time
									) VALUES(
										'$id',
										's',
										'$u_id',
										'".$user_info['name']."',
										'0',
										'$start_date',
										'".$_SESSION['u_id']."',
										'".time()."'
									)";
						}
						else{
							$sql = "UPDATE project_assign 
									SET is_curr='0',start_date='$start_date' 
									WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
						}
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"保存销售派单过往记录出错：".mysql_error()
							);
							echo json_encode($arr);
							exit;
						}
					}
				}
				
				$v_status_assign = check_status_assign($id);
				$sql = "UPDATE project SET status_assign='$v_status_assign' WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"更新项目派单状态出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				
				$sql = "UPDATE project_assign SET is_curr=0 WHERE project_id='$id' AND u_type='s'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"设置销售派单过往记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				if(isset($_POST['curr_assign'])){
					foreach($_POST['curr_assign'] as $u_id){
						$start_date = $_POST['curr_start_date_'.$u_id];
						$user_info = get_user_info($u_id);
						
						$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
						$has_rec = mysql_result(mysql_query($sql),0);
						if($has_rec==0){
							$sql = "INSERT INTO project_assign(
										project_id,
										u_type,
										u_id,
										name,
										is_curr,
										start_date,
										assigned_by,
										assigned_time
									) VALUES(
										'$id',
										's',
										'$u_id',
										'".$user_info['name']."',
										'1',
										'$start_date',
										'".$_SESSION['u_id']."',
										'".time()."'
									)";
							if(mysql_query($sql)){
								wxmsg_assign_task("s", $id, $u_id, $start_date);
								save_log("s",$id,"派单");
							}
							else{
								$arr = array(
									'success'=>0,
									'error'=>"保存销售派单当前记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
						}
						else{
							$sql = "UPDATE project_assign 
									SET is_curr='1',start_date='$start_date' 
									WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
							if(!mysql_query($sql)){
								$arr = array(
									'success'=>0,
									'error'=>"保存销售派单当前记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
						}
					}
				}
				if(isset($_POST['pass_assign'])){
					foreach($_POST['pass_assign'] as $u_id){
						$start_date = $_POST['pass_start_date_'.$u_id];
						$user_info = get_user_info($u_id);
						
						$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
						$has_rec = mysql_result(mysql_query($sql),0);
						if($has_rec==0){
							$sql = "INSERT INTO project_assign(
										project_id,
										u_type,
										u_id,
										name,
										is_curr,
										start_date,
										assigned_by,
										assigned_time
									) VALUES(
										'$id',
										's',
										'$u_id',
										'".$user_info['name']."',
										'0',
										'$start_date',
										'".$_SESSION['u_id']."',
										'".time()."'
									)";
						}
						else{
							$sql = "UPDATE project_assign 
									SET is_curr='0',start_date='$start_date' 
									WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
						}
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"保存销售派单过往记录出错：".mysql_error()
							);
							echo json_encode($arr);
							exit;
						}
					}
				}
				
				$v_status_assign = check_status_assign($id);
				$sql = "UPDATE project SET status_assign='$v_status_assign' WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"更新项目派单状态出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="edit"){
	$id = mysql_escape_string($_POST['id']);
	$project_info = get_project_info($id);
	$raw_progress = $project_info['progress'];
	
	$db_assign_arr = [];
	$sql = "SELECT u_id FROM project_assign WHERE project_id='$id' AND u_type='s'";
	$get_assign = mysql_query($sql);
	if(mysql_num_rows($get_assign)>0){
		while($row = mysql_fetch_array($get_assign)){
			$db_assign_arr[] = $row[0];
		}
	}
	
	$assign_arr = [];
	if(isset($_POST['curr_assign'])){
		foreach($_POST['curr_assign'] as $u_id){
			$assign_arr[] = $u_id;
			$start_date = $_POST['curr_start_date_'.$u_id];
			$user_info = get_user_info($u_id);
			
			$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO project_assign(
							project_id,
							u_type,
							u_id,
							name,
							is_curr,
							start_date,
							assigned_by,
							assigned_time
						) VALUES(
							'$id',
							's',
							'$u_id',
							'".$user_info['name']."',
							'1',
							'$start_date',
							'".$_SESSION['u_id']."',
							'".time()."'
						)";
				if(mysql_query($sql)){
					wxmsg_assign_task("s", $id, $u_id, $start_date);
					save_log("s",$id,"派单");
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"保存销售派单当前记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
			else{
				$sql = "UPDATE project_assign 
						SET is_curr='1',start_date='$start_date' 
						WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"保存销售派单当前记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	if(isset($_POST['pass_assign'])){
		foreach($_POST['pass_assign'] as $u_id){
			$assign_arr[] = $u_id;
			$start_date = $_POST['pass_start_date_'.$u_id];
			$user_info = get_user_info($u_id);
			
			$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO project_assign(
							project_id,
							u_type,
							u_id,
							name,
							is_curr,
							start_date,
							assigned_by,
							assigned_time
						) VALUES(
							'$id',
							's',
							'$u_id',
							'".$user_info['name']."',
							'0',
							'$start_date',
							'".$_SESSION['u_id']."',
							'".time()."'
						)";
			}
			else{
				$sql = "UPDATE project_assign 
						SET is_curr='0',start_date='$start_date' 
						WHERE project_id='$id' AND u_type='s' AND u_id='$u_id'";
			}
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"保存销售派单过往记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	foreach($db_assign_arr as $db_assign){
		if(!in_array($db_assign,$assign_arr)){
			$sql = "DELETE FROM project_assign WHERE project_id='$id' AND u_id='$db_assign' AND u_type='s'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"删除销售派单记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	
	$v_status_assign = check_status_assign($id);
	if(isset($_POST['v_outsource'])){
		$i = 4;
		foreach($_POST['v_outsource'] as $v_outsource){
			if($v_outsource=="t"){
				$sql = "DELETE FROM project_assign WHERE project_id='$id' AND u_type='t'";
				if(mysql_query($sql)){
					$i += 1;
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"删除技术派单记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
			if($v_outsource=="f"){
				$sql = "DELETE FROM project_assign WHERE project_id='$id' AND u_type='f'";
				if(mysql_query($sql)){
					$i += 2;
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"删除财务派单记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
		$v_status_assign = $i;
	}
	
	$update_field_str = "";
	foreach($GLOBALS['sales_fields'] as $key){
		if($key!="id"){			
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="status_assign"){
				$status_assign = $v_status_assign;
			}
			if($key=="remark"){
				$append_remark = mysql_escape_string($_POST['v_append_remark']);
				
				$sql = "SELECT remark FROM project WHERE id='$id'";
				$old_remark = mysql_result(mysql_query($sql),0);
				
				$sql = "SELECT name FROM user WHERE id='".$_SESSION['u_id']."'";
				$remark_by = mysql_result(mysql_query($sql),0);
				
				$new_remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";			
				$remark = $new_remark.$old_remark;
			}
			if($key=="progress"){
				if($v_status_assign<4 && $v_progress!=2 || $v_status_assign==5 && !check_assign_finance($id))$progress = 0;
				if($progress!=$raw_progress){
					record_progress("s", $id, $progress);
				}
			}
		
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$key='".${$key}."'";
		}
	}
	
	if(isset($_POST['platform'])){
		$sql = "DELETE FROM platform_account WHERE project_id='$id'";
		if(!mysql_query($sql)){
			$arr = array(
				'success'=>0,
				'error'=>"清空项目平台记录出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
		
		foreach($_POST['platform'] as $pf_id){
			$platform_id = mysql_escape_string($_POST['platform_'.$pf_id]);
			
			$project_info = get_project_info($id);
			$company_id = $project_info['company_id'];
			
			$type = mysql_escape_string($_POST['type_'.$pf_id]);
			$account = mysql_escape_string($_POST['account_'.$pf_id]);
			$password = mysql_escape_string($_POST['password_'.$pf_id]);
			$remark = mysql_escape_string($_POST['remark_'.$pf_id]);
			
			$sql = "INSERT INTO platform_account(
						platform_id,
						company_id,
						project_id,
						type,
						account,
						password,
						remark
					) VALUES(
						'$platform_id',
						'$company_id',
						'$id',
						'$type',
						'$account',
						'$password',
						'$remark'
					)";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"添加项目平台记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}		
	}
	
	$sql = "UPDATE project 
			SET $update_field_str 
			WHERE id='$id'";
	if(mysql_query($sql)){
		if(isset($_POST['v_pay_type'])){
			$pay_type = mysql_escape_string($_POST['v_pay_type']);
			$amt_contract = mysql_escape_string($_POST['v_amt_contract']);
			$amt_prepay = mysql_escape_string($_POST['v_amt_prepay']);
			$amt_actual = mysql_escape_string($_POST['v_amt_actual']);
			$amt_commission = mysql_escape_string($_POST['v_amt_commission']);
			$agent = mysql_escape_string($_POST['v_agent']);
			$finance_remark = mysql_escape_string($_POST['v_finance_remark']);
			
			$insert_f_key_str = "";
			$insert_f_val_str = "";
			$update_f_key_str = "";
			foreach($GLOBALS['project_finance_fields'] as $key){
				if($insert_f_key_str!="")$insert_f_key_str .= ",";
				$insert_f_key_str .= $key;
				
				if($insert_f_val_str!="")$insert_f_val_str .= ",";
				$insert_f_val_str .= "'".${$key}."'";
				
				if($update_f_key_str!="")$update_f_key_str .= ",";
				$update_f_key_str .= $key."='".${$key}."'";
			}
			
			$sql = "SELECT COUNT(*) FROM project_finance WHERE project_id='$id'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO project_finance(
							project_id,
							$insert_f_key_str
						) VALUES(
							'$id',
							$insert_f_val_str
						)";
			}
			else{
				$sql = "UPDATE project_finance SET $update_f_key_str WHERE project_id='$id'";
			}
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"保存项目财务记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新项目销售信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("s",$id,"更新");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}
<?php
header('Content-type: text/html; charset=utf-8');

require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="accept_task"){	
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
				accept_task($id, "t");
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				accept_task($id, "t");
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="assign_technology"){
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
				$project_info = get_project_info($id);
				if($project_info['year_apply']>=date('Y')){
					$sql = "UPDATE project_assign SET is_curr=0 WHERE project_id='$id' AND u_type='t'";
					if(!mysql_query($sql)){
						$arr = array(
							'success'=>0,
							'error'=>"设置技术派单过往记录出错：".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
					
					if(isset($_POST['curr_assign'])){
						foreach($_POST['curr_assign'] as $u_id){
							$start_date = $_POST['curr_start_date_'.$u_id];
							$user_info = get_user_info($u_id);
							
							$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
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
											't',
											'$u_id',
											'".$user_info['name']."',
											'1',
											'$start_date',
											'".$_SESSION['u_id']."',
											'".time()."'
										)";
								if(mysql_query($sql)){
									wxmsg_assign_task("t", $id, $u_id, $start_date);
									save_log("t",$id,"派单");
								}
								else{
									$arr = array(
										'success'=>0,
										'error'=>"保存技术派单当前记录出错：".mysql_error()
									);
									echo json_encode($arr);
									exit;
								}
							}
							else{
								$sql = "UPDATE project_assign 
										SET is_curr='1',start_date='$start_date' 
										WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
								if(!mysql_query($sql)){
									$arr = array(
										'success'=>0,
										'error'=>"保存技术派单当前记录出错：".mysql_error()
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
							
							$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
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
											't',
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
										WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
							}
							if(!mysql_query($sql)){
								$arr = array(
									'success'=>0,
									'error'=>"保存技术派单过往记录出错：".mysql_error()
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
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				$project_info = get_project_info($id);
				if($project_info['year_apply']>=date('Y')){
					$v_status_assign = check_status_assign($id);
					if($v_status_assign!=5){//不是技术外包
						$sql = "UPDATE project_assign SET is_curr=0 WHERE project_id='$id' AND u_type='t'";
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"设置技术派单过往记录出错：".mysql_error()
							);
							echo json_encode($arr);
							exit;
						}
						
						if(isset($_POST['curr_assign'])){
							foreach($_POST['curr_assign'] as $u_id){
								$start_date = $_POST['curr_start_date_'.$u_id];
								$user_info = get_user_info($u_id);
								
								$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
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
												't',
												'$u_id',
												'".$user_info['name']."',
												'1',
												'$start_date',
												'".$_SESSION['u_id']."',
												'".time()."'
											)";
									if(mysql_query($sql)){
										wxmsg_assign_task("t", $id, $u_id, $start_date);
										save_log("t",$id,"派单");
									}
									else{
										$arr = array(
											'success'=>0,
											'error'=>"保存技术派单当前记录出错：".mysql_error()
										);
										echo json_encode($arr);
										exit;
									}
								}
								else{
									$sql = "UPDATE project_assign 
											SET is_curr='1',start_date='$start_date' 
											WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
									if(!mysql_query($sql)){
										$arr = array(
											'success'=>0,
											'error'=>"保存技术派单当前记录出错：".mysql_error()
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
								
								$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
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
												't',
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
											WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
								}
								if(!mysql_query($sql)){
									$arr = array(
										'success'=>0,
										'error'=>"保存技术派单过往记录出错：".mysql_error()
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
	$sql = "SELECT u_id FROM project_assign WHERE project_id='$id' AND u_type='t'";
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
			
			$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
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
							't',
							'$u_id',
							'".$user_info['name']."',
							'1',
							'$start_date',
							'".$_SESSION['u_id']."',
							'".time()."'
						)";
				if(mysql_query($sql)){
					wxmsg_assign_task("t", $id, $u_id, $start_date);
					save_log("t",$id,"派单");
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"保存技术派单当前记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
			else{
				$sql = "UPDATE project_assign 
						SET is_curr='1',start_date='$start_date' 
						WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"保存技术派单当前记录出错：".mysql_error()
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
			$assigned_time = strtotime($_POST['pass_assigned_time_'.$u_id]);
			$user_info = get_user_info($u_id);
			
			$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
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
							't',
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
						WHERE project_id='$id' AND u_type='t' AND u_id='$u_id'";
			}
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"保存技术派单过往记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	foreach($db_assign_arr as $db_assign){
		if(!in_array($db_assign,$assign_arr)){
			$sql = "DELETE FROM project_assign WHERE project_id='$id' AND u_id='$db_assign' AND u_type='t'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"删除技术派单记录出错：".mysql_error()
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
	foreach($GLOBALS['technology_fields'] as $key){
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
				if($v_status_assign==5){
					if(!check_assign_finance($id))$progress = 0;
					if($progress<3)$progress = 6;
				}
				if($progress!=$raw_progress){
					record_progress("t", $id, $progress);
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
			'error'=>"更新项目技术信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("t",$id,"更新");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_REQUEST['action']=="export_project"){
	$cond_str = "";
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = build_responsible_cond();
		include_once("fetch_project_cond.php");
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				if($cond_str!="")$cond_str .= ",";
				$cond_str .= "'".$id."'";
			}
			$cond_str = "project.id IN ($cond_str)";
		}
	}
	
	$sort_str = "";
	if($_REQUEST['sort']){
		foreach($_REQUEST['sort'] as $sort){
			$sort_arr = explode("|",$sort);
			if($sort_str!="")$sort_str .= ",";
			if(	$sort_arr[0]=="sales" || 
				$sort_arr[0]=="technology" || 
				$sort_arr[0]=="finance"
			){
				$sort_str .= "CONVERT(".$sort_arr[0]."_name USING GBK) ".strtoupper($sort_arr[1]);
			}
			else{
				$sort_str .= "CONVERT(project.".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
			}
		}
	}
	if($sort_str!=""){
		$sort_str = "ORDER BY ".$sort_str;
	}
	else{
		$sort_str = "ORDER BY id DESC";
	}
	
	$select_key_str = "";
	foreach($GLOBALS['technology_list_fields'] as $key=>$val){
		if(	$key!="sales" && 
			$key!="technology" && 
			$key!="finance"
		){
			if($select_key_str!="")$select_key_str .= ",";		
			$select_key_str .= "project.".$key;
		}
	}
	
	$i = 0;
	$head = "";
	$data = "";
	$sql = "SELECT 
				project.id,
				project.status_apply,
				company.name AS company_name,
				a_sales.u_id AS sales,
				a_technology.u_id AS technology,
				a_finance.u_id AS finance,
				a_sales.name AS sales_name,
				a_technology.name AS technology_name,
				a_finance.name AS finance_name,
				$select_key_str 
			FROM project 
				LEFT JOIN company ON project.company_id=company.id 
				LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='s' AND is_curr=1) AS a_sales ON project.id=a_sales.project_id 
				LEFT JOIN (SELECT project_id,u_id,name,accepted_time FROM project_assign WHERE u_type='t' AND is_curr=1) AS a_technology ON project.id=a_technology.project_id 
				LEFT JOIN (SELECT project_id,u_id,name,accepted_time FROM project_assign WHERE u_type='f' AND is_curr=1) AS a_finance ON project.id=a_finance.project_id 
			$cond_str 
			$sort_str";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			if($i==0)$head .= '<tr height="40" style="color:#000;">';
			$data .= '<tr height="30">';
			$raw_status_assign = 0;
			foreach($GLOBALS['technology_list_fields'] as $key=>$val){
				${$key} = $row[$key];
				
				if($key=="company_id")$company_id = $row['company_name'];
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
				if($key=="category")$category = $GLOBALS['project_category_opt'][$category];
				if($key=="level")$level = ($level>0)?$GLOBALS['project_level_opt'][$level]:"";
				if($key=="date_expire")$date_expire = ($date_expire!="0000-00-00")?$date_expire:"-";
				if($key=="status_assign"){
					$raw_status_assign = $status_assign;
					$status_assign = $GLOBALS['status_assign_opt'][$status_assign];
				}
				if($key=="progress")$progress = $GLOBALS['project_progress_opt'][$progress];
				if($key=="sales"){
					$sales_name = $row['sales_name'];
					$sales = ($sales_name!="")?$sales_name:"-";
				}
				if($key=="technology"){
					$technology_name = $row['technology_name'];
					$technology = ($technology_name!="")?$technology_name:"-";
				}
				if($key=="finance"){
					$finance_name = $row['finance_name'];
					$finance = ($finance_name!="")?$finance_name:"-";
				}
				
				$val_arr = explode("#",$val);
				$width = ($val_arr[1]!="")?'width="'+$val_arr[1]+'"':'';
				if($i==0)$head .= '<th bgcolor="#EEEEEE" '.$width.'>'.$val_arr[0].'</th>';
				
				$bg_color = "";
				if($key=="progress")$bg_color = 'bgcolor="#e8fff2"';
				$data .= '<td align="center" '.$bg_color.'>'.${$key}.'</td>';
			}
			
			$status_apply = $row['status_apply'];
			$apply_arr = json_decode($status_apply,true);
			$apply_str = "";
			foreach($apply_arr as $au_type=>$a_data){
				if($au_type=="t"){
					if($raw_status_assign==5){
						$apply_str .= "业务外包";
					}
					else{
						$status_str = "";
						foreach($a_data as $a_type=>$status){								
							if($a_type=="o"){
								switch($status){
									case 0: $status_str = "未收材料"; break;
									case 1: $status_str = "已收材料"; break;
								}
							}
							if($a_type=="d"){
								switch($status){
									case 0: $status_str = "未完稿"; break;
									case 1: $status_str = "已完稿"; break;
								}
							}
							if($a_type=="e"){
								switch($status){
									case 0: $status_str = "未交电子"; break;
									case 1: $status_str = "已交电子"; break;
									case 2: $status_str = "无需交电子"; break;
								}
							}
							if($a_type=="p"){
								switch($status){
									case 0: $status_str .= "，未交纸质"; break;
									case 1: $status_str .= "，已交纸质"; break;
									case 2: $status_str .= "，无需交纸质"; break;
								}
							}
						}
						$apply_str .= $status_str;
					}
				}
			}
			if($i==0)$head .= '<th bgcolor="#EEEEEE">申报进度</th>';
			$data .= '<td align="center" bgcolor="#fff9e0">'.$apply_str.'</td>';
			
			if($i==0)$head .= '</tr>';
			$data .= '</tr>';
			$i++;
		}
	
$html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
	<head>
	<!--[if gte mso 9]>
		<xml>
			<x:ExcelWorkbook>
			<x:ExcelWorksheets>
			<x:ExcelWorksheet>
			<x:Name></x:Name>
			<x:WorksheetOptions>
			<x:DisplayGridlines/>
			</x:WorksheetOptions>
			</x:ExcelWorksheet>
			</x:ExcelWorksheets>
			</x:ExcelWorkbook>
		</xml>
	<![endif]-->
	</head>

<table border="1" bordercolor="#AAAAAA" style="border-collapse:collapse;font-family:Arial;color:#333;font-size:12px;">
	$head
	$data
</table>
HTML;
	}
	
	$filename = "project_".date('Ymd_His');
	$encoded_filename = urlencode($filename);
	$encoded_filename = str_replace("+", "%20", $encoded_filename);
	$ua = $_SERVER['HTTP_USER_AGENT'];
	
	header("Content-Type:application/vnd.ms-excel;charset=UTF-8");		
	if (preg_match("/MSIE/", $ua)){
		header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
	} 
	else if (preg_match("/Firefox/", $ua)){
		header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.xls"');
	} 
	else{
		header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
	}		
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");
	header("Expires: 0");
	print $html;
	exit;
}
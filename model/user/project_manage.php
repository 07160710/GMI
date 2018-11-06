<?php
header('Content-type: text/html; charset=utf-8');

require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="add"){
	$new_id = get_new_id("project");
	
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['project_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="company_id"){
				$company = mysql_escape_string($_POST['v_company']);
				$sql = "SELECT id FROM company WHERE name='$company'";
				$company_id = mysql_result(mysql_query($sql),0);
				if($company_id==0){
					$arr = array(
						'success'=>0,
						'error'=>"找不到此公司名称，请先到“公司”栏目添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			if($key=="remark"){
				$append_remark = mysql_escape_string($_POST['v_append_remark']);
				
				$user_info = get_user_info($_SESSION['u_id']);
				$remark_by = $user_info['name'];
				
				$remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";
			}
			if($key=="progress")$progress = 0;
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";		
			$insert_val_str .= "'".${$key}."'";
		}
	}
	
	$sql = "INSERT INTO project(
				id,
				$insert_key_str
			) VALUES(
				'$new_id',
				$insert_val_str
			)";
	if(mysql_query($sql)){
		$sql = "SELECT COUNT(*) FROM pbase WHERE name='$name'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO pbase(
						id,
						name,
						type
					) VALUES(
						'".get_new_id("pbase")."',
						'$name',
						'p'
					)";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"添加基础项目出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$sql = "SELECT id FROM pbase WHERE name='$name' AND type='p'";
		$pbase_id = mysql_result(mysql_query($sql),0);
		
		$sql = "SELECT COUNT(*) FROM pbase_info WHERE pbase_id='$pbase_id' AND year_apply='$year_apply'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO pbase_info(
						pbase_id,
						year_apply
					) VALUES(
						'$pbase_id',
						'$year_apply'
					)";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"添加基础项目信息出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
		
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
			
			$sql = "SELECT COUNT(*) FROM project_finance WHERE project_id='$new_id'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO project_finance(
							project_id,
							$insert_f_key_str
						) VALUES(
							'$new_id',
							$insert_f_val_str
						)";
			}
			else{
				$sql = "UPDATE project_finance SET $update_f_key_str WHERE project_id='$new_id'";
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
		
		wxmsg_create_task($new_id);
		save_log("p",$new_id,"创建");
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"添加项目信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
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
	
	$update_key_str = "";
	foreach($GLOBALS['project_fields'] as $key){
		if(	$key!='id' && 
			$key!='status_assign' && 
			$key!='status_apply'
		){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="company_id"){
				$company = mysql_escape_string($_POST['v_company']);
				$sql = "SELECT id FROM company WHERE name='$company'";
				$company_id = mysql_result(mysql_query($sql),0);
				if($company_id==0){
					$arr = array(
						'success'=>0,
						'error'=>"找不到此公司名称，请先到“公司”栏目添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			if($key=="remark"){
				$append_remark = mysql_escape_string($_POST['v_append_remark']);
				
				$old_remark = $project_info['remark'];
				
				$user_info = get_user_info($_SESSION['u_id']);
				$remark_by = $user_info['name'];
				
				$new_remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";			
				$remark = $new_remark.$old_remark;
			}
			if($key=="progress"){
				if($progress!=$raw_progress){
					record_progress("p", $id, $progress);
				}
			}
		
			if($update_key_str!="")$update_key_str .= ",";
			$update_key_str .= "$key='".${$key}."'";
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
	$update_key_str .= ",status_assign='$v_status_assign'";
	
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
			SET $update_key_str  
			WHERE id='$id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"更新项目信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("p",$id,"更新");
	$arr = array(
		'success'=>1,
		'project_id'=>$id,
	);
	echo json_encode($arr);
	exit;
}

function delete_project_data($project_id){
	$sql = "SELECT name FROM project WHERE id='$project_id'";
	$name = mysql_result(mysql_query($sql),0);
	
	$sql = "DELETE FROM project WHERE id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除项目信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM project_assign WHERE project_id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除项目派单信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM project_apply WHERE project_id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除项目申报信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM project_finance WHERE project_id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除项目财务信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM log WHERE (object='p' OR object='s' OR object='t' OR object='f') AND object_id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除项目日志出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("p",$project_id,"删除项目[".$name."]");
}
if($_POST['action']=="delete_project"){
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
				delete_project_data($id);
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				delete_project_data($id);
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

function clear_assign_data($project_id){
	$sql = "DELETE FROM project_assign WHERE project_id='$project_id' AND assigned_time>=1514736000";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"清除派单记录出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM log WHERE object_id='$project_id' AND (object='p' OR object='s' OR object='t' OR object='f') AND (content LIKE '%派单%' OR content LIKE '%接单%')";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除日志出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
}
if($_POST['action']=="clear_assign"){
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
				clear_assign_data($id);
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				clear_assign_data($id);
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

function clear_accept_data($project_id){
	$sql = "UPDATE project_assign SET accepted_time=0 WHERE project_id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"清除接单时间出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM log WHERE object_id='$project_id' AND (object='p' OR object='s' OR object='t' OR object='f') AND (content LIKE '%派单%' OR content LIKE '%接单%')";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除日志出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
}
if($_POST['action']=="clear_accept"){
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
				clear_accept_data($id);
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				clear_accept_data($id);
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

function create_agreement($project_id){
	$select_key_str = "";
	foreach($GLOBALS['project_fields'] as $key){
		if(	$key!="id" && 
			$key!="level" && 
			$key!="category" && 
			$key!="year_apply" && 
			$key!="status_assign" && 
			$key!="status_apply" && 
			$key!="need_approve" && 
			$key!="need_check" && 
			$key!="need_fund" && 
			$key!="progress"
		){
			if($select_key_str!="")$select_key_str .= ",";
			$select_key_str .= $key;
		}
	}
	
	$sql = "SELECT $select_key_str,receive FROM project WHERE id='$project_id'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
		$insert_key_str = "";
		$insert_val_str = "";
		$update_key_str = "";
		foreach($GLOBALS['project_fields'] as $key){
			if(	$key!="id" && 
				$key!="level" && 
				$key!="category" && 
				$key!="year_apply" && 
				$key!="status_assign" && 
				$key!="status_apply" && 
				$key!="need_approve" && 
				$key!="need_check" && 
				$key!="need_fund" && 
				$key!="progress"
			){
				${$key} = $row[$key];
				
				if($insert_key_str!="")$insert_key_str .= ",";
				$insert_key_str .= $key;
				
				if($insert_val_str!="")$insert_val_str .= ",";
				$insert_val_str .= "'".${$key}."'";
				
				if($update_key_str!="")$update_key_str .= ",";
				$update_key_str .= $key."='".${$key}."'";
			}
		}
		$receive = $row['receive'];
		
		$sql = "SELECT COUNT(*) FROM agreement WHERE company_id='$company_id' AND name='$name' AND date_sign='$date_sign'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO agreement(
						id,
						$insert_key_str,
						receive
					) VALUES(
						'".get_new_id("agreement")."',
						$insert_val_str,
						'$receive'
					)";
		}
		else{
			$sql = "UPDATE agreement 
					SET $update_key_str,receive='$receive' 
					WHERE company_id='$company_id' AND name='$name' AND date_sign='$date_sign'";
		}
		if(mysql_query($sql)){
			$sql = "SELECT id FROM agreement WHERE company_id='$company_id' AND name='$name' AND date_sign='$date_sign'";
			$agreement_id = mysql_result(mysql_query($sql),0);
			
			//转存销售
			$sql = "SELECT u_id,name FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
			$get_sales = mysql_query($sql);
			if(mysql_num_rows($get_sales)>0){
				$s_row = mysql_fetch_array($get_sales);
				$sales_id = $s_row[0];
				$sales_name = $s_row[1];
			}
			
			$sql = "UPDATE agreement 
					SET sales_id='$sales_id',
						sales_name='$sales_name',
						update_by='".$_SESSION['u_id']."',
						update_time='".time()."' 
					WHERE id='$agreement_id'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"更新负责销售出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
			
			//转存审核日志
			$sql = "SELECT u_id,log_time WHERE object='p' AND object_id='$project_id' AND content='审核'";
			$get_log = mysql_query($sql);
			if(mysql_num_rows($get_log)>0){
				$l_row = mysql_fetch_array($get_log);
				$u_id = $l_row[0];
				$log_time = $l_row[1];
				
				$sql = "SELECT COUNT(*) FROM log WHERE object='a' AND object_id='$agreement_id' AND content='审核'";
				$has_rec = mysql_result(mysql_query($sql),0);
				if($has_rec==0){
					$sql = "INSERT INTO log(
								object,
								object_id,
								u_id,
								content,
								log_time
							) VALUES(
								'a',
								'$agreement_id',
								'$u_id',
								'审核',
								'$log_time'
							)";
					if(!mysql_query($sql)){
						$arr = array(
							'success'=>0,
							'error'=>"插入审核日志出错：".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"更新协议出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
}
if($_POST['action']=="move_agreement"){
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
				create_agreement($id);
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				create_agreement($id);
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="rename_project"){
	$rename = mysql_escape_string($_POST['rename']);
	
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
				
				$sql = "UPDATE project SET name='$rename' WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"重命名项目出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				save_log("p",$id,"重命名[$rename]");
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				
				$sql = "UPDATE project SET name='$rename' WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"重命名项目出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				save_log("p",$id,"重命名[$rename]");
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="rename_company"){
	$rename = mysql_escape_string($_POST['rename']);
	$sql = "SELECT id FROM company WHERE name='$rename'";
	$company_id = mysql_result(mysql_query($sql),0);
	if($company_id==""){
		$arr = array(
			'success'=>0,
			'error'=>"找不到此公司名称，请先到“公司”栏目添加！"
		);
		echo json_encode($arr);
		exit;
	}
	
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
				
				$sql = "UPDATE project SET company_id='$company_id' WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"修改所属公司出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				save_log("p",$id,"修改公司[$rename]");
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				
				$sql = "UPDATE project SET company_id='$company_id' WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"修改所属公司出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				save_log("p",$id,"修改公司[$rename]");
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="mark_project"){
	$type = mysql_escape_string($_POST['type']);
	$mark_type = mysql_escape_string($_POST['mark_type']);
	
	$update_key_str = "";
	$action_str = "";
	if($mark_type=="progress"){
		$m_progress = mysql_escape_string($_POST['m_progress']);
		$update_key_str .= "progress='$m_progress'";
	}
	if($mark_type=="year"){
		$m_year = mysql_escape_string($_POST['m_year']);
		$update_key_str .= "year_apply='$m_year'";
		$action_str = "更改年度[$m_year]";
	}
	if($mark_type=="branch"){
		$m_branch = mysql_escape_string($_POST['m_branch']);
		$update_key_str .= "branch='$m_branch'";
		
		$sql = "SELECT name FROM branch WHERE id='$m_branch'";
		$b_name = mysql_result(mysql_query($sql),0);
		$action_str = "更改属地[$b_name]";
	}
	if($mark_type=="level"){
		$m_level = mysql_escape_string($_POST['m_level']);
		$update_key_str .= "level='$m_level'";
		$action_str = "更改级别[".$GLOBALS['project_level_opt'][$m_level]."]";
	}
	if($mark_type=="category"){
		$m_category = mysql_escape_string($_POST['m_category']);
		$update_key_str .= "category='$m_category'";
		$action_str = "更改类型[".$GLOBALS['project_category_opt'][$m_category]."]";
	}
	
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = build_responsible_cond();
		include_once("fetch_project_cond.php");
		$sql = "SELECT project.id 
				FROM project 
					LEFT JOIN company ON project.company_id=company.id 
					LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='s' AND is_curr=1) AS a_sales ON project.id=a_sales.project_id 
					LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='t' AND is_curr=1) AS a_technology ON project.id=a_technology.project_id 
					LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='f' AND is_curr=1) AS a_finance ON project.id=a_finance.project_id 
				$cond_str";
		$get_project = mysql_query($sql);
		if(mysql_num_rows($get_project)>0){
			while($row = mysql_fetch_array($get_project)){
				$id = $row['id'];
				
				$sql = "UPDATE project 
						SET $update_key_str 
						WHERE id='$id'";
				if(mysql_query($sql)){
					if($mark_type=="progress")record_progress($type, $id, $m_progress);
					else save_log($type, $id, $action_str);
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"批量标记项目出错：".mysql_error()
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
				
				$sql = "UPDATE project 
						SET $update_key_str 
						WHERE id='$id'";
				if(mysql_query($sql)){
					if($mark_type=="progress")record_progress($type, $id, $m_progress);
					else save_log($type, $id, $action_str);
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"批量标记项目出错：".mysql_error()
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

if($_POST['action']=="verify"){
	$id = mysql_escape_string($_POST['id']);
	save_log("p",$id,"审核");
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
			else if($sort_arr[0]=="progress"){
				$sort_str .= "progress ".strtoupper($sort_arr[1]);
			}
			else{
				if($sort_arr[0]=="company_id")$sort_arr[0] = "company.name";
				else $sort_arr[0] = "project.".$sort_arr[0];
				$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
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
	foreach($GLOBALS['project_exp_fields'] as $key=>$val){
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
			foreach($GLOBALS['project_exp_fields'] as $key=>$val){
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
				if($key=="progress"){					
					switch($progress){
						case 1: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 2: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 3: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 4: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 5: $bg_color = 'bgcolor="#fffde8"'; break;
						case 6: $bg_color = 'bgcolor="#e8fff2"'; break;
						case 7: $bg_color = 'bgcolor="#ffe8e8"'; break;
						case 8: $bg_color = 'bgcolor="#e8fff2"'; break;
						case 9: $bg_color = 'bgcolor="#ffe8e8"'; break;
						case 10: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 11: $bg_color = 'bgcolor="#f4f8fb"'; break;
						case 12: $bg_color = 'bgcolor="#e8fff2"'; break;
						case 20: $bg_color = 'bgcolor="#eeeeee"'; break;
						case 21: $bg_color = 'bgcolor="#eeeeee"'; break;
						case 22: $bg_color = 'bgcolor="#ffe8e8"'; break;
					}
					$progress = $GLOBALS['project_progress_opt'][$progress];
				}
				
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
			$data .= '<td align="center">'.$apply_str.'</td>';
			
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
	//echo $html;exit;
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
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="project_action"){ //立项、验收、请款、收款、回款
	$type = mysql_escape_string($_POST['type']);
	$project_id = mysql_escape_string($_POST['project_id']);
	if(!$project_id||!$type){
		echo json_encode(array('success'=>0,'msg'=>'参数有误！'));exit;
	}
	$fail_remark = '';
	$u_id = $_SESSION['u_id'];
	if($type=='approve_success'){ //立项成功
		$progress_name = '立项成功';
		$batch = mysql_escape_string($_POST['batch']);
		$bonus = mysql_escape_string($_POST['bonus']);
		$remark = mysql_escape_string($_POST['remark']);
		$sql = "INSERT INTO project_approval(
				project_id,
				batch,
				bonus,
				remark,
				update_by
			) VALUES(
				'$project_id',
				'$batch',
				'$bonus',
				'$remark',
				'$u_id'
			)";
		$progress_sql = "UPDATE project SET progress=6 WHERE id='$project_id'";

	}elseif($type=='approve_fail'){ //立项失败 7
		$progress_name = '立项失败';
		$fail_remark = $remark = mysql_escape_string($_POST['remark']);
		$sql = "INSERT INTO project_approval(
				project_id,
				remark,
				result,
				update_by
			) VALUES(
				'$project_id',
				'$remark',
				2,
				'$u_id'
			)";
		$progress_sql = "UPDATE project SET progress=20 WHERE id='$project_id'";
	}elseif($type=='check1_fail'||$type=='check2_fail'){ //验收失败
		$fail_remark = $reason = mysql_escape_string($_POST['remark']);
		$sql = "INSERT INTO project_check(
				project_id,
				reason,
				result,
				created_by
			) VALUES(
				'$project_id',
				'$reason',
				2,
				'$u_id'
			)";
		if($type=='check2_fail'){

			$progress = 20; //11
			$progress_name = '第二次验收失败';
		}else{
			$progress_name = '第一次验收失败';
			$progress = 9;
		}
		$progress_sql = "UPDATE project SET progress='$progress' WHERE id='$project_id'";
	}elseif($type=='check1_success'||$type=='check2_success'){ //验收成功
		$sql = "INSERT INTO project_check(
				project_id,
				result,
				created_by
			) VALUES(
				'$project_id',
				1,
				'$u_id'
			)";
		if($type=='check2_success'){
			$progress = 10; 
			$progress_name = '第二次验收成功';
		}else{
			$progress_name = '第一次验收成功';
			$progress = 8;
		}
		$progress_sql = "UPDATE project SET progress='$progress' WHERE id='$project_id'";
	}elseif($type=='rf'||$type=='cf'||$type=='zf'){ //请款、企业收款、中科回款
		$remark = mysql_escape_string($_POST['remark']);
		$money = mysql_escape_string($_POST['money']);
		$people = mysql_escape_string($_POST['people']);
		$date = mysql_escape_string($_POST['date']);
		$sql = "INSERT INTO project_fund(
				project_id,
				type,
				people,
				`date`,
				`money`,
				remark,
				created_by
			) VALUES(
				'$project_id',
				'$type',
				'$people',
				'$date',
				'$money',
				'$remark',
				'$u_id'
			)";
		switch ($type) {
			case 'rf':
				$progress = 12;
				$progress_name = '已请款';
				break;
			case 'zf':
				$progress = 13;
				$progress_name = '已收款';
				break;
			default:
				$progress = 20; //14
				$progress_name = '已回款';
				break;
		}
		$progress_sql = "UPDATE project SET progress='$progress' WHERE id='$project_id'";
	}

	if(mysql_query($sql)){ 
		if(mysql_query($progress_sql)==false){
			echo json_encode(array('success'=>0,'msg'=>'保存成功，修改项目总进度失败：'.mysql_error()));exit;
		}
		if(!empty($fail_remark)){
			$remark_sql = "UPDATE project SET remark ='$fail_remark' WHERE id='$project_id'";
			if(mysql_query($remark_sql)==false){
				echo json_encode(array('success'=>0,'msg'=>'保存成功，修改项目备注失败：'.mysql_error()));exit;
			}
		}
		wxmsg_flow_update($project_id,$progress_name,$fail_remark);
		save_log("p",$project_id,"更新流程[$progress_name]");
		echo json_encode(array('success'=>1,'msg'=>'保存成功'));exit;
	}else{
		echo json_encode(array('success'=>0,'msg'=>'保存失败：'.mysql_error().';'.$sql));exit;
	}
	
}

function create_project($project_id){
	$select_key_str = "";
	foreach($GLOBALS['project_fields'] as $key){
		if(	$key!="id" && 
			$key!="year_apply" && 
			$key!="status_assign" && 
			$key!="status_apply" && 
			$key!="progress" && 
			$key!="remark"
		){
			if($select_key_str!="")$select_key_str .= ",";
			$select_key_str .= $key;
		}
	}
	
	$sql = "SELECT $select_key_str FROM project WHERE id='$project_id'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
		$insert_key_str = "";
		$insert_val_str = "";
		foreach($GLOBALS['project_fields'] as $key){
			if(	$key!="id" && 
				$key!="year_apply" && 
				$key!="status_assign" && 
				$key!="status_apply" && 
				$key!="progress" && 
				$key!="remark"
			){
				if($insert_key_str!="")$insert_key_str .= ",";
				$insert_key_str .= $key;
				
				if($insert_val_str!="")$insert_val_str .= ",";
				$insert_val_str .= "'".$row[$key]."'";
			}
		}
		$sql = "INSERT INTO project(
					id,
					year_apply,
					$insert_key_str
				) VALUES(
					'".get_new_id("project")."',
					'".date('Y')."',
					$insert_val_str
				)";
		if(!mysql_query($sql)){
			$arr = array(
				'success'=>0,
				'error'=>"复制创建项目出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
}
if($_POST['action']=="copy_create"){
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
				create_project($id);
			}
		}
	}
	else{
		if(isset($_REQUEST['project_id'])){
			foreach($_REQUEST['project_id'] as $id){
				$id = mysql_escape_string($id);
				create_project($id);
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="set_need"){
	$project_id = $_POST['project_id'];
	$u_type = $_POST['u_type'];
	$object = $_POST['object'];
	$type = $_POST['type'];
	
	$sql = "UPDATE project SET ".$object."='$type' WHERE id='$project_id'";
	if(mysql_query($sql)){
		$action_str = "设置".(($type==1)?"需要":"无需");		
		switch($object){
			case "need_approve": $action_str .= "立项"; break;
			case "need_check": $action_str .= "验收"; break;
			case "need_fund": $action_str .= "请款"; break;
		}
		
		save_log($u_type,$project_id,$action_str);
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新项目所需状态出错：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="mark_receive"){
	$project_id = $_POST['project_id'];
	$u_type = $_POST['u_type'];
	
	$sql = "SELECT receive FROM project WHERE id='$project_id'";
	$receive = mysql_result(mysql_query($sql),0);
	
	$set_receive = 0;
	$action_str = "取消标记已收";
	if($receive==0){
		$set_receive = 1;
		$action_str = "标记已收合同";
	}
	
	$sql = "UPDATE project SET receive='$set_receive' WHERE id='$project_id'";
	if(mysql_query($sql)){
		save_log($u_type,$project_id,$action_str);
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新已收合同状态出错：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="process_task"){
	$project_id = $_POST['project_id'];
	$u_type = $_POST['u_type'];
	$type = $_POST['type'];
	$option = $_POST['option'];
	$date = strtotime($_POST['date']);
	
	if($type=="accept"){//接单
		accept_task($project_id, $u_type);
	}
	else if(strpos($type,"apply")!==false){//申报
		$type_arr = explode("_",$type);
		$type = $type_arr[1];
		
		$sql = "SELECT COUNT(*) FROM project_apply WHERE project_id='$project_id' AND u_id='".$_SESSION['u_id']."' AND u_type='$u_type' AND type='$type'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$user_info = get_user_info($_SESSION['u_id']);
			$name = $user_info['name'];
			
			$sql = "INSERT INTO project_apply(
						project_id,						
						u_type,
						u_id,
						name,
						type,
						process_time
					) VALUES(
						'$project_id',
						'$u_type',
						'".$_SESSION['u_id']."',
						'$name',
						'$type',
						'$date'
					)";
		}
		else{
			$sql = "UPDATE project_apply 
					SET process_time='$date' 
					WHERE project_id='$project_id' AND u_id='".$_SESSION['u_id']."' AND u_type='$u_type' AND type='$type'";
		}
		if(mysql_query($sql)){
			update_status_apply($project_id, $u_type, $type, $option);
			if($option!=2){
				$action_str = "";
				switch($type){
					case "o": $action_str = "收集材料"; break;
					case "d": $action_str = "材料完稿"; break;
					case "e": $action_str = "提交电子材料"; break;
					case "p": $action_str = "提交纸质材料"; break;
				}
				save_log($u_type,$project_id,$action_str);
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"保存出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	else if(strpos($type,"reset")!==false){//重置
		$type_arr = explode("_",$type);
		$type = $type_arr[1];
		
		$sql = "DELETE FROM project_apply WHERE project_id='$project_id' AND u_type='$u_type' AND type='$type'";
		if(mysql_query($sql)){
			update_status_apply($project_id, $u_type, $type, $option);
			if($option==0){
				$action_str = "";
				switch($type){
					case "o": $action_str = "重置收集材料日期"; break;
					case "d": $action_str = "重置材料完稿日期"; break;
					case "e": $action_str = "重置提交电子材料日期"; break;
					case "p": $action_str = "重置提交纸质材料日期"; break;
					case "c": $action_str = "重置申报确认"; break;
				}
				save_log($u_type,$project_id,$action_str);
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"保存出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	else if($type=="confirm"){//确认
		$sql = "SELECT COUNT(*) FROM project_apply WHERE project_id='$project_id' AND u_id='".$_SESSION['u_id']."' AND u_type='$u_type' AND type='c'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$user_info = get_user_info($_SESSION['u_id']);
			$name = $user_info['name'];
			
			$sql = "INSERT INTO project_apply(
						project_id,
						u_type,
						u_id,
						name,
						type,
						process_time
					) VALUES(
						'$project_id',
						'$u_type',
						'".$_SESSION['u_id']."',
						'$name',
						'c',
						'".time()."'
					)";
		}
		else{
			$sql = "UPDATE project_apply 
					SET process_time='".time()."' 
					WHERE project_id='$project_id' AND u_id='".$_SESSION['u_id']."' AND u_type='$u_type' AND type='c'";
		}
		if(mysql_query($sql)){
			update_status_apply($project_id, $u_type, "c", 1);
			save_log($u_type,$project_id,"申报确认");
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"保存出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="read_request"){
	$project_id = $_POST['project_id'];
	$sent_time = $_POST['sent_time'];
	
	$sql = "UPDATE message SET read_time='".time()."' WHERE to_user='".$_SESSION['u_id']."' AND project_id='$project_id' AND sent_time='$sent_time'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"更新阅读消息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log($u_type,$project_id,"已阅请求");
	
	$sql = "SELECT COUNT(*) FROM message WHERE to_user='".$_SESSION['u_id']."' AND read_time=0";
	$to_read = mysql_result(mysql_query($sql),0);
	$arr = array(
		'success'=>1,
		'to_read'=>$to_read,
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="request_help"){
	$project_id = $_POST['project_id'];
	$from_u_type = $_POST['u_type'];
	$content = mysql_escape_string($_POST['content']);
	
	$project_info = get_project_info($project_id);
	$from_user_info = get_user_info($_SESSION['u_id']);
	
	$to_user_arr = [];
	$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type!='$from_u_type' AND is_curr=1";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$to_user = $row[0];
			$to_user_info = get_user_info($to_user);			
			
			$type_str = "";
			switch($from_u_type){
				case "s": $type_str = "销售"; break;
				case "t": $type_str = "技术"; break;
				case "f": $type_str = "财务"; break;
			}
			
			if(!in_array($to_user,$to_user_arr)){
				$sql = "INSERT INTO message(
							project_id,
							from_user,
							from_u_type,
							to_user,
							title,
							content,
							sent_time
						) VALUES(
							'$project_id',						
							'".$_SESSION['u_id']."',
							'$from_u_type',
							'$to_user',
							'[请求协助] ".$to_user_info['name']."发出".$type_str."协助请求',
							'$content',
							'".time()."'
						)";
				if(mysql_query($sql)){
					$msg = 	"[请求协助] ".$to_user_info['name']."，".$from_user_info['name']."于".date('Y/m/d H:i')."发出项目协助请求：\n".
							"公司名称：".$project_info['company']."\n".
							"项目名称：".$project_info['project']."\n".
							"请求类别：".$type_str."\n".
							"请求内容：".$content."\n".
							"请协助跟进。";
					send_wxqy_msg($to_user_info['userid'],$msg);
					$to_user_arr[] = $to_user;					
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"插入请求记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	
	save_log($from_u_type,$project_id,"请求协助");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_REQUEST['action']=="import_approval"){
	$pbase_name = $_REQUEST['pbase_name'];
	$save_path = _SAVE_PATH_."/upload/";
	
	if (empty($_FILES) === false) {//if file exists
		$file_name = $_FILES['file_approval']['name'];
		$tmp_name = $_FILES['file_approval']['tmp_name'];
		$file_size = $_FILES['file_approval']['size'];
		$file_type = $_FILES['file_approval']['type'];
		
		if ($file_name!="") {
			//get extension
			$temp_arr = explode(".", $file_name);
			$file_ext = array_pop($temp_arr);
			$file_ext = trim($file_ext);
			$file_ext = strtolower($file_ext);
			//check extension
			if ($file_ext!="xls" && $file_ext!="csv") {
				$arr = array(
					'success'=>0,
					'error'=>"仅支持XLS和CSV格式！"
				);
				echo json_encode($arr);
				exit;
			}
			
			$target_dir = "excel/";			
			if ($target_dir!="") {	
				$target_path = $save_path.$target_dir;
				if (!is_dir($target_path)) {
					if(!mkdirs($target_path)){
						$arr = array(
							'success'=>0,
							'error'=>"创建上传目录失败！"
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
			
			//create file name
			$new_file_name = date("Ymd")."_".rand_letter().".".$file_ext;
			$file_path = $target_path.$new_file_name;
			
			//move file
			if (move_uploaded_file($tmp_name, $file_path) === false) {
				$arr = array(
					'success'=>0,
					'error'=>"上传文件失败！"
				);
				echo json_encode($arr);
				exit;
			}
			@chmod($file_path, 0644);
			
			//Include PHPExcel_IOFactory
			include_once('PHPExcel/PHPExcel/IOFactory.php');
			
			$inputFileName = $file_path;
			
			//Read your Excel workbook
			try {
				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
			}
			catch(Exception $e){
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
			
			//Get worksheet dimensions
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();
			
			//get database approval
			$db_approval_arr = array();
			$sql = "SELECT project_id FROM project_approval";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				while($row = mysql_fetch_array($stmt)){
					$db_approval_arr[] = $row[0];
				}
			}
			
			//Loop through each row of the worksheet in turn
			$read_num = 0;
			$insert_num = 0;
			$update_num = 0;			
			for ($row = 2; $row <= $highestRow; $row++){
				$read_num++;
				//Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
												NULL,
												TRUE,
												FALSE);
				
				foreach($rowData as $row_arr){					
					$year = trim($row_arr[0]);
					$type = trim($row_arr[1]);
					$batch = trim($row_arr[2]);
					$company = trim($row_arr[3]);
					$city = trim($row_arr[4]);
					$district = trim($row_arr[5]);
					$bonus = trim($row_arr[6]);
					$remark = trim($row_arr[7]);
					
					$sql = "SELECT id FROM company WHERE name='$company'";
					$company_id = mysql_result(mysql_query($sql),0);
					
					$sql = "SELECT id FROM project WHERE name='$pbase_name' AND company_id='$company_id' AND year_apply='$year'";
					$project_id = mysql_result(mysql_query($sql),0);
					if($project_id!=""){
						$sql = "SELECT COUNT(*) FROM project_approval WHERE project_id='$project_id'";
						$has_rec = mysql_result(mysql_query($sql),0);
						if($has_rec==0){
							$sql = "INSERT INTO project_approval(
										project_id,
										batch,
										bonus,
										remark
									) VALUES(
										'$project_id',
										'$batch',
										'$bonus',
										'$remark'
									)";
							$insert_num++;
						}
						else{
							$sql = "UPDATE project_approval 
									SET batch='$batch',
										bonus='$bonus',
										remark='$remark' 
									WHERE project_id='$project_id'";
							$update_num++;
						}
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"保存项目立项信息出错：".mysql_error()
							);
							echo json_encode($arr);
							exit;
						}
						
						$progress = ($year<date('Y')-1)?20:6;
						
						$project_info = get_project_info($project_id);
						$old_remark = $project_info['remark'];
						$new_remark = ($batch!="")?$batch."立项":"";
						$new_remark .= ($bonus>0)?(($new_remark!="")?"，":"")."奖补".$bonus."万":"";
						$new_remark .= ($remark!="")?(($new_remark!="")?"，":"").$remark:"";
						$remark = $new_remark.(($new_remark!="")?"|":"").$old_remark;
						
						$sql = "UPDATE project SET progress='$progress',remark='$remark' WHERE id='$project_id'";
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"更新项目进度出错：".mysql_error()
							);
							echo json_encode($arr);
							exit;
						}
					}
				}
			}
			
			if(unlink($file_path)){
				$arr = array(
					'success'=>1,
					'read_num'=>$read_num,
					'insert_num'=>$insert_num,
					'update_num'=>$update_num,
				);
			}
			else{
				$arr = array(
					'action'=>"read excel",
					'success'=>0,
					'error'=>"删除文档出错"
				);
			}
			echo json_encode($arr);
			exit;
		}
	}
}
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$notice_url = _WF_URL_."base";

if($_POST['action']=="publish_nbase"){//批量发布
	$recommend_arr = [];
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = "";
		include_once("fetch_nbase_cond.php");
		$sql = "SELECT id 
				FROM nbase 
				$cond_str";
		$get_nbase = mysql_query($sql);
		if(mysql_num_rows($get_nbase)>0){
			while($row = mysql_fetch_array($get_nbase)){
				$nbase_id = $row['id'];
				
				$sql = "SELECT is_recommend FROM nbase WHERE id='$nbase_id'";
				$is_recommend = mysql_result(mysql_query($sql),0);
				if($is_recommend==1)$recommend_arr[] = $nbase_id;
				
				$sql = "UPDATE nbase 
						SET published_by='".$_SESSION['u_id']."',
							published_time='".time()."' 
						WHERE id='$nbase_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"发布通知出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		if(isset($_REQUEST['n_id'])){
			foreach($_REQUEST['n_id'] as $nbase_id){
				$nbase_id = mysql_escape_string($nbase_id);
				
				$sql = "SELECT is_recommend FROM nbase WHERE id='$nbase_id'";
				$is_recommend = mysql_result(mysql_query($sql),0);
				if($is_recommend==1)$recommend_arr[] = $nbase_id;
				
				$sql = "UPDATE nbase 
						SET published_by='".$_SESSION['u_id']."',
							published_time='".time()."' 
						WHERE id='$nbase_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"发布通知出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	
	if(count($recommend_arr)>0){
		$msg = "";
		foreach($recommend_arr as $nbase_id){
			$sql = "SELECT name FROM nbase WHERE id='$nbase_id'";
			$n_name = mysql_result(mysql_query($sql),0);		
			$msg .= '\n《'.$n_name.'》\n<a href=\"'.$notice_url.'?id='.$nbase_id.'\">详情</a>';
		}		
		$msg = '中科智库向您推荐'.count($recommend_arr).'个通知，详情如下：'.$msg;
		send_wxqy_msg($msg);
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="publish_notice"){//单个发布
	$nbase_id = mysql_escape_string($_POST['id']);
	$type = mysql_escape_string($_POST['type']);
	
	if($type==1){
		$sql = "UPDATE nbase 
				SET published_by='".$_SESSION['u_id']."',
					published_time='".time()."' 
				WHERE id='$nbase_id'";
	}
	else{
		$sql = "UPDATE nbase 
				SET published_by='',
					published_time='' 
				WHERE id='$nbase_id'";
	}
	if(mysql_query($sql)){
		if($type==1){//如果发布通知
			$sql = "SELECT is_recommend FROM nbase WHERE id='$nbase_id'";
			$is_recommend = mysql_result(mysql_query($sql),0);
			if($is_recommend==1){//如果是推荐
				$sql = "SELECT name FROM nbase WHERE id='$nbase_id'";
				$n_name = mysql_result(mysql_query($sql),0);
				
				//$msg = '中科智库向您推荐政策通知《'.$n_name.'》，点击 <a href=\"'.$notice_url.'?id='.$nbase_id.'\">这里</a> 查看详情。';
				//send_wxqy_msg($msg);
				
				$sql = "SELECT apply_deadline FROM nbase WHERE id='$nbase_id'";
				$apply_deadline = mysql_result(mysql_query($sql),0);
				$apply_deadline = ($apply_deadline=="0000-00-00")?"无截止日":date('Y年m月d日',strtotime($apply_deadline))."前";
				
				get_wxfw_token();
				$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$_SESSION['wxfw_token'];
				$result = https_post($url);
				$result = json_decode($result,true);
				if(array_key_exists("errcode",$result)){
					$_SESSION['wxfw_token'] = "";
					$arr = array(
						'success'=>0,
						'error'=>$result['errcode'].":".$result['errmsg']
					);
					echo json_encode($arr);
					exit;			
				}
				$openid_arr = $result['data']['openid'];
				foreach($openid_arr as $openid){
					$data = '{
								"touser":"'.$openid.'",
								"template_id":"ZOwRY-q7r3OF19WKCl_A2Exdaxyxu_SU--XEnnvxYWM",
								"url":"'._PORTAL_URL_.'base?id='.$nbase_id.'",   
								"data":{
									"first": {
										"value":"您好，中科索顿向您推荐以下最新政策通知。",
										"color":"#000000"
									},
									"keyword1":{
										"value":"《'.$n_name.'》",
										"color":"#173177"
									},
									"keyword2": {
										"value":"现正接受申报，'.$apply_deadline.'",
										"color":"#173177"
									},
									"remark":{
										"value":"点击查看详情。若需了解贵司是否有资质申请，请拨打 020-2817 2662 咨询我们的项目顾问。",
										"color":"#000000"
									}
								}
							}';
					send_wxfw_msg($data);
				}
			}
		}
		
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新文件显示状态失败：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="add"){
	$append_remark = mysql_escape_string($_POST['v_append_remark']);
	
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['nbase_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";
			
			if($key=="region"){
				$province = $_POST['v_province'];
				$region = $province;
				
				$city = $_POST['v_city'];
				$region .= ",".$city;
				
				$district = $_POST['v_district'];
				$region .= ",".$district;
			}
			if($key=="policy_type"){
				$sm_str = "";
				if(isset($_POST['v_policy_type'])){
					foreach($_POST['v_policy_type'] as $sm){
						if($sm_str!="")$sm_str .= ",";
						$sm_str .= $sm;
					}
				}
				$policy_type = $sm_str;
			}
			if($key=="remark"){
				$sql = "SELECT remark FROM nbase WHERE id='$id'";
				$old_remark = mysql_result(mysql_query($sql),0);
				
				$sql = "SELECT name FROM user WHERE id='".$_SESSION['u_id']."'";
				$remark_by = mysql_result(mysql_query($sql),0);
				
				$new_remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";			
				$remark = $new_remark.$old_remark;
			}
			
			$insert_val_str .= "'".${$key}."'";
		}
	}
	$sql = "SELECT COUNT(*) FROM nbase WHERE name='$name' AND release_date='$release_date'";
	$has_notice = mysql_result(mysql_query($sql),0);
	if($has_notice>0){
		$arr = array(
			'success'=>0,
			'error'=>"此通知已存在。"
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "INSERT INTO nbase(
				id,
				$insert_key_str,
				created_by,
				created_time
			) VALUES(
				'".get_new_id("nbase")."',
				$insert_val_str,
				'".$_SESSION['u_id']."',
				'".time()."'
			)";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"插入通知出错：".mysql_error()
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
	$update_field_str = "";
	$append_remark = mysql_escape_string($_POST['v_append_remark']);
	foreach($GLOBALS['nbase_fields'] as $key){
		if(	$key=="is_recommend" || 
			$key=="is_hot" || 
			$key=="is_top"
		){
			${$key} = (isset($_POST['v_'.$key]))?1:0;
		}
		else ${$key} = mysql_escape_string($_POST['v_'.$key]);
		
		if($key!="id"){
			if($update_field_str!="")$update_field_str .= ",";
			
			if($key=="region"){
				$province = $_POST['v_province'];
				$region = $province;
				
				$city = $_POST['v_city'];
				$region .= ",".$city;
				
				$district = $_POST['v_district'];
				$region .= ",".$district;
			}
			if($key=="policy_type"){
				$sm_str = "";
				if(isset($_POST['v_policy_type'])){
					foreach($_POST['v_policy_type'] as $sm){
						if($sm_str!="")$sm_str .= ",";
						$sm_str .= $sm;
					}
				}
				$policy_type = $sm_str;
			}
			if($key=="remark"){
				$sql = "SELECT remark FROM nbase WHERE id='$id'";
				$old_remark = mysql_result(mysql_query($sql),0);
				
				$sql = "SELECT name FROM user WHERE id='".$_SESSION['u_id']."'";
				$remark_by = mysql_result(mysql_query($sql),0);
				
				$new_remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";			
				$remark = $new_remark.$old_remark;
			}
			
			$update_field_str .= "$key='".${$key}."'";
		}
	}
	
	$sql = "UPDATE nbase 
			SET 
				$update_field_str,
				updated_by='".$_SESSION['u_id']."',
				updated_time='".time()."' 
			WHERE id='$id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"更新登记信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$arr = array(
		'success'=>1,
		'nbase_id'=>$id,
	);
	echo json_encode($arr);
	exit;
}

if($_REQUEST['action']=="import_nbase"){
	$save_path = _SAVE_PATH_."/upload/";
	
	if (empty($_FILES) === false) {//if file exists
		$file_name = $_FILES['file_excel']['name'];
		$tmp_name = $_FILES['file_excel']['tmp_name'];
		$file_size = $_FILES['file_excel']['size'];
		$file_type = $_FILES['file_excel']['type'];
		
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
			
			//get database nbase
			$db_nbase_arr = array();			
			$sql = "SELECT release_date,name FROM nbase";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				while($row = mysql_fetch_array($stmt)){
					$db_nbase_arr[] = $row[0]."|".$row[1];
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
					$serial = $row_arr[0];
					$region = trim($row_arr[1]);
					$bureau = trim($row_arr[2]);
					
					$d = 25569;
					$t = 24*60*60;
					
					$release_date = $row_arr[3];
					if($release_date!="")$release_date = date('Y-m-d',($release_date-$d)*$t);
					
					$name = trim($row_arr[4]);
					$apply_deadline = trim($row_arr[5]);
					if($apply_deadline!="")$apply_deadline = date('Y-m-d',($apply_deadline-$d)*$t);
					
					$policy_type = trim($row_arr[6]);
					$support_industry = trim($row_arr[7]);
					$apply_condition = trim($row_arr[8]);
					
					$list_link = $row_arr[9];
					$url = $row_arr[10];
					
					$remark = trim($row_arr[11]);
					
					$key = $release_date."|".$name;
					
					if($key!=""){
						if(!in_array($key,$db_nbase_arr)){
							$insert_key_str = "";
							$insert_val_str = "";
							foreach($GLOBALS['nbase_fields'] as $key){
								if($key!="id"){
									if($insert_key_str!="")$insert_key_str .= ",";
									$insert_key_str .= $key;
									
									if($insert_val_str!="")$insert_val_str .= ",";
									$insert_val_str .= "'".${$key}."'";
								}
							}
							
							$sql = "INSERT INTO nbase(
										id,
										$insert_key_str,
										created_by,
										created_time
									) VALUES(
										'".get_new_id("nbase")."',
										$insert_val_str,
										'".$_SESSION['u_id']."',
										'".time()."'
									)";
							$insert_num++;
						}
						else{
							$update_val_str = "";
							foreach($GLOBALS['nbase_fields'] as $key){
								if($key!="id"){
									if($update_val_str!="")$update_val_str .= ",";
									$update_val_str .= "$key='".${$key}."'";
								}
							}
							$sql = "UPDATE nbase 
									SET 
										$update_val_str,
										created_by='".$_SESSION['u_id']."',
										created_time='".time()."'
									WHERE release_date='$release_date' AND name='$name'";
							$update_num++;
						}
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"导入政策通知记录出错：".mysql_error()
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

function delete_file($nbase_id, $media_id){
	$sql = "SELECT file_url FROM media_table WHERE id='$media_id'";
	$file_url = mysql_result(mysql_query($sql),0);
	if($file_url!=""){
		$file_path = _ROOT_PATH_.$file_url;	
		unlink($file_path);
	}
	
	$sql = "DELETE FROM media_table WHERE id='$media_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除文件记录出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM nbase_file WHERE n_id='$nbase_id' AND m_id='$media_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除通知附件记录出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="delete_file"){
	$nbase_id = $_POST['nbase_id'];
	$media_id = $_POST['media_id'];
	
	if($nbase_id==""){
		$arr = array(
			'success'=>0,
			'error'=>"通知ID缺失"
		);
		echo json_encode($arr);
		exit;
	}
	if($media_id==""){
		$arr = array(
			'success'=>0,
			'error'=>"附件ID缺失"
		);
		echo json_encode($arr);
		exit;
	}
	
	delete_file($nbase_id, $media_id);
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="delete_nbase"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = "";
		include_once("fetch_nbase_cond.php");
		$sql = "SELECT id 
				FROM nbase 
				$cond_str";		
		$get_nbase = mysql_query($sql);
		if(mysql_num_rows($get_nbase)>0){
			while($row = mysql_fetch_array($get_nbase)){
				$nbase_id = $row['id'];
				
				$sql = "SELECT m_id FROM nbase_file WHERE n_id='$nbase_id'";
				$get_media = mysql_query($sql);
				if(mysql_num_rows($get_media)>0){
					while($m_row = mysql_fetch_array($get_media)){
						$media_id = $m_row[0];						
						delete_file($nbase_id, $media_id);
					}
				}
				
				$sql = "DELETE FROM nbase WHERE id='$nbase_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除通知记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		if(isset($_REQUEST['n_id'])){
			foreach($_REQUEST['n_id'] as $nbase_id){
				$nbase_id = mysql_escape_string($nbase_id);
				
				$sql = "SELECT m_id FROM nbase_file WHERE n_id='$nbase_id'";
				$get_media = mysql_query($sql);
				if(mysql_num_rows($get_media)>0){
					while($m_row = mysql_fetch_array($get_media)){
						$media_id = $m_row[0];						
						delete_file($nbase_id, $media_id);
					}
				}
				
				$sql = "DELETE FROM nbase WHERE id='$nbase_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除通知记录出错：".mysql_error()
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

if($_REQUEST['action']=="export_nbase"){	
	$html = "";
	
	$cond_str = "";
	include_once("fetch_nbase_cond.php");
	
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
		$sort_str = "ORDER BY status,updated_time DESC,created_time DESC";
	}
	
	$select_field_str = "";
	foreach($GLOBALS['nbase_list_fields'] as $key=>$val){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= $key;
	}
	
	$i = 0;
	$head = "";
	$data = "";
	$sql = "SELECT 
				$select_field_str 
			FROM nbase 
			$cond_str 
			$sort_str";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			if($i==0)$head .= '<tr height="40">';
			$data .= '<tr height="30">';
			foreach($GLOBALS['nbase_list_fields'] as $key){
				${$key} = $row[$key];
				
				$birthdate = ($birthdate!="0000-00-00")?$birthdate:"";
				$enroll_date = ($enroll_date!="0000-00-00")?$enroll_date:"";
				$leave_date = ($leave_date!="0000-00-00")?$leave_date:"";
				$pass_date = ($pass_date!="0000-00-00")?$pass_date:"";
				$end_date = ($end_date!="0000-00-00")?$end_date:"";
				
				$sql = "SELECT name FROM branch WHERE id='$branch'";
				$branch = mysql_result(mysql_query($sql),0);
				
				$gender = $GLOBALS['gender_opt'][$gender];			
				$education = $GLOBALS['education_opt'][$education];
				$status = $GLOBALS['status_opt'][$status];
				$has_body_check = ($has_body_check==1)?"是":"-";
				$body_check_pay = ($body_check_pay!="")?$body_check_pay:"-";
				$has_id_card = ($has_id_card==1)?"是":"-";
				$household_register = $GLOBALS['household_register_opt'][$household_register];
				$has_certificate = ($has_certificate==1)?"是":"-";
				$has_photo = ($has_photo==1)?"是":"-";
				$has_contract = ($has_contract==1)?"是":"-";
				$has_agreement = ($has_agreement==1)?"是":"-";
				$probation = $GLOBALS['probation_opt'][$probation];
				
				$remark = str_replace('|','<br/>',$remark);
				
				if($i==0)$head .= '<th style="background:#eee;color:#000;">'.$val.'</th>';
				
				if(	$key=="staff_id" || 
					$key=="bank_acct"
				){
					$data .= '<td align="center" style="vnd.ms-excel.numberformat:@">'.${$key}.'</td>';
				}
				else{
					$data .= '<td align="center">'.${$key}.'</td>';
				}
			}
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

<table border="1" bordercolor="#999999" style="border-collapse:collapse;font-family:Arial;color:#333;font-size:12px;">
	$head
	$data
</table>
HTML;
	}
	
	$filename = "nbase_".date('Y-m-d_His');
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
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_REQUEST['action']=="import_cbase"){
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
			
			//get database cbase
			$db_cbase_arr = array();			
			$sql = "SELECT year,type,batch,company FROM cbase";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				while($row = mysql_fetch_array($stmt)){
					$db_cbase_arr[] = $row[0]."|".$row[1]."|".$row[2]."|".$row[3];
				}
			}
			
			//Loop through each row of the worksheet in turn
			$read_num = 0;
			$insert_num = 0;
			$update_num = 0;
			$import_type = $_REQUEST['type'];
			if($import_type==0){
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
						$batch = $row_arr[2];
						$company = trim($row_arr[3]);
						$city = trim($row_arr[4]);
						$district = trim($row_arr[5]);
						
						$key = $year."|".$type."|".$batch."|".$company;
						
						if($key!=""){
							if(!in_array($key,$db_cbase_arr)){
								$insert_key_str = "";
								$insert_val_str = "";
								foreach($GLOBALS['cbase_fields'] as $key){
									if($insert_key_str!="")$insert_key_str .= ",";
									$insert_key_str .= $key;
									
									if($insert_val_str!="")$insert_val_str .= ",";
									$insert_val_str .= "'".${$key}."'";
								}
								
								$sql = "INSERT INTO cbase(
											id,
											$insert_key_str,
											imported_by,
											imported_time
										) VALUES(
											'".get_new_id("cbase")."',
											$insert_val_str,
											'".$_SESSION['u_id']."',
											'".time()."'
										)";
								$insert_num++;
							}
							else{
								$update_val_str = "";
								foreach($GLOBALS['cbase_fields'] as $key=>$val){
									if($key!="imported_by" && $key!="imported_time"){
										if($update_val_str!="")$update_val_str .= ",";
										$update_val_str .= "$key='".${$key}."'";
									}
								}
								$sql = "UPDATE cbase 
										SET 
											$update_val_str,
											imported_by='".$_SESSION['u_id']."',
											imported_time='".time()."'
										WHERE year='$year' AND type='$type' AND batch='$batch' AND company='$company'";
								$update_num++;
							}
							if(!mysql_query($sql)){
								$arr = array(
									'success'=>0,
									'error'=>"导入高新认定记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
						}
					}
				}
			}
			if($import_type==1){
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
						$bonus = $row_arr[6];
						
						$key = $year."|".$type."|".$batch."|".$company;
						
						if($key!=""){
							if(!in_array($key,$db_cbase_arr)){
								$insert_key_str = "";
								$insert_val_str = "";
								foreach($GLOBALS['cbase_fields'] as $key){
									if($insert_key_str!="")$insert_key_str .= ",";
									$insert_key_str .= $key;
									
									if($insert_val_str!="")$insert_val_str .= ",";
									$insert_val_str .= "'".${$key}."'";
								}
								
								$sql = "INSERT INTO cbase(
											id,
											$insert_key_str,
											imported_by,
											imported_time
										) VALUES(
											'".get_new_id("cbase")."',
											$insert_val_str,
											'".$_SESSION['u_id']."',
											'".time()."'
										)";
								$insert_num++;
							}
							else{
								$update_val_str = "";
								foreach($GLOBALS['cbase_fields'] as $key=>$val){
									if($update_val_str!="")$update_val_str .= ",";
									$update_val_str .= "$key='".${$key}."'";
								}
								$sql = "UPDATE cbase 
										SET 
											$update_val_str,
											imported_by='".$_SESSION['u_id']."',
											imported_time='".time()."'
										WHERE year='$year' AND type='$type' AND batch='$batch' AND company='$company'";
								$update_num++;
							}
							if(!mysql_query($sql)){
								$arr = array(
									'success'=>0,
									'error'=>"导入高培认定记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
						}
					}
				}
			}
			if($import_type==2){
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
						$batch = "";
						$company = trim($row_arr[2]);
						$city = trim($row_arr[3]);
						$district = trim($row_arr[4]);
						$remark = trim($row_arr[5]);
						
						$key = $year."|".$type."|".$batch."|".$company;
						
						if($key!=""){
							if(!in_array($key,$db_cbase_arr)){
								$insert_key_str = "";
								$insert_val_str = "";
								foreach($GLOBALS['cbase_fields'] as $key){
									if($insert_key_str!="")$insert_key_str .= ",";
									$insert_key_str .= $key;
									
									if($insert_val_str!="")$insert_val_str .= ",";
									$insert_val_str .= "'".${$key}."'";
								}
								
								$sql = "INSERT INTO cbase(
											id,
											$insert_key_str,
											imported_by,
											imported_time
										) VALUES(
											'".get_new_id("cbase")."',
											$insert_val_str,
											'".$_SESSION['u_id']."',
											'".time()."'
										)";
								$insert_num++;
							}
							else{
								$update_val_str = "";
								foreach($GLOBALS['cbase_fields'] as $key=>$val){
									if($update_val_str!="")$update_val_str .= ",";
									$update_val_str .= "$key='".${$key}."'";
								}
								$sql = "UPDATE cbase 
										SET 
											$update_val_str,
											imported_by='".$_SESSION['u_id']."',
											imported_time='".time()."'
										WHERE year='$year' AND type='$type' AND batch='$batch' AND company='$company'";
								$update_num++;
							}
							if(!mysql_query($sql)){
								$arr = array(
									'success'=>0,
									'error'=>"导入高培认定记录出错：".mysql_error()
								);
								echo json_encode($arr);
								exit;
							}
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

if($_POST['action']=="delete_cbase"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = "";
		include_once("fetch_cbase_cond.php");
		$sql = "SELECT id 
				FROM cbase 
				$cond_str";		
		$get_cbase = mysql_query($sql);
		if(mysql_num_rows($get_cbase)>0){
			while($row = mysql_fetch_array($get_cbase)){
				$c_id = $row['id'];
				
				$sql = "DELETE FROM cbase WHERE id='$c_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除认定记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		if(isset($_REQUEST['c_id'])){
			foreach($_REQUEST['c_id'] as $c_id){
				$c_id = mysql_escape_string($c_id);
				
				$sql = "DELETE FROM cbase WHERE id='$c_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除认定记录出错：".mysql_error()
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

if($_REQUEST['action']=="export_cbase"){	
	$html = "";
	
	$cond_str = "";
	include_once("fetch_cbase_cond.php");
	
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
	foreach($GLOBALS['cbase_fields'] as $key=>$val){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= $key;
	}
	
	$i = 0;
	$head = "";
	$data = "";
	$sql = "SELECT 
				$select_field_str 
			FROM cbase 
			$cond_str 
			$sort_str";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			if($i==0)$head .= '<tr height="40">';
			$data .= '<tr height="30">';
			foreach($GLOBALS['cbase_fields'] as $key=>$val){
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
	
	$filename = "cbase_".date('Y-m-d_His');
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
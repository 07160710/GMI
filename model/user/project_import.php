<?php
header('Content-type: text/html; charset=utf-8');

require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_REQUEST['action']=="import_project"){
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
			
			//if($_REQUEST['type']=="apply")import_assign();
			if($_REQUEST['type']=="apply")import_apply();
		}
	}
}

function import_apply(){
	global $file_path;
	global $sheet;
	
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	
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
			$company = trim($row_arr[0]);
			$sql = "SELECT id FROM company WHERE name='$company'";
			$company_id = mysql_result(mysql_query($sql),0);
			
			$name = trim($row_arr[1]);
			$year_apply = trim($row_arr[3]);
			
			$sql = "SELECT id FROM project WHERE company_id='$company_id' AND name='$name' AND year_apply='$year_apply'";
			$project_id = mysql_result(mysql_query($sql),0);
			
			$sales = trim($row_arr[5]);
			$technology = trim($row_arr[6]);					
			$finance = trim($row_arr[7]);
			
			$d = 25569;
			$t = 24*60*60;
			
			$apply_e = trim($row_arr[8]);
			$apply_e = ($apply_e!="")?($apply_e-$d)*$t:0;
			$apply_p = trim($row_arr[9]);
			$apply_p = ($apply_p!="")?($apply_p-$d)*$t:0;
			
			if($project_id!=""){				
				if($technology!=""){
					$t_arr = explode("|",$technology);
					foreach($t_arr as $t_name){
						if($t_name!=""){
							$sql = "SELECT id FROM user WHERE name='$t_name'";
							$t_id = mysql_result(mysql_query($sql),0);
							
							if($apply_e>0){
								$sql = "SELECT COUNT(*) FROM project_apply WHERE project_id='$project_id' AND u_type='t' AND u_id='$t_id' AND type='e'";
								$has_rec = mysql_result(mysql_query($sql),0);
								if($has_rec==0){
									$sql = "INSERT INTO project_apply(
												project_id,
												u_type,
												u_id,
												name,
												type,
												process_time
											) VALUES(
												'$project_id',
												't',
												'$t_id',
												'$t_name',
												'e',
												'$apply_e'
											)";
								}
								else{
									$sql = "UPDATE project_apply 
											SET process_time='$apply_e' 
											WHERE project_id='$project_id' AND u_type='t' AND u_id='$t_id' AND type='e'";
								}
								mysql_query($sql);
							}
							if($apply_p>0){
								$sql = "SELECT COUNT(*) FROM project_apply WHERE project_id='$project_id' AND u_type='t' AND u_id='$t_id' AND type='p'";
								$has_rec = mysql_result(mysql_query($sql),0);
								if($has_rec==0){
									$sql = "INSERT INTO project_apply(
												project_id,
												u_type,
												u_id,
												name,
												type,
												process_time
											) VALUES(
												'$project_id',
												't',
												'$t_id',
												'$t_name',
												'p',
												'$apply_p'
											)";
								}
								else{
									$sql = "UPDATE project_apply 
											SET process_time='$apply_p' 
											WHERE project_id='$project_id' AND u_type='t' AND u_id='$t_id' AND type='p'";
								}
								mysql_query($sql);
							}
						}
					}
				}
				
				if($finance!=""){
					$f_arr = explode("|",$finance);
					foreach($f_arr as $f_name){
						if($f_name!=""){
							$sql = "SELECT id FROM user WHERE name='$f_name'";
							$f_id = mysql_result(mysql_query($sql),0);
							
							if($apply_e>0){
								$sql = "SELECT COUNT(*) FROM project_apply WHERE project_id='$project_id' AND u_type='f' AND u_id='$f_id' AND type='e'";
								$has_rec = mysql_result(mysql_query($sql),0);
								if($has_rec==0){
									$sql = "INSERT INTO project_apply(
												project_id,
												u_type,
												u_id,
												name,
												type,
												process_time
											) VALUES(
												'$project_id',
												'f',
												'$f_id',
												'$f_name',
												'e',
												'$apply_e'
											)";
								}
								else{
									$sql = "UPDATE project_apply 
											SET process_time='$apply_e' 
											WHERE project_id='$project_id' AND u_type='f' AND u_id='$f_id' AND type='e'";
								}
								mysql_query($sql);
							}
							if($apply_p>0){
								$sql = "SELECT COUNT(*) FROM project_apply WHERE project_id='$project_id' AND u_type='f' AND u_id='$f_id' AND type='p'";
								$has_rec = mysql_result(mysql_query($sql),0);
								if($has_rec==0){
									$sql = "INSERT INTO project_apply(
												project_id,
												u_type,
												u_id,
												name,
												type,
												process_time
											) VALUES(
												'$project_id',
												'f',
												'$f_id',
												'$f_name',
												'p',
												'$apply_p'
											)";
								}
								else{
									$sql = "UPDATE project_apply 
											SET process_time='$apply_p' 
											WHERE project_id='$project_id' AND u_type='f' AND u_id='$f_id' AND type='p'";
								}
								mysql_query($sql);
							}
						}
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

function import_assign(){
	global $file_path;
	global $sheet;
	
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	
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
			$company = trim($row_arr[0]);
			$sql = "SELECT id FROM company WHERE name='$company'";
			$company_id = mysql_result(mysql_query($sql),0);
			
			$name = trim($row_arr[1]);
			$year_apply = trim($row_arr[3]);
			
			$sql = "SELECT id FROM project WHERE company_id='$company_id' AND name='$name' AND year_apply='$year_apply'";
			$project_id = mysql_result(mysql_query($sql),0);
			
			$sales = trim($row_arr[5]);
			$technology = trim($row_arr[6]);					
			$finance = trim($row_arr[7]);
			
			if($project_id!=""){
				if($sales!=""){
					$s_arr = explode("|",$sales);
					$s = 0;
					foreach($s_arr as $s_name){
						if($s_name!=""){
							$sql = "SELECT id FROM user WHERE name='$s_name'";
							$s_id = mysql_result(mysql_query($sql),0);
							
							$is_curr = ($s==0)?1:0;
							
							$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND u_id='$s_id'";
							$has_rec = mysql_result(mysql_query($sql),0);
							if($has_rec==0){								
								$sql = "INSERT INTO project_assign(
											project_id,
											u_type,
											u_id,
											name,
											is_curr
										) VALUES(
											'$project_id',
											's',
											'$s_id',
											'$s_name',
											'$is_curr'
										)";
								mysql_query($sql);
							}
							else{
								$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
								$has_curr = mysql_result(mysql_query($sql),0);
								if($has_curr==0){
									$sql = "UPDATE project_assign 
											SET is_curr='$is_curr' 
											WHERE project_id='$project_id' AND u_type='s' AND u_id='$s_id'";
									mysql_query($sql);
								}
							}
						}
						$s++;
					}
				}
				
				if($technology!=""){
					$t_arr = explode("|",$technology);
					$t = 0;
					foreach($t_arr as $t_name){
						if($t_name!=""){
							$sql = "SELECT id FROM user WHERE name='$t_name'";
							$t_id = mysql_result(mysql_query($sql),0);
							
							$is_curr = ($t==0)?1:0;
							
							$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='t' AND u_id='$t_id'";
							$has_rec = mysql_result(mysql_query($sql),0);
							if($has_rec==0){
								$sql = "INSERT INTO project_assign(
											project_id,
											u_type,
											u_id,
											name,
											is_curr
										) VALUES(
											'$project_id',
											't',
											'$t_id',
											'$t_name',
											'$is_curr'
										)";
								mysql_query($sql);
							}
							else{
								$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='t' AND is_curr=1";
								$has_curr = mysql_result(mysql_query($sql),0);
								if($has_curr==0){
									$sql = "UPDATE project_assign 
											SET is_curr='$is_curr' 
											WHERE project_id='$project_id' AND u_type='t' AND u_id='$t_id'";
									mysql_query($sql);
								}
							}
						}
						$t++;
					}
				}
				
				if($finance!=""){
					$f_arr = explode("|",$finance);
					$f = 0;
					foreach($f_arr as $f_name){
						if($f_name!=""){
							$sql = "SELECT id FROM user WHERE name='$f_name'";
							$f_id = mysql_result(mysql_query($sql),0);
							
							$is_curr = ($f==0)?1:0;
							
							$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='f' AND u_id='$f_id'";
							$has_rec = mysql_result(mysql_query($sql),0);
							if($has_rec==0){
								$sql = "INSERT INTO project_assign(
											project_id,
											u_type,
											u_id,
											name,
											is_curr
										) VALUES(
											'$project_id',
											'f',
											'$f_id',
											'$f_name',
											'$is_curr'
										)";
								mysql_query($sql);
							}
							else{
								$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='f' AND is_curr=1";
								$has_curr = mysql_result(mysql_query($sql),0);
								if($has_curr==0){
									$sql = "UPDATE project_assign 
											SET is_curr='$is_curr' 
											WHERE project_id='$project_id' AND u_type='f' AND u_id='$f_id'";
									mysql_query($sql);
								}
							}
						}
						$f++;
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
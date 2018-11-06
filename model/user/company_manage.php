<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="unbind_member"){
	$company_id = $_POST['company_id'];
	$mobile = $_POST['mobile'];
	
	$sql = "UPDATE member SET active=0,code='' WHERE company_id='$company_id' AND mobile='$mobile'";
	if(mysql_query($sql)){
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"解绑企业员工出错：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="reset_code"){
	$company_id = $_POST['id'];
	$code = generate_code($company_id);
	save_log("c",$company_id,"重设绑定码");
	$arr = array(
		'success'=>1,
		'code'=>$code,
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="add"){
	$new_id = get_new_id("company");
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['company_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="name"){
				$sql = "SELECT COUNT(*) FROM company WHERE name='".trim($name)."'";
				$has_company = mysql_result(mysql_query($sql),0);
				if($has_company){
					$arr = array(
						'success'=>0,
						'error'=>"此公司名称已存在，请勿重复添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			if($key=="code")$code = generate_code($new_id);
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";		
			$insert_val_str .= "'".${$key}."'";
		}
	}
	
	$sql = "INSERT INTO company(
				id,
				$insert_key_str
			) VALUES(
				'$new_id',
				$insert_val_str
			)";
	if(mysql_query($sql)){
		if(isset($_POST['platform'])){
			foreach($_POST['platform'] as $pf_id){
				$platform_id = mysql_escape_string($_POST['platform_'.$pf_id]);
				$project_id = mysql_escape_string($_POST['project_id_'.$pf_id]);
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
							password
						) VALUES(
							'$platform_id',
							'$new_id',
							'$project_id',
							'$type',
							'$account',
							'$password'
						)";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"保存公司平台记录出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
		
		if(isset($_POST['employee'])){
			foreach($_POST['employee'] as $e_id){
				$is_leader = mysql_escape_string($_POST['is_leader_'.$e_id]);
				$name = mysql_escape_string($_POST['name_'.$e_id]);
				$mobile = mysql_escape_string($_POST['mobile_'.$e_id]);
				$position = mysql_escape_string($_POST['position_'.$e_id]);
				$email = mysql_escape_string($_POST['email_'.$e_id]);
				
				$sql = "INSERT INTO member(
							company_id,
							mobile,
							name,
							position,
							is_leader,
							email
						) VALUES(
							'$new_id',
							'$mobile',
							'$name',
							'$position',
							'$is_leader',
							'$email'
						)";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"保存公司员工信息出错：".mysql_error()
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
			'error'=>"添加公司信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("c",$new_id,"创建");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="edit"){
	$id = mysql_escape_string($_POST['id']);
	$update_field_str = "";
	foreach($GLOBALS['company_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
		
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$key='".${$key}."'";
		}
	}
	
	//平台账号
	$db_account_arr = [];
	$sql = "SELECT company_id,platform_id,project_id,type FROM platform_account WHERE company_id='$id'";
	$get_db_account = mysql_query($sql);
	if(mysql_num_rows($get_db_account)>0){
		while($row = mysql_fetch_array($get_db_account)){
			$db_account_arr[] = $row[0]."|".$row[1]."|".$row[2]."|".$row[3];
		}
	}
	
	$account_arr = [];
	if(isset($_POST['platform'])){
		foreach($_POST['platform'] as $pf_id){
			$platform_id = mysql_escape_string($_POST['platform_'.$pf_id]);
			$project_id = mysql_escape_string($_POST['project_id_'.$pf_id]);
			$type = mysql_escape_string($_POST['type_'.$pf_id]);
			$account = mysql_escape_string($_POST['account_'.$pf_id]);
			$password = mysql_escape_string($_POST['password_'.$pf_id]);
			$remark = mysql_escape_string($_POST['remark_'.$pf_id]);
			
			$account_arr[] = $id."|".$platform_id."|".$project_id."|".$type;
			
			$sql = "SELECT COUNT(*) FROM platform_account WHERE company_id='$id' AND platform_id='$platform_id' AND project_id='$project_id' AND type='$type'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO platform_account(
							platform_id,
							company_id,
							project_id,
							type,
							account,
							password
						) VALUES(
							'$platform_id',
							'$id',
							'$project_id',
							'$type',
							'$account',
							'$password'
						)";
			}
			else{
				$sql = "UPDATE platform_account 
						SET account='$account',
							password='$password' 
						WHERE company_id='$id' AND platform_id='$platform_id' AND project_id='$project_id' AND type='$type'";
			}
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"保存公司平台记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	foreach($db_account_arr as $key){
		if(!in_array($key,$account_arr)){
			$key_arr = explode("|",$key);
			$sql = "DELETE FROM platform_account WHERE company_id='".$key_arr[0]."' AND platform_id='".$key_arr[1]."' AND project_id='".$key_arr[2]."' AND type='".$key_arr[3]."'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"删除公司平台记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	
	//企业员工
	$db_employee_arr = [];
	$sql = "SELECT mobile FROM member WHERE company_id='$id'";
	$get_db_employee = mysql_query($sql);
	if(mysql_num_rows($get_db_employee)>0){
		while($row = mysql_fetch_array($get_db_employee)){
			$db_employee_arr[] = $row[0];
		}
	}
	
	$employee_arr = [];
	if(isset($_POST['employee'])){
		foreach($_POST['employee'] as $e_id){
			$is_leader = mysql_escape_string($_POST['is_leader_'.$e_id]);
			$name = mysql_escape_string($_POST['name_'.$e_id]);
			$mobile = mysql_escape_string($_POST['mobile_'.$e_id]);
			$position = mysql_escape_string($_POST['position_'.$e_id]);
			$email = mysql_escape_string($_POST['email_'.$e_id]);
			
			$employee_arr[] = $mobile;
			
			$sql = "SELECT COUNT(*) FROM member WHERE company_id='$id' AND mobile='$mobile'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO member(
							company_id,
							mobile,
							name,
							position,
							is_leader,
							email
						) VALUES(
							'$id',
							'$mobile',
							'$name',
							'$position',
							'$is_leader',
							'$email'
						)";
			}
			else{
				$sql = "UPDATE member 
						SET name='$name',
							position='$position',
							is_leader='$is_leader',
							email='$email' 
						WHERE company_id='$id' AND mobile='$mobile'";
			}
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"保存公司员工信息出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	foreach($db_employee_arr as $key){
		if(!in_array($key,$employee_arr)){
			$sql = "DELETE FROM member WHERE company_id='$id' AND mobile='$key'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"删除公司员工信息出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	
	$sql = "UPDATE company 
			SET $update_field_str  
			WHERE id='$id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"更新公司信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("c",$id,"更新");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="delete_company"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = "";
		include_once("fetch_company_cond.php");
		$sql = "SELECT id,name 
				FROM company 
				$cond_str";		
		$get_company = mysql_query($sql);
		if(mysql_num_rows($get_company)>0){
			while($row = mysql_fetch_array($get_company)){
				$id = $row[0];
				$name = $row[1];
				
				$sql = "DELETE FROM company WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除公司信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				save_log("c",$id,"删除[$name]");
			}
		}
	}
	else{
		if(isset($_REQUEST['company_id'])){
			foreach($_REQUEST['company_id'] as $company_id){
				$company_id = mysql_escape_string($company_id);
				
				$sql = "SELECT name FROM company WHERE id='$company_id'";
				$name = mysql_result(mysql_query($sql),0);
				
				$sql = "DELETE FROM company WHERE id='$company_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除公司信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				save_log("c",$company_id,"删除[$name]");
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

function delete_file($company_id, $media_id){
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
	
	$sql = "DELETE FROM company_file WHERE company_id='$company_id' AND media_id='$media_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除企业附件记录出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
}
if($_POST['action']=="delete_file"){
	$company_id = $_POST['company_id'];
	$media_id = $_POST['media_id'];
	
	if($company_id==""){
		$arr = array(
			'success'=>0,
			'error'=>"公司ID缺失"
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
	
	delete_file($company_id, $media_id);
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_REQUEST['action']=="import_company"){
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
			
			//get database company
			$db_company_arr = array();
			$sql = "SELECT name FROM company";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				while($row = mysql_fetch_array($stmt)){
					$db_company_arr[] = $row[0];
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
					$name = trim($row_arr[0]);
					
					$province = trim($row_arr[1]);
					$sql = "SELECT id FROM region WHERE name='$province'";
					$province = mysql_result(mysql_query($sql),0);
					
					$city = trim($row_arr[2]);
					$sql = "SELECT id FROM region WHERE name='$city'";
					$city = mysql_result(mysql_query($sql),0);
					
					$district = trim($row_arr[3]);
					$sql = "SELECT id FROM region WHERE name='$district'";
					$district = mysql_result(mysql_query($sql),0);
					
					$key = $name;
					
					if($key!=""){
						if(in_array($key,$db_company_arr)){
							$sql = "UPDATE company 
									SET province='$province',
										city='$city',
										district='$district' 
									WHERE name='$name'";
							$update_num++;
						}
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"导入公司记录出错：".mysql_error()
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

if($_REQUEST['action']=="export_company"){	
	$html = "";
	
	$cond_str = "";
	include_once("fetch_company_cond.php");
	
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
		$sort_str = "ORDER BY id";
	}
	
	$select_field_str = "";
	foreach($GLOBALS['company_list_fields'] as $key=>$val){
		if($key!="file" && $key!="code"){
			if($select_field_str!="")$select_field_str .= ",";
			$select_field_str .= $key;
		}
	}
	
	$i = 0;
	$head = "";
	$data = "";
	$sql = "SELECT 
				$select_field_str 
			FROM company 
			$cond_str 
			$sort_str";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			if($i==0)$head .= '<tr height="40">';
			$data .= '<tr height="30">';
			foreach($GLOBALS['company_list_fields'] as $key=>$val){
				if($key!="file" && $key!="code"){
					${$key} = $row[$key];
					
					$val_arr = explode("#",$val);
					$title = $val_arr[0];
					$width = ($val_arr[1]!="")?"width=\"".$val_arr[1]."\"":"";
					if($i==0)$head .= '<th style="background:#eee;color:#000;" '.$width.'>'.$title.'</th>';				
					
					if(	$key=="province" || 
						$key=="city" || 
						$key=="district"
					){
						$sql = "SELECT name FROM region WHERE id='".${$key}."'";
						${$key} = mysql_result(mysql_query($sql),0);
					}
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
	
	$filename = "company_".date('Y-m-d_His');
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
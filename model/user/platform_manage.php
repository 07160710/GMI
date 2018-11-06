<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="add"){
	$new_id = get_new_id("platform");
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['platform_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="name"){
				$sql = "SELECT COUNT(*) FROM platform WHERE name='$name'";
				$has_platform = mysql_result(mysql_query($sql),0);
				if($has_platform){
					$arr = array(
						'success'=>0,
						'error'=>"此平台名称已存在，请勿重复添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";		
			$insert_val_str .= "'".${$key}."'";
		}
	}
	
	$sql = "INSERT INTO platform(
				id,
				$insert_key_str
			) VALUES(
				'$new_id',
				$insert_val_str
			)";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"添加平台信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("pf",$new_id,"创建");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="edit"){
	$id = mysql_escape_string($_POST['id']);
	$update_field_str = "";
	foreach($GLOBALS['platform_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
		
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$key='".${$key}."'";
		}
	}
	
	$sql = "UPDATE platform 
			SET $update_field_str  
			WHERE id='$id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"更新平台信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("pf",$id,"更新");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="delete_platform"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = "";
		include_once("fetch_platform_cond.php");
		$sql = "SELECT id,name 
				FROM platform 
				$cond_str";		
		$get_platform = mysql_query($sql);
		if(mysql_num_rows($get_platform)>0){
			while($row = mysql_fetch_array($get_platform)){
				$id = $row[0];
				$name = $row[1];
				
				$sql = "DELETE FROM platform WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除平台信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		if(isset($_REQUEST['platform_id'])){
			foreach($_REQUEST['platform_id'] as $platform_id){
				$platform_id = mysql_escape_string($platform_id);
				
				$sql = "SELECT name FROM platform WHERE id='$platform_id'";
				$name = mysql_result(mysql_query($sql),0);
				
				$sql = "DELETE FROM platform WHERE id='$platform_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除平台信息出错：".mysql_error()
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

if($_REQUEST['action']=="import_platform"){
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
			
			//get database platform
			$db_platform_arr = array();
			$sql = "SELECT name FROM platform";
			$stmt = mysql_query($sql);
			if(mysql_num_rows($stmt)>0){
				while($row = mysql_fetch_array($stmt)){
					$db_platform_arr[] = $row[0];
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
					$url = trim($row_arr[1]);
					$remark = trim($row_arr[2]);
					
					$key = $name;
					
					if($key!=""){
						if(!in_array($key,$db_platform_arr)){
							$sql = "INSERT INTO platform(
										id,
										name,
										url,
										remark
									) VALUES(
										'".get_new_id("platform")."',
										'$name',
										'$url',
										'$remark'
									)";
							$insert_num++;
						}
						else{
							$sql = "UPDATE platform 
									SET url='$url',
										remark='$remark' 
									WHERE name='$name'";
							$update_num++;
						}
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"导入平台记录出错：".mysql_error()
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

if($_REQUEST['action']=="export_platform"){	
	$html = "";
	
	$cond_str = "";
	include_once("fetch_platform_cond.php");
	
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
	foreach($GLOBALS['platform_fields'] as $key=>$val){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= $key;
	}
	
	$i = 0;
	$head = "";
	$data = "";
	$sql = "SELECT 
				$select_field_str 
			FROM platform 
			$cond_str 
			$sort_str";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			if($i==0)$head .= '<tr height="40">';
			$data .= '<tr height="30">';
			foreach($GLOBALS['platform_fields'] as $key=>$val){
				${$key} = $row[$key];
				
				if($i==0)$head .= '<th style="background:#eee;color:#000;">'.$val.'</th>';
				
				$data .= '<td align="center">'.${$key}.'</td>';
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
	
	$filename = "platform_".date('Y-m-d_His');
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
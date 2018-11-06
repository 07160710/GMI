<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$save_path = _ROOT_PATH_._UPLOAD_FOLDER_."upload/";

$parent_id = $_REQUEST['parent_id'];

//if file exists
if (empty($_FILES) === false) {
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
		if ($file_ext!="xls") {
			$error = "Unauthorized file extension, ONLY XLS format is allowed.";
			$arr = array(
				'success'=>0,
				'error'=>$error
			);
			echo json_encode($arr);
			exit;
		}
		
		$dir_name = "tmp";
		$target_dir = "";		
		if ($dir_name!="") {
			$target_path = $save_path.$dir_name."/".$target_dir;
			if (!is_dir($target_path)) {
				if(!mkdirs($target_path)){
					$error = "Failed in creating upload folder.";
					$arr = array(
						'success'=>0,
						'error'=>$error
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
			$error = "Failed in uploading file.";
			$arr = array(
				'success'=>0,
				'error'=>$error
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
		
		$faq_fields = array(
			'title_en',
			'title',
			'content_en',
			'content',
		);
		
		//get faq database list
		$db_faq_arr = array();
		$sql = "SELECT title FROM content_table WHERE type='faq' AND parent_id='$parent_id'";
		$get_faq = mysql_query($sql);
		if(mysql_num_rows($get_faq)>0){
			while($row = mysql_fetch_array($get_faq)){
				$db_faq_arr[] = $row['title'];
			}
		}
		
		$r = 1;
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
			
			$title_key = $rowData[0][1];
			if(!in_array($title_key,$db_faq_arr)){
				$insert_field_str = "";
				foreach($faq_fields as $key){
					if($insert_field_str!="")$insert_field_str .= ",\n";
					$insert_field_str .= $key;
				}
				
				$name_en = "";
				$insert_val_str = "";
				foreach($rowData as $row_arr){
					$i = 0;
					foreach($row_arr as $val){
						if($insert_val_str!="")$insert_val_str .= ",\n";						
						$insert_val_str .= "'".mysql_escape_string($val)."'";
						
						if($i==0)$name_en = $val;
						$i++;
					}
				}
				$alias = str_replace(' ','-',$name_en);
				$alias = preg_replace('/[^0-9a-zA-Z\-]+/','',$alias);
				
				$sql = "SELECT * FROM content_table WHERE id='$parent_id'";
				$stmt = mysql_query($sql);
				if(mysql_num_rows($stmt)>0){
					$c_row = mysql_fetch_array($stmt);
					$p_level = $c_row['level'];
					$p_route = $c_row['route'];
				}
				
				$new_id = get_new_id("content_table");
				
				$sql = "INSERT INTO content_table(
							id,
							parent_id,
							sort_order,
							level,
							name,
							alias,
							route,
							type,
							created_time,
							$insert_field_str
						) VALUES(
							'$new_id',
							'$parent_id',
							'$r',
							'".($p_level+1)."',
							'$name_en',
							'$alias',
							'".$p_route."/".$new_id."',
							'faq',
							'".CURR_TIME."',
							$insert_val_str
						)";
				if(mysql_query($sql)){
					$insert_num++;
					$r++;
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"Error in inserting FAQ: ".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
			else{//update shop
				$update_field_str = "";
				foreach($rowData as $row_arr){
					for($i=0;$i<count($row_arr);$i++){
						if($update_field_str!="")$update_field_str .= ",\n";
						$update_field_str .= $faq_fields[$i]."='".mysql_escape_string($row_arr[$i])."'";
					}
				}
				
				$sql = "UPDATE content_table 
						SET $update_field_str 
						WHERE parent_id='$parent_id' AND type='faq' AND title='$title_key'";
				if(mysql_query($sql)){
					$update_num++;
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"Error in updating FAQ: ".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
		
		if(unlink($inputFileName)){
			$arr = array(
				'success'=>1,
				'read_num'=>$read_num,
				'insert_num'=>$insert_num,
				'update_num'=>$update_num,
			);
			echo json_encode($arr);
			exit;
		}
		else{			
			$arr = array(
				'success'=>0,
				'error'=>"UNLINK: ".$inputFileName
			);
			echo json_encode($arr);
			exit;
		}
	}
}
?>

<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$log_type = "";

if($_POST['action']=="manual"){
	if(isset($_POST['tb'])){
		foreach($_POST['tb'] as $table)
		{
			$table = mysql_escape_string($table);
			
			$result = mysql_query('SELECT * FROM '.$table);
			$num_fields = mysql_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysql_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j<($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		
		$bk_db_folder = _ADMIN_PATH_._BK_DB_FOLDER_;
		if(!is_dir($bk_db_folder)){
			if(!mkdirs($bk_db_folder)){
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"创建备份文件夹失败"
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$handle = fopen($bk_db_folder."db-".date('Y_m_d_His').".sql","w+");
		if(fwrite($handle,$return)){
			fclose($handle);
			
			$table_list_str = "";
			foreach($_POST['tb'] as $table_name){
				if($table_list_str!="")$table_list_str .= "|";
				$table_list_str .= $table_name;
			}
			$sql = "INSERT INTO backup_table(
						table_list,
						file_name,
						created_by
					) VALUES(
						'$table_list_str',
						'db-".date('Y_m_d_His').".sql',
						'".$_SESSION['u_id']."'
					)";
			if(!mysql_query($sql)){
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"插入备份记录失败：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
			else{
				$file_name = "db-".date('Y_m_d_His').".sql";
				$log_type = "create";
				include_once("backup_log.php");
				
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>1
				);
			}
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>"保存备份文件失败。"
			);
		}
		echo json_encode($arr);
		exit;
	}
}

function restore_db($file){
	$file = file_get_contents($file);
	$sql_arr = explode(";\n",$file);
	foreach($sql_arr as $sql){
		if($sql!="\n" && $sql!="\n\n" && $sql!="\n\n\n"){
			if(!mysql_query($sql)){
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"ERROR|$sql_str"
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	return true;
}

if($_POST['action']=="restore"){
	$file = mysql_escape_string($_POST['file']);
	if(file_exists($file)){	
		if(restore_db($file)){
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>1
			);
		}
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"ERROR|NO FILE"
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="delete"){
	$file = mysql_escape_string($_POST['file']);
	if(file_exists($file)){
		if(unlink($file)){
			$file_arr = explode("/",$file);
			$file_name = $file_arr[count($file_arr)-1];
		
			$delete_backup = "DELETE FROM backup_table WHERE file_name='$file_name'";
			if(!mysql_query($delete_backup)){
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"ERROR|DELETE BACKUP"
				);
				echo json_encode($arr);
				exit;
			}
			else{
				$log_type = "delete";
				include_once("backup_log.php");
				
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>1
				);
			}
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>"ERROR|UNLINK"
			);
		}
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"ERROR|NO FILE"
		);
	}
	echo json_encode($arr);
	exit;
}
?>
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$file_url = "";

//if file exists
if (empty($_FILES) === false) {
	if(isset($_FILES['file'])){
		$file_name = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$file_size = $_FILES['file']['size'];
		$ext_arr = explode(".",$file_name);
		$ext_arr = array_reverse($ext_arr);
		$ext = $ext_arr[0];
		$m_name = $ext_arr[1];
	}
	$media_id = $_REQUEST['m_id'];
	$nbase_id = $_REQUEST['nbase_id'];
	
	$m_alias = date('Ymd')."_".rand_letter();
	$media_name = $m_alias.".".$ext;
	
	if ($file_name!="") {
		if (@is_dir(_SAVE_PATH_) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"上传文件夹不存在。"
			);
			echo json_encode($arr);
			exit;
		}
		if (@is_writable(_SAVE_PATH_) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"上传文件夹没有写权限。"
			);
			echo json_encode($arr);
			exit;
		}
		if (@is_uploaded_file($tmp_name) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"上传文件失败。"
			);
			echo json_encode($arr);
			exit;
		}
		if ($file_size > MAX_UPLOAD_SIZE) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"上传文件大小超过限制。"
			);
			echo json_encode($arr);
			exit;
		}
		
		if(isset($_REQUEST['m_id']))$file_dir = fetch_route($media_id,'media_table')."/";
		if(isset($_REQUEST['nbase_id']))$file_dir = "Notification-Base/".$nbase_id."/";
		
		if(!is_dir(_MEDIA_PATH_.$file_dir)){
			if(!mkdirs(_MEDIA_PATH_.$file_dir)){
				$arr = array(
					'action'=>"upload media",
					'success'=>0,
					'error'=>"创建文件夹失败。".$file_dir
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$file_link = $file_dir.$media_name;
		$file_path = _MEDIA_PATH_.$file_link;
		$file_path = iconv('UTF-8','GB2312',$file_path);
		
		//move file		
		if (move_uploaded_file($tmp_name, $file_path) === false) {
			$arr = array(
				'action'=>"upload media",
				'success'=>0,
				'error'=>"保存文件到服务器失败。"
			);
			echo json_encode($arr);
			exit;
		}
		else{
			@chmod($file_path, 0777);		
			$file_url = _UPLOAD_FOLDER_._MEDIA_FOLDER_.$file_link;
			
			if(isset($_REQUEST['m_id'])){
				$parent_id = $media_id;
				$cond_str = "WHERE parent_id='$parent_id' AND alias='$m_alias'";
				$sql = "SELECT COUNT(*) FROM media_table $cond_str";
			}
			if(isset($_REQUEST['nbase_id'])){
				$sql = "SELECT id FROM media_table WHERE alias='Notification-Base'";
				$parent_id = mysql_result(mysql_query($sql),0);
				$cond_str = "WHERE parent_id='$parent_id' AND name='$m_name' AND ext='$ext'";
				$sql = "SELECT COUNT(*) FROM media_table $cond_str";
			}
			
			$has_file = mysql_result(mysql_query($sql),0);
			if($has_file==0){
				$media_id = get_new_id('media_table');
				
				$sql = "SELECT MAX(sort_order) FROM media_table WHERE parent_id='$parent_id'";
				$new_sort = mysql_result(mysql_query($sql),0)+1;
				
				$sql = "SELECT route FROM media_table WHERE id='$parent_id'";
				$new_route = mysql_result(mysql_query($sql),0)."/".get_new_id('media_table');
				
				$sql = "SELECT level FROM media_table WHERE id='$parent_id'";
				$new_level = mysql_result(mysql_query($sql),0)+1;
				
				$sql = "INSERT INTO media_table(
							id,
							parent_id,
							sort_order,
							name,
							alias,
							route,
							type,
							ext,
							size,
							file_url,
							level,
							created_by,
							created_time,
							uploaded_by,
							uploaded_time
						) VALUES(
							'$media_id',
							'$parent_id',
							'$new_sort',
							'$m_name',
							'$m_alias',
							'$new_route',
							'file',
							'$ext',
							'$file_size',
							'$file_url',
							'$new_level',
							'".$_SESSION['u_id']."',
							'".time()."',
							'".$_SESSION['u_id']."',
							'".time()."'
						)";
			}
			else{
				$sql = "SELECT id FROM media_table WHERE parent_id='$parent_id' AND alias='$m_alias'";
				$media_id = mysql_result(mysql_query($sql),0);
				
				$sql = "UPDATE media_table 
						SET	
							name='$m_name',
							ext='$ext',
							size='$file_size',
							file_url='$file_url',
							uploaded_by='".$_SESSION['u_id']."',
							uploaded_time='".time()."' 
						$cond_str";
			}
			if(mysql_query($sql)){
				if(isset($_REQUEST['nbase_id'])){
					$sql = "SELECT id FROM media_table $cond_str";
					$media_id = mysql_result(mysql_query($sql),0);
					
					$sql = "SELECT COUNT(*) FROM nbase_file WHERE n_id='$nbase_id' AND m_id='$media_id'";
					$has_rec = mysql_result(mysql_query($sql),0);
					if($has_rec==0){
						$sql = "INSERT INTO nbase_file(
									n_id,
									m_id
								) VALUES(
									'$nbase_id',
									'$media_id'
								)";
						if(!mysql_query($sql)){
							$arr = array(
								'success'=>0,
								'error'=>"保存通知库附件记录失败：".mysql_error()
							);
							echo json_encode($arr);
							exit;
						}
					}
				}
			}
			else{
				$arr = array(
					'action'=>"upload media",
					'success'=>0,
					'error'=>"保存文件信息失败：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$arr = array(
			'action'=>"upload media",
			'success'=>1,
			'file_url'=>$file_url,
			'm_id'=>$media_id,
		);
		echo json_encode($arr);
		exit;
	}
	else{
		$arr = array(
			'action'=>"upload media",
			'success'=>1,
			'file_url'=>""
		);
		echo json_encode($arr);
		exit;	
	}
}
else{
	$arr = array(
		'action'=>"upload media",
		'success'=>0
	);
	echo json_encode($arr);
	exit;	
}
?>
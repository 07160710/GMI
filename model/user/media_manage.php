<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

function build_route($parent_id,$m_id){
	$get_route_query = "SELECT route FROM media_table WHERE id='$parent_id'";
	$get_route = mysql_query($get_route_query);
	$route_str = "/".$m_id;
	if(mysql_num_rows($get_route)>0){
		$r_row = mysql_fetch_array($get_route);
		$p_route = $r_row['route'];
		$route_str = $p_route.$route_str;
	}
	return $route_str;
}

function update_child_level($parent_id){
	$get_child_query = "SELECT id,level FROM media_table WHERE parent_id='$parent_id' ORDER BY sort_order";
	$get_child = mysql_query($get_child_query);
	
	if(mysql_num_rows($get_child)>0){		
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			$child_level = $c_row['level'];
			
			if(has_child($child_id,'media')){
				update_child_level($child_id);
			}
			
			//update level
			$child_level += 1;
			$update_level_query = "UPDATE media_table SET level='$child_level' WHERE id='$child_id'";
			$update_level = mysql_query($update_level_query);
			if(!$update_level){
				$arr = array(
					'action'=>"update child level",
					'success'=>0,
					'error'=>"Error in updating child level: ".mysql_error()
				);					
				echo json_encode($arr);
				exit;
			}
		}
	}
}

function move_child($parent_id,$dst_parent_id,$dst_alias){
	$get_child_query = "SELECT id,file_url FROM media_table WHERE parent_id='$parent_id' ORDER BY sort_order";
	$get_child = mysql_query($get_child_query);

	if(mysql_num_rows($get_child)>0){		
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			$child_file_url = $c_row['file_url'];
			
			$dst_file_url = "";
			if($child_file_url!=""){
				$file_url_arr = explode("/",$child_file_url);
				$file_name = $file_url_arr[count($file_url_arr)-1];
				$dst_file_url = fetch_route($dst_parent_id,'media')."/".$dst_alias."/".$file_name;
			}
			//print $dst_file_url;exit;
			if(has_child($child_id,'media')){
				move_child($child_id,$dst_parent_id,$dst_alias);
			}
			
			//update child info
			$update_info_query = "UPDATE media_table SET file_url='$dst_file_url' WHERE id='$child_id'";
			$update_info = mysql_query($update_info_query);
			if(!$update_info){
				return false;
				break;
			}
		}
	}
}

if($_POST['action']=="move"){
	if($_POST['id']!="" && $_POST['parent_id']!=""){
		$m_id = mysql_escape_string($_POST['id']);
		$parent_id = mysql_escape_string($_POST['parent_id']);
		
		//update old branch sort
		$old_parent_id = "";
		$old_sort_order = "";
		$old_level = "";
		$old_alias = "";
		$m_type = "";
		$old_file_url = "";
		$new_file_url = "";
		
		$get_media_query = "SELECT parent_id,sort_order,level,alias,type,file_url FROM media_table WHERE id='$m_id'";
		$get_media = mysql_query($get_media_query);
		if(mysql_num_rows($get_media)>0){
			$m_row = mysql_fetch_array($get_media);
			$old_parent_id = $m_row['parent_id'];
			$old_sort_order = $m_row['sort_order'];
			$old_level = $m_row['level'];
			$old_alias = $m_row['alias'];
			$m_type = $m_row['type'];
			$old_file_url = $m_row['file_url'];
		}
		
		if($m_type=="folder"){//move folder
			$src_dir = _MEDIA_PATH_.fetch_route($m_id,'media');
			$dst_dir = _MEDIA_PATH_.fetch_route($parent_id,'media')."/".$old_alias;
			//print $src_dir."|".$dst_dir;exit;
			if(move_child($m_id,$parent_id,$old_alias)===false){
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"移动子目录失败！"
				);
				echo json_encode($arr);
				exit;
			}
			else{
				mvdir($src_dir,$dst_dir);
			}
		}
		else{
			$src_file = _ROOT_PATH_.$old_file_url;			
			$file_url_arr = explode("/",$old_file_url);
			$file_name = $file_url_arr[count($file_url_arr)-1];
			$dst_file_url = fetch_route($parent_id,'media')."/".$file_name;
			$new_file_url = _UPLOAD_FOLDER_."media/".$dst_file_url;
			$dst_file = _MEDIA_FOLDER_.$dst_file_url;
			$src_file = iconv('UTF-8','GB2312',$src_file);
			$dst_file = iconv('UTF-8','GB2312',$dst_file);
			//print $new_file_url."|".$src_file."|".$dst_file;exit;
			if(rename($src_file,$dst_file)===false){
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"移动文件失败！"
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$get_sibling_query = "SELECT id,sort_order FROM media_table WHERE parent_id='$old_parent_id' ORDER BY sort_order";
		$get_sibling = mysql_query($get_sibling_query);
		if(mysql_num_rows($get_sibling)>0){
			while($s_row = mysql_fetch_array($get_sibling)){
				$s_id = $s_row['id'];
				$s_sort_order = $s_row['sort_order'];
				
				if($s_sort_order>$old_sort_order){
					$s_sort_order -= 1;
					$update_sort_query = "UPDATE media_table SET sort_order='$s_sort_order' WHERE id='$s_id'";
					$update_sort = mysql_query($update_sort_query);
					if(!$update_sort){
						$arr = array(
							'action'=>$_POST['action'],
							'success'=>0,
							'error'=>"更新元素排序失败！"
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
		}		
		//append node to new branch
		//get new order
		$get_sort_query = "SELECT MAX(sort_order) FROM media_table WHERE parent_id = '$parent_id'";
		$new_sort_order = mysql_result(mysql_query($get_sort_query),0) + 1;
		//get new level
		$get_level_query = "SELECT level FROM media_table WHERE id = '$parent_id'";
		$new_level = mysql_result(mysql_query($get_level_query),0) + 1;
		//change child level
		if($new_level!=$old_level){
			if(has_child($m_id,'media')){
				update_child_level($m_id);
			}
		}
		
		$new_route = build_route($parent_id,$m_id);
		$update_file_url_str = "";
		if($m_type!="folder"){
			$update_file_url_str = "file_url = '$new_file_url',";
		}
		
		$update_media_query = "	UPDATE media_table 
								SET 
									parent_id = '$parent_id',
									sort_order = '$new_sort_order',
									$update_file_url_str
									level = '$new_level',
									route = '$new_route' 
								WHERE id = '$m_id'";		
		$update_media = mysql_query($update_media_query);
		if($update_media){
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>1,
				'id'=>$m_id
			);		
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>"更新附件信息失败：".mysql_error()
			);
		}	
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="sort"){
	if(isset($_POST['sort'])){
		$update_str = "";
		
		$i = 1;
		foreach($_POST['sort'] as $m_id){			
			$m_id = mysql_escape_string($m_id);
			
			$update_order_query = "UPDATE media_table SET sort_order = '$i' WHERE id = '$m_id'";
			
			try {   
				mysql_query($update_order_query);
				$i++;
			} 
			catch (Exception $e) {   
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"ERROR|UPDATE"
				);
				echo json_encode($arr);
				exit;   
			}
		}
		
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1
		);		
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="add"){
	$parent_id = mysql_escape_string($_POST['parent_id']);
	$name = mysql_escape_string($_POST['m_name']);
	$alias = mysql_escape_string($_POST['m_alias']);	
	$type = mysql_escape_string($_POST['m_type']);
	
	$new_id = get_new_id('media_table');
	
	$sql = "SELECT MAX(sort_order) FROM media_table WHERE parent_id='$parent_id'";
	$sort_order = mysql_result(mysql_query($sql),0)+1;
	
	$sql = "SELECT level FROM media_table WHERE id='$parent_id'";
	$level = mysql_result(mysql_query($sql),0)+1;
	
	$route = build_route($parent_id, $new_id);
	
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['media_fields'] as $val){
		if($insert_key_str!="")$insert_key_str .= ",";
		$insert_key_str .= $val;
		
		if($insert_val_str!="")$insert_val_str .= ",";
		$insert_val_str .= "'".${$val}."'";
	}
	
	$sql = "INSERT INTO media_table(
				id,
				$insert_key_str,
				created_by,
				created_time
			)
			VALUES(
				'$new_id',
				$insert_val_str,
				'".$_SESSION['u_id']."',
				'".time()."'
			)";
	if(mysql_query($sql)){
		if($type=="folder"){
			$mk_dir = _MEDIA_PATH_.fetch_route($new_id,'media_table');
			if(mkdirs($mk_dir)===false){
				$arr = array(
					'success'=>0,
					'error'=>"创建文件夹失败"
				);
				echo json_encode($arr);
				exit;
			}
		}
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1,
			'id'=>$new_id
		);
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"创建附件对象失败: ".mysql_error()
		);
	}	
	echo json_encode($arr);
	exit;
}

function update_child_url($parent_id,$old_alias,$new_alias){
	$get_child_query = "SELECT id,file_url FROM media_table WHERE parent_id='$parent_id'";
	$get_child = mysql_query($get_child_query);
	if(mysql_num_rows($get_child)>0){
		while($c_row = mysql_fetch_array($get_child)){
			$c_id = $c_row['id'];
			$file_url = $c_row['file_url'];
			$file_url = str_replace($old_alias,$new_alias,$file_url);
			$update_child_query = "UPDATE media_table SET file_url='$file_url' WHERE id='$c_id'";
			if(mysql_query($update_child_query)){
				update_child_url($c_id,$old_alias,$new_alias);
			}
			else{
				$arr = array(
					'action'=>"update child url",
					'success'=>0,
					'error'=>"更新子项目URL失败：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
}

if($_POST['action']=="edit"){
	$m_id = mysql_escape_string($_POST['media_id']);
	$m_parent_id = mysql_escape_string($_POST['parent_id']);
	$m_name = mysql_escape_string($_POST['m_name']);
	$m_alias = mysql_escape_string($_POST['m_alias']);
	$m_route = build_route($m_parent_id,$m_id);
	$m_type = mysql_escape_string($_POST['m_type']);
	
	$get_old_alias = "SELECT alias FROM media_table WHERE id='$m_id'";
	$old_alias = mysql_result(mysql_query($get_old_alias),0);
	if($m_alias!=$old_alias && $m_type=="folder"){
		$old_dir = _MEDIA_PATH_.fetch_route($m_id,'media_table');
		$old_dir_arr = explode("/",$old_dir);
		$new_dir = "";
		for($i=0;$i<count($old_dir_arr);$i++){			
			if($new_dir!="")$new_dir .= "/";
			if($i<count($old_dir_arr)-1){
				$new_dir .= $old_dir_arr[$i];
			}
			else{
				$new_dir .= $m_alias;
			}
		}
		$new_dir = "/".$new_dir;
		
		if(is_dir($old_dir)){
			//print $old_dir."|".$new_dir;
			if(rename($old_dir,$new_dir)){
				update_child_url($m_parent_id,$old_alias,$m_alias);
			}
			else{
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"更改目录名称出错！"
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	
	$update_field_str = "";
	foreach($GLOBALS['media_fields'] as $key){
		if($key!="sort_order" && $key!="level"){
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$key='".${"m_".$key}."'";
		}
	}
	
	$sql = "UPDATE media_table 
			SET $update_field_str 
			WHERE id='$m_id'";
	if(mysql_query($sql)){
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1,
			'id'=>$m_id
		);		
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"更新附件信息失败：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="save_auth"){
	$media_id = mysql_escape_string($_POST['media_id']);
	
	$update_wu_str = "";
	if(isset($_POST['wu'])){
		foreach($_POST['wu'] as $wu_id){
			if($update_wu_str!="")$update_wu_str .= ",";
			$update_wu_str .= $wu_id;
		}
	}
	$update_wd_str = "";
	if(isset($_POST['wd'])){
		foreach($_POST['wd'] as $wd_id){
			if($update_wd_str!="")$update_wd_str .= ",";
			$update_wd_str .= $wd_id;
		}
	}
	$update_bu_str = "";
	if(isset($_POST['bu'])){
		foreach($_POST['bu'] as $bu_id){
			if($update_bu_str!="")$update_bu_str .= ",";
			$update_bu_str .= $bu_id;
		}
	}
	$update_bd_str = "";
	if(isset($_POST['bd'])){
		foreach($_POST['bd'] as $bd_id){
			if($update_bd_str!="")$update_bd_str .= ",";
			$update_bd_str .= $bd_id;
		}
	}
	
	$sql = "UPDATE media_table 
			SET 
				wu_list='$update_wu_str',
				wd_list='$update_wd_str',
				bu_list='$update_bu_str',
				bd_list='$update_bd_str' 
			WHERE id='$media_id'";
	if(mysql_query($sql)){
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新对象权限失败：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="update_file_name"){
	$file_id = mysql_escape_string($_POST['id']);
	$name = mysql_escape_string($_POST['name']);
	
	$sql = "UPDATE media_table 
			SET name='$name' 
			WHERE id='$file_id'";
	if(mysql_query($sql)){
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新文件名失败：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="publish_file"){
	$file_id = mysql_escape_string($_POST['id']);
	$type = mysql_escape_string($_POST['type']);
	
	$sql = "UPDATE media_table 
			SET publish='$type' 
			WHERE id='$file_id'";
	if(mysql_query($sql)){
		$arr = array(
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新文件发布状态失败：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="publish_files"){
	if(isset($_POST['file_id'])){
		foreach($_POST['file_id'] as $file_id){
			$sql = "UPDATE media_table 
					SET publish='1' 
					WHERE id='$file_id'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"更新文件发布状态失败：".mysql_error()
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
}

if($_POST['action']=="delete_file"){
	if(isset($_POST['file_id'])){
		foreach($_POST['file_id'] as $file_id){
			$get_file_url = "SELECT file_url FROM media_table WHERE id='$file_id'";
			$file_url = mysql_result(mysql_query($get_file_url),0);
			$rm_file = iconv('UTF-8','GB2312',_ROOT_PATH_.$file_url);
			if(file_exists($rm_file))unlink($rm_file);
			
			$sql = "DELETE FROM media_table WHERE id='$file_id'";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"删除文件记录失败：".mysql_error()
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
}

function delete_child($parent_id){	
	//delete children
	$get_child_query = "SELECT id FROM media_table WHERE parent_id='$parent_id'";
	$get_child = mysql_query($get_child_query);

	if(mysql_num_rows($get_child)>0){		
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			
			if(has_child($child_id,'media')){
				delete_child($child_id);
			}
			
			$delete_child_query = "DELETE FROM media_table WHERE id = '$child_id'";				
			if(!mysql_query($delete_child_query)){
				$arr = array(
					'action'=>"delete child",
					'success'=>0,
					'error'=>"ERROR|DELETE"
				);					
				echo json_encode($arr);
				exit;
			}
		}
	}
}

if($_POST['action']=="delete"){
	$m_id = mysql_escape_string($_POST['m_id']);
	$m_type = mysql_escape_string($_POST['type']);
	$m_parent_id = get_parent_id($m_id,"media");
	
	if(has_child($m_id,'media')){
		delete_child($m_id);
	}
	
	if($m_type=="folder"){//remove folder
		$rm_dir = _MEDIA_PATH_.fetch_route($m_id,'media');
		rmdirs($rm_dir);
	}
	else{//remove file
		$get_file_url = "SELECT file_url FROM media_table WHERE id='$m_id'";
		$file_url = mysql_result(mysql_query($get_file_url),0);
		$rm_file = _ROOT_PATH_.$file_url;
		unlink($rm_file);
	}
	
	$delete_media_query = "DELETE FROM media_table WHERE id = '$m_id'";	
	if(mysql_query($delete_media_query)){		
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1,
			'id'=>$m_id,
			'parent_id'=>$m_parent_id
		);
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"Error in deleting folder: ".mysql_error()
		);
	}	
	echo json_encode($arr);
	exit;
}
?>
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_REQUEST['id']!=""){	
	$n_id = mysql_escape_string($_REQUEST['id']);
	
	$get_path_query = "SELECT img_url FROM nbase WHERE id='$n_id'";
	$get_path = mysql_query($get_path_query);
	if(mysql_result($get_path,0)!=""){
		$img_thumb_path = mysql_result($get_path,0);
		$img_full_path = str_replace(_THUMB_FOLDER_,"",$img_thumb_path);
		//delete image and thumb
		unlink($delete_path.$img_full_path);
		unlink($delete_path.$img_thumb_path);
		//delete from db
		$del_img_query = "UPDATE nbase SET img_url='' WHERE id='$n_id'";		
		if(mysql_query($del_img_query)){
			$arr = array(
				'action'=>"delete image",
				'success'=>1
			);
			if(!isset($file_path)){//if not included in img_upload.php
				echo json_encode($arr);
			}
		}
		else{
			$arr = array(
				'action'=>"delete image",
				'success'=>0,
				'error'=>"删除图片失败: ".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
}

?>

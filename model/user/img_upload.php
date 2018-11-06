<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$file_url = "";

$action = "";
$url_type = "";
$object = "";
$object_type = "";
if(isset($_REQUEST['object'])){
	$object = $_REQUEST['object'];
	switch($object){
		case "content":
			$action = "upload content image";
			$url_type = "file_url";
			$object_type = mysql_escape_string($_REQUEST['object_type']);
			break;
		case "logo":
			$action = "upload logo";
			$url_type = "file_url";
			break;
		case "banner":
			$action = "upload banner";
			$url_type = "banner_url";
			break;
		case "account":
			$action = "upload account image";
			$url_type = "file_url";
			break;
	}
}


//if file exists
if (empty($_FILES) === false) {
	if(isset($_FILES['n_image'])){
		$file_name = $_FILES['n_image']['name'];
		$tmp_name = $_FILES['n_image']['tmp_name'];
		$file_size = $_FILES['n_image']['size'];
		$file_type = $_FILES['n_image']['type'];
	}
	// if(isset($_REQUEST['b_id'])){
	// 	$b_id = $_REQUEST['b_id'];
	// 	if(isset($_FILES['b_image_'.$b_id])){
	// 		$file_name = $_FILES['b_image_'.$b_id]['name'];
	// 		$tmp_name = $_FILES['b_image_'.$b_id]['tmp_name'];
	// 		$file_size = $_FILES['b_image_'.$b_id]['size'];
	// 		$file_type = $_FILES['b_image_'.$b_id]['type'];	
	// 	}
	// }
	// if(isset($_FILES['s_logo'])){
	// 	$file_name = $_FILES['s_logo']['name'];
	// 	$tmp_name = $_FILES['s_logo']['tmp_name'];
	// 	$file_size = $_FILES['s_logo']['size'];
	// 	$file_type = $_FILES['s_logo']['type'];
	// }
	// if(isset($_REQUEST['pa_id'])){
	// 	$pa_id = $_REQUEST['pa_id'];
	// 	if(isset($_FILES['pa_image_'.$pa_id])){
	// 		$file_name = $_FILES['pa_image_'.$pa_id]['name'];
	// 		$tmp_name = $_FILES['pa_image_'.$pa_id]['tmp_name'];
	// 		$file_size = $_FILES['pa_image_'.$pa_id]['size'];
	// 		$file_type = $_FILES['pa_image_'.$pa_id]['type'];	
	// 	}
	// }
	// if(isset($_FILES['acc_image'])){
	// 	$acc_id = $_REQUEST['acc_id'];
	// 	$file_name = $_FILES['acc_image']['name'];
	// 	$tmp_name = $_FILES['acc_image']['tmp_name'];
	// 	$file_size = $_FILES['acc_image']['size'];
	// 	$file_type = $_FILES['acc_image']['type'];
	// }
	// if(isset($_FILES['upload_qrcode'])){
	// 	$file_name = $_FILES['upload_qrcode']['name'];
	// 	$tmp_name = $_FILES['upload_qrcode']['tmp_name'];
	// 	$file_size = $_FILES['upload_qrcode']['size'];
	// 	$file_type = $_FILES['upload_qrcode']['type'];
	// }
	if ($file_name!="") {
		if (@is_dir(_SAVE_PATH_) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"Upload folder doesn't exist."
			);
			echo json_encode($arr);
			exit;
		}
		if (@is_writable(_SAVE_PATH_) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"Upload folder doesn't have write permission."
			);
			echo json_encode($arr);
			exit;
		}
		if (@is_uploaded_file($tmp_name) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"Uploading image failed."
			);
			echo json_encode($arr);
			exit;
		}
		if ($file_size > MAX_UPLOAD_SIZE) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"Uploading file size exceeds limit."
			);
			echo json_encode($arr);
			exit;
		}
		
		$dir_name = "image/";
		
		//get extension
		$temp_arr = explode(".", $file_name);
		$file_ext = array_pop($temp_arr);
		$file_ext = trim($file_ext);
		$file_ext = strtolower($file_ext);
		//check extension
		if (in_array($file_ext, $ext_arr['image']) === false) {
			$error = "Uploading file extension is not allowed.\n".
					"Only the following extensions are allowed: ".implode(",", $ext_arr[$dir_name]);
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>$error
			);
			echo json_encode($arr);
			exit;
		}
		//build target dir
		$target_dir = "nbase/";
		
		if ($dir_name!="") {		
			$target_path = _SAVE_PATH_.$dir_name.$target_dir;
			$target_url = _SAVE_URL_.$dir_name.$target_dir;
			if (!is_dir($target_path)) {
				if(!mkdirs($target_path)){
					$arr = array(
						'action'=>$action,
						'success'=>0,
						'error'=>'创建文件夹失败:'.$target_path
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
		
		//create file name
		$new_file_name = date('Ymd')."_".rand_letter().".".$file_ext;
		$file_path = $target_path.$new_file_name;
		
		//delete old image first
		include_once("img_delete.php");
		
		//move file		
		if (move_uploaded_file($tmp_name, $file_path) === false) {
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"Uploading file failed."
			);
			echo json_encode($arr);
			exit;
		}
		@chmod($file_path, 0644);
		$file_url = _UPLOAD_FOLDER_.$dir_name.$target_dir._THUMB_FOLDER_.$new_file_name;


		//create thumb image
		$img = $imagecreatefrom[$file_type]($file_path);
		$width = imagesx( $img );
		$height = imagesy( $img );
		
		//create thumb folder
		$target_thumb_path = _SAVE_PATH_.$dir_name.$target_dir._THUMB_FOLDER_;
		if(!is_dir($target_thumb_path)) {
			mkdir($target_thumb_path);
		}
		//calc thumb size
		$thumb_width = 0;
		$thumb_height = 0;
		
		if($object_type=="trip_image")define('THUMB_MAX_WIDTH',480);//trip thumb
		if($object=="banner")define('THUMB_MAX_WIDTH',640);//banner thumb
		
		if($width>=THUMB_MAX_WIDTH){
			$thumb_width = THUMB_MAX_WIDTH;
			$thumb_height = floor(THUMB_MAX_WIDTH*$height/$width);
		}
		else if($height>=THUMB_MAX_HEIGHT){
			$thumb_height = THUMB_MAX_HEIGHT;
			$thumb_width = floor(THUMB_MAX_HEIGHT*$width/$height);
		}
		else{
			$thumb_width = $width;
			$thumb_height = $height;
		}
		
		//create thumb
		$thumb_img = imagecreatetruecolor($thumb_width,$thumb_height);
		
		$thumb_quality = THUMB_QUALITY;
		if($file_type=="image/png"){
			$thumb_quality = 10;
			imagealphablending($thumb_img, false);
			imagesavealpha($thumb_img, true);
			$transparentindex = imagecolorallocatealpha($thumb_img, 255, 255, 255, 127);
			imagefill($thumb_img, 0, 0, $transparentindex);
		}
		
		//resize image
		imagecopyresampled( $thumb_img, $img, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height );
		//save thumb
		$thumb_file_path = $target_thumb_path.$new_file_name;
		
		if($imageto[$file_type]($thumb_img, $thumb_file_path, $thumb_quality) === false){
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"Saving thumbnail failed."
			);
			echo json_encode($arr);
			exit;
		}		
		@chmod($thumb_file_path, 0644);		
		$file_url = _UPLOAD_FOLDER_.$dir_name.$target_dir._THUMB_FOLDER_.$new_file_name;
		
		$update_sql = "UPDATE nbase SET img_url='$file_url' WHERE id='".$_REQUEST['id']."'";
		if(!mysql_query($update_sql)){
			$arr = array(
				'action'=>$action,
				'success'=>0,
				'error'=>"保存头图失败！"
			);
			echo json_encode($arr);
			exit;
		}
		
		$arr = array(
			'action'=>$action,
			'success'=>1,
			$url_type=>$file_url
		);
		echo json_encode($arr);
		exit;	
	}
	else{
		$arr = array(
			'action'=>$action,
			'success'=>1,
			$url_type=>""
		);
		echo json_encode($arr);
		exit;	
	}
}
else{
	$arr = array(
		'action'=>$action,
		'success'=>1,
		$url_type=>""
	);
	echo json_encode($arr);
	exit;	
}
?>

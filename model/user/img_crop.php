<?php
require_once("check_login.php");

if($_POST['action']=="load"){
	$img_path = $_POST['img_path'];
	$img_path = _ROOT_PATH_.substr($img_path,strpos($img_path,_UPLOAD_FOLDER_));

	if(file_exists($img_path)){
		//get image size
		list($width, $height) = getimagesize($img_path);
		$arr = array(
			'action'=>"load image",
			'success'=>1,
			'width'=>$width,
			'height'=>$height
		);
		echo json_encode($arr);
		exit;
	}
	else{
		$arr = array(
			'action'=>"load image",
			'success'=>0,
			'error'=>"No image found."
		);
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="crop"){
	$img_path = $_POST['img_path'];
	$img_path = _ROOT_PATH_.substr($img_path,strpos($img_path,_UPLOAD_FOLDER_));
	$img_x = $_POST['img_x'];
	$img_y = $_POST['img_y'];
	$img_w = $_POST['img_w'];
	$img_h = $_POST['img_h'];
	$info = getimagesize($img_path);
	$file_type = $info['mime'];
	
	if(file_exists($img_path)){
		$img = $imagecreatefrom[$file_type]($img_path);
		$width = imagesx( $img );
		$height = imagesy( $img );
		$tmp_img = imagecreatetruecolor($img_w,$img_h);
		
		if($file_type=="image/png"){
			$img_quality = 10;
			imagealphablending($tmp_img, false);
			imagesavealpha($tmp_img, true);
			$transparentindex = imagecolorallocatealpha($tmp_img, 255, 255, 255, 127);
			imagefill($tmp_img, 0, 0, $transparentindex);
		}
		
		imagecopyresampled( $tmp_img, $img, 0, 0, $img_x, $img_y, $img_w, $img_h, $img_w, $img_h );
		
		if($imageto[$file_type]($tmp_img,$img_path,IMAGE_QUALITY) === false){
			$arr = array(
				'action'=>"crop image",
				'success'=>0,
				'error'=>"Replacing image failed."
			);
			echo json_encode($arr);
			exit;
		}
		
		$img = $imagecreatefrom[$file_type]($img_path);
		$width = imagesx( $img );
		$height = imagesy( $img );
		
		//get file name
		$img_path_arr = explode("/",$img_path);
		$img_path_arr = array_reverse($img_path_arr);
		$file_name = $img_path_arr[0];			
		
		//build thumb folder
		$target_thumb_path = "";
		for($i=count($img_path_arr)-1;$i>0;$i--){
			$target_thumb_path .= $img_path_arr[$i]."/";
		}
		$target_thumb_path .= _THUMB_FOLDER_;
		
		//calc thumb size
		$thumb_width = 0;
		$thumb_height = 0;
		if($width>=$height){//landscape
			if($width>=$b_thumb_max_width){
				$thumb_width = $b_thumb_max_width;
				$thumb_height = floor($b_thumb_max_width*$height/$width);
			}
			else{
				$thumb_width = $width;
				$thumb_height = $height;
			}
			
			if($thumb_height>$b_thumb_max_height){
				$thumb_width = floor($b_thumb_max_height*$width/$height);
				$thumb_height = $b_thumb_max_height;
			}
		}
		else{//portrait
			if($height>=$b_thumb_max_height){
				$thumb_width = floor($b_thumb_max_height*$width/$height);
				$thumb_height = $b_thumb_max_height;				
			}
			else{
				$thumb_width = $width;
				$thumb_height = $height;
			}
		}
		//create thumb
		$thumb_img = imagecreatetruecolor($thumb_width,$thumb_height);
		
		if($file_type=="image/png"){
			define('THUMB_QUALITY',9);
			imagealphablending($thumb_img, false);
			imagesavealpha($thumb_img, true);
			$transparentindex = imagecolorallocatealpha($thumb_img, 255, 255, 255, 127);
			imagefill($thumb_img, 0, 0, $transparentindex);
		}
		
		//resize image
		imagecopyresampled( $thumb_img, $img, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height );
		//save thumb
		$thumb_file_path = $target_thumb_path.$file_name;
		if($imageto[$file_type]($thumb_img,$thumb_file_path,THUMB_QUALITY) === false){
			$arr = array(
				'action'=>"crop image",
				'success'=>0,
				'error'=>"Creating thumbnail failed."
			);
			echo json_encode($arr);
			exit;
		}
		
		$arr = array(
			'action'=>"crop image",
			'success'=>1,
			//'file_url'=>
		);
		echo json_encode($arr);
		exit;
	}
	else{
		$arr = array(
			'action'=>"crop image",
			'success'=>0,
			'error'=>"No image found."
		);
		echo json_encode($arr);
		exit;
	}
}
?>
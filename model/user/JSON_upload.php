<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");
require_once("JSON.php");

//PHP上传失败
if (!empty($_FILES['imgFile']['error'])) {
	switch($_FILES['imgFile']['error']){
		case '1':
			$error = '超过php.ini允许的大小。';
			break;
		case '2':
			$error = '超过表单允许的大小。';
			break;
		case '3':
			$error = '图片只有部分被上传。';
			break;
		case '4':
			$error = '请选择图片。';
			break;
		case '6':
			$error = '找不到临时目录。';
			break;
		case '7':
			$error = '写文件到硬盘出错。';
			break;
		case '8':
			$error = 'File upload stopped by extension。';
			break;
		case '999':
		default:
			$error = '未知错误。';
	}
	alert($error);
}

//有上传文件时
if (empty($_FILES) === false) {	
	$file_name = $_FILES['imgFile']['name'];	
	$tmp_name = $_FILES['imgFile']['tmp_name'];	
	$file_size = $_FILES['imgFile']['size'];
	$file_type = $_FILES['imgFile']['type'];
	
	if (!$file_name) {
		alert("请选择文件。");
	}
	if (@is_dir(_SAVE_PATH_) === false) {
		alert("上传目录不存在。");
	}
	if (@is_writable(_SAVE_PATH_) === false) {
		alert("上传目录没有写权限。");
	}
	if (@is_uploaded_file($tmp_name) === false) {
		alert("上传失败。");
	}
	if ($file_size > MAX_UPLOAD_SIZE) {
		alert("上传文件大小超过限制。");
	}
	
	$dir_name = empty($_GET['dir']) ? "image" : trim($_GET['dir']);
	if (empty($ext_arr[$dir_name])) {
		alert("目录名不正确。");
	}
	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//检查扩展名
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("上传文件扩展名是不允许的扩展名。\n只允许".implode(",", $ext_arr[$dir_name])."格式。");
	}
	//build target dir
	$target_dir = "";
	if($_REQUEST['targetID']!=""){
		$target_id = mysql_escape_string($_REQUEST['targetID']);
		$target_dir = fetch_route($target_id)."/";
		if($_REQUEST['action']=="chat"){
			$user_name = mysql_result(mysql_query("SELECT nickname FROM user WHERE id='$target_id'"),0);
			$target_dir = "chat/".$user_name."/";
		}
	}
	else{
		$ymd = date("Ymd");
		$target_dir = $ymd."/";
	}
	
	if ($dir_name!="") {
		$target_path = _SAVE_PATH_.$dir_name."/".$target_dir;
		$target_url = _SAVE_URL_.$dir_name."/".$target_dir;
		if (!is_dir($target_path)) {
			if(!mkdirs($target_path)){
				alert("创建文件夹失败。");
			}
		}
	}
	
	//新文件名
	$new_file_name = date("Ymd")."_".rand_letter().".".$file_ext;
	
	//移动文件
	$file_path = $target_path.$new_file_name;
	if (move_uploaded_file($tmp_name, $file_path) === false) {
		alert("上传文件失败。");
	}
	@chmod($file_path, 0644);
	
	$file_url = $target_url.$new_file_name;

	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}
?>
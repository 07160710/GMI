<?php
$u_id = 999;
if(isset($_SESSION['u_id']))$u_id = $_SESSION['u_id'];

$log_str = "";
switch($log_type){
	case "create":$log_str = "创建备份";break;
	case "delete":$log_str = "删除备份";break;
}

$get_username = "SELECT name FROM user WHERE id='$u_id'";
$username = mysql_result(mysql_query($get_username),0);

$fp = @fopen(_ADMIN_PATH_._LOG_FOLDER_."backup_log.txt","a+");
fwrite($fp,date("Y-m-d H:i:s")." $log_str [$file_name] by $username\r\n");
fclose($fp);
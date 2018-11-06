<?php
require_once("public_param.php");

if($_SESSION['u_id']!=""){
	if(isset($_SESSION['session_time'])){
		$elapsed_time = time() - $_SESSION['session_time'];
		
		if($elapsed_time >= SESSION_TIMEOUT){
			header("Location:manage_login.php?logout=2");
			exit;
		}
	}
	
	$_SESSION['session_time'] = time();
}
else{
	header("Location:./");
	exit;
}
?>
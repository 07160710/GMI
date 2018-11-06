<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$action = $_REQUEST['action'];
if($_REQUEST['object']=="group"){	
	include('account_control_group.php');
}
else if($_REQUEST['object']=="user"){
	include('account_control_user.php');
}
?>

<script>
$(function() {
	set_public_attr();
});
</script>
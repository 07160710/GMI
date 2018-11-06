<?php
require("check_login.php");
require("include/conn.php");
require("function.php");

$page_name = str_replace(".php","",get_path());
setcookie("referer", $_SERVER['PHP_SELF']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="no-cache,no-store, must-revalidate">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="js/basic_fn.js" type="text/javascript"></script>	
	<script src="js/jquery-ui-1.10.3.js" type="text/javascript"></script>
	<script src="js/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
	<script src="js/datePicker_cn.js" type="text/javascript"></script>
	<script src="js/public_fn.js" type="text/javascript"></script>
<?php
switch($page_name){
	case "home": 
		$page_title = "首页";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "park": 
		$page_title = "园区";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/default.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"js/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script charset=\"utf-8\" src=\"js/kindeditor/kindeditor-all.js\"></script>";
		print "<script charset=\"utf-8\" src=\"js/kindeditor/lang/zh-CN.js\"></script>";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
	break;
	case "company": 
		$page_title = "企业";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"js/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script charset=\"utf-8\" src=\"js/kindeditor/kindeditor-all.js\"></script>";
		print "<script charset=\"utf-8\" src=\"js/kindeditor/lang/zh-CN.js\"></script>";
	break;
	case "contract": 
		$page_title = "合同";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"js/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
	break;
	case "building": 
		$page_title = "楼宇";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/media.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
	break;
	case "office": 
		$page_title = "办公室";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "public_resource": 
		$page_title = "公共平台";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "service": 
		$page_title = "服务项目";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "fee": 
		$page_title = "租金";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "water_electric": 
		$page_title = "水电费";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "parking": 
		$page_title = "停车费";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "property": 
		$page_title = "物业费";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "user": 
		$page_title = "管理员";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<script src=\"js/ajaxfileupload.js\" type=\"text/javascript\"></script>";
		print "<script src=\"js/Jcrop/js/jquery.Jcrop.min.js\" type=\"text/javascript\"></script>";
		print "<link href=\"js/Jcrop/css/jquery.Jcrop.min.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	case "backup": 
		$page_title = "备份";
		print "<link href=\"css/content.css\" rel=\"stylesheet\" type=\"text/css\" />";
		print "<link href=\"css/account.css\" rel=\"stylesheet\" type=\"text/css\" />";
	break;
	default: 
		$page_title = SITE_NAME;
}
?>	
	<script>
	var page_name = '<?php print $page_name;?>',
		default_lang = '<?php print $_SESSION['u_lang'];?>';
	</script>
	
	<title><?php print $page_title." | ".SITE_NAME;?></title>
</head>

<body>
<a name="top"></a>
<header>
	<div id="logo_holder"></div>
	<div id="welcome_holder">
		欢迎，<?php print $_SESSION['u_name'];?>！&nbsp;
		<a id="lnk_logout" href="manage_login.php?logout=1">
			登出
		</a>
		<img id="user_img" src="<?php print ($_SESSION['u_img']!="")?$_SESSION['u_img']:"./images/no_profile_image.jpg";?>">
	</div>	
	<ul id="nav_bar">
		<li>
			<a class="nav_link <?php print ($page_name=="home")?"curr":"";?>" href="home.php">
				首页
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="park")?"curr":"";?>" href="content.php">
				园区
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="company")?"curr":"";?>" href="nbase.php">
				企业
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="contract")?"curr":"";?>" href="cbase.php">
				合同
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="building")?"curr":"";?>" href="cbase.php">
				楼宇
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="office")?"curr":"";?>" href="media.php">
				办公室
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="public_resource")?"curr":"";?>" href="media.php">
				公共平台
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="service")?"curr":"";?>" href="media.php">
				服务项目
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="fee")?"curr":"";?>" href="media.php">
				租金
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="water_electric")?"curr":"";?>" href="media.php">
				水电费
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="parking")?"curr":"";?>" href="media.php">
				停车费
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="property")?"curr":"";?>" href="media.php">
				物业费
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="user")?"curr":"";?>" href="media.php">
				管理员
			</a>
		</li>
		<li>
			<a class="nav_link <?php print ($page_name=="backup")?"curr":"";?>" href="backup.php">
				备份
			</a>
		</li>
	</ul>
</header>

<div id="main_content">	
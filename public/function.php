<?php
function https_post($url,$data){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($curl);
	if(curl_errno($curl)){
		return 'Errno'.curl_error($curl);
	}
	curl_close($curl);
	return $result;
}

function send_mail($type,$params,$attach=""){
	require_once("include/smtp.php");
	include("include/mail_setting.php");
	
	$sql = "SELECT * FROM mail_table WHERE type='".$type."' AND company_id='".$params['company_id']."'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
	
		$mail_to = $row['mail_to'];
		if(preg_match("/\{.*?\}/",$mail_to)){
			preg_match_all("/\{.*?\}/",$mail_to,$matches);
			
			$replace = array();
			foreach($matches as $match){
				foreach($match as $val){
					$val = preg_replace(array("/\{/","/\}/"),"",$val);
					$mail_str = "";
					switch($val){
						default: $mail_str = $params[$val];
					}
					
					$replace[] = $mail_str;
				}
				for($i=0;$i<count($match);$i++){
					$mail_to = str_replace($match[$i],$replace[$i],$mail_to);
				}
			}
		}
		
		$mail_subject = $row['mail_subject'];
		if(preg_match("/\{.*?\}/",$mail_subject)){
			preg_match_all("/\{.*?\}/",$mail_subject,$matches);
			
			$replace = array();
			foreach($matches as $match){
				foreach($match as $val){
					$val = preg_replace(array("/\{/","/\}/"),"",$val);
					$subject_str = "";
					switch($val){
						default: $subject_str = $params[$val];
					}
					
					$replace[] = $subject_str;
				}
				for($i=0;$i<count($match);$i++){
					$mail_subject = str_replace($match[$i],$replace[$i],$mail_subject);
				}
			}
		}
		
		$mail_body = nl2br($row['mail_body']);
		if(preg_match("/\{.*?\}/",$mail_body)){
			preg_match_all("/\{.*?\}/",$mail_body,$matches);
			
			$replace = array();
			foreach($matches as $match){
				foreach($match as $val){
					$val = preg_replace(array("/\{/","/\}/"),"",$val);
					$body_str = "";
					switch($val){
						case 'site_name': $body_str = SITE_NAME; break;
						case 'site_link': $body_str = "<a href='"._ROOT_URL_."' target='_blank'>".SITE_NAME."</a>"; break;
						default: $body_str = $params[$val];
					}
					
					$replace[] = $body_str;
				}
				for($i=0;$i<count($match);$i++){
					$mail_body = str_replace($match[$i],$replace[$i],$mail_body);
				}
			}
		}
		
		if($attach!="")$mail_body .= "<p><hr/></p>".$attach;
		$mail_body = "<font style=\"font-family:Microsoft YaHei, Arial;font-size:14px;\">".$mail_body."</font>";
		
		$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
		//$smtp->debug = true;
		$smtp->sendmail($mail_to, $smtpusername, $smtpusermail, $mail_subject, $mail_body, $mailtype);
	}
}

function translate_str($str){
	$str_arr = explode("@",$str);
	return $str_arr[$_SESSION['u_lang']];
}

function get_path(){	
	$url = $_SERVER['REQUEST_URI'];
	$url_params = parse_url($url);
	$url_arr = explode("/",$url_params['path']);	
	$path = $url_arr[count($url_arr)-1];	
	
	return $path;
}

function get_new_id($tb_name="content_table"){
	$cond_str = ($tb_name=="user")?" WHERE id!='999'":"";
	
	$get_max_id = "SELECT MAX(id) FROM ".$tb_name.$cond_str;
	$max_id = mysql_result(mysql_query($get_max_id),0);
	return $max_id+1;
}

function get_userid($acc_id){
	$sql = "SELECT userid FROM user WHERE id='$acc_id'";
	$userid = mysql_result(mysql_query($sql),0);
	return $userid;
}

function has_child($parent_id, $tb_name="content_table"){
	$cond_str = "";
	if($tb_name=="media_table")$cond_str = " AND type='folder'";
	$sql = "SELECT COUNT(*) FROM ".$tb_name." WHERE parent_id='$parent_id' $cond_str";
	$has_child = mysql_result(mysql_query($sql),0);
	if($has_child>0)return true;
	else return false;
}

function check_publish($node_id,$tb_name="content"){
	$sql = "SELECT COUNT(*) FROM pub_".$tb_name."_table WHERE id='$node_id'";
	$is_publish = mysql_result(mysql_query($sql),0);
	
	return $is_publish;
}

function format_time($time, $type = ""){
	if($time>0){
		if($type=="short")
			return date('Y/m/d',$time);
		else
			return date('Y/m/d H:i',$time);
	}
	else{
		return "-";
	}
}

function validate_email($email){ 
	return preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/",$email); 
}

function generate_password($length = 8){
    $str = substr(md5(time()), 0, 6);
    return strtoupper($str);
}

function validate_password($password){
	return 	mb_strlen($password)>=6 && 
			mb_strlen($password)<=20 && 
			preg_match("/[a-zA-Z0-9]+$/", $password);
}

function rand_letter(){
    $seed = str_split(	'abcdefghijklmnopqrstuvwxyz'
						.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
						//.'0123456789!@#$%^&*()'
			); // and any other characters
				
	shuffle($seed);
	$seed = substr(implode("",$seed),0,6);
	return $seed;
}

function validate_folder($dir){
	if(strpos($dir,"-")>0 || strpos($dir,"_")>0){
		return preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_\-]+[a-zA-Z0-9]+$/", $dir); 
	}
	else{
		return preg_match("/^[a-zA-Z0-9]+$/", $dir); 
	}
}

function get_parent_id($child_id,$tb_name="content_table"){
	$get_parent_query = "SELECT parent_id FROM ".$tb_name." WHERE id='$child_id'";
	$parent_id = mysql_result(mysql_query($get_parent_query),0);
	return $parent_id;
}

function fetch_route($n_id, $tb_name="content_table"){
	$route_str = "";
	$get_route_query = "SELECT route FROM ".$tb_name." WHERE id='$n_id'";
	$get_route = mysql_query($get_route_query);	
	if(mysql_num_rows($get_route)>0){
		$r_row = mysql_fetch_array($get_route);
		$route = $r_row['route'];
		$route_arr = explode("/",$route);
		$route_arr = array_reverse($route_arr);
		foreach($route_arr as $r_id){
			$get_node_query = "SELECT alias FROM ".$tb_name." WHERE id='$r_id'";
			$get_node = mysql_query($get_node_query);	
			if(mysql_num_rows($get_node)>0){		
				$n_row = mysql_fetch_array($get_node);
				$n_alias = $n_row['alias'];
				if($route_str!=""){
					$route_str = "/".$route_str;
				}
				$route_str = $n_alias.$route_str;
			}	
		}		
	}
	
	return $route_str;
}

function build_route($parent_id,$id,$tb_name){
	$get_route_query = "SELECT route FROM ".$tb_name." WHERE id='$parent_id'";
	$get_route = mysql_query($get_route_query);
	$route_str = "/".$id;
	if(mysql_num_rows($get_route)>0){
		$r_row = mysql_fetch_array($get_route);
		$p_route = $r_row['route'];
		$route_str = $p_route.$route_str;
	}
	return $route_str;
}

function mkdirs($dir){
	if(!is_dir($dir)){
		if(!mkdirs(dirname($dir))){
			return false;  
		}  
		if(!mkdir($dir,0755,true)){
			return false;  
		}
	}
	return true;
}

function rmdirs($dir){
	foreach(glob($dir."/*") as $file){
		if(is_dir($file)){
			rmdirs($file);
		}
		else{
			unlink($file);
		}
	}
	rmdir($dir);
}

function mvdir($src,$dst){
	if(file_exists($dst)){
		rmdirs($dst);
	}
	if(is_dir($src)){
		mkdirs($dst);
		$files = scandir($src);
		foreach($files as $file){
			if($file!="." && $file!=".."){
				mvdir("$src/$file","$dst/$file",$action);
			}
		}
		if($action=="move")rmdirs($src);
	}
	else if(file_exists($src)){
		rename($src,$dst);
	}
}

function rename_win($oldfile,$newfile){
	if(!rename($oldfile,$newfile)){
		$newfile_arr = explode("/",$newfile);
		$newfile_dir = "";
		for($i=0;$i<count($newfile_arr);$i++){
			if($newfile_dir!=""){
				$newfile_dir .= "/";			
			}
			if(strpos($newfile_arr[$i],".")==false){
				$newfile_dir .= $newfile_arr[$i];
			}
		}
		if(!is_dir($newfile_dir)){
			mkdir($newfile_dir);
			return TRUE;
		}
		if(copy($oldfile,$newfile)){
			unlink($oldfile);
			return TRUE;
		}
		return FALSE;
	}
	return TRUE;
}

function save_log($object,$object_id,$content,$u_id=""){
    $u_id = ($u_id!="")?$u_id:$_SESSION['u_id'];
    $sql = "INSERT INTO gmi_log(
				u_id,
				object,
				object_id,
				content,
				log_time
			) VALUES(
				'$u_id',
				'$object',
				'$object_id',
				'$content',
				'".time()."'
			)";
    if(!mysql_query($sql)){
        echo "保存日志出错: ".mysql_error();
        exit;
    }
}

function write_log($type,$msg){
	$fp = fopen(_ADMIN_PATH_._LOG_FOLDER_.$type."_log.txt","a+");
	if(fwrite($fp, $msg."\r\n")){
		$msg = str_replace("\r\n","<br/>",$msg);
		//print $msg."<br/>";
		fclose($fp);
	}
}

function get_qywx(){
	$qywx = "";
	$sql = "SELECT qywx_appid,qywx_agentid,qywx_appsecret FROM site_table WHERE id='0'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
		$qywx_appid = $row[0];
		$qywx_agentid = $row[1];
		$qywx_appsecret = $row[2];
		$qywx = array(
			'appid' => $qywx_appid,
			'agentid' => $qywx_agentid,
			'appsecret' => $qywx_appsecret,
		);
	}
	return $qywx;
}

function build_conn($db){
	${$db.'_conn'} = mysql_connect($GLOBALS['db_host'], $GLOBALS['db_username'], $GLOBALS['db_password']);
	if(!${$db.'_conn'})die("Could not connect: " . mysql_error(${$db.'_conn'}));
	mysql_select_db($GLOBALS[$db.'_db'],${$db.'_conn'});
	mysql_query("SET NAMES utf8",${$db.'_conn'});
	return ${$db.'_conn'};
}
function close_conn($db){
	mysql_close(${$db.'_conn'});
}

function get_wxqy_token(){
	if($_SESSION['wxqy_token']==""){//获取access_token
		$url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$GLOBALS['qy_appid'].'&corpsecret='.$GLOBALS['qy_appsecret'];		
		$result = https_post($url);
		$result = json_decode($result,true);	
		if(array_key_exists("errcode",$result)){
			if($result['errcode']!=0){
				$arr = array(
					'success'=>0,
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				echo json_encode($arr);
				exit;
			}
			$_SESSION['wxqy_token'] = $result['access_token'];
		}
	}
}

function send_wxqy_msg($msg){
	get_wxqy_token();
	
	$url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$_SESSION['wxqy_token'];		
	$data = '{
				"toparty" : "1",
				"msgtype" : "text",
				"agentid" : '.$GLOBALS['agentid'].',
				"text" : {
					"content" : "[政策通知] '.$msg.'"
				}
			}';
	$result = https_post($url,$data);
	$result = json_decode($result,true);
	if(array_key_exists("errcode",$result)){
		if($result['errcode']!=0){
			$_SESSION['wxqy_token'] = "";
			$arr = array(
				'success'=>0,
				'error'=>$result['errcode'].":".$result['errmsg']
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	$zkwf_conn = build_conn("zkwf");
	$sql = "SELECT id FROM user WHERE status=1";
	$get_user = mysql_query($sql);
	if(mysql_num_rows($get_user)>0){
		while($u_row = mysql_fetch_array($get_user)){
			$u_id = $u_row[0];
			
			$sql = "INSERT INTO message(
						to_user,
						title,
						content,
						sent_time
					) VALUES(
						'$u_id',
						'[政策通知] 中科智库向您推荐政策通知',
						'$msg',
						'".time()."'
					)";
			if(!mysql_query($sql,$zkwf_conn)){
				$arr = array(
					'success'=>0,
					'error'=>"添加消息记录失败：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
	close_conn("zkwf");
}

function get_wxfw_token(){
	if($_SESSION['wxfw_token']==""){//获取access_token
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$GLOBALS['fw_appid'].'&secret='.$GLOBALS['fw_appsecret'];		
		$result = https_post($url);
		$result = json_decode($result,true);
		if(array_key_exists("errcode",$result)){
			$_SESSION['wxfw_token'] = "";
			$arr = array(
				'success'=>0,
				'error'=>$result['errcode'].":".$result['errmsg']
			);
			echo json_encode($arr);
			exit;			
		}
		$_SESSION['wxfw_token'] = $result['access_token'];
	}
}

function send_wxfw_msg($data){
	get_wxfw_token();
	
	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$_SESSION['wxfw_token'];	
	$result = https_post($url,$data);
	$result = json_decode($result,true);		
	if(array_key_exists("errcode",$result)){
		if($result['errcode']!=0){
			$_SESSION['wxfw_token'] = "";
			$arr = array(
				'success'=>0,
				'error'=>$result['errcode'].":".$result['errmsg']
			);
		}
	}
}

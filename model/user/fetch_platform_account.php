<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$platform_accounts = $doc->createElement("platform_accounts");
$doc->appendChild($platform_accounts);

$target = $_REQUEST['target'];
$id = $_REQUEST['id'];
$cond_str = "";
switch($target){
	case "company": $cond_str = "company_id='$id'"; break;
	default: $cond_str = "project_id='$id'";
}

$account_arr = [];
$sql = "SELECT 
			platform_id,
			company_id,
			platform.url,
			platform.remark AS note,
			project_id,
			type,
			account,
			password,
			platform_account.remark 
		FROM platform_account 
			LEFT JOIN platform ON platform_account.platform_id=platform.id 
		WHERE $cond_str 
		ORDER BY CONVERT(platform.name USING GBK)";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$platform_id = $row['platform_id'];
		$company_id = $row['company_id'];
		$project_id = $row['project_id'];
		$url = $row['url'];
		$note = $row['note'];
		$remark = $row['remark'];		
		$type = $row['type'];
		$account = $row['account'];
		$password = $row['password'];
		
		$account_arr[$company_id."|".$platform_id."|".$type."|".$url."|".$note][] = array(
			'project_id' => $project_id,
			'account' => $account,
			'password' => $password,
			'remark' => $remark,
		);
	}
	//print_r($account_arr);
	foreach($account_arr as $sign=>$data){
		$key_arr = explode("|",$sign);
		
		$platform_account = $doc->createElement("platform_account");
		$platform_accounts->appendChild($platform_account);
		
		$platform_id = $doc->createElement("platform_id",htmlspecialchars($key_arr[1]));
		$platform_account->appendChild($platform_id);
		
		$type = $doc->createElement("type",htmlspecialchars($key_arr[2]));
		$platform_account->appendChild($type);
		
		$url = $doc->createElement("url",htmlspecialchars($key_arr[3]));
		$platform_account->appendChild($url);
		
		$note = $doc->createElement("note",htmlspecialchars($key_arr[4]));
		$platform_account->appendChild($note);
		
		foreach($data as $info){
			foreach($info as $key=>$val){
				${$key} = $doc->createElement($key,htmlspecialchars($val));
				$platform_account->appendChild(${$key});
			}
		}
	}
}

echo $doc->saveXML();
?>
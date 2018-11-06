<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$employee_accounts = $doc->createElement("employee_accounts");
$doc->appendChild($employee_accounts);

$id = $_REQUEST['id'];

$account_arr = [];
$sql = "SELECT * 
		FROM member 
		WHERE company_id='$id' 
		ORDER BY CONVERT(name USING GBK)";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$mobile = $row['mobile'];
		$name = $row['name'];		
		$position = $row['position'];
		$is_leader = $row['is_leader'];
		$email = $row['email'];
		$code = $row['code'];
		
		$employee_account = $doc->createElement("employee_account");
		$employee_accounts->appendChild($employee_account);
		
		$e_mobile = $doc->createElement("mobile",htmlspecialchars($mobile));
		$employee_account->appendChild($e_mobile);
		
		$e_name = $doc->createElement("name",htmlspecialchars($name));
		$employee_account->appendChild($e_name);
		
		$e_position = $doc->createElement("position",htmlspecialchars($position));
		$employee_account->appendChild($e_position);
		
		$e_is_leader = $doc->createElement("is_leader",htmlspecialchars($is_leader));
		$employee_account->appendChild($e_is_leader);
		
		$e_email = $doc->createElement("email",htmlspecialchars($email));
		$employee_account->appendChild($e_email);
		
		$e_code = $doc->createElement("code",htmlspecialchars($code));
		$employee_account->appendChild($e_code);
	}
}

echo $doc->saveXML();
?>
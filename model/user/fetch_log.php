<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$logs = $doc->createElement("logs");
$doc->appendChild($logs);

$target = $_REQUEST['target'];
$object_id = $_REQUEST['object_id'];

$cond_str = "";
if($target=="company")$cond_str = " AND object='c'";
else if($target=="agreement")$cond_str = " AND object='a'";
else if($target=="platform")$cond_str = " AND object='pf'";
else if($target=="pbase")$cond_str = " AND object='pb'";
else $cond_str = " AND object!='c' AND object!='pf' AND object!='pb'";

$sql = "SELECT 
			user.name,
			object,
			content,
			log_time 
		FROM log 
			LEFT JOIN user ON log.u_id=user.id 
		WHERE object_id='$object_id' $cond_str 
		ORDER BY log_time DESC";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$log = $doc->createElement("log");
		$logs->appendChild($log);
		
		$l_user = $doc->createElement("user",htmlspecialchars($row[0]));
		$log->appendChild($l_user);
		
		$object = $row[1];
		switch($object){
			case "a": $object = "协议"; break;
			case "c": $object = "公司"; break;
			case "p": $object = "项目"; break;
			case "s": $object = "销售"; break;
			case "t": $object = "技术"; break;
			case "f": $object = "财务"; break;
			case "pf": $object = "平台"; break;
			case "pb": $object = "基础项目"; break;
		}
		$l_object = $doc->createElement("object",htmlspecialchars($object));
		$log->appendChild($l_object);
		
		$l_content = $doc->createElement("content",htmlspecialchars($row[2]));
		$log->appendChild($l_content);
		
		$log_time = date('Y/m/d H:i',$row[3]);
		$l_log_time = $doc->createElement("log_time",htmlspecialchars($log_time));
		$log->appendChild($l_log_time);
	}
}

echo $doc->saveXML();
?>
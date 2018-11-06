<?php
$sql = "SELECT * FROM site_table WHERE id='".$_SESSION['site_id']."'";
$stmt = mysql_query($sql);
$row = mysql_fetch_array($stmt);
$smtpserver = $row['smtpserver'];
$smtpserverport = $row['smtpserverport'];
$smtpuser = $row['smtpuser'];
$smtppass = $row['smtppass'];
$smtpusername = $row['smtpsender'];
$smtpusermail = $row['smtpmail'];

$mailtype = "HTML";
?>
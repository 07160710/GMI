<?php 
header('Content-type: text/html; charset=utf-8');

require_once("include/conn.php");
require_once("public_param.php");
require_once("function.php");

$sql = "TRUNCATE TABLE pbase";
mysql_query($sql);

$sql = "TRUNCATE TABLE pbase_info";
mysql_query($sql);

$sql = "SELECT DISTINCT name,year_apply 
		FROM project 
		WHERE year_apply>2016 
			AND name NOT LIKE '%托管%' 
			AND name NOT LIKE '%实用新型%' 
			AND name NOT LIKE '%著作权' 
			AND name NOT LIKE '%商标%' 
			AND name NOT LIKE '%技术处理%' 
			AND name NOT LIKE '%代理记账%' 
		ORDER BY CONVERT(name USING GBK),year_apply";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$name = $row[0];
		$year_apply = $row[1];
		
		$sql = "SELECT COUNT(*) FROM pbase WHERE name='$name'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO pbase(
						id,
						name
					) VALUES(
						'".get_new_id("pbase")."',
						'$name'
					)";
			if(mysql_query($sql)){
				echo "成功添加基础项目[$name]！<br/>";
			}
		}
		
		$sql = "SELECT id FROM pbase WHERE name='$name'";
		$pbase_id = mysql_result(mysql_query($sql),0);
		
		$sql = "SELECT COUNT(*) FROM pbase_info WHERE pbase_id='$pbase_id' AND year_apply='$year_apply'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO pbase_info(
						pbase_id,
						year_apply
					) VALUES(
						'$pbase_id',
						'$year_apply'
					)";
			if(mysql_query($sql)){
				echo "成功添加基础项目信息[$name - $year_apply]！<br/>";
			}
		}
	}
}
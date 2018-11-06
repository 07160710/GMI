<?php 
header('Content-type: text/html; charset=utf-8');

require_once("../include/conn.php");
require_once("../public_param.php");
require_once("../function.php");

/* 加插中科名片识别OCR记录 */
for($i=1;$i<=4;$i++){
	$sql = "INSERT INTO ocr(
				ocr_type,
				channel,
				u_id,
				ocr_time
			) VALUES(
				'bc',
				'zk',
				'999',
				'".(strtotime('-1 days')+$i)."'
			)";
	if(mysql_query($sql)){
		echo "成功插入OCR记录[$i]<br/>";
	}
}

/* 加插传宇身份证识别OCR记录 */
/*build_cy_conn();
for($i=1;$i<=3;$i++){
	$sql = "INSERT INTO qs_ocr(
				ocr_type,
				channel,
				u_id,
				ocr_time
			) VALUES(
				'id',
				'cy',
				'999',
				'".(strtotime('-1 days')+$i)."'
			)";
	if(mysql_query($sql)){
		echo "成功插入OCR记录[$i]<br/>";
	}
}
close_cy_conn();

/* 加插传宇名片识别OCR记录 */
/*build_cy_conn();
for($i=1;$i<=8;$i++){
	$sql = "INSERT INTO qs_ocr(
				ocr_type,
				channel,
				u_id,
				ocr_time
			) VALUES(
				'bc',
				'cy',
				'999',
				'".(strtotime('-1 days')+$i)."'
			)";
	if(mysql_query($sql)){
		echo "成功插入OCR记录[$i]<br/>";
	}
}
close_cy_conn();
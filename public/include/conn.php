<?php
$host = "localhost";
$username = "root";
$password = "root";
$db_name = "park";

date_default_timezone_set("Asia/Shanghai");

mysql_connect($host,$username,$password) or die("Could not connect222: " . mysql_error());

mysql_select_db($db_name) or die ("Can not use database : " . mysql_error());

mysql_query("SET NAMES utf8");
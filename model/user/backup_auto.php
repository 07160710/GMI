<?php
require_once("include/conn.php");
require_once("public_param.php");
require_once("function.php");

$log_type = "";
$tables = array();
$result = mysql_query('SHOW TABLES');
while($row = mysql_fetch_row($result)){
	if($row[0]!="backup_table"){
		$tables[] = $row[0];
	}
}

$excludes = array(
);

$return = "";
foreach($tables as $table){
	if(!in_array($table,$excludes)){
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
}

$bk_db_folder = _ADMIN_PATH_._BK_DB_FOLDER_;
if(!is_dir($bk_db_folder)){
	mkdirs($bk_db_folder);
}

$handle = fopen($bk_db_folder."db-".date('Y_m_d_His').".sql","w+");
if(fwrite($handle,$return)){
	fclose($handle);
	
	$table_list_str = "";
	foreach($tables as $table){
		if($table_list_str!="")$table_list_str .= "|";
		$table_list_str .= $table;
	}
	
	$insert_backup = "INSERT INTO backup_table(table_list,file_name,created_by) VALUES('$table_list_str','db-".date('Y_m_d_His').".sql','0')";
	if(mysql_query($insert_backup)){
		$file_name = "db-".date('Y_m_d_His').".sql";
		$log_type = "create";
		include_once("backup_log.php");
	}
}
//删除一周前备份
$deadline = date('Y_m_d',strtotime('-1 week'));
$sql = "SELECT id,file_name FROM backup_table WHERE file_name LIKE '%".$deadline."%'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$file_id = $row[0];
		$file_name = $row[1];
		$file = _ADMIN_PATH_._BK_DB_FOLDER_.$file_name;
		
		$sql = "DELETE FROM backup_table WHERE id='$file_id'";
		mysql_query($sql);
		
		if(file_exists($file))unlink($file);
		$log_type = "delete";
		include_once("backup_log.php");
	}
}


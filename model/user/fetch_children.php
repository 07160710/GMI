<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

if($_REQUEST['tb']!="" && $_REQUEST['parent_id']!=""){
	$tb_name = mysql_escape_string($_REQUEST['tb']);
	$parent_id = mysql_escape_string($_REQUEST['parent_id']);
	
	if($tb_name!="user_group"){
		$cond_str = "";
		if($tb_name=="media_table")$cond_str = "AND type='folder'";
		
		$sql = "SELECT 
					id,
					name,
					level,
					type 
				FROM ".$tb_name." 
				WHERE parent_id='$parent_id' 
					$cond_str 
				ORDER BY sort_order";
		$get_node = mysql_query($sql);
		if(mysql_num_rows($get_node)>0){
			$nodes = $doc->createElement("nodes");
			$doc->appendChild($nodes);

			while($row = mysql_fetch_array($get_node)){
				$n_id = $row['id'];
				$n_name = htmlspecialchars($row['name']);				
				$n_level = $row['level'];
				$n_type = $row['type'];
				
				$node = $doc->createElement("node");
				$nodes->appendChild($node);
				
				$id = $doc->createElement("id",$n_id);
				$node->appendChild($id);
				
				$name = $doc->createElement("name",$n_name);
				$node->appendChild($name);
				
				$level = $doc->createElement("level",$n_level);
				$node->appendChild($level);
				
				$type = $doc->createElement("type",$n_type);
				$node->appendChild($type);
				
				$has_child = 0;
				if(has_child($n_id,$tb_name))$has_child = 1;			
				$has_child = $doc->createElement("has_child",$has_child);
				$node->appendChild($has_child);
				
				$is_publish = check_publish($n_id);
				$publish = $doc->createElement("publish",$is_publish);
				$node->appendChild($publish);
			}
		}
	}
	else{
		$sql = "SELECT 
					id,
					name 
				FROM user_group 
				WHERE parent_id='$parent_id' 
				ORDER BY sort_order DESC";
		$get_node = mysql_query($sql);		
		if(mysql_num_rows($get_node)>0){
			$nodes = $doc->createElement("nodes");
			$doc->appendChild($nodes);

			while($row = mysql_fetch_array($get_node)){
				$n_id = $row['id'];
				$n_name = htmlspecialchars($row['name']);
				
				$node = $doc->createElement("node");
				$nodes->appendChild($node);
				
				$id = $doc->createElement("id",$n_id);
				$node->appendChild($id);
				
				$name = $doc->createElement("name",$n_name);
				$node->appendChild($name);
				
				$has_child = 0;
				if(has_child($n_id,$tb_name))$has_child = 1;			
				$has_child = $doc->createElement("has_child",$has_child);
				$node->appendChild($has_child);
			}
		}
	}
}

echo $doc->saveXML();
?>
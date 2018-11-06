<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

if($_REQUEST['n_id']!=""){
	$nodes = $doc->createElement("nodes");
	$doc->appendChild($nodes);

	foreach($_REQUEST['n_id'] as $n_id){
		$get_node_query = "SELECT id,name,type FROM content_table WHERE id = '$n_id'";
		$get_node = mysql_query($get_node_query);

		if(mysql_num_rows($get_node)>0){
			while($n_row = mysql_fetch_array($get_node)){
				$n_id = $n_row['id'];
				$n_name = $n_row['name'];
				$n_type = $n_row['type'];
				
				$node = $doc->createElement("node");
				$nodes->appendChild($node);
				
				$id = $doc->createElement("id",$n_id);
				$node->appendChild($id);
				
				$name = $doc->createElement("name",htmlspecialchars($n_name));
				$node->appendChild($name);
				
				$type = $doc->createElement("type",$n_type);
				$node->appendChild($type);
			}
		}
	}
}

echo $doc->saveXML();
?>
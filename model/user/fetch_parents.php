<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

if(isset($_REQUEST['n_id'])){
	$tb_name = "content_table";
	$child_id = mysql_escape_string($_REQUEST['n_id']);
}
if(isset($_REQUEST['m_id'])){
	$tb_name = "media_table";
	$child_id = mysql_escape_string($_REQUEST['m_id']);
}
if(isset($_REQUEST['g_id'])){
	$tb_name = "user_group";
	$child_id = mysql_escape_string($_REQUEST['g_id']);
}

$tmp_id = get_parent_id($child_id,$tb_name);
$nodes = $doc->createElement("nodes");
$doc->appendChild($nodes);

if($tmp_id>0){
	while($tmp_id>0){
		$node = $doc->createElement("node");
		$nodes->insertBefore($node,$nodes->firstChild);
		
		$id = $doc->createElement("id",$child_id);
		$node->appendChild($id);
		
		$parent_id = $doc->createElement("parent_id",$tmp_id);
		$node->appendChild($parent_id);
		
		$get_parent_name = "SELECT name FROM ".$tb_name." WHERE id='$tmp_id'";
		$p_name = mysql_result(mysql_query($get_parent_name),0);
		$p_name = htmlspecialchars($p_name);
		$parent_name = $doc->createElement("parent_name",$p_name);
		$node->appendChild($parent_name);
		
		$tmp_id = get_parent_id($tmp_id,$tb_name);
	}
}
else{
	$node = $doc->createElement("node");
	$nodes->appendChild($node);
	
	$id = $doc->createElement("id",$child_id);
	$node->appendChild($id);
	
	$parent_id = $doc->createElement("parent_id",$tmp_id);
	$node->appendChild($parent_id);
	
	$get_parent_name = "SELECT name FROM ".$tb_name." WHERE id='$tmp_id'";
	$p_name = mysql_result(mysql_query($get_parent_name),0);
	$parent_name = $doc->createElement("parent_name",$p_name);
	$node->appendChild($parent_name);
}

echo $doc->saveXML();
?>
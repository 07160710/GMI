<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="sort_banner"){
	$b_type = mysql_escape_string($_POST['b_type']);
	if(isset($_POST['banner'])){
		$i = 1;
		foreach($_POST['banner'] as $b_id){
			$b_id = mysql_escape_string($b_id);
			
			$update_order_query = "UPDATE banner_table SET sort_order = '$i' WHERE id = '$b_id' AND type='$b_type'";
			try {   
				mysql_query($update_order_query);
				
				if(check_publish($b_id,"banner")!=0){//update record			
					$publish_query = "UPDATE pub_banner_table SET sort_order = '$i' WHERE id = '$b_id' AND type='$b_type'";
					if(!mysql_query($publish_query)){
						$arr = array(
							'action'=>$_POST['action'],
							'success'=>0,
							'error'=>"Error in updating published banner: ".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
				}				
				$i++;
			} 
			catch (Exception $e) {   
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"Error in updating banner order: ".mysql_error()
				);
				echo json_encode($arr);
				exit;   
			}
		}
		
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1
		);
		echo json_encode($arr);
		exit;
	}
}

if($_POST['sub_action']=="save_banner"){
	$b_type = mysql_escape_string($_POST['b_type']);
	
	if($_POST['nb_img_title']!=""){//insert banner
		$n_id = mysql_escape_string($_POST['node_id']);		
		$nb_img_title = mysql_escape_string($_POST['nb_img_title']);
		$nb_img_desc = mysql_escape_string($_POST['nb_img_desc']);
		$nb_img_link = mysql_escape_string($_POST['nb_img_link']);
		
		//get new order
		$get_sort_query = "SELECT MAX(sort_order) FROM banner_table WHERE type='$b_type'";
		$nb_sort_order = mysql_result(mysql_query($get_sort_query),0) + 1;
		
		$insert_query = "INSERT INTO banner_table(
							id,
							c_id,
							type,
							sort_order,
							img_title,
							img_desc,
							img_link,
							created_by,
							created_time
						) VALUES(
							'".get_new_id('banner_table')."',
							'$n_id',
							'$b_type',
							'$nb_sort_order',
							'$nb_img_title',
							'$nb_img_desc',
							'$nb_img_link',
							'".$_SESSION['u_id']."',
							'".time()."'
						)";
		if(!mysql_query($insert_query)){
			$arr = array(
				'action'=>$_POST['sub_action'],
				'success'=>0,
				'error'=>"Error in inserting banner: ".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	if(isset($_POST['b_img_title'])){//update banner
		$b_id_arr = $_POST['b_id'];
		$b_img_title_arr = $_POST['b_img_title'];
		$b_img_desc_arr = $_POST['b_img_desc'];
		$b_img_link_arr = $_POST['b_img_link'];	
		
		for($i=0;$i<count($b_img_title_arr);$i++){
			$update_query = "	UPDATE banner_table 
								SET 
									img_title = '".mysql_escape_string($b_img_title_arr[$i])."',
									img_desc = '".mysql_escape_string($b_img_desc_arr[$i])."',
									img_link = '".mysql_escape_string($b_img_link_arr[$i])."' 
								WHERE id = '".mysql_escape_string($b_id_arr[$i])."' AND type='$b_type'";
			if(!mysql_query($update_query)){
				$arr = array(
					'action'=>$_POST['sub_action'],
					'success'=>0,
					'error'=>"Error in updating banner: ".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
			
			if(check_publish($b_id_arr[$i],"banner")!=0){//update record			
				$publish_query = "	UPDATE pub_banner_table 
									SET 
										img_title = '".mysql_escape_string($b_img_title_arr[$i])."',
										img_desc = '".mysql_escape_string($b_img_desc_arr[$i])."',
										img_link = '".mysql_escape_string($b_img_link_arr[$i])."' 
									WHERE id = '".mysql_escape_string($b_id_arr[$i])."' AND type='$b_type'";
				if(!mysql_query($publish_query)){
					$arr = array(
						'action'=>$_POST['sub_action'],
						'success'=>0,
						'error'=>"Error in updating published banner: ".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	$arr = array(
		'action'=>$_POST['sub_action'],
		'success'=>1
	);
	echo json_encode($arr);
	exit;	
}

if($_POST['action']=="publish" || $_POST['action']=="unpublish"){
	$b_id = mysql_escape_string($_REQUEST['b_id']);
	
	if($_POST['action']=="publish"){
		$get_banner_query = "SELECT * FROM banner_table WHERE id='$b_id'";
		$get_banner = mysql_query($get_banner_query);
		if(mysql_num_rows($get_banner)>0){
			$b_row = mysql_fetch_array($get_banner);
			
			$insert_field_str = "";
			$insert_value_str = "";
			foreach($pub_banner_fields as $val){
				if($insert_field_str!="")$insert_field_str .= ",";
				$insert_field_str .= $val;
				
				if($insert_value_str!="")$insert_value_str .= ",";
				$insert_value_str .= "'".mysql_escape_string($b_row[$val])."'";
			}
			
			$sql = "INSERT INTO pub_banner_table(
						$insert_field_str
					) VALUES(
						$insert_value_str
					)";
		}
	}
	else{
		$sql = "DELETE FROM pub_banner_table WHERE id='$b_id'";
	}
	if(mysql_query($sql)){
		if($type==0){//wxapp update
			$sql = "INSERT INTO wxapp_update(type,updated_time) VALUES('0','".time()."')";
			mysql_query($sql);
		}
		
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1
		);
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"Error in publishing/unpulishing banner: ".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}
?>
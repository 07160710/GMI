<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

function build_route($parent_id,$n_id){
	$get_route_query = "SELECT route FROM content_table WHERE id='$parent_id'";
	$get_route = mysql_query($get_route_query);
	$route_str = "/".$n_id;
	if(mysql_num_rows($get_route)>0){
		$r_row = mysql_fetch_array($get_route);
		$p_route = $r_row['route'];
		$route_str = $p_route.$route_str;
	}
	return $route_str;
}

function copy_child($parent_id,$new_parent_id,$new_parent_level){
	global $content_basic;
	global $content_fields;
	global $content_info;

	$get_child_query = "SELECT * FROM content_table WHERE parent_id = '$parent_id' ORDER BY sort_order";
	$get_child = mysql_query($get_child_query);	
	
	if(mysql_num_rows($get_child)>0){
		$i = 1;
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			
			$new_sort_order = $i;
			$new_level = $new_parent_level + 1;
			
			//insert child
			$insert_field_str = "";
			$insert_value_str = "";
			foreach($content_basic as $val){
				if($insert_field_str!="")$insert_field_str .= ",";
				$insert_field_str .= $val;
			}
			foreach($content_fields as $val){
				if($val!="image"){
					if($insert_field_str!="")$insert_field_str .= ",";
					$insert_field_str .= $val;
					
					if($insert_value_str!="")$insert_value_str .= ",";
					$insert_value_str .= "'".mysql_escape_string($c_row[$val])."'";
				}
			}
			foreach($content_info as $val){
				if($insert_field_str!="")$insert_field_str .= ",";
				$insert_field_str .= $val;
			}
			
			$sql = "INSERT INTO content_table(
						$insert_field_str
					) 
					VALUES(
						'".get_new_id()."',
						'$new_parent_id',
						'$new_sort_order',
						'$new_level',
						$insert_value_str,
						'".$_SESSION['u_id']."',
						'".time()."',
						'".$_SESSION['u_id']."',
						'".time()."'
					)";
			$insert_child = mysql_query($sql);
			if(!$insert_child){
				$arr = array(
					'action'=>"copy child",
					'success'=>0,
					'error'=>"Error in copying child: ".mysql_error()
				);					
				echo json_encode($arr);
				exit;
			}
			$i++;
			if(has_child($child_id,'content')){
				copy_child($child_id,$new_id,$new_level);
			}
		}
	}
}

if($_POST['action']=="copy"){
	if($_POST['id']!="" && $_POST['parent_id']!=""){
		$n_id = mysql_escape_string($_POST['id']);
		$parent_id = mysql_escape_string($_POST['parent_id']);
		
		$get_node_query = "SELECT * FROM content_table WHERE id = '$n_id'";
		$get_node = mysql_query($get_node_query);		
		if(mysql_num_rows($get_node)>0){//copy node to new branch	
			while($n_row = mysql_fetch_array($get_node)){
				//get new order
				$sql = "SELECT MAX(sort_order) FROM content_table WHERE parent_id = '$parent_id'";
				$new_sort_order = mysql_result(mysql_query($sql),0) + 1;
				//get new level
				$sql = "SELECT level FROM content_table WHERE id = '$parent_id'";
				$new_level = mysql_result(mysql_query($sql),0) + 1;
				
				$insert_field_str = "";
				$insert_value_str = "";
				foreach($content_basic as $val){
					if($insert_field_str!="")$insert_field_str .= ",";
					$insert_field_str .= $val;
				}
				foreach($content_fields as $val){
					if($val!="image"){
						if($insert_field_str!="")$insert_field_str .= ",";
						$insert_field_str .= $val;
						
						if($insert_value_str!="")$insert_value_str .= ",";
						if($val!="route"){						
							if($val=="name" || $val=="alias"){
								$insert_value_str .= "'".mysql_escape_string($n_row[$val])."(1)'";
							}
							else{
								$insert_value_str .= "'".mysql_escape_string($n_row[$val])."'";
							}
						}
						else{
							$insert_value_str .= "'".build_route($parent_id,$new_id)."'";
						}
					}
				}
				foreach($content_info as $val){
					if($insert_field_str!="")$insert_field_str .= ",";
					$insert_field_str .= $val;
				}
				
				$sql = "INSERT INTO content_table(
							$insert_field_str
						) 
						VALUES(
							'".get_new_id()."',
							'$parent_id',
							'$new_sort_order',
							'$new_level',
							$insert_value_str,
							'".$_SESSION['u_id']."',
							'".time()."',
							'".$_SESSION['u_id']."',
							'".time()."'
						)";
				$insert_node = mysql_query($sql);
				if(!$insert_node){
					$arr = array(
						'action'=>$_POST['action'],
						'success'=>0,
						'error'=>"ERROR|INSERT|".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				if(has_child($n_id,'content')){
					copy_child($n_id,$new_id,$new_level);
				}
				
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>1,
					'id'=>$n_id
				);
				echo json_encode($arr);
				exit;
			}
		}	
	}
}

function update_child_level($parent_id,$root_level){
	$sql = "SELECT id FROM content_table WHERE parent_id = '$parent_id' ORDER BY sort_order";
	$get_child = mysql_query($sql);
	if(mysql_num_rows($get_child)>0){		
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			$parent_level = $root_level + 1;
			
			if(has_child($child_id,'content')){
				update_child_level($child_id,$parent_level);
			}
			
			//update level
			$child_level = $parent_level + 1;
			$sql = "UPDATE content_table SET level='$child_level' WHERE id='$child_id'";
			$update_level = mysql_query($sql);
			if(!$update_level){
				$arr = array(
					'action'=>"update child level",
					'success'=>0,
					'error'=>"ERROR|UPDATE"
				);					
				echo json_encode($arr);
				exit;
			}
			
			if(check_publish($child_id)!=0){//update record			
				$sql = "UPDATE pub_content_table SET level='$child_level' WHERE id='$child_id'";				
				if(!mysql_query($sql)){
					$arr = array(
						'action'=>"update child level",
						'success'=>0,
						'error'=>"ERROR|PUBLISH"
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
}

if($_POST['action']=="move"){
	if($_POST['id']!="" && $_POST['parent_id']!=""){
		$n_id = mysql_escape_string($_POST['id']);
		$parent_id = mysql_escape_string($_POST['parent_id']);
		
		//update old branch sort
		$old_parent_id = "";
		$old_sort_order = "";
		$old_level = "";
		$old_image = "";
		$sql = "SELECT parent_id,sort_order,level,image FROM content_table WHERE id='$n_id'";
		$get_node = mysql_query($sql);
		if(mysql_num_rows($get_node)>0){
			$n_row = mysql_fetch_array($get_node);
			$old_parent_id = $n_row['parent_id'];
			$old_sort_order = $n_row['sort_order'];
			$old_level = $n_row['level'];
			$old_image = $n_row['image'];
		}
		
		//get new order
		$sql = "SELECT MAX(sort_order) FROM content_table WHERE parent_id = '$parent_id'";
		$new_sort_order = mysql_result(mysql_query($sql),0) + 1;
		//get new level
		$sql = "SELECT level FROM content_table WHERE id = '$parent_id'";
		$parent_level = mysql_result(mysql_query($sql),0);
		$new_level = $parent_level + 1;
		//change child level
		if($new_level!=$old_level){
			if(has_child($n_id,'content')){
				update_child_level($n_id,$parent_level);
			}
		}
		
		$new_route = build_route($parent_id,$n_id);
		
		//move image
		$new_image = $old_image;
		if($old_image!=""){
			$old_image_path = iconv('UTF-8','GB2312',_ROOT_PATH_.$old_image);
			if(file_exists($old_image_path)){
				$old_image_arr = explode("/",$old_image);
				$old_image_arr = array_reverse($old_image_arr);
				$image_name = $old_image_arr[0];
			
				$new_image_route = "";
				$route_arr = explode("/",$new_route);
				$route_arr = array_reverse($route_arr);
				foreach($route_arr as $r_id){
					$sql = "SELECT alias FROM content_table WHERE id='$r_id'";
					$get_node = mysql_query($sql);	
					if(mysql_num_rows($get_node)>0){		
						$n_row = mysql_fetch_array($get_node);
						$n_alias = $n_row['alias'];
						if($new_image_route!=""){
							$new_image_route = "/".$new_image_route;
						}
						$new_image_route = $n_alias.$new_image_route;
					}	
				}
				$old_image_thumb_path = "../".$old_image;		
				$new_image_thumb_path = "../files/image/".$new_image_route."/"._THUMB_FOLDER_.$image_name;				
				$old_image_full_path = str_replace(_THUMB_FOLDER_,"",$old_image_thumb_path);
				$new_image_full_path = "../files/image/".$new_image_route."/".$image_name;
				
				$old_image_thumb_path = iconv('UTF-8','GB2312',$old_image_thumb_path);
				$new_image_thumb_path = iconv('UTF-8','GB2312',$new_image_thumb_path);
				$old_image_full_path = iconv('UTF-8','GB2312',$old_image_full_path);
				$new_image_full_path = iconv('UTF-8','GB2312',$new_image_full_path);
				//print $old_image_thumb_path."\n".$new_image_thumb_path."\n";
				//print $old_image_full_path."\n".$new_image_full_path."\n";exit;
				if(rename_win($old_image_thumb_path,$new_image_thumb_path)!==false){
					if(rename_win($old_image_full_path,$new_image_full_path)!==false){
						$new_image = "files/image/".$new_image_route."/"._THUMB_FOLDER_.$image_name;
					}
					else{
						$arr = array(
							'success'=>0,
							'error'=>"ERROR|MOVE FULL IMAGE"
						);
						echo json_encode($arr);
						exit;
					}
				}
				else{
					$arr = array(
						'action'=>$_POST['action'],
						'success'=>0,
						'error'=>"ERROR|MOVE THUMB IMAGE"
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
		
		$sql = "SELECT id,sort_order FROM content_table WHERE parent_id='$old_parent_id' ORDER BY sort_order";
		$get_sibling = mysql_query($sql);
		if(mysql_num_rows($get_sibling)>0){
			while($s_row = mysql_fetch_array($get_sibling)){
				$s_id = $s_row['id'];
				$s_sort_order = $s_row['sort_order'];
				
				if($s_sort_order>$old_sort_order){
					$s_sort_order -= 1;
					$update_sort_query = "UPDATE content_table SET sort_order='$s_sort_order' WHERE id='$s_id'";
					$update_sort = mysql_query($update_sort_query);
					if(!$update_sort){
						$arr = array(
							'success'=>0,
							'error'=>"ERROR|UPDATE SIBLING SORT"
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
		}
		
		$sql = "UPDATE content_table 
				SET 
					parent_id = '$parent_id',
					sort_order = '$new_sort_order',
					level = '$new_level',
					route = '$new_route',
					image = '$new_image' 
				WHERE id = '$n_id'";
		if(mysql_query($sql)){
			if(check_publish($n_id)!=0){//update record			
				$sql = "UPDATE pub_content_table 
						SET 
							parent_id = '$parent_id',
							sort_order = '$new_sort_order',
							level = '$new_level',
							route = '$new_route',
							image = '$new_image' 
						WHERE id='$n_id'";				
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"ERROR|PUBLISH"
					);
					echo json_encode($arr);
					exit;
				}
			}			
			$arr = array(
				'success'=>1,
				'id'=>$n_id
			);
			echo json_encode($arr);
			exit;			
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"ERROR|UPDATE"
			);
			echo json_encode($arr);
			exit;
		}
	}
}

if($_POST['action']=="sort"){
	if(isset($_POST['sort'])){
		$update_str = "";
		
		$i = 1;
		foreach($_POST['sort'] as $n_id){			
			$n_id = mysql_escape_string($n_id);
			
			$sql = "UPDATE content_table SET sort_order='$i' WHERE id='$n_id'";			
			try {   
				mysql_query($sql);
				
				if(check_publish($n_id)!=0){//update record
					$sql = "UPDATE pub_content_table SET sort_order='$i' WHERE id='$n_id'";
					if(!mysql_query($sql)){
						$arr = array(
							'success'=>0,
							'error'=>"Error in updating published content: ".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
				}
				
				$i++;
			} 
			catch (Exception $e) {   
				$arr = array(
					'success'=>0,
					'error'=>"Error in updating content order: ".mysql_error()
				);
				echo json_encode($arr);
				exit;   
			}
		}
		
		$arr = array(
			'success'=>1
		);		
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="sort_banner"){
	$n_type = mysql_escape_string($_POST['type']);
	$banner_type = 1;	
	if($n_type=="slide_show")$banner_type = 2;
	if($n_type=="custom_home")$banner_type = 3;
	
	if(isset($_POST['banner'])){		
		$update_str = "";		
		$i = 1;
		foreach($_POST['banner'] as $b_id){			
			$b_id = mysql_escape_string($b_id);
			
			$sql = "UPDATE banner_table SET sort_order='$i' WHERE id='$b_id' AND type='$banner_type'";			
			try {   
				mysql_query($sql);
				
				if(check_publish($b_id,"banner")!=0){//update record			
					$sql = "UPDATE pub_banner_table SET sort_order='$i' WHERE id='$b_id' AND type='$banner_type'";				
					if(!mysql_query($sql)){
						$arr = array(
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
					'success'=>0,
					'error'=>"Error in updating published banner order: ".mysql_error()
				);
				echo json_encode($arr);
				exit;   
			}
		}
		
		$arr = array(
			'success'=>1
		);
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="add"){
	$n_parent_id = mysql_escape_string($_POST['parent_id']);
	$n_name = mysql_escape_string($_POST['n_name']);
	$n_title = mysql_escape_string($_POST['n_title']);
	$n_alias = mysql_escape_string($_POST['n_alias']);	
	$n_type = mysql_escape_string($_POST['n_type']);
	$n_summary = mysql_escape_string(stripslashes($_POST['n_summary']));
	$n_content = mysql_escape_string(stripslashes($_POST['n_content']));
	
	$n_redirect = mysql_escape_string($_POST['n_redirect']);
	$n_meta_desc = mysql_escape_string($_POST['n_meta_desc']);
	$n_meta_kwrd = mysql_escape_string($_POST['n_meta_kwrd']);	
	
	$n_publish = 0;
	if($_POST['n_publish']=="on")$n_publish = 1;
	
	$n_show_navi = 0;
	if($_POST['n_show_navi']=="on")$n_show_navi = 1;
	
	//get new id
	$n_id = get_new_id();
	//get new order
	$sql = "SELECT MAX(sort_order) FROM content_table WHERE parent_id = '$n_parent_id'";
	$n_sort_order = mysql_result(mysql_query($sql),0) + 1;
	//get new level
	$sql = "SELECT level FROM content_table WHERE id = '$n_parent_id'";
	$n_level = mysql_result(mysql_query($sql),0) + 1;
	
	$n_route = build_route($n_parent_id,$n_id);
	
	$insert_field_str = "";
	$insert_value_str = "";
	foreach($content_basic as $val){
		if($insert_field_str!="")$insert_field_str .= ",";
		$insert_field_str .= $val;
		
		if($insert_value_str!="")$insert_value_str .= ",";
		$insert_value_str .= "'".${"n_".$val}."'";
	}
	foreach($content_fields as $val){
		if($insert_field_str!="")$insert_field_str .= ",";
		$insert_field_str .= $val;
		
		if($insert_value_str!="")$insert_value_str .= ",";
		$insert_value_str .= "'".${"n_".$val}."'";
	}
	foreach($content_info as $val){
		if($insert_field_str!="")$insert_field_str .= ",";
		$insert_field_str .= $val;
	}
	
	$sql = "INSERT INTO content_table(
				$insert_field_str
			) 
			VALUES(
				$insert_value_str,
				'".$_SESSION['u_id']."',
				'".time()."',
				'".$_SESSION['u_id']."',
				'".time()."'
			)";
	if(mysql_query($sql)){		
		$arr = array(
			'success'=>1,
			'id'=>$n_id
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"Error in inserting content: ".mysql_error()
		);
	}	
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="edit"){
	$n_id = mysql_escape_string($_POST['node_id']);
	$n_parent_id = mysql_escape_string($_POST['parent_id']);
	$n_name = mysql_escape_string($_POST['n_name']);
	$n_title = mysql_escape_string($_POST['n_title']);
	$n_title_en = mysql_escape_string($_POST['n_title_en']);
	$n_alias = mysql_escape_string($_POST['n_alias']);
	$n_route = build_route($n_parent_id,$n_id);
	$n_type = mysql_escape_string($_POST['n_type']);
	$n_summary = mysql_escape_string(stripslashes($_POST['n_summary']));
	$n_summary_en = mysql_escape_string(stripslashes($_POST['n_summary_en']));
	$n_content = mysql_escape_string(stripslashes($_POST['n_content']));
	$n_content_en = mysql_escape_string(stripslashes($_POST['n_content_en']));
	
	$n_ads = "";
	if(isset($_POST['ba'])){
		$layout_node_str = "";
		foreach($_POST['ba'] as $val){
			if($layout_node_str!="")$layout_node_str .= "#";
			$layout_node_str .= $val;
		}
		$n_ads .= $layout_node_str;
	}
	
	$n_ads .= "|";
	if(isset($_POST['pa'])){		
		$layout_node_str = "";
		foreach($_POST['pa'] as $val){
			if($layout_node_str!="")$layout_node_str .= "#";
			$layout_node_str .= $val;
		}
		$n_ads .= $layout_node_str;
	}
	
	$n_ads .= "|";
	if(isset($_POST['fa'])){		
		$layout_node_str = "";
		foreach($_POST['fa'] as $val){
			if($layout_node_str!="")$layout_node_str .= "#";
			$layout_node_str .= $val;
		}
		$n_ads .= $layout_node_str;
	}
	
	$update_created_date = "";
	if(isset($_POST['n_created_date']) && $_POST['n_created_date']!=""){
		$n_created_date = mysql_escape_string($_POST['n_created_date']);
		if($n_created_date!=""){
			$n_created_date = date('Y-m-d H:i:s',strtotime($n_created_date));
			$update_created_date = ",created_time = '$n_created_date'";
		}
	}
	
	$n_redirect = mysql_escape_string($_POST['n_redirect']);
	$n_meta_desc = mysql_escape_string($_POST['n_meta_desc']);
	$n_meta_kwrd = mysql_escape_string($_POST['n_meta_kwrd']);
	
	$n_show_navi = 0;
	if($_POST['n_show_navi']=="on")$n_show_navi = 1;
	
	$update_field_str = "";
	foreach($content_fields as $val){
		if($val!="image"){
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$val = '".${"n_".$val}."'";
		}
	}
	
	if($n_type=="topic" || $n_type=="news"){
		$t_start_date = mysql_escape_string($_POST['t_start_date']);
		$t_end_date = mysql_escape_string($_POST['t_end_date']);		
		if($t_start_date!="")$t_start_date = date('Y-m-d',strtotime($t_start_date));
		if($t_end_date!="")$t_end_date = date('Y-m-d',strtotime($t_end_date));
		
		$update_attr_str = "";		
		$sql = "SELECT id,type FROM field_table WHERE dt_id IN(SELECT id FROM data_table WHERE alias='"._TOPIC_ATTR_ALIAS_."')";
		$stmt = mysql_query($sql);
		if(mysql_num_rows($stmt)>0){			
			while($row = mysql_fetch_array($stmt)){
				$cat_id = $row['id'];
				$cat_type = $row['type'];
				
				if(is_array($_POST['cat_'.$cat_id])){
					if($update_cat_str!="")$update_cat_str .= "|";
					$update_cat_str .= $cat_id.":";
					$update_val_str = "";
					foreach($_POST['cat_'.$cat_id] as $cat_val){
						$cat_val = mysql_escape_string($cat_val);
						
						if($update_val_str!="")$update_val_str .= "#";
						$update_val_str .= $cat_val;
					}
					$update_cat_str .= $update_val_str;
				}
				else{
					$cat_val = mysql_escape_string($_POST['cat_'.$cat_id]);
					if($update_cat_str!="")$update_cat_str .= "|";
					$update_cat_str .= $cat_id.":".$cat_val;
				}
			}
		}
		
		$sql = "SELECT COUNT(*) FROM content_info WHERE c_id='$n_id'";
		$has_topic = mysql_result(mysql_query($sql),0);		
		if($has_topic==0){
			$sql = "INSERT INTO content_info(
						c_id,
						start_date,
						end_date,
						attribute
					) VALUES(
						'$n_id',
						'$t_start_date',
						'$t_end_date',
						'$update_cat_str'
					)";
		}
		else{
			$sql = "UPDATE content_info 
					SET 
						start_date='$t_start_date',
						end_date='$t_end_date',
						attribute='$update_cat_str' 
					WHERE c_id='$n_id'";
		}	
		if(!mysql_query($sql)){
			$arr = array(
				'success'=>0,
				'error'=>"Error in editing topic info: ".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	$sql = "UPDATE content_table
			SET 
				$update_field_str,
				updated_by = '".$_SESSION['u_id']."',
				updated_time = '".time()."' 
				$update_created_date 
			WHERE id = '$n_id'";
	if(mysql_query($sql)){
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1,
			'id'=>$n_id
		);		
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"Error in updating content: ".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

function delete_child($parent_id){	
	//delete children
	$get_child_query = "SELECT id FROM content_table WHERE parent_id = '$parent_id'";
	$get_child = mysql_query($get_child_query);

	if(mysql_num_rows($get_child)>0){		
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			
			if(has_child($child_id,'content')){
				delete_child($child_id);
			}
			
			//delete image
			$get_path_query = "SELECT image FROM content_table WHERE id='$child_id'";
			$get_path = mysql_query($get_path_query);
			if(mysql_num_rows($get_path)>0){
				$p_row = mysql_fetch_array($get_path);				
				
				$img_thumb_path = $p_row['image'];
				if($img_thumb_path!=""){				
					$img_full_path = str_replace(_THUMB_FOLDER_,"",$img_thumb_path);
					//delete image and thumb
					if(!unlink(_ROOT_PATH_.$img_full_path) || !unlink(_ROOT_PATH_.$img_thumb_path)){			
						$arr = array(
							'action'=>"delete child image",
							'success'=>0,
							'error'=>"Error in deleting child image"
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
			
			$unpublish_child_query = "DELETE FROM pub_content_table WHERE id = '$child_id'";
			if(mysql_query($unpublish_child_query)){
				$delete_child_query = "DELETE FROM content_table WHERE id = '$child_id'";
				if(!mysql_query($delete_child_query)){
					$arr = array(
						'action'=>"delete child",
						'success'=>0,
						'error'=>"Error in deleting child: ".mysql_error()
					);					
					echo json_encode($arr);
					exit;
				}
			}
			else{
				$arr = array(
					'action'=>"delete child",
					'success'=>0,
					'error'=>"Error in unpublishing child: ".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
}

if($_POST['action']=="delete"){
	$n_id = mysql_escape_string($_POST['n_id']);
	$parent_id = "";
	
	if(has_child($n_id,'content')){
		delete_child($n_id);
	}	
	
	//delete image
	$get_path_query = "SELECT image FROM content_table WHERE id='$n_id'";
	$img_thumb_path = mysql_result(mysql_query($get_path_query),0);
	if($img_thumb_path!=""){		
		$img_full_path = str_replace(_THUMB_FOLDER_,"",$img_thumb_path);
		$img_full_path = iconv('UTF-8','GB2312',_ROOT_PATH_.$img_full_path);
		$img_thumb_path = iconv('UTF-8','GB2312',_ROOT_PATH_.$img_thumb_path);
		//delete image and thumb
		if(file_exists($img_thumb_path)){
			if(!unlink($img_full_path) || !unlink($img_thumb_path)){			
				$arr = array(
					'action'=>"delete image",
					'success'=>0,
					'error'=>"Error in deleting image"
				);
				echo json_encode($arr);
				exit;
			}
		}		
	}
	
	$get_parent_query = "SELECT parent_id FROM content_table WHERE id = '$n_id'";
	$get_parent = mysql_query($get_parent_query);
	$parent_id = mysql_result($get_parent,0);
	
	$unpublish_node_query = "DELETE FROM pub_content_table WHERE id = '$n_id'";
	if(mysql_query($unpublish_node_query)){
		$delete_node_query = "DELETE FROM content_table WHERE id = '$n_id'";
		if(!mysql_query($delete_node_query)){
			$arr = array(
				'success'=>0,
				'error'=>"Error in deleting content: ".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"Error in unpublishing content: ".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	$arr = array(
		'success'=>1,
		'id'=>$n_id,
		'parent_id'=>$parent_id
	);
	echo json_encode($arr);
	exit;
}

function publish_child($parent_id){
	global $pub_content_fields;

	//publish children
	$get_child_query = "SELECT * FROM content_table WHERE parent_id = '$parent_id'";
	$get_child = mysql_query($get_child_query);

	if(mysql_num_rows($get_child)>0){		
		while($c_row = mysql_fetch_array($get_child)){
			$child_id = $c_row['id'];
			
			$publish_child_query = "";
			if(check_publish($child_id)==0){//insert record
				$insert_field_str = "";
				$insert_value_str = "";
				foreach($pub_content_fields as $val){
					if($insert_field_str!="")$insert_field_str .= ",";
					$insert_field_str .= $val;
					
					if($insert_value_str!="")$insert_value_str .= ",";
					$insert_value_str .= "'".mysql_escape_string($c_row[$val])."'";
				}
			
				$publish_child_query = "INSERT INTO pub_content_table(
											$insert_field_str
										) VALUES(
											$insert_value_str
										)";
			}
			else{//update record
				$update_field_str = "";
				foreach($pub_content_fields as $val){
					if($val!="id"){
						if($update_field_str!="")$update_field_str .= ",";
						$update_field_str .= "$val = '".mysql_escape_string($c_row[$val])."'";
					}
				}
			
				$publish_child_query = "UPDATE pub_content_table SET $update_field_str WHERE id='$child_id'";
			}
			if(has_child($child_id)){
				publish_child($child_id);
			}
			
			if(mysql_query($publish_child_query)){
				$sql = "UPDATE pub_content_table SET published_time='".time()."' WHERE id='$child_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'action'=>"publish child",
						'success'=>0,
						'error'=>"Error in updating published time: ".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
			else{
				$arr = array(
					'action'=>"publish child",
					'success'=>0,
					'error'=>"Error in publishing child: ".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
	}
}

if($_POST['action']=="publish"){
	$n_id = mysql_escape_string($_POST['n_id']);
	
	$get_content_query = "SELECT * FROM content_table WHERE id='$n_id'";
	$get_content = mysql_query($get_content_query);
	if(mysql_num_rows($get_content)>0){
		$c_row = mysql_fetch_array($get_content);
		foreach($pub_content_fields as $val){
			${'c_'.$val} = $c_row[$val];
		}
		
		$topic_link = _BASE_URL_.fetch_route($n_id);
		$topic_img = _BASE_URL_.str_replace(_THUMB_FOLDER_,"",$c_image);
		
		$publish_node_query = "";
		if(check_publish($n_id)==0){//insert record
			$insert_field_str = "";
			$insert_value_str = "";
			foreach($pub_content_fields as $val){
				if($insert_field_str!="")$insert_field_str .= ",";
				$insert_field_str .= $val;
				
				if($insert_value_str!="")$insert_value_str .= ",";
				$insert_value_str .= "'".mysql_escape_string(${'c_'.$val})."'";
			}
		
			$publish_node_query = "	INSERT INTO pub_content_table(
										$insert_field_str,
										published_time
									) VALUES(
										$insert_value_str,
										'".time()."'
									)";
		}
		else{//update record
			$update_field_str = "";
			foreach($pub_content_fields as $val){
				if($val!="id"){
					if($val!="created_time"){
						if($update_field_str!="")$update_field_str .= ",";
						$update_field_str .= "$val = '".mysql_escape_string(${'c_'.$val})."'";
					}
				}
			}
		
			$publish_node_query = "	UPDATE pub_content_table 
									SET $update_field_str,
										published_time='".time()."' 
									WHERE id='$n_id'";
		}
		
		if(mysql_escape_string($_POST['publish_child'])=="true"){
			if(has_child($n_id)){
				publish_child($n_id);
			}
		}
		
		if(mysql_query($publish_node_query)){
			$sql = "UPDATE content_table SET published_time='".time()."' WHERE id='$n_id'";
			if(mysql_query($sql)){

if($c_type=="topic" || $c_type=="news"){
	//推送消息
	if($_SESSION['wechat_token']==""){//获取access_token
		$url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$GLOBALS['appid'].'&corpsecret='.$GLOBALS['appsecret'];		
		$result = https_post($url);
		$result = json_decode($result,true);	
		if(array_key_exists("errcode",$result)){
			if($result['errcode']!=0){
				$arr = array(
					'success'=>0,
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				echo json_encode($arr);
				exit;
			}
			$_SESSION['wechat_token'] = $result['access_token'];
		}		
	}

	$url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$_SESSION['wechat_token'];

	$sql = "SELECT userid,name,lang FROM member_table WHERE status=1";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$userid = $row['userid'];
			$name = $row['name'];
			$lang = $row['lang'];
			$topic_postfix = ($lang==0)?'_en':'';
			
			$sql = "SELECT title".$topic_postfix." AS title FROM pub_content_table WHERE id='$n_id'";
			$topic_title = mysql_result(mysql_query($sql),0);
			
			if($c_type=="topic"){
				$type_en = "topic";
				$type_zh = "主题";
			}
			else if($c_type=="news"){
				$type_en = "message";
				$type_zh = "资讯";
			}
			
			if($lang==0){
				$title = "New ".ucfirst($type_en)." Published";
				$description = 'Dear '.$name.', we just published a new '.$type_en.'. Enter and check it out!';
			}
			else{
				$title = "新".$type_zh."发布";
				$description = '尊敬的 '.$name.'，我们刚发布了新的'.$type_zh.'。进入查看一下吧！';
			}
			
			$sql = "INSERT INTO notification_table(
						id,
						userid,
						title,
						description,
						url,
						sent_by,
						sent_time
					) VALUES(
						'".get_new_id("notification_table")."',
						'$userid',
						'$title',
						'$description',
						'{n_id:".$n_id."}',
						'".$_SESSION['u_id']."',
						'".time()."'
					)";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"Error in inserting notification record: ".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
			
			$data = '{
						"touser" : "'.$userid.'",
						"msgtype" : "news",
						"agentid" : '.$GLOBALS['agentid'].',
						"news" : {
							"articles" : [
								{
									"title" : "'.$topic_title.'",
									"description" : "'.$description.'",
									"url" : "'.$topic_link.'",
									"picurl" : "'.$topic_img.'"
								}
							]
						}
					}';
			$result = https_post($url,$data);
			$result = json_decode($result,true);
			if(array_key_exists("errcode",$result)){
				if($result['errcode']!=0){
					$_SESSION['wechat_token'] = "";
					$arr = array(
						'success'=>0,
						'error'=>$result['errcode'].":".$result['errmsg']
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
}
			}
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>"Error in publishing content: ".mysql_error()
			);
		}
	}
	$arr = array(
		'success'=>1,
		'id'=>$n_id
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="unpublish"){
	$n_id = mysql_escape_string($_POST['n_id']);
	
	$unpublish_node_query = "DELETE FROM pub_content_table WHERE id='$n_id'";
	if(mysql_query($unpublish_node_query)){
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1,
			'id'=>$n_id
		);
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"Error in unpublishing content: ".mysql_error()
		);
	}	
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="post_weibo"){
	$t_id = mysql_escape_string($_POST['t_id']);
	$content = mysql_escape_string(stripslashes($_POST['content']));
	
	$get_image = "SELECT image FROM content_table WHERE id='$t_id'";
	$image = mysql_result(mysql_query($get_image),0);
	$img = $GLOBALS['root_url'].str_replace($GLOBALS['thumb_folder']."/","",$image);
	
	$sina_k = '1656421639'; //新浪微博应用App Key
	$sina_s = '3a4ac22b89ef44b92992c831ed781bd2'; //新浪微博应用App Secret
	require_once("../oauth/weibo_sdk.php");
	
	function getimgp($u){//图片处理
		$c=@file_get_contents($u);
		$name=md5($u).'.jpg';
		$mime='image/unknown';
		return array($mime, $name, $c);
	}
	
	$weibo_sdk = new sinaPHP($sina_k, $sina_s);
	$result = $weibo_sdk->access_token($callback_url, $_GET['code']);
	$sina_t = $_COOKIE['weibo_token'];
	
	if($sina_t!=""){
		//发布微博
		$img_a = getimgp($img);
		$sina = new sinaPHP($sina_k, $sina_s, $sina_t);
		if($img_a[2]!=''){//发布带图片微博
			$result=$sina->update($content, $img_a);
		}
		else{//发布纯文字微博
			$result=$sina->update($content);
		}
		
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1
		);
		echo json_encode($arr);
		exit;
	}
}
?>
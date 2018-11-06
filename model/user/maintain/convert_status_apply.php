<?php
require_once("../include/conn.php");
require_once("../function.php");

/*$sql = "SELECT project_id,u_type,u_id,type,process_time FROM project_apply WHERE name=''";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$u_type = $row[1];
		$u_id = $row[2];
		$type = $row[3];
		$process_time = $row[4];		
		
		$sql = "SELECT name FROM user WHERE id='$u_id'";
		$u_name = mysql_result(mysql_query($sql),0);
		
		$sql = "UPDATE project_apply SET name='$u_name' WHERE project_id='$project_id' AND u_type='$u_type' AND u_id='$u_id' AND type='$type' AND process_time='$process_time'";
		if(mysql_query($sql)){
			echo "Update apply user name [$u_name] successfully!<br/>";
		}
	}
}*/

$sql = "SELECT id,status_assign,status_apply FROM project WHERE year_apply=2018";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$status_assign = $row[1];
		$status_apply = $row[2];
		/*$status_apply = json_decode($status_apply,true);		
		if(	$status_apply['t']['o']==0 && 
			$status_apply['t']['d']==0 && 
			$status_apply['t']['e']==0 && 
			$status_apply['t']['p']==0 && 
			$status_apply['f']['o']==0 && 
			$status_apply['f']['d']==0 && 
			$status_apply['f']['e']==0 && 
			$status_apply['f']['p']==0
		){*/
		if($status_apply==""){
			$tmp_arr = [
				't'=>[
					'o'=>0,
					'd'=>0,
					'e'=>0,
					'p'=>0,
					'c'=>0,
				],
				'f'=>[
					'o'=>0,
					'd'=>0,
					'e'=>0,
					'p'=>0,
					'c'=>0,
				]
			];
			
			$sql = "SELECT u_type,type FROM project_apply WHERE project_id='$project_id'";
			$get_apply = mysql_query($sql);
			if(mysql_num_rows($get_apply)>0){
				while($a_row = mysql_fetch_array($get_apply)){
					$u_type = $a_row[0];
					$type = $a_row[1];
					$tmp_arr[$u_type][$type] = 1;
				}
			}
			
			$sql = "UPDATE project SET status_apply='".json_encode($tmp_arr,true)."' WHERE id='$project_id'";
			if(mysql_query($sql)){
				echo "Update apply status for project [$project_id] successfully!<br/>";
			}
		}
	}
}

/*$sql = "SELECT id,status_assign,status_apply FROM project WHERE year_apply='".date('Y')."' AND progress>0 AND progress<6";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$status_assign = $row[1];
		$status_apply = $row[2];
		if($status_apply!=""){
			$apply_arr = json_decode($status_apply,true);			
			$progress = 1;
			if($status_assign==5){//技术外包
				if($apply_arr['f']['o']>0)$progress = 2;//已收集
				if($apply_arr['f']['d']>0)$progress = 3;//已完稿
				if($apply_arr['f']['e']>0 && $apply_arr['f']['p']>0 && $apply_arr['f']['c']==0 && $u_type=="f"){
					$progress = 4;//已提交
				}
				if($apply_arr['f']['c']>0)$progress = 5;//已确认，待立项	
			}
			else if($status_assign==6){//财务外包
				if($apply_arr['t']['o']>0)$progress = 2;//已收集
				if($apply_arr['t']['d']>0)$progress = 3;//已完稿
				if($apply_arr['t']['e']>0 && $apply_arr['t']['p']>0 && $apply_arr['t']['c']==0 && $u_type=="t"){
					$progress = 4;//已提交
				}
				if($apply_arr['t']['c']>0)$progress = 5;//已确认，待立项
			}
			else{
				if($apply_arr['t']['o']>0 && $apply_arr['f']['o']>0)$progress = 2;//已收集
				if($apply_arr['t']['d']>0 && $apply_arr['f']['d']>0)$progress = 3;//已完稿
				if(	$apply_arr['t']['e']>0 && $apply_arr['t']['p']>0 && 
					$apply_arr['f']['e']>0 && $apply_arr['f']['p']>0 && 
					($apply_arr['t']['c']==0 || $apply_arr['f']['c']==0)
				){
					$progress = 4;//已提交
				}
				if($apply_arr['t']['c']>0 && $apply_arr['f']['c']>0)$progress = 5;//已确认，待立项
			}
			
			$tmp_arr = [];
			if(isset($apply_arr['t']['o']))$tmp_arr['t']['o'] = $apply_arr['t']['o'];
			else $tmp_arr['t']['o'] = 0;
			
			if(isset($apply_arr['t']['d']))$tmp_arr['t']['d'] = $apply_arr['t']['d'];
			else $tmp_arr['t']['d'] = 0;
			
			if(isset($apply_arr['t']['e']))$tmp_arr['t']['e'] = $apply_arr['t']['e'];
			else $tmp_arr['t']['e'] = 0;
			
			if(isset($apply_arr['t']['p']))$tmp_arr['t']['p'] = $apply_arr['t']['p'];
			else $tmp_arr['t']['p'] = 0;
			
			if(isset($apply_arr['t']['c']))$tmp_arr['t']['c'] = $apply_arr['t']['c'];
			else $tmp_arr['t']['c'] = 0;
			
			if(isset($apply_arr['f']['o']))$tmp_arr['f']['o'] = $apply_arr['f']['o'];
			else $tmp_arr['f']['o'] = 0;
			
			if(isset($apply_arr['f']['d']))$tmp_arr['f']['d'] = $apply_arr['f']['d'];
			else $tmp_arr['f']['d'] = 0;
			
			if(isset($apply_arr['f']['e']))$tmp_arr['f']['e'] = $apply_arr['f']['e'];
			else $tmp_arr['f']['e'] = 0;
			
			if(isset($apply_arr['f']['p']))$tmp_arr['f']['p'] = $apply_arr['f']['p'];
			else $tmp_arr['f']['p'] = 0;
			
			if(isset($apply_arr['f']['c']))$tmp_arr['f']['c'] = $apply_arr['f']['c'];
			else $tmp_arr['f']['c'] = 0;

			//echo $progress."<br/>";
			$sql = "UPDATE project SET status_apply='".json_encode($tmp_arr,true)."',progress='$progress' WHERE id='$project_id'";
			if(mysql_query($sql)){
				echo "Update progress [$progress] for project [$project_id] successfully!<br/>";
			}
		}
	}
}
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$curr_page = 1;
if(isset($_REQUEST['page']) && $_REQUEST['page']!="")$curr_page = $_REQUEST['page'];
$rows = $_REQUEST['per_page'];
$object = $_REQUEST['object'];

$doc = new DOMDocument('1.0', 'UTF-8');

$projects = $doc->createElement("projects");
$doc->appendChild($projects);

$cond_str = build_responsible_cond();
include_once("fetch_project_cond.php");

$sql = "SELECT COUNT(*) 
		FROM project 
			LEFT JOIN company ON project.company_id=company.id 
			LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='s' AND is_curr=1) AS a_sales ON project.id=a_sales.project_id 
			LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='t' AND is_curr=1) AS a_technology ON project.id=a_technology.project_id 
			LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='f' AND is_curr=1) AS a_finance ON project.id=a_finance.project_id 
		$cond_str";
$project_num = mysql_result(mysql_query($sql),0);
$r_num = $doc->createElement("project_num",$project_num);
$projects->appendChild($r_num);

$curr_p = $doc->createElement("curr_page",$curr_page);
$projects->appendChild($curr_p);

$page_num = ceil($project_num/$rows);
$p_num = $doc->createElement("page_num",$page_num);
$projects->appendChild($p_num);

$sort_str = "";
if($_REQUEST['sort']){
	foreach($_REQUEST['sort'] as $sort){
		$sort_arr = explode("|",$sort);
		if($sort_str!="")$sort_str .= ",";
		if(	$sort_arr[0]=="sales" || 
			$sort_arr[0]=="technology" || 
			$sort_arr[0]=="finance"
		){
			$sort_str .= "CONVERT(".$sort_arr[0]."_name USING GBK) ".strtoupper($sort_arr[1]);
		}
		else{
			if($sort_arr[0]=="company_id")$sort_arr[0] = "company.name";
			else $sort_arr[0] = "project.".$sort_arr[0];
			$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
		}
	}
}
if($sort_str!=""){
	$sort_str = "ORDER BY ".$sort_str;
}
else{
	$sort_str = "ORDER BY date_sign DESC";
}

$select_key_str = "";
foreach($GLOBALS[$object.'_list_fields'] as $key=>$val){
	if(	$key!="sales" && 
		$key!="technology" && 
		$key!="finance"
	){
		if($select_key_str!="")$select_key_str .= ",";		
		$select_key_str .= "project.".$key;
	}
}

$sql = "SELECT 
			project.id,
			project.status_assign,
			project.status_apply,
			project.receive,
			company.name AS company_name,
			a_sales.u_id AS sales,
			a_technology.u_id AS technology,
			a_finance.u_id AS finance,
			a_sales.name AS sales_name,
			a_technology.name AS technology_name,
			a_finance.name AS finance_name,
			a_technology.accepted_time AS technology_accepted_time,
			a_finance.accepted_time AS finance_accepted_time,
			$select_key_str 
		FROM project 
			LEFT JOIN company ON project.company_id=company.id 
			LEFT JOIN (SELECT project_id,u_id,name FROM project_assign WHERE u_type='s' AND is_curr=1) AS a_sales ON project.id=a_sales.project_id 
			LEFT JOIN (SELECT project_id,u_id,name,accepted_time FROM project_assign WHERE u_type='t' AND is_curr=1) AS a_technology ON project.id=a_technology.project_id 
			LEFT JOIN (SELECT project_id,u_id,name,accepted_time FROM project_assign WHERE u_type='f' AND is_curr=1) AS a_finance ON project.id=a_finance.project_id 
		$cond_str 
		$sort_str 
		LIMIT ".(($curr_page-1)*$rows).",$rows";
$get_project = mysql_query($sql);
if(mysql_num_rows($get_project)>0){
	while($row = mysql_fetch_array($get_project)){		
		$project = $doc->createElement("project");
		$projects->appendChild($project);
		
		$project_id = $row['id'];
		$r_project_id = $doc->createElement("project_id",$project_id);
		$project->appendChild($r_project_id);
		
		$receive = $row['receive'];
		$r_receive = $doc->createElement("receive",$receive);
		$project->appendChild($r_receive);
		
		$status_assign = $row['status_assign'];
		$status_apply = $row['status_apply'];
		
		$raw_progress = 0;
		$raw_status_assign = 0;
		foreach($GLOBALS[$object.'_list_fields'] as $key=>$val){
			${$key} = $row[$key];
			
			if($key=="company_id")$company_id = $row['company_name'];
			if($key=="name"){
				$sql = "SELECT user.name,log_time 
						FROM log LEFT JOIN user ON log.u_id=user.id 
						WHERE object='p' AND object_id='$project_id' AND content='审核'";
				$get_verify = mysql_query($sql);
				if(mysql_num_rows($get_verify)>0){
					$v_row = mysql_fetch_array($get_verify);
					$r_verify = $doc->createElement('verify',htmlspecialchars($v_row[0]."于".date('Y/m/d H:i',$v_row[1])."审核"));
					$project->appendChild($r_verify);
				}
			}
			if($key=="branch"){
				$branch_arr = explode(",",$branch);
				$branch_str = "";
				foreach($branch_arr as $b_id){
					$sql = "SELECT name FROM branch WHERE id='$b_id'";
					$b_name = mysql_result(mysql_query($sql),0);
					
					if($branch_str!="")$branch_str .= "+";
					$branch_str .= $b_name;
				}
				$branch = $branch_str;
			}
			if($key=="category")$category = $GLOBALS['project_category_opt'][$category];
			if($key=="level")$level = ($level>0)?$GLOBALS['project_level_opt'][$level]:"";
			if($key=="date_expire")$date_expire = ($date_expire!="0000-00-00")?$date_expire:"-";
			if($key=="status_apply"){
				$apply_arr = json_decode($status_apply,true);
				$apply_str = "";
				foreach($apply_arr as $au_type=>$data){
					if($au_type=="t"){
						$apply_str .= "技术：";
						if($raw_status_assign==5){
							$apply_str .= "业务外包";
						}
						else{
							$status_str = "";
							foreach($data as $a_type=>$status){								
								if($a_type=="o"){
									switch($status){
										case 0: $status_str = "未收材料"; break;
										case 1: $status_str = "已收材料"; break;
									}
								}
								if($a_type=="e"){
									switch($status){
										case 0: $status_str = "未交电子"; break;
										case 1: $status_str = "已交电子"; break;
										case 2: $status_str = "无需交电子"; break;
									}
								}
								if($a_type=="p"){
									switch($status){
										case 0: $status_str .= "，未交纸质"; break;
										case 1: $status_str .= "，已交纸质"; break;
										case 2: $status_str .= "，无需交纸质"; break;
									}
								}
							}
							$apply_str .= $status_str;
						}
					}
					if($au_type=="f"){
						if($apply_str!="")$apply_str .= "<br/>";
						$apply_str .= "财务：";
						if($raw_status_assign==6){
							$apply_str .= "业务外包";
						}
						else{
							$status_str = "";
							foreach($data as $a_type=>$status){								
								if($a_type=="o"){
									switch($status){
										case 0: $status_str = "未收材料"; break;
										case 1: $status_str = "已收材料"; break;
									}
								}
								if($a_type=="e"){
									switch($status){
										case 0: $status_str = "未交电子"; break;
										case 1: $status_str = "已交电子"; break;
										case 2: $status_str = "免交电子"; break;
									}
								}
								if($a_type=="p"){
									switch($status){
										case 0: $status_str .= "，未交纸质"; break;
										case 1: $status_str .= "，已交纸质"; break;
										case 2: $status_str .= "，免交纸质"; break;
									}
								}
							}
							$apply_str .= $status_str;
						}
					}
				}					
				$status_apply = $apply_str;
			}
			if($key=="sales"){
				$sales_name = $row['sales_name'];
				$sales = ($sales_name!="")?$sales_name:"-";
			}
			if($key=="technology"){
				$technology_name = $row['technology_name'];
				$technology_name = ($technology_name!="")?$technology_name:(($status_assign==5 || $status_assign==7)?"无需":"-");
				
				$t_accepted_time = $row['technology_accepted_time'];
				$technology_class = ($t_accepted_time>0)?"done":(($status_assign==5 || $status_assign==7)?"alarm":"");
				$technology = $technology_name."|".$technology_class;
				
				$t_accepted_time = ($t_accepted_time>0)?date('Y/m/d H:i',$t_accepted_time):"";
				$r_t_accepted_time = $doc->createElement('t_accepted_time',$t_accepted_time);
				$project->appendChild($r_t_accepted_time);
			}
			if($key=="finance"){
				$finance_name = $row['finance_name'];
				$finance_name = ($finance_name!="")?$finance_name:(($status_assign==6 || $status_assign==7)?"无需":"-");
				
				$f_accepted_time = $row['finance_accepted_time'];
				$finance_class = ($f_accepted_time>0)?"done":(($status_assign==6 || $status_assign==7)?"alarm":"");
				$finance = $finance_name."|".$finance_class;
				
				$f_accepted_time = ($f_accepted_time>0)?date('Y/m/d H:i',$f_accepted_time):"";
				$r_f_accepted_time = $doc->createElement('f_accepted_time',$f_accepted_time);
				$project->appendChild($r_f_accepted_time);
			}
			/* SALES */
			if($object=="sales"){			
				if($key=="status_assign"){
					$raw_status_assign = $status_assign;
					$status_assign_class = "";
					switch($status_assign){
						case 0: $status_assign_class = "pending"; break;
						case 5: $status_assign_class = "alarm"; break;
						case 6: $status_assign_class = "alarm"; break;
						case 7: $status_assign_class = "alarm"; break;
						default: $status_assign_class = "done";
					}
					
					$status_assign = $GLOBALS['status_assign_opt'][$status_assign]."|".$status_assign_class;
				}
			}
			/* TECHNOLOGY */
			if($object=="technology"){
				if($key=="status_assign"){
					$raw_status_assign = $status_assign;
					$status_assign_class = "";
					switch($status_assign){
						case 0: $status_assign_class = "pending"; break;
						case 1: $status_assign_class = "pending"; break;
						case 3: $status_assign_class = "pending"; break;
						case 5: $status_assign_class = "alarm"; break;
						case 6: $status_assign_class = "alarm"; break;
						case 7: $status_assign_class = "alarm"; break;
						default: $status_assign_class = "done";
					}
					
					$status_assign = $GLOBALS['status_assign_opt'][$status_assign]."|".$status_assign_class;
				}
			}
			/* FINANCE */
			if($object=="finance"){
				if($key=="status_assign"){
					$raw_status_assign = $status_assign;
					$status_assign_class = "";
					switch($status_assign){
						case 0: $status_assign_class = "pending"; break;
						case 1: $status_assign_class = "pending"; break;
						case 2: $status_assign_class = "pending"; break;
						case 5: $status_assign_class = "alarm"; break;
						case 6: $status_assign_class = "alarm"; break;
						case 7: $status_assign_class = "alarm"; break;
						default: $status_assign_class = "done";
					}
					
					$status_assign = $GLOBALS['status_assign_opt'][$status_assign]."|".$status_assign_class;
				}
			}
			if($key=="progress"){
				$raw_progress = $progress;
				$progress_class = "";
				switch($progress){
					case 1: $progress_class = "ongoing"; break;//已接单
					case 2: $progress_class = "ongoing"; break;//已收集
					case 3: $progress_class = "ongoing"; break;//已完稿
					case 4: $progress_class = "ongoing"; break;//已提交
					case 5: $progress_class = "followup"; break;//待立项
					case 6: $progress_class = "done"; break;//成功立项
					case 7: $progress_class = "alarm"; break;//未能立项
					case 8: $progress_class = "done"; break;//验收1成功
					case 9: $progress_class = "alarm"; break;//验收1失败
					case 10: $progress_class = "done"; break;//验收2成功
					case 11: $progress_class = "alarm"; break;//验收2失败
					case 12: $progress_class = "followup"; break;//已请款
					case 13: $progress_class = "pending"; break;//企业到账
					case 14: $progress_class = "done"; break;//中科到账
					case 20: $progress_class = "cancel"; break;//合同完结
					case 21: $progress_class = "cancel"; break;//合同取消
					case 22: $progress_class = "cancel"; break;//合同转让
				}
				$progress = $GLOBALS['project_progress_opt'][$progress];
				
				if($raw_progress==4){
					$status_apply = json_decode($status_apply,true);
					if($object=="technology"){
						if($status_apply['t']['e']>0 && $status_apply['t']['p']>0 && $status_apply['t']['c']==0){
							$progress = "待确认";
							$progress_class = "attention";
						}
					}
					if($object=="finance"){
						if($status_apply['f']['e']>0 && $status_apply['f']['p']>0 && $status_apply['f']['c']==0){
							$progress = "待确认";
							$progress_class = "attention";
						}
					}
				}
				
				$progress .= "|".$progress_class;
			}
			if($key=="need_approve" || $key=="need_check" || $key=="need_fund"){
				${$key} = (${$key}==1)?"<a class=\"btn_need $key yes\" title=\"设为无需\"></a>":"<a class=\"btn_need $key no\" title=\"设为需要\"></a>";
			}
			
			${'r_'.$key} = $doc->createElement($key,htmlspecialchars(${$key}));
			$project->appendChild(${'r_'.$key});
		}
	}
}

echo $doc->saveXML();
?>
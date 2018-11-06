<?php
header('Content-type: text/html; charset=utf-8');

require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="add"){
	$new_id = get_new_id("agreement");
	
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['agreement_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="company_id"){
				$company = mysql_escape_string($_POST['v_company']);
				$sql = "SELECT id FROM company WHERE name='$company'";
				$company_id = mysql_result(mysql_query($sql),0);
				if($company_id==0){
					$arr = array(
						'success'=>0,
						'error'=>"找不到此公司名称，请先到“公司”栏目添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			if($key=="sales_name"){
				$sql = "SELECT name FROM user WHERE id='$sales_id'";
				$sales_name = mysql_result(mysql_query($sql),0);
			}
			if($key=="remark"){
				$append_remark = mysql_escape_string($_POST['v_append_remark']);
				
				$user_info = get_user_info($_SESSION['u_id']);
				$remark_by = $user_info['name'];
				
				$remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";
			}
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";		
			$insert_val_str .= "'".${$key}."'";
		}
	}
	
	$sql = "INSERT INTO agreement(
				id,
				$insert_key_str
			) VALUES(
				'$new_id',
				$insert_val_str
			)";
	if(mysql_query($sql)){
		$sql = "SELECT COUNT(*) FROM pbase WHERE name='$name'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO pbase(
						id,
						name,
						type
					) VALUES(
						'".get_new_id("pbase")."',
						'$name',
						'a'
					)";
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"添加基础协议出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		if(isset($_POST['v_pay_type'])){
			$pay_type = mysql_escape_string($_POST['v_pay_type']);
			$amt_contract = mysql_escape_string($_POST['v_amt_contract']);
			$amt_prepay = mysql_escape_string($_POST['v_amt_prepay']);
			$amt_actual = mysql_escape_string($_POST['v_amt_actual']);
			$amt_commission = mysql_escape_string($_POST['v_amt_commission']);
			$agent = mysql_escape_string($_POST['v_agent']);
			$finance_remark = mysql_escape_string($_POST['v_finance_remark']);
			
			$insert_f_key_str = "";
			$insert_f_val_str = "";
			$update_f_key_str = "";
			foreach($GLOBALS['project_finance_fields'] as $key){
				if($insert_f_key_str!="")$insert_f_key_str .= ",";
				$insert_f_key_str .= $key;
				
				if($insert_f_val_str!="")$insert_f_val_str .= ",";
				$insert_f_val_str .= "'".${$key}."'";
				
				if($update_f_key_str!="")$update_f_key_str .= ",";
				$update_f_key_str .= $key."='".${$key}."'";
			}
			
			$sql = "SELECT COUNT(*) FROM agreement_finance WHERE agreement_id='$id'";
			$has_rec = mysql_result(mysql_query($sql),0);
			if($has_rec==0){
				$sql = "INSERT INTO agreement_finance(
							agreement_id,
							$insert_f_key_str
						) VALUES(
							'$new_id',
							$insert_f_val_str
						)";
			}
			else{
				$sql = "UPDATE agreement_finance SET $update_f_key_str WHERE agreement_id='$new_id'";
			}
			if(!mysql_query($sql)){
				$arr = array(
					'success'=>0,
					'error'=>"保存协议财务记录出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		save_log("a",$new_id,"创建");
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"添加协议信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="edit"){
	$id = mysql_escape_string($_POST['id']);
	$agreement_info = get_agreement_info($id);
	$raw_progress = $agreement_info['progress'];
	
	$update_key_str = "";
	foreach($GLOBALS['agreement_fields'] as $key){
		if($key!='id'){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="company_id"){
				$company = mysql_escape_string($_POST['v_company']);
				$sql = "SELECT id FROM company WHERE name='$company'";
				$company_id = mysql_result(mysql_query($sql),0);
				if($company_id==0){
					$arr = array(
						'success'=>0,
						'error'=>"找不到此公司名称，请先到“公司”栏目添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			if($key=="sales_name"){
				$sql = "SELECT name FROM user WHERE id='$sales_id'";
				$sales_name = mysql_result(mysql_query($sql),0);
			}
			if($key=="remark"){
				$append_remark = mysql_escape_string($_POST['v_append_remark']);
				
				$old_remark = $agreement_info['remark'];
				
				$user_info = get_user_info($_SESSION['u_id']);
				$remark_by = $user_info['name'];
				
				$new_remark = ($append_remark!='')?$remark_by."于".date('Y/m/d H:i')."备注：".$append_remark."|":"";			
				$remark = $new_remark.$old_remark;
			}
		
			if($update_key_str!="")$update_key_str .= ",";
			$update_key_str .= "$key='".${$key}."'";
		}
	}
	
	if(isset($_POST['v_pay_type'])){
		$pay_type = mysql_escape_string($_POST['v_pay_type']);
		$amt_contract = mysql_escape_string($_POST['v_amt_contract']);
		$amt_prepay = mysql_escape_string($_POST['v_amt_prepay']);
		$amt_actual = mysql_escape_string($_POST['v_amt_actual']);
		$amt_commission = mysql_escape_string($_POST['v_amt_commission']);
		$agent = mysql_escape_string($_POST['v_agent']);
		$finance_remark = mysql_escape_string($_POST['v_finance_remark']);
		
		$insert_f_key_str = "";
		$insert_f_val_str = "";
		$update_f_key_str = "";
		foreach($GLOBALS['project_finance_fields'] as $key){
			if($insert_f_key_str!="")$insert_f_key_str .= ",";
			$insert_f_key_str .= $key;
			
			if($insert_f_val_str!="")$insert_f_val_str .= ",";
			$insert_f_val_str .= "'".${$key}."'";
			
			if($update_f_key_str!="")$update_f_key_str .= ",";
			$update_f_key_str .= $key."='".${$key}."'";
		}
		
		$sql = "SELECT COUNT(*) FROM agreement_finance WHERE agreement_id='$id'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "INSERT INTO agreement_finance(
						agreement_id,
						$insert_f_key_str
					) VALUES(
						'$id',
						$insert_f_val_str
					)";
		}
		else{
			$sql = "UPDATE agreement_finance SET $update_f_key_str WHERE agreement_id='$id'";
		}
		if(!mysql_query($sql)){
			$arr = array(
				'success'=>0,
				'error'=>"保存协议财务记录出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	$sql = "UPDATE agreement 
			SET $update_key_str  
			WHERE id='$id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"更新协议信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("a",$id,"更新");
	$arr = array(
		'success'=>1,
		'agreement_id'=>$id,
	);
	echo json_encode($arr);
	exit;
}

function delete_agreement_data($agreement_id){
	$sql = "SELECT name FROM agreement WHERE id='$agreement_id'";
	$name = mysql_result(mysql_query($sql),0);
	
	$sql = "DELETE FROM agreement WHERE id='$agreement_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除协议信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM agreement_finance WHERE agreement_id='$agreement_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除协议财务信息出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "DELETE FROM log WHERE object='a' AND object_id='$project_id'";
	if(!mysql_query($sql)){
		$arr = array(
			'success'=>0,
			'error'=>"删除协议日志出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("a",$agreement_id,"删除协议[".$name."]");
}
if($_POST['action']=="delete_agreement"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = build_responsible_cond();
		include_once("fetch_agreement_cond.php");
		$sql = "SELECT agreement.id 
				FROM agreement 
					LEFT JOIN company ON agreement.company_id=company.id 
				$cond_str";		
		$get_agreement = mysql_query($sql);
		if(mysql_num_rows($get_agreement)>0){
			while($row = mysql_fetch_array($get_agreement)){
				$id = $row[0];				
				delete_agreement_data($id);
			}
		}
	}
	else{
		if(isset($_REQUEST['agreement_id'])){
			foreach($_REQUEST['agreement_id'] as $id){
				$id = mysql_escape_string($id);
				delete_agreement_data($id);
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="verify"){
	$id = mysql_escape_string($_POST['id']);
	save_log("a",$id,"审核");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_REQUEST['action']=="export_agreement"){
	$cond_str = "";
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = build_responsible_cond();
		include_once("fetch_agreement_cond.php");
	}
	else{
		if(isset($_REQUEST['agreement_id'])){
			foreach($_REQUEST['agreement_id'] as $id){
				$id = mysql_escape_string($id);
				if($cond_str!="")$cond_str .= ",";
				$cond_str .= "'".$id."'";
			}
			$cond_str = "agreement.id IN ($cond_str)";
		}
	}
	
	$sort_str = "";
	if($_REQUEST['sort']){
		foreach($_REQUEST['sort'] as $sort){
			$sort_arr = explode("|",$sort);
			if($sort_str!="")$sort_str .= ",";
			if($sort_arr[0]=="sales_name"){
				$sort_str .= "CONVERT(".$sort_arr[0]."_name USING GBK) ".strtoupper($sort_arr[1]);
			}
			else{
				if($sort_arr[0]=="company_id")$sort_arr[0] = "company.name";
				else $sort_arr[0] = "agreement.".$sort_arr[0];
				$sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
			}
		}
	}
	if($sort_str!=""){
		$sort_str = "ORDER BY ".$sort_str;
	}
	else{
		$sort_str = "ORDER BY id DESC";
	}
	
	$select_key_str = "";
	foreach($GLOBALS['agreement_exp_fields'] as $key=>$val){
		if(	$key!="sales" && 
			$key!="technology" && 
			$key!="finance"
		){
			if($select_key_str!="")$select_key_str .= ",";		
			$select_key_str .= "agreement.".$key;
		}
	}
	
	$i = 0;
	$head = "";
	$data = "";
	$sql = "SELECT 
				agreement.id,
				agreement.status_apply,
				company.name AS company_name,
				a_sales.u_id AS sales,
				a_technology.u_id AS technology,
				a_finance.u_id AS finance,
				a_sales.name AS sales_name,
				a_technology.name AS technology_name,
				a_finance.name AS finance_name,
				$select_key_str 
			FROM agreement 
				LEFT JOIN company ON agreement.company_id=company.id 
				LEFT JOIN (SELECT agreement_id,u_id,name FROM agreement_assign WHERE u_type='s' AND is_curr=1) AS a_sales ON agreement.id=a_sales.agreement_id 
				LEFT JOIN (SELECT agreement_id,u_id,name,accepted_time FROM agreement_assign WHERE u_type='t' AND is_curr=1) AS a_technology ON agreement.id=a_technology.agreement_id 
				LEFT JOIN (SELECT agreement_id,u_id,name,accepted_time FROM agreement_assign WHERE u_type='f' AND is_curr=1) AS a_finance ON agreement.id=a_finance.agreement_id 
			$cond_str 
			$sort_str";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			if($i==0)$head .= '<tr height="40" style="color:#000;">';
			$data .= '<tr height="30">';
			$raw_status_assign = 0;
			foreach($GLOBALS['agreement_exp_fields'] as $key=>$val){
				${$key} = $row[$key];
				
				if($key=="company_id")$company_id = $row['company_name'];
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
				if($key=="category")$category = $GLOBALS['agreement_category_opt'][$category];
				if($key=="level")$level = ($level>0)?$GLOBALS['agreement_level_opt'][$level]:"";
				if($key=="date_expire")$date_expire = ($date_expire!="0000-00-00")?$date_expire:"-";
				if($key=="status_assign"){
					$raw_status_assign = $status_assign;
					$status_assign = $GLOBALS['status_assign_opt'][$status_assign];
				}
				if($key=="sales"){
					$sales_name = $row['sales_name'];
					$sales = ($sales_name!="")?$sales_name:"-";
				}
				if($key=="technology"){
					$technology_name = $row['technology_name'];
					$technology = ($technology_name!="")?$technology_name:"-";
				}
				if($key=="finance"){
					$finance_name = $row['finance_name'];
					$finance = ($finance_name!="")?$finance_name:"-";
				}
				
				$val_arr = explode("#",$val);
				$width = ($val_arr[1]!="")?'width="'+$val_arr[1]+'"':'';
				if($i==0)$head .= '<th bgcolor="#EEEEEE" '.$width.'>'.$val_arr[0].'</th>';
				
				$bg_color = "";
				if($key=="progress"){					
					switch($progress){
						case 1: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 2: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 3: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 4: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 5: $bg_color = 'bgcolor="#fffde8"'; break;
						case 6: $bg_color = 'bgcolor="#e8fff2"'; break;
						case 7: $bg_color = 'bgcolor="#ffe8e8"'; break;
						case 8: $bg_color = 'bgcolor="#e8fff2"'; break;
						case 9: $bg_color = 'bgcolor="#ffe8e8"'; break;
						case 10: $bg_color = 'bgcolor="#e6f6ff"'; break;
						case 11: $bg_color = 'bgcolor="#f4f8fb"'; break;
						case 12: $bg_color = 'bgcolor="#e8fff2"'; break;
						case 20: $bg_color = 'bgcolor="#eeeeee"'; break;
						case 21: $bg_color = 'bgcolor="#eeeeee"'; break;
						case 22: $bg_color = 'bgcolor="#ffe8e8"'; break;
					}
					$progress = $GLOBALS['agreement_progress_opt'][$progress];
				}
				
				$data .= '<td align="center" '.$bg_color.'>'.${$key}.'</td>';
			}
			
			$status_apply = $row['status_apply'];
			$apply_arr = json_decode($status_apply,true);
			$apply_str = "";
			foreach($apply_arr as $au_type=>$a_data){
				if($au_type=="t"){
					if($raw_status_assign==5){
						$apply_str .= "业务外包";
					}
					else{
						$status_str = "";
						foreach($a_data as $a_type=>$status){								
							if($a_type=="o"){
								switch($status){
									case 0: $status_str = "未收材料"; break;
									case 1: $status_str = "已收材料"; break;
								}
							}
							if($a_type=="d"){
								switch($status){
									case 0: $status_str = "未完稿"; break;
									case 1: $status_str = "已完稿"; break;
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
			}
			if($i==0)$head .= '<th bgcolor="#EEEEEE">申报进度</th>';
			$data .= '<td align="center">'.$apply_str.'</td>';
			
			if($i==0)$head .= '</tr>';
			$data .= '</tr>';
			$i++;
		}
	
$html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
	<head>
	<!--[if gte mso 9]>
		<xml>
			<x:ExcelWorkbook>
			<x:ExcelWorksheets>
			<x:ExcelWorksheet>
			<x:Name></x:Name>
			<x:WorksheetOptions>
			<x:DisplayGridlines/>
			</x:WorksheetOptions>
			</x:ExcelWorksheet>
			</x:ExcelWorksheets>
			</x:ExcelWorkbook>
		</xml>
	<![endif]-->
	</head>

<table border="1" bordercolor="#AAAAAA" style="border-collapse:collapse;font-family:Arial;color:#333;font-size:12px;">
	$head
	$data
</table>
HTML;
	}
	//echo $html;exit;
	$filename = "agreement_".date('Ymd_His');
	$encoded_filename = urlencode($filename);
	$encoded_filename = str_replace("+", "%20", $encoded_filename);
	$ua = $_SERVER['HTTP_USER_AGENT'];
	
	header("Content-Type:application/vnd.ms-excel;charset=UTF-8");		
	if (preg_match("/MSIE/", $ua)){
		header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
	} 
	else if (preg_match("/Firefox/", $ua)){
		header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.xls"');
	} 
	else{
		header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
	}		
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");
	header("Expires: 0");
	print $html;
	exit;
}
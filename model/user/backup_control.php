<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if(isset($_REQUEST['ctrl']))$_SESSION['backup_ctrl'] = $_REQUEST['ctrl'];

$list_tables_query = "SHOW TABLES FROM ".$GLOBALS['zkkb_db'];
$list_tables = mysql_query($list_tables_query);
if(mysql_num_rows($list_tables)>0){
	if($_SESSION['backup_ctrl']=="restore_backup"){
	?>
	<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
		<tr>
			<td class="alert_holder">
			<?php
			$db_arr = glob(_ADMIN_PATH_._BK_DB_FOLDER_."*.sql");
			
			if($_SESSION['level']>2){
				if(count($db_arr)>0)print "请选择需要还原的数据库备份。";
				else print "没有任何可还原的数据表备份。";
			}
			else{
				print "你没有足够权限还原数据库备份。";
			} 
			?>
			</td>
		</tr>
		<tr>
			<td id="design_holder">
				<?php
				if(count($db_arr)>0){
					print "<ul id=\"restore_list\">";					
					foreach(array_reverse($db_arr) as $file){
						$info_str = "<b class=\"title\">备份时间</b>";
						
						$file_name = str_replace(".sql","",$file);
						$file_name_arr = explode("/",$file_name);
						$file_name_arr = array_reverse($file_name_arr);
						$file_name = $file_name_arr[0];
						$file_arr = explode("-",$file_name);
						$file_time = $file_arr[1];
						$time_arr = explode("_",$file_time);
						$year = $time_arr[0];
						$month = $time_arr[1];
						$day = $time_arr[2];
						$time = $time_arr[3];
						$hour = substr($time,0,2);
						$min = substr($time,2,2);
						$info_str .= "<span>".$year."年".$month."月".$day."日 ".$hour.":".$min."</span><div class=\"clear\"></div>";
						
						$file_arr = explode("/",$file);
						$file_name = $file_arr[count($file_arr)-1];
						$sql = "SELECT 
									table_list,
									(SELECT name FROM backup_table bk,user ut WHERE bk.created_by = ut.id AND bk.file_name='$file_name') AS created_user 
								FROM backup_table 
								WHERE file_name='$file_name'";								
						$get_tl = mysql_query($sql);
						if(mysql_num_rows($get_tl)>0){
							$tl_row = mysql_fetch_array($get_tl);
							$table_list = $tl_row['table_list'];
							$created_user = $tl_row['created_user'];
							if($created_user=="")$created_user = "SYSTEM";
							
							$info_str .= "<b class=\"title\">备份用户</b>";
							$info_str .= "<span>$created_user</span>";
							
							$tb_arr = explode("|",$table_list);
							$tb_list = "<ul class=\"bk_tb_list\">";
							foreach($tb_arr as $tb_name){
								$get_tb_comment = "SELECT TABLE_COMMENT AS comment 
													FROM information_schema.TABLES 
													WHERE table_name='".$tb_name."'";
								$tb_comment = mysql_result(mysql_query($get_tb_comment),0);
								
								$tb_list .= "<li class=\"bk_tb\">".
												"<font class=\"tb_name\">$tb_name</font>".
												"<font class=\"tb_comment\">$tb_comment</font>".
											"</li>";
							}
							$tb_list .= "</ul>";
							
							$delete_bk_str = "";
							if($_SESSION['level']>2)$delete_bk_str = "<a class=\"icon delete\" onclick=\"delete_bk('$file','$file_time');\" title=\"删除\"></a>";
							
							print 	"<li id=\"bk_$file_time\">".
										"<a>".
											"<input type=\"radio\" name=\"tb[]\" id=\"tb_$file_time\" value=\"$file\">".
											"<label for=\"tb_$file_time\">$info_str</label>".
										"</a>".
										$delete_bk_str.
										"<a class=\"show_detail\">详情$tb_list</a>".
									"</li>";
						}
					}
					print "</ul>";
				}
				?>				
			</td>
		</tr>
		<?php if($_SESSION['level']>2 && count($db_arr)>0){ ?>
		<tr>
			<td>
				<button name="btn_change" class="ctrl_btn active backup_db" style="margin:0;" onclick="backup_db('restore');">还原备份</button>
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php
	}
	if($_SESSION['backup_ctrl']=="manual_backup"){
?>
	<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
		<tr>
			<td class="alert_holder">请选择所需备份的数据表放在右边方框内。</td>
		</tr>
		<tr>
			<td>
				<div id="auth_col">
					<span class="title">备份数据库</span>
					<table border="0" cellpadding="0" cellspacing="0" class="tab_panel" style="display:block;">
						<tr>
							<td class="left_col" width="50%" valign="top">
								<span>
									<input type="checkbox" class="check_all" id="all_tb">
									<label for="all_tb">所有数据表</label>
								</span>
								<div class="clear"></div>
								<div class="auth_col_holder">
									<ul>
<?php
while($tb_row = mysql_fetch_row($list_tables)){
	$tb_name = $tb_row[0];
	
	if($tb_name!="backup_table"){
		$get_tb_size = "SELECT 
		ROUND(SUM(DATA_LENGTH/1024)+SUM(INDEX_LENGTH/1024),1) AS size 
		FROM  information_schema.TABLES 
		WHERE table_name='".$tb_name."'";
		$tb_size = mysql_result(mysql_query($get_tb_size),0);
		
		$get_tb_comment = "SELECT TABLE_COMMENT AS comment 
							FROM information_schema.TABLES 
							WHERE table_name='".$tb_name."'";
		$tb_comment = mysql_result(mysql_query($get_tb_comment),0);
		
		if($tb_size/1024<1)$tb_size .= " KB";
		else $tb_size = round($tb_size/1024,1)." MB";
		
		print 	"<li>
					<label for=\"tb_$tb_name\">
						<input type=\"checkbox\" name=\"$tb_name\" id=\"tb_$tb_name\">
							<font class=\"tb_name\">$tb_name</font>
							<font class=\"tb_comment\">$tb_comment</font>
						<div class=\"right\">[$tb_size]</div>
					</label>
				</li>";
	}
}
?>
									</ul>
								</div>
							</td>
							<td class="mid_col">
								<div class="btn_holder">
									<button class="move_item add"></button>
									<button class="move_item remove"></button>
								</div>
							</td>
							<td class="right_col" width="50%" valign="top">
								<span>
									<input type="checkbox" class="check_all" id="all_bk_tb">
									<label for="all_bk_tb">需备份数据表</label>
								</span>
								<div class="clear"></div>
								<div class="auth_col_holder">
									<ul></ul>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<button name="btn_save" class="ctrl_btn active backup_db" onclick="backup_db('manual');">备份数据表</button>
			</td>
		</tr>
	</table>	
	<?php
	}
}
?>

<script>
$(function() {
	set_move_item();
	
	$('#restore_list li input[type=radio]').change(function(){
		if($(this).is(':checked')){
			$('#restore_list li input[type=radio]').parent('li').removeClass('curr');
			$(this).parent('li').addClass('curr');
		}
	});
});

function delete_bk(file,id){
	if(confirm('确定删除此备份文件?')){
		show_alert('正在删除备份文件，请稍候 ...','load');
		
		var params = 'action=delete&file='+file;		
		$.ajax({
			type: 'post',
			url: 'backup_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功删除备份文件！');
					$('#bk_'+id).remove();
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function backup_db(e){	
	if(e!='restore'){
		if($('#auth_col .right_col .auth_col_holder ul li').length==0){
			show_alert('请先选择需要备份的数据表！');
			return false;
		}
		else{
			show_alert('正在备份数据库，请稍候 ...','load');
			
			var params = '';
			$('#auth_col .right_col .auth_col_holder ul li input[type=checkbox]').each(function(){
				if(params!='')params += '&';
				params += 'tb[]='+$(this).attr('name');
			});		
			params += '&action='+e;
			
			$.ajax({
				type: 'post',
				url: 'backup_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功备份数据表！');
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	else{
		if($('#restore_list li input[type=radio]:checked').length==0){
			show_alert('请先选择需要还原的备份！');
			return false;
		}
		else{
			show_alert('正在还原数据库，请稍候 ...','load');
			
			var params = 'file='+$('#restore_list li input[type=radio]:checked').val()+'&action='+e;
			
			$.ajax({
				type: 'post',
				url: 'backup_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功还原数据表！');
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
}
</script>
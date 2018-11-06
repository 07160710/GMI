<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

session_start();
$media_id = "";
$parent_id = "";

$title_str = "";
if($_REQUEST['m_id']!=""){
	setcookie("media_id",mysql_escape_string($_REQUEST['m_id']));
	$media_id = mysql_escape_string($_REQUEST['m_id']);
	
	$title_str = "编辑";
}
if($_REQUEST['parent_id']!=""){
	$parent_id = mysql_escape_string($_REQUEST['parent_id']);
	
	$sql = "SELECT name FROM media_table WHERE id='$parent_id'";
	$p_name = mysql_result(mysql_query($sql),0);
	$p_name = ($p_name!="")?$p_name:"附件根目录";
	
	$title_str = "在[".$p_name."]下创建";
}

$select_field_str = "";
foreach($GLOBALS['media_fields'] as $val){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $val;
}

$sql = "SELECT 
			$select_field_str,
			created_time,
			uploaded_time,
			(SELECT ut.name FROM media_table mt,user ut WHERE mt.created_by=ut.id AND mt.id='".$media_id."') AS created_user,
			(SELECT ut.name FROM media_table mt,user ut WHERE mt.uploaded_by=ut.id AND mt.id='".$media_id."') AS uploaded_user 
		FROM media_table 
		WHERE id='".$media_id."'";	
$get_media = mysql_query($sql);
if(mysql_num_rows($get_media)>0){
	$m_row = mysql_fetch_array($get_media);
	
	$parent_id = $m_row['parent_id'];	
	$m_name = $m_row['name'];
	$m_alias = $m_row['alias'];
	$m_type = $m_row['type'];
	
	$title_str .= $GLOBALS['media_type'][$m_type]."[".$m_name."]";
	
	$m_created_user = $m_row['created_user'];
	$m_uploaded_user = $m_row['uploaded_user'];
	
	$m_created_time = format_time($m_row['created_time']);
	$m_uploaded_time = format_time($m_row['uploaded_time']);
}
?>

<form action="media" method="post" name="media_form" id="media_form" onsubmit="return false;" enctype="multipart/form-data">

<input type="hidden" name="media_id" id="media_id" value="<?php print $media_id;?>">
<input type="hidden" name="parent_id" id="parent_id" value="<?php print $parent_id;?>">
<input type="hidden" name="action" id="action" value="<?php print ($media_id!="")?"edit":"add";?>">

<table border="0" cellpadding="0" cellspacing="0" style="width:100%;height:100%;">
<tr height="46px"><td>

<table class="ctrl_header top" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"><?php if($title_str!="")print $title_str;?></td>
		<td class="ctrl_holder">
			<?php if($_SESSION['auth_media']==2){ ?>
			<div class="btn_holder">
				<button class="ctrl_btn active save" onclick="save_page_info();">
					保存
				</button>
				<button name="reset" type="reset"  class="ctrl_btn">
					重置
				</button>
			</div>
			<?php } ?>	
			</div>
		</td>
	</tr>
	<tr>
		<td class="tab_list" colspan="2">
			<div id="tab_container">
				<ul class="tab_holder">
					<?php if($media_id!=""){ ?>
					<li>
						<a id="tab_1" class="tab">列表</a>
					</li>
					<?php } ?>
					<li>
						<a id="tab_2" class="tab">属性</a>
					</li>
				</ul>	
			</div>
			<div id="tab_line"></div>
		</td>
	</tr>
</table>

<table class="ctrl_content" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>
		
<?php if($media_id!=""){ ?>
<!--TAB PANEL 1-->
<table id="tab_panel_1" width="100%" class="tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<table id="file_ctrl_holder" class="data_ctrl_holder" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td height="42">
						<table class="data_holder" style="padding:0;" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="ctrl_holder" style="padding:0 5px;">
<div class="ctrl_link">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span>
					操作
				</span>
			</td>
			<td>
				<div class="menu_holder">
					<a class="ctrl_arrow"></a>
					<div class="ctrl_menu">
						<a class="item upload" onclick="display_upload('<?php print $media_id;?>');">
							上传
						</a>
						<a class="item sort" onclick="multi_edit_file('sort');">
							排序
						</a>
						<a class="item publish" onclick="multi_edit_file('publish');">
							发布
						</a>
						<a class="item delete" onclick="multi_edit_file('delete');">
							删除
						</a>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
								</td>
							</tr>						
						</table>						
					</td>
				</tr>
				<tr>
					<td valign="top">
						<div id="file_list_holder">
							<ul class="th_row">
								<li style="width:30px;">
									<input type="checkbox" class="check_all" id="curr_page_order">
								</li>
								<?php
								foreach($file_fields as $key=>$val){
									$width_str = "";
									if(strpos($val,"#")!==false){
										$val_arr = explode("#",$val);
										$width_str = "style=\"width:".$val_arr[1]."px;\"";
										$title = $val_arr[0];										
									}
									else{
										$title = $val;
									}
									
									print "<li $width_str><a class=\"sort_$key\">".$title."<i></i></a></li>";
								}
								?>
								<li style="width:30px;">
									<a class="btn_display" title="发布/隐藏"></a>
								</li>
								<li style="width:30px;">
									<a class="btn_lock" title="权限"></a>
								</li>
								<li style="width:30px;">
									<a class="btn_download" title="下载"></a>
								</li>
							</ul>
							<div id="file_list"></div>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php } ?>

<!--TAB PANEL 2-->
<table id="tab_panel_2" width="100%" class="tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">		
			<table class="data_holder" border="0" cellpadding="0" cellspacing="10px">
				<tr>
					<td class="title">
						文件夹名称
					</td>
					<td>
						<input name="m_name" type="text" id="m_title" value="<?php print $m_name;?>">
					</td>
				</tr>
				<tr>
					<td class="title">
						文件夹别名
					</td>
					<td>
						<input name="m_alias" type="text" id="m_alias" value="<?php print $m_alias;?>">
						<?php
						if($m_alias!=""){
							print "<label style=\"display:inline-block;float:none;\">";
							if($m_type=="file"){
								if($m_file_url==""){
									print "(请先上传文档)";
								}
							}
							else if($m_type=="folder"){
								print _UPLOAD_FOLDER_."media/".fetch_route($media_id,'media_table');
							}
							print "</label>";
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="title">
						附件类型
					</td>
					<td>
<?php
if($m_type!=""){
	print "<input type=\"hidden\" name=\"m_type\" value=\"$m_type\">";
	foreach($GLOBALS['media_type'] as $key=>$val){
		if($m_type==$key)print "<label id=\"m_type\">$val</label>";
	}
}
else{
	print "<select name=\"m_type\" id=\"m_type\">";
	foreach($GLOBALS['media_type'] as $key=>$val){
		print "<option value=\"$key\">$val</option>";
	}
	print "</select>";
}
?>
					</td>
				</tr>
			</table>
		</td>		
<?php
$info_print = <<<INFO
<td class="info_holder" width="130px" valign="top">
	<h1>附件信息</h1>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<b>创建时间</b><br/>$m_created_time
			</td>
		</tr>
		<tr>
			<td>
				<b>创建用户</b><br/>$m_created_user
			</td>
		</tr>
		$uploaded_print	
	</table>
</td>
INFO;

if($media_id!="")print $info_print;
?>
	</tr>
</table>

		</td>
	</tr>
</table>

<table class="ctrl_header bottom" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"></td>
		<td class="ctrl_holder">
		<?php if($_SESSION['auth_media']==2){ ?>
			<div class="btn_holder">
				<button class="ctrl_btn active save" onclick="save_page_info();">
					保存
				</button>
				<button name="reset" type="reset"  class="ctrl_btn">
					重置
				</button>
			</div>
		<?php } ?>
		</td>
	</tr>
</table>

</td></tr>
</table>

<script>
var targetID = '<?php print $media_id;?>';
var node_type_arr = <?php print json_encode($media_type_arr);?>;
var auth_media = '<?php print $_SESSION['auth_media'];?>';
var file_sort_arr = new Array;

$(function() {
	if(auth_media!=2)$('.tab_panel').append('<div class="not_editable"></div>');
	
	var first_tab = $('.tab:visible:eq(0)').attr('id').replace('tab_','');
	$('#tab_'+first_tab).addClass('curr');
	$('#tab_panel_'+first_tab).show();
	curr_tab_panel = '#tab_panel_'+first_tab;
	
	set_public_attr();
	get_file_list();
});

function set_assign_item(){
	$('.assign_panel .check_all').change(function(){
		if($(this).is(':checked')){
			$(this).closest('td').find('ul input[type=checkbox]').prop('checked',true);
			$(this).closest('td').find('ul li').addClass('selected');
		}
		else{
			$(this).closest('td').find('ul input[type=checkbox]').prop('checked',false);
			$(this).closest('td').find('ul li').removeClass('selected');
		}
	});
	
	$('.assign_panel input[type=checkbox]').change(function(){
		var parent = $(this).closest('td');
		if($(this).is(':checked')){
			$(this).parents('li').addClass('selected');
		}
		else{
			$(this).parents('li').removeClass('selected');
			if(parent.find('.check_all').is(':checked'))parent.find('.check_all').prop('checked',false);
		}
	});
	
	$('.assign_panel .move_item').unbind('click').click(function(){
		var move_action = $(this).attr('class').replace('move_item ',''),
			parent_table = $(this).closest('table'),
			item_haystack = '';
		
		if(move_action=='add'){
			parent_table.find('.left_col ul input[type=checkbox]:checked').each(function(){
				$(this).parents('li').removeClass('selected');
				item_haystack += $(this).parents('li')[0].outerHTML;
				$(this).parents('li').remove();
			});
			
			parent_table.find('.right_col ul').append(item_haystack);
			set_assign_item();
			parent_table.find('.left_col input[type=checkbox]').prop('checked',false);
		}
		else{
			parent_table.find('.right_col ul input[type=checkbox]:checked').each(function(){
				$(this).parents('li').removeClass('selected');
				item_haystack += $(this).parents('li')[0].outerHTML;
				$(this).parents('li').remove();
			});
			
			parent_table.find('.left_col ul').append(item_haystack);
			set_assign_item();
			parent_table.find('.right_col input[type=checkbox]').prop('checked',false);
		}
	});
}

function fetch_auth(e){
	var params = 'media_id='+e;
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_auth.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('#wau_list').html('');
			$(xml).find('wau').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#wau_list').append(
					'<li id="user_'+id+'">' + 
						'<label for="wu_'+id+'">' + 
							'<input type="checkbox" name="user[]" id="wu_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			$('#wau_filter').keyup(function(){
				var keyword = $(this).val();
				if(keyword!=''){
					$('#wau_list li').each(function(){
						var val = $(this).children('label').text();
						if(val.indexOf(keyword)>=0){
							$(this).show();
						}
						else{
							$(this).hide();
						}
					});
				}
				else{
					$('#wau_list li').show();
				}
			});
			
			$('#wsu_list').html('');
			$(xml).find('wsu').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#wsu_list').append(
					'<li id="user_'+id+'">' + 
						'<label for="wu_'+id+'">' + 
							'<input type="checkbox" name="user[]" id="wu_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			
			$('#wad_list').html('');
			$(xml).find('wad').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#wad_list').append(
					'<li id="dept_'+id+'">' + 
						'<label for="wd_'+id+'">' + 
							'<input type="checkbox" name="dept[]" id="wd_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			
			$('#wsd_list').html('');
			$(xml).find('wsd').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#wsd_list').append(
					'<li id="dept_'+id+'">' + 
						'<label for="wd_'+id+'">' + 
							'<input type="checkbox" name="dept[]" id="wd_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			
			$('#bau_list').html('');
			$(xml).find('bau').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#bau_list').append(
					'<li id="user_'+id+'">' + 
						'<label for="bu_'+id+'">' + 
							'<input type="checkbox" name="user[]" id="bu_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			$('#bau_filter').keyup(function(){
				var keyword = $(this).val();
				if(keyword!=''){
					$('#bau_list li').each(function(){
						var val = $(this).children('label').text();
						if(val.indexOf(keyword)>=0){
							$(this).show();
						}
						else{
							$(this).hide();
						}
					});
				}
				else{
					$('#bau_list li').show();
				}
			});
			
			$('#bsu_list').html('');
			$(xml).find('bsu').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#bsu_list').append(
					'<li id="user_'+id+'">' + 
						'<label for="bu_'+id+'">' + 
							'<input type="checkbox" name="user[]" id="bu_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			
			$('#bad_list').html('');
			$(xml).find('bad').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#bad_list').append(
					'<li id="dept_'+id+'">' + 
						'<label for="bd_'+id+'">' + 
							'<input type="checkbox" name="dept[]" id="bd_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			
			$('#bsd_list').html('');
			$(xml).find('bsd').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#bsd_list').append(
					'<li id="dept_'+id+'">' + 
						'<label for="bd_'+id+'">' + 
							'<input type="checkbox" name="dept[]" id="bd_'+id+'">' + 
							name + 
						'</label>' + 
					'</li>'
				);
			});
			
			set_assign_item();
		}
	});
}

function auth_manage(e, id){
	if(e=='close'){
		$('#auth_manage_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var params = 'action=save_auth&media_id='+ctrl_id;
		$('#wsu_list li').each(function(){
			var id = $(this).attr('id').replace('user_','');
			params += '&wu[]='+id;
		});
		$('#wsd_list li').each(function(){
			var id = $(this).attr('id').replace('dept_','');
			params += '&wd[]='+id;
		});
		$('#bsu_list li').each(function(){
			var id = $(this).attr('id').replace('user_','');
			params += '&bu[]='+id;
		});
		$('#bsd_list li').each(function(){
			var id = $(this).attr('id').replace('dept_','');
			params += '&bd[]='+id;
		});
		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功保存对象权限！');
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
	else{
		ctrl_id = id;
		
		if(e=='folder'){
			$('.auth_obj').text('['+$('#m_'+id+' .node_link').attr('title')+']');
		}
		else{
			$('.auth_obj').text('['+$('#file_'+id+' li.name strong').text()+']');
		}
		
		$('.assign_holder').each(function(){
			$(this).find('.subtab').removeClass('curr');
			$(this).find('.subtab_panel').hide();
			
			$(this).find('.subtab:first').addClass('curr');
			$(this).find('.subtab_panel:first').show();
		});
		fetch_auth(id);
		
		$('#auth_manage_holder').show().setOverlay();
		
		$('.assign_holder .tab_holder .subtab').click(function(){
			var parent = $(this).parents('.assign_holder');
			parent.find('.subtab').removeClass('curr');
			$(this).addClass('curr');
			
			var tab_id = $(this).attr('id').replace('subtab_','');
			parent.find('.subtab_panel').hide();
			$('#subtab_panel_'+tab_id).show();
		});
	}
}

function set_sort(){
	$('#file_list_holder .th_row a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#file_list_holder .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#file_list_holder .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,file_sort_arr)==-1)file_sort_arr.push(sort_id);
			}
			else if($('#file_list_holder .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#file_list_holder .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,file_sort_arr);
			}
			else{//switch to asc
				$('#file_list_holder .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,file_sort_arr)==-1)file_sort_arr.push(sort_id);
			}
			
			get_file_list();
		}
	});
}

function get_sort_params(params,sort_arr){
	if(sort_arr.length>0){
		for(var s=0;s<sort_arr.length;s++){
			var tmp_sort = sort_arr[s].replace('sort_','');
			
			if($('.'+sort_arr[s]+':visible').children('i').hasClass('asc')){
				if(params!='')params += '&';
				params += 'sort[]='+tmp_sort+'|asc';
			}
			if($('.'+sort_arr[s]+':visible').children('i').hasClass('desc')){
				if(params!='')params += '&';
				params += 'sort[]='+tmp_sort+'|desc';
			}
		}
	}
	return params;
}

function set_input(){
	$('.edit_holder strong').unbind('click').bind('click', function(){
		var strong = $(this),
			tmp_val = strong.text(),
			type = strong.attr('class').replace('design_input ','');
		
		strong.unbind('click');
		if(type=='remark'){
			strong.html('<textarea class="design_input">'+tmp_val+'</textarea>');			
		}
		else{
			strong.html('<input type="text" class="design_input" value="'+tmp_val+'">');
		}
		
		var child = strong.children('.design_input');
		child.focus().blur(function(){
			var parent = strong.parents('.td_row'),
				file_id = parent.attr('id').replace('file_',''),
				file_name = $(this).val();
				
			$('#file_list').append('<div class="loading cover"></div>');
			var params = 'action=update_file_name&id='+file_id+'&name='+encodeURIComponent(file_name);
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						get_file_list();
					}
					else{
						show_alert(result.error);
					}
					$('.loading.cover').remove();
				}
			});
		});
	});
}

function publish_file(e,type){
	var params = 'action=publish_file&id='+e+'&type='+type;
	$.ajax({
		data: 	params,
		type:	'post',
		url: 	post_url,
		dataType: 'json',
		success: function(result){
			if(result.success==1){
				get_file_list();
			}
			else{
				show_alert(result.error);
			}
		}
	});
}

function get_file_list(){
	$('#file_list').append('<div class="loading cover"></div>');
	
	var params = 'parent_id='+targetID;
	params = get_sort_params(params,file_sort_arr);	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_media.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {		
			$('#file_list .td_row').remove();
			
			var i = 0;
			$(xml).find('file').each(function(){
				var file_id = $(this).find('id').text();
				var file_url = $(this).find('file_url').text();
				<?php
				foreach($file_fields as $key=>$val){
					print "var $key = $(this).find(\"$key\").text();";					
				}
				?>
				var publish = $(this).find('publish').text();
				
				$('#file_list').append(
					'<ul id="file_'+file_id+'" class="td_row">' + 
						'<li style="width:30px;"><input type="checkbox" name="file_'+file_id+'"  id="chk_file_'+file_id+'"></li>' + 
						<?php						
						foreach($file_fields as $key=>$val){
							$width_str = "";
							if(strpos($val,"#")!==false){
								$val_arr = explode("#",$val);
								$width = $val_arr[1];
								$width_str = "width:".$width."px;";
							}
							$align_str = "";
							if($key=="name"){
								$align_str = "text-align:left;";
								print "\"<li class='$key \"+ext+\" edit_holder' style='".$width_str.$align_str."'><label for='chk_file_\"+file_id+\"'></label><strong class='text'>\"+$key+\"</strong></li>\" + ";
							}
							else{
								print "\"<li class='$key \"+ext+\"' style='".$width_str.$align_str."'><label for='chk_file_\"+file_id+\"'>\"+$key+\"</label></li>\" + ";
							}							
						}
						?>
						'<li style="width:30px;">' + 
							'<a class="btn_display '+((publish==0)?'hide':'')+'" onclick="publish_file(\''+file_id+'\',\''+((publish==0)?1:0)+'\');" target="_blank" title="'+((publish==1)?'已发布':'未发布')+'"></a>' + 
						'</li>' +
						'<li style="width:30px;">' + 
							'<a class="btn_lock" onclick="auth_manage(\'file\',\''+file_id+'\');" target="_blank" title="权限"></a>' + 
						'</li>' + 
						'<li style="width:30px;">' + 
							'<a class="btn_download" href="<?php print _ROOT_URL_;?>download_file.php?file_name='+name+'&file_url='+file_url+'&file_ext='+ext+'" target="_blank" title="下载"></a>' + 
						'</li>' + 
					'</ul>'
				);
				i++;
			});
			
			set_sort();
			set_input();
			set_check_file();
			$('#file_list .loading').fadeOut();
		}
	});
}

function multi_edit_file(e){
	if(e=='sort'){
		sort_node(targetID,'file');
	}
	if(e=='delete'){
		if($('#file_list .td_row input[type=checkbox]:checked').length==0){
			alert('请选择文件进行操作！');
			return false;
		}
		
		if(confirm('确定删除所选文件？')){
			show_alert('正在删除所选文件，请稍候 ...','load');
			
			var params = 'action=delete_file';
			$('#file_list .td_row input[type=checkbox]:checked').each(function(){
				var file_id = $(this).attr('name').replace('file_','');
				if(params!='')params += '&';
				params += 'file_id[]='+file_id;
			});
			
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						show_alert('成功删除所选文件！');
						get_file_list();
						$('.check_all').prop('checked',false);
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	if(e=='publish'){
		if($('#file_list .td_row input[type=checkbox]:checked').length==0){
			alert('请选择文件进行操作！');
			return false;
		}
		
		if(confirm('确定发布所选文件？')){
			show_alert('正在发布所选文件，请稍候 ...','load');
			
			var params = 'action=publish_files';
			$('#file_list .td_row input[type=checkbox]:checked').each(function(){
				var file_id = $(this).attr('name').replace('file_','');
				if(params!='')params += '&';
				params += 'file_id[]='+file_id;
			});
			
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						show_alert('成功发布所选文件！');
						get_file_list();
						$('.check_all').prop('checked',false);
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
}

function set_check_file(){
	$('#file_list_holder .check_all').change(function(){
		if($(this).is(':checked')){
			$('#file_list .td_row input[type=checkbox]').prop('checked',true);
			$('#file_list .td_row').addClass('selected');
		}
		else{
			$('#file_list .td_row input[type=checkbox]').prop('checked',false);
			$('#file_list .td_row').removeClass('selected');
		}
	});
	
	$('#file_list_holder .td_row input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('.td_row').addClass('selected');
		}
		else{
			$('.check_all').prop('checked',false);
			$(this).closest('.td_row').removeClass('selected');
		}
	});
}

function display_upload(e){
	if(e=='close'){
		$('#upload_holder').hide().setOverlay();
		$('#fileList').html('');
	}
	else{
		ctrl_id = e;console.log(e);
		set_upload(e);
		$('#upload_holder').show().setOverlay();
	}
}

function check_form(){
	if($('input[name=m_name]').val()==''){
		show_alert('请输入名称！');
		return false;
	}
	
	if($('input[name=m_alias]').val()==''){
		show_alert('请输入别名！');
		return false;
	}
	else{		
		//check duplicate alias
		var params = {
			m_id:		$('#media_id').val(),
			parent_id:	$('#parent_id').val(),
			alias:		$('input[name=m_alias]').val()
		};
		
		var flag = true;
		$.ajax({
			type: 'post',
			url: 'check_alias.php',
			dataType: 'json',
			data: params,
			async: false,
			success: function(result){
				if(result.success==0){
					$('.alert_holder').html('别名重复，请重新输入！');
					flag = false;
				}
			}
		});
		
		if(!flag)return false;
	}
	
	return true;
}

function save_page_info(){
	if(check_form()){
		show_alert('正在保存附件对象，请稍候 ...','load');
		
		var params = $('#media_form').serialize();
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					document.cookie = 'media_id='+result.id;
					show_alert('保存附件对象成功！','reload');
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}
</script>
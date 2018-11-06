<?php
include("../../public/header.php");
?>
<table class="ctrl_header top" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"></td>
		<td class="ctrl_holder"></td>
	</tr>
	<tr>
		<td class="tab_list" style="padding:0;" colspan="2">
			<div id="tab_container">
				<ul class="tab_holder">
					<li>
						<a id="tab_1" class="tab curr">
							概览
						</a>
					</li>
				</ul>
			</div>
			<div id="tab_line"></div>
		</td>
	</tr>
</table>
	
<table id="content_holder" class="ctrl_content" style="margin:50px 0 0 0;" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>
			<?php include('nbase_control_tab_1.php');?>
		</td>
	</tr>
</table>

<script>
var act_tab = '<?php print $_COOKIE['nbase_tab'];?>';
var ctrl_id = '';
	ctrl_action = '';	
var post_url = 'nbase_manage.php';
var curr_tab_panel = '';
var nbase_sort_arr = new Array;
var curr_page = 1,
	page_span = 5;

$(function() {	
	if(act_tab==''){
		document.cookie = 'nbase_tab=1';
		$('#tab_panel_1').show();
		curr_tab_panel = '#tab_panel_1';
	}
	else{
		$('.tab').removeClass('curr');
		$('#tab_'+act_tab).addClass('curr');
		$('#tab_panel_'+act_tab).show();
		curr_tab_panel = '#tab_panel_'+act_tab;
	}
});
</script>
<script>
var node_arr = new Array();
if(id_prefix==undefined)var id_prefix = '';
if(post_id==undefined)var post_id = '';
var curr_id = '',
	ctrl_action = '',
	ctrl_id = '',
	ctrl_curr_id = '',
	ctrl_panel_str = '#left_col_holder',
	ctrl_str = '',
	post_last = (id_prefix=='n_')?0:false;

function select_node(n_id){
	if(ctrl_action=='move'){
		if(n_id==ctrl_id){//cannot move to self
			alert('不能移动元素到自身元素之下！');
			return false;
		}
		if($(ctrl_panel_str+' #'+id_prefix+n_id).parents('li[id='+id_prefix+ctrl_id+']').length>0){//cannot move to child
			alert('不能移动元素到其子元素之下！');
			return false;
		}		
		if($('#'+id_prefix+n_id+' ul.branch:eq(0) > li[id='+id_prefix+ctrl_id+']').length>0){//cannot move to same parent
			alert('此元素已在其父元素之下！');
			return false;
		}
	}
	
	ctrl_curr_id = n_id;
	set_curr(ctrl_curr_id);
	
	var str_ctrl_child = '';
	if($(ctrl_panel_str+' #'+id_prefix+ctrl_id+' .node_holder:eq(0) .toogle').length>0){
		str_ctrl_child = '及其子元素';
	}
	var prefix = (id_prefix=='u_')?'g_':id_prefix;
	
	$('#'+ctrl_action+'_holder .alert_holder').html('元素['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']'+str_ctrl_child+'将会'+ctrl_str+'到父元素['+$(ctrl_panel_str+' #'+prefix+ctrl_curr_id+' .node_link:eq(0)').text()+']之下。');
}

function expand_node(e){
	post_id = e;
	curr_id = e;
	$.ajax({
		type:		'GET',
		url: 		'fetch_parents.php?'+id_prefix+'id='+e,
		dataType:	'xml',
		success: function (xml) {
			var n_id = '';		
			$(xml).find('node').each(function(){
				if(n_id!='')n_id += '|';
				n_id += $(this).find('parent_id').text();
			});
			
			toogle_node(n_id);
		}
	});
}

function toogle_node(n_id){
	var prefix = (id_prefix=='u_')?'g_':id_prefix;
	if(n_id.indexOf('|')<0){//single
		if($(ctrl_panel_str+' #'+prefix+n_id+' .toogle').hasClass('expand')){
			$(ctrl_panel_str+' #'+prefix+n_id+' .toogle').removeClass('expand').addClass('shrink');
			$(ctrl_panel_str+' #'+prefix+n_id+' .branch').html('');
		}
		else{
			node_arr.push(n_id);
		}	
	}
	else{//multiple
		var n_id_arr = n_id.split('|');
		for(var i=0;i<n_id_arr.length;i++){
			node_arr.push(n_id_arr[i]);
		}
	}
	fetch_children(0);
}

var auth_add = '<?php print $_SESSION['auth_add'];?>',
	auth_publish = '<?php print $_SESSION['auth_publish'];?>',
	auth_move = '<?php print $_SESSION['auth_move'];?>',
	auth_sort = '<?php print $_SESSION['auth_sort'];?>',
	auth_delete = '<?php print $_SESSION['auth_delete'];?>';	
function fetch_children(i){
	var index = i,
		parent_id = node_arr[index],
		set_curr_id = 0;
		
	var prefix = (id_prefix=='u_')?'g_':id_prefix;
	if((ctrl_panel_str.indexOf('#left_col_holder')>=0 && parent_id>0) || ctrl_action!=''){
		if($(ctrl_panel_str+' #'+prefix+parent_id+' .toogle:eq(0)').hasClass('shrink')){
			$(ctrl_panel_str).append('<div class="loading_overlay"></div>');
		}
		$(ctrl_panel_str+' #'+prefix+parent_id+' .toogle:eq(0)').removeClass('shrink').addClass('expand');
		$(ctrl_panel_str+' #'+prefix+parent_id+' .branch:eq(0)').append('<div class="loading"><i></i></div>');
	}
	
	var tb_name = 'content_table';
	switch(id_prefix){
		case 'm_':tb_name = 'media_table';break;
		case 'g_':tb_name = 'user_group';break;
		case 'u_':tb_name = 'user_group';break;
	}
	
	if(parent_id!=undefined)
	$.ajax({
		type:		'GET',
		url: 		'fetch_children.php?tb='+tb_name+'&parent_id='+parent_id,
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$(ctrl_panel_str+' #'+prefix+parent_id+' .branch .loading').remove();
			$('.loading_overlay').remove();
			
			$(xml).find('node').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text(),
					type = $(this).find('type').text(),
					level = $(this).find('level').text(),
					publish = $(this).find('publish').text(),
					has_child = $(this).find('has_child').text();
				
				if(id==post_id)post_last = (id_prefix=='n_')?(post_last+1):true;
				
				var publish_class = (publish!=1 && page_name!='media')?'hide':'';
				
				var toogle_str = '',
					sort_str = '',
					branch_str = '';
				if(has_child==1){
					toogle_str = '<a class="toogle shrink" onclick="toogle_node(\''+id+'\');"></a>';
					sort_str = '<a class="item sort" onclick="sort_node(\''+id+'\');">排序</a>';
					branch_str = '<ul class="branch l_'+(parseInt(level)+1)+'"></ul>';
				}
				
				if(ctrl_panel_str.indexOf('#left_col_holder')>=0){
					if(parent_id>0){
						var ctrl_menu_item = '';
						
						ctrl_menu_item += '<a class="item add" onclick="add_node(\''+id+'\');">添加</a>';
						
						if(page_name=='media'){
							ctrl_menu_item += '<a class="item upload" onclick="display_upload(\''+id+'\');">上传</a>';
						}
						
						ctrl_menu_item += '<a class="item move" onclick="move_node(\''+id+'\');">移动</a>';
						ctrl_menu_item += sort_str;
						
						if(page_name=='content'){
							ctrl_menu_item += '<a class="item publish" onclick="publish_node(\''+id+'\');">发布</a>';
						}
						if(page_name=='media'){
							ctrl_menu_item += '<a class="item auth" onclick="auth_manage(\'folder\',\''+id+'\');">权限</a>';
						}
						
						ctrl_menu_item += '<a class="item delete" onclick="delete_node('+id+');">删除</a>';
						
						var ctrl_menu = '';
						if(ctrl_menu_item!=''){
							ctrl_menu = '<div class="ctrl_menu">' + 
											ctrl_menu_item + 
										'</div>';
						}						
						
						$(ctrl_panel_str+' #'+id_prefix+parent_id+' .l_'+level).append(
							'<li id="'+id_prefix+id+'">' + 
								toogle_str + 
								'<div class="node_holder '+publish_class+'">' + 
									'<span class="node">' + 
										'<a class="node_link '+type+'" title="'+name+'" onclick="edit_node(\''+id+'\');">'+name+'</a>' + 
										'<div class="menu_holder">' + 
											ctrl_menu + 
										'</div>' + 
									'</span>' + 
								'</div>' + 
								branch_str + 
							'</li>'
						);
					}
					set_curr_id = curr_id;
				}
				else{//if ctrl holder shows
					$(ctrl_panel_str+' #'+prefix+parent_id+' .branch:eq(0)').append(
						'<li id="'+prefix+id+'">' + 
							toogle_str + 
							'<div class="node_holder">' + 								
								'<a class="node_link" title="'+name+'" onclick="select_node(\''+id+'\');">'+name+'</a>' + 
							'</div>' + 
							branch_str + 
						'</li>'
					);
					set_curr_id = ctrl_curr_id;
				}
			});
			
			if(index+1<node_arr.length){
				fetch_children(index+1);
			}
			else{
				set_public_attr();
				node_arr.length = 0;
			}
			
			if(ctrl_panel_str.indexOf('#left_col_holder')>=0){
				if(	id_prefix=='n_' && post_last==2 || 
					id_prefix!='n_' && post_last==true
				){
					edit_node(post_id);
					post_last = (id_prefix=='n_')?0:false;
				}
			}
			set_curr(set_curr_id);
		}
	});
}
</script>

<!--IMPORT HOLDER-->
<div id="import_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					导入对象
				</span>
				<a class="btn_close" onclick="import_close();"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder"></td>
		</tr>		
		<tr>
			<td id="import_panel">
				<form name="upload_form" id="upload_form" enctype="multipart/form-data">
					<input type="file" name="file_excel" id="file_excel">
				</form>
			</td>
		</tr>
	</table>
</div>
<script>
function import_close(){
	$('#import_holder').hide().setOverlay();
}
</script>

<!--COPY HOLDER-->
<div id="copy_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					复制
				</span>				
				<a class="btn_close" onclick="ctrl_node('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder"></td>
		</tr>
		<tr>
			<td id="copy_panel">
				<div class="scroll_holder">
					<ul class="node_tree">
						<?php if($_SESSION['level']>=2){ ?>
						<li id="n_0" class="home">
							<div class="node_holder">
								<a class="toogle expand" onclick="toogle_node('0');"></a>
								<a class="node_link" onclick="select_node('0');" title="首页">首页</a>
							</div>
						<?php } ?>
<?php
$print_col = "";
$level = 1;

$get_col_query = "SELECT id,name FROM content_table WHERE parent_id = '0' AND level = '$level' $col_cond_str ORDER BY sort_order";
$get_col = mysql_query($get_col_query);

if(mysql_num_rows($get_col)>0){
	$print_col .= "<ul class=\"branch l_$level\">";
	
	while($c_row = mysql_fetch_array($get_col)){
		$n_id = $c_row['id'];
		$n_name = $c_row['name'];
		
		$toogle_str = "";
		$sort_str = "";
		$child_str = "";
		if(has_child($n_id)){
			$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$n_id');\"></a>";
			$sort_str = "<a class=\"item\" onclick=\"sort_node('$n_id');\"><i class=\"sort\"></i>排序</a>";
			$child_str = "<ul class=\"branch l_".($level+1)."\"></ul>";
		}
		
$print_col .= <<<CONTENT
<li id="n_$n_id">
	$toogle_str
	<div class="node_holder">
		<a class="node_link" title="$n_name" onclick="select_node('$n_id');">$n_name</a>
	</div>
	$child_str
</li>
CONTENT;
	}
	
	$print_col .= "</ul>";
}
print $print_col;
?>							
						<?php if($_SESSION['level']>=2){ ?>						
						</li>
						<?php } ?>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="ctrl_node('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<!--MOVE HOLDER-->
<div id="move_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					移动
				</span>				
				<a class="btn_close" onclick="ctrl_node('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder"></td>
		</tr>
		<tr>
			<td id="move_panel">
				<div class="scroll_holder">
<?php
$print_col = "";
$level = 1;
if($page_name=="account"){
	$sql = "SELECT id,name FROM user_group WHERE parent_id='0'";
	$get_group = mysql_query($sql);
	if(mysql_num_rows($get_group)>0){
		$print_col .= "<ul class=\"node_tree\">";
		while($row = mysql_fetch_array($get_group)){
			$id = $row[0];
			$name = $row[1];
			
			$toogle_str = "";
			$child_str = "";
			if(has_child($id,'user_group')){
				$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$id');\"></a>";
				$child_str = "<ul class=\"branch l_".$level."\"></ul>";
			}
		
$print_col .= <<<CONTENT
<li id="g_$id">
	$toogle_str
	<div class="node_holder">
		<a class="node_link" title="$name" onclick="select_node('$id');">$name</a>
	</div>
	$child_str
</li>
CONTENT;
		}
		$print_col .= "</ul>";
	}
}
else{
	$sql = "SELECT id,name 
			FROM content_table 
			WHERE parent_id='0' AND 
				level='$level' 
				$col_cond_str 
			ORDER BY sort_order";
	$get_col = mysql_query($sql);
	if(mysql_num_rows($get_col)>0){
		$print_col .= "<ul class=\"node_tree\">";
		
		while($c_row = mysql_fetch_array($get_col)){
			$n_id = $c_row['id'];
			$n_name = $c_row['name'];
			
			$toogle_str = "";
			$child_str = "";
			if(has_child($n_id)){
				$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$n_id');\"></a>";
				$child_str = "<ul class=\"branch l_".$level."\"></ul>";
			}
		
$print_col .= <<<CONTENT
<li id="n_$n_id">
	$toogle_str
	<div class="node_holder">
		<a class="node_link" title="$n_name" onclick="select_node('$n_id');">$n_name</a>
	</div>
	$child_str
</li>
CONTENT;
		}
		
		$print_col .= "</ul>";
	}
}
print $print_col;
?>				
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="ctrl_node('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>
<script>
function copy_node(id){
	ctrl_action = 'copy';
	ctrl_str = '复制';
	ctrl_node(id);
}

function move_node(id){	
	ctrl_action = 'move';
	ctrl_str = '移动';
	ctrl_node(id);
}

var ctrl_panel_html = '';
function ctrl_node(e){
	if(e=='close'){
		ctrl_curr_id = '';
		ctrl_panel_str = '#left_col_holder';
		$('#'+ctrl_action+'_holder').hide().setOverlay();
		$('#'+ctrl_action+'_panel').html(ctrl_panel_html);
	}
	else if(e=='save'){
		if(ctrl_curr_id==''){
			alert('没有父页面被选择！');
			return false;
		}
		
		var object = 'content';
		switch(id_prefix){
			case 'm_':object = 'media';break;
			case 'g_':object = 'group';break;
			case 'u_':object = 'user';break;
		}
		var params = {
			action:	ctrl_action,
			object: object,
			id:	ctrl_id,
			parent_id:ctrl_curr_id
		};
		
		$('#'+ctrl_action+'_holder .alert_holder').html('<div class="loading"><i></i>正在'+ctrl_str+'页面，请稍候 ...</div>');
		
		$.ajax({
			data: 	params,
			type:	'post',
			url: 	post_url,
			dataType: 'json',
			success: function(result){
				if(result.success==1){
					document.cookie = 'node_id='+result.id;
					window.location.reload();
				}
				else{
					$('#'+ctrl_action+'_holder .alert_holder').html(result.error);
				}	
			}
		});
	}
	else{
		if(e.indexOf('_')>0){
			var id_arr = e.split('_');
			id_prefix = id_arr[0]+'_';
			ctrl_id = id_arr[1];
		}
		else{
			ctrl_id = e;
		}
		
		ctrl_panel_str = '#'+ctrl_action+'_panel';
		ctrl_panel_html = $('#'+ctrl_action+'_panel').html();
		
		if(page_name=='account')toogle_node('1');
		
		$('#'+ctrl_action+'_holder .alert_holder').html('请选择元素['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']所'+ctrl_str+'到的父元素。');
		$('#'+ctrl_action+'_holder').show().setOverlay();
	}
}
</script>

<!--SORT HOLDER-->
<div id="sort_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					排序
				</span>				
				<a class="btn_close" onclick="sort_node('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder"></td>
		</tr>
		<tr>
			<td id="sort_panel"><div class="scroll_holder"></div></td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="sort_node('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>
<script>
function sort_node(e,o){
	if($('#sort_holder').is(':visible')){
		if(e=='close'){
			$('#sort_panel div.scroll_holder').html('');
			$('#sort_holder').hide().setOverlay();
		}
		if(e=='save'){//save order
			$('#sort_holder .alert_holder').html('<div class="loading"><i></i>正在保存页面排序，请稍候 ...</div>');
			
			var params = $('#sort_panel div.scroll_holder').sortable('serialize');
			params = 'action=sort&' + params;		
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						document.cookie = 'node_id='+ctrl_id;
						window.location.reload();
					}
					else{
						$('#sort_holder .alert_holder').html(result.error);
					}
				}
			});
		}
	}
	else{
		ctrl_id = e;
		var tb_name = '';
		switch(page_name){
			case 'media': tb_name = 'media_table'; break;
			case 'account': tb_name = 'user_group'; break;
			default: tb_name = 'content_table';
		}
		
		var obj_param = '';
		if(o!=undefined)obj_param = '&obj='+o;
		
		//fetch node
		$.ajax({
			type:		'GET',
			url: 		'fetch_children.php?tb='+tb_name+'&parent_id='+ctrl_id+obj_param,
			dataType:	'xml',
			success: function (xml) {
				var node_str = '';
				$(xml).find('node').each(function(){
                    var id = $(this).find('id').text();
                    var name = $(this).find('name').text();
					
					node_str += '<a id="sort_'+id+'">'+name+'</a>';
                });
				
				$('#sort_panel div.scroll_holder').html(node_str).sortable({
					containment: 'parent',
					axis: 'y',
					placeholder: 'node-placeholder'
				}).disableSelection();
				
				$('#sort_holder').show().setOverlay();
			}
		});
	}
}
</script>

<!--PUBLISH HOLDER-->
<div id="publish_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					发布
				</span>				
				<a class="btn_close" onclick="publish_node('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder"></td>
		</tr>
		<tr>
			<td id="publish_panel">
				<input type="checkbox" name="publish_child" id="publish_child">
				<label for="publish_child">发布子页面</label>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="publish_node('save');">
					发布
				</button>
			</td>
		</tr>
	</table>
</div>
<script>
function publish_node(e){
	if($('#publish_holder').is(':visible')){
		if(e=='close'){
			$('#publish_child').prop('checked',false);
			$('#publish_holder').hide().setOverlay();
		}
		if(e=='save'){
			//save order
			var params = 'action=publish&n_id='+ctrl_id+'&publish_child=' + $('#publish_child').prop('checked');		
			
			var publish_child_str = '';
			if($('#publish_child').prop('checked')==true){
				publish_child_str = '及其所有子页面';
			}
			
			$('#publish_holder .alert_holder').html('<span class="loading"><i></i>正在发布页面'+publish_child_str+'，请稍候 ...</span>');
			
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						document.cookie = 'node_id='+ctrl_id;
						window.location.reload();
					}
					else{
						$('#publish_holder .alert_holder').html(result.error);
					}	
				}
			});
		}
	}
	else{
		ctrl_id = e;
		
		$('#publish_child').change(function(){			
			if($(this).prop('checked')==true){
				$('#publish_holder .alert_holder').html('发布页面['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']及其所有子页面');
			}
			else{
				$('#publish_holder .alert_holder').html('发布页面['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']');
			}
		});
		
		$('#publish_holder .alert_holder').html('发布页面['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']');
		$('#publish_holder').show().setOverlay();
	}
}
</script>

<script>
//process banner
function set_banner_upload(){
	$('input.upload_banner').change(function(){		
		if($(this).val()!='')upload_banner($(this).attr('id'));
	});
}

function upload_banner(b_id){
	show_alert('正在上传横幅，请稍候 ...','load');
	
	var banner_id = b_id.replace('b_image_','');
	
	$.ajaxFileUpload({//upload banner
		url:'img_upload.php?object=banner&b_id='+banner_id,
		secureuri :false,
		fileElementId :b_id,
		dataType : 'json',
		success : function (result){
			if(result.success==1){
				var banner_thumb = result.banner_url,
					banner_full = banner_thumb.replace('<?php print _THUMB_FOLDER_;?>',''),
					delete_ctrl_str = 	'<a class="ctrl_arrow"></a>' + 
										'<div class="ctrl_menu">' + 
											'<input name="b_image_'+banner_id+'" class="btn_file upload_banner" type="file" id="b_image_'+banner_id+'">' + 
											'<a class="item replace">替换</a>' + 
											'<a class="item publish show" onclick="publish_banner(\''+banner_id+'\');">显示</a>' + 
											'<a class="item delete" onclick="delete_banner(\''+banner_id+'\');">删除</a>' + 
										'</div>';
				
				$('#banner_'+banner_id+' #img_ctrl_area').html(
					'<div id="img_ctrl_holder" class="display_area">' + 
						'<img src="<?php print _ROOT_URL_;?>'+banner_thumb+'">' + 
						'<div id="img_ctrl" class="menu_holder">' + 
							delete_ctrl_str + 
						'</div>' + 
					'</div>'
				);
				$('#banner_'+banner_id).addClass('hide');
				set_public_attr();
				set_banner_upload();
				show_alert('横幅图片已成功上传！');
			}
			else{
				show_alert(result.error);
			}
		}
	});
}

function set_banner_sortable(){
	$('#sort_banner').sortable({
		containment: 'parent',
		axis: 'y',
		placeholder: 'banner-placeholder',
		opacity: .5,
		handle: '.handle',
		start: function(){
			$('#sort_banner').height(sort_banner_height);				
		},
		stop: function(event, ui){
			sort_banner();
		}
	}).disableSelection();
	
	$('.banner_info').mouseover(function(){
		$(this).addClass('over');
	}).mouseout(function(){
		$(this).removeClass('over');
	});
}

function sort_banner(){
	show_alert('正在保存横幅排序，请稍候 ...','load');
	
	var params = $('#sort_banner').sortable('serialize');
	params = 'action=sort_banner&b_type='+banner_type+'&'+params;
	
	$.ajax({
		data: 	params,
		type:	'post',
		url: 	'banner_manage.php',
		dataType: 'json',
		success: function(result){
			if(result.success==1){
				$('#alert_panel').hide().setOverlay();
			}
			else{
				show_alert(result.error);
			}	
		}
	});
}

function save_banner(){
	show_alert('正在保存横幅信息，请稍候 ...','load');
	
	var params = (banner_type==0)?$('#default_form').serialize():$('#content_form').serialize();
		params += '&sub_action=save_banner&b_type='+banner_type;
	
	$.ajax({
		type: 'post',
		url: 'banner_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				document.cookie = 'response=banner_save';
				window.location.reload();
			}
			else{
				show_alert(result.error);
			}	
		}
	});	
}

function publish_banner(b_id){
	var params = new Array();
	if($('#banner_'+b_id+' .publish').hasClass('show')){//publish banner
		params = {
			action:	'publish',
			b_id:	b_id
		};
	}
	else{//unpublish banner
		params = {
			action:	'unpublish',
			b_id:	b_id
		};
	}

	$.ajax({
		type: 'post',
		url: 'banner_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				if(result.action=='publish'){
					$('#banner_'+b_id).removeClass('hide');
					$('#banner_'+b_id+' .item:eq(1)').addClass('hide').text('隐藏');
				}
				else{
					$('#banner_'+b_id).addClass('hide');
					$('#banner_'+b_id+' .item:eq(1)').addClass('hide').text('显示');
				}
			}
			else{
				show_alert(result.error);
			}	
		}
	});
}

function delete_banner(b_id){
	if(confirm('确定要删除此横幅?')){
		show_alert('正在删除横幅，请稍候 ...','load');
	
		$.ajax({
			type: 'post',
			url: 'img_delete.php?action=delete&b_id='+b_id,
			dataType: 'json',
			success: function(result){
				if(result.success==1){
					$('#sort_banner').attr('height','auto');
					$('#banner_holder #banner_'+b_id).remove();
					show_alert('成功删除横幅！');
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function add_banner(e){
	if(e=='cancel'){
		$('.banner_info.new').remove();
		$('#btn_cancel').hide();
		$('#btn_add').show();
	}
	else{
		var recomm_str = '640*360';
		
		var banner_haystack = 
		'<div class="banner_info new">' + 
			'<table border="0" cellpadding="0" cellspacing="10">' + 
				'<tr>' + 
					'<td class="title">图片标题</td>' + 
					'<td width="265px">' + 
						'<input name="nb_img_title" type="text" id="nb_img_title" maxlength="20">' + 
					'</td>' + 
					'<td id="img_ctrl_area" valign="top" rowspan="3">' + 
						'(请先保存横幅再上传图片，建议尺寸: '+recomm_str+')' + 
					'</td>' + 
				'</tr>' + 
				'<tr valign="top">' + 
					'<td class="title">图片描述</td>' + 
					'<td>' + 
						'<textarea name="nb_img_desc" class="txt_area" id="nb_image_desc"></textarea>' + 
					'</td>' + 
				'</tr>' + 
				'<tr valign="top">' + 
					'<td class="title">图片链接</td>' + 
					'<td>' + 
						'<input name="nb_img_link" type="text" id="nb_img_link">' + 
					'</td>' + 
				'</tr>' + 
			'</table>' + 
		'</div>';
		
		$('#banner_holder').append(banner_haystack);
		set_public_attr();
		$('#btn_add').hide();
		$('#btn_cancel').show();
	}
}
</script>
<?php
include("footer.php");
?>
<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

setcookie("node_id",0);

$title_str = "编辑首页";

$select_field_str = "";
foreach($default_fields as $val){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $val;
}

$sql = "SELECT $select_field_str 
		FROM site_table 
		WHERE id='".$_SESSION['site_id']."'";					
$get_site = mysql_query($sql);
if(mysql_num_rows($get_site)>0){
	$s_row = mysql_fetch_array($get_site);
	
	foreach($default_fields as $val){
		${"s_".$val} = $s_row[$val];
	}
}
?>

<form id="default_form" enctype="multipart/form-data" onsubmit="return false;">

<input type="hidden" name="site_id" id="site_id" value="<?php print $_SESSION['site_id'];?>">
<input type="hidden" name="action" id="action" value="edit">

<table class="ctrl_header top" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"><?php if($title_str!="")print $title_str;?></td>
		<td class="ctrl_holder">
		<?php if($_SESSION['auth_content']==2){ ?>
			<div class="btn_holder">
				<button class="ctrl_btn preview" onclick="window.open('../?mode=preview','_blank');">
					预览
				</button>
				<button class="ctrl_btn active save" onclick="save_page_info();">
					保存
				</button>
				<button class="ctrl_btn active publish" value="Publish" onclick="save_page_info('publish');">
					发布
				</button>
			</div>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="tab_list" colspan="2">
			<div id="tab_container">
				<ul class="tab_holder">
					<li>
						<a id="tab_1" class="tab curr">							
							基本
						</a>
					</li>
					<li>
						<a id="tab_2" class="tab">							
							横幅
						</a>
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
			<?php include('default_control_tab_1.php');?>
			<?php include('default_control_tab_2.php');?>
		</td>
	</tr>
</table>

<table class="ctrl_header bottom" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"></td>
		<td class="ctrl_holder">
		<?php if($_SESSION['auth_content']==2){ ?>
			<div class="btn_holder">
				<button class="ctrl_btn active save" onclick="save_page_info();">
					保存
				</button>
				<button class="ctrl_btn active publish" value="Publish" onclick="save_page_info('publish');">
					发布
				</button>
			</div>
		<?php } ?>
		</td>
	</tr>
</table>

</form>

<script src="js/public_fn.js" type="text/javascript"></script>
<script>
var default_layout = '<?php print $s_layout;?>';
var layout_print = '';
var header_print = 		'<div id="layout_header" class="layout whole">' + 
							'<span class="legend"><div>页眉链接拖拽区</div></span>' + 
						'</div>';
var footer_print = 		'<div id="layout_footer" class="layout whole">' + 
							'<span class="legend"><div>页脚链接拖拽区</div></span>' + 
						'</div>';
var layout_whole = 		'<div class="layout whole">' + 
							'<span class="legend"><div>拖拽区-实际宽960px</div></span>' + 
						'</div>';
var layout_half = 		'<div class="layout half">' + 
							'<span class="legend"><div>拖拽区-实际宽475px</div></span>' + 
						'</div>';
var layout_one_third = 	'<div class="layout one_third">' + 
							'<span class="legend"><div>拖拽区-实际宽317px</div></span>' + 
						'</div>';
var layout_two_thrid = 	'<div class="layout two_third">' + 
							'<span class="legend"><div>拖拽区-实际宽633px</div></span>' + 
						'</div>';
var draggable_id,
	draggable_type;
var node_type = '';
var node_type_arr = <?php print json_encode(array_merge($node_type_arr,$trip_type_arr,$article_type_arr,$custom_type_arr,$share_type_arr,$ads_type_arr));?>;
var layout_header = '<?php print $s_layout_header;?>';
var layout_body = '<?php print $s_layout_body;?>';
var layout_footer = '<?php print $s_layout_footer;?>';
var layout_ads = '<?php print $s_ads;?>';
var sort_banner_height = '';
var banner_type = 0;

var act_tab = '<?php print $_COOKIE['content_tab'];?>';

$(function() {
	if(act_tab==''){
		document.cookie = 'content_tab=1';
		$('#tab_panel_1').show();
	}
	else{
		$('form .tab_holder .tab').removeClass('curr');
		$('#tab_'+act_tab).addClass('curr');
		$('#tab_panel_'+act_tab).show();
	}
	set_public_attr();
	apply_layout(default_layout);
	set_layout_draggable();	
	
	$('input[type=radio]').change(function(){
		apply_layout($(this).attr('id'));		
	});
	
	fill_ads();
	set_banner_sortable();
	set_banner_upload();
});

function fill_ads(){
	var node_arr = layout_ads.split('#');
	var params = '';
	for(var j=0;j<node_arr.length;j++){
		if(params!='')params += '&';
		params += 'n_id[]='+node_arr[j];
	}
	
	$.ajax({
		type:		'GET',
		url: 		'fetch_node.php?'+params,
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$(xml).find('node').each(function(){
				var id = $(this).find('id').text();
				var name = $(this).find('name').text();
				var type = $(this).find('type').text();
				
				$('#promo_ads').append(
					'<div class="node_holder" id="n_'+id+'">' + 
						'<a class="node_link" title="'+name+'">'+name+'</a>' + 
						'<a class="remove" title="移除"></a>' + 
					'</div>'
				);
				
				set_remove();
			});
		}
	});
}

function apply_layout(e){
	switch(e){
		case 'two_col_50_50':
			layout_print = 	'<div class="layout_row">' + 
								layout_half + 
								layout_half + 
							'</div>';
			break;
		case 'two_col_30_70':
			layout_print = 	'<div class="layout_row">' + 
								layout_one_third + 
								layout_two_thrid + 
							'</div>';
			break;
		case 'two_col_70_30':
			layout_print = 	'<div class="layout_row">' + 
								layout_two_thrid + 
								layout_one_third + 
							'</div>';
			break;
		case 'one_two_col_30_70':
			layout_print = 	'<div class="layout_row one_third">' + 
								layout_whole + 
							'</div>' + 								
							'<div class="layout_row two_third">' + 
								layout_one_third + 
								layout_two_thrid + 
							'</div>';
			break;
		case 'one_two_col_70_30':
			layout_print = 	'<div class="layout_row one_third">' + 
								layout_whole + 
							'</div>' + 								
							'<div class="layout_row two_third">' + 									
								layout_two_thrid + 
								layout_one_third + 
							'</div>';
			break;
		case 'two_two_col':
			layout_print = 	'<div class="layout_row half">' + 
								layout_one_third + 
								layout_two_thrid + 
							'</div>' + 								
							'<div class="layout_row half">' + 									
								layout_two_thrid + 
								layout_one_third + 
							'</div>';
			break;
		case 'three_col':
			layout_print = 	'<div class="layout_row">' + 
								layout_one_third + 
								layout_one_third + 
								layout_one_third + 
							'</div>';
			break;
		default: layout_print = '<div class="layout_row">' + 
									layout_whole + 
								'</div>';
	}
	var haystack_header = '';
	$('#layout_header .node_holder').each(function(){
		haystack_header += $(this)[0].outerHTML;
	});
	
	var haystack_footer = '';
	$('#layout_footer .node_holder').each(function(){
		haystack_footer += $(this)[0].outerHTML;
	});
	
	var haystack_body = '';
	$('.layout_row:visible .layout .node_holder').each(function(){
		haystack_body += $(this)[0].outerHTML;
	});
	
	layout_print = header_print + '<div id="layout_body">' + layout_print + '</div>' + footer_print;
	$('#layout_preview').html(layout_print);
	set_layout_sortable();
	
	if(e==default_layout){
		fill_header();
		fill_footer();
		fill_layout(0);
	}
	else{
		$('#layout_header').append(haystack_header);
		$('#layout_footer').append(haystack_footer);
		$('.layout_row .layout:eq(0)').append(haystack_body);
		//reset node name length
		$('.layout_row .layout:eq(0) .node_link').each(function(){
			//total length = 8
			var name = $(this).attr('title');
			var type = $(this).next('.type');
			$(this).text(name);
		});
		set_remove();
	}
}

function unpublish_page_info(){
	if(confirm('This site will be invisible, unpublish this site?')){
		show_alert('正在取消发布本站，请稍候 ...','load');
		
		var params = 'action=unpublish&s_id='+$('#site_id').val();
		
		$.ajax({//发布页面信息
			type: 'post',
			url: 'default_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					window.location.reload();
				}
				else{
					show_alert(result.error);
				}
			}
		});
	}
}

function save_page_info(e){
	if(check_form()){
		show_alert('正在保存站点信息，请稍候 ...','load');
		
		var params = $('#default_form').serialize();
		
		var header_params = $('#layout_header').sortable('serialize');
		header_params = header_params.replace(/n\[\]/g,'header[]');
		
		var body_params = '';
		for(var i=0;i<$('#layout_body .layout_row .layout').length;i++){
			if(body_params!='')body_params += '&';						
			var sort_params = $('#layout_body .layout_row .layout:eq('+i+')').sortable('serialize');
			body_params += sort_params.replace(/n\[\]/g,'body_'+i+'[]');
		}
		
		var footer_params = $('#layout_footer').sortable('serialize');
		footer_params = footer_params.replace(/n\[\]/g,'footer[]');
		
		params += '&'+header_params+'&'+body_params+'&'+footer_params;
		
		$.ajax({
			type: 'post',
			url: 'default_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					if(e=='publish'){
						show_alert('正在发布站点信息，请稍候 ...','load');
	
						$('#site_id').val(result.id);
						var params = 'action=publish&s_id='+result.id;
						
						$.ajax({
							type: 'post',
							url: 'default_manage.php',
							dataType: 'json',
							data: params,
							success: function(result1){
								if(result1.success==1){
									document.cookie = 'response=site_publish';
									window.location.reload();
								}
								else{
									show_alert(result1.error);
								}
							}
						});
					}
					else{
						document.cookie = 'response=site_save';
						window.location.reload();
					}
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}			
}

function check_form(){
	if($('input[name=s_name]').val()==''){
		show_alert('请填写站点名称！');
		return false;
	}
	if($('input[name=s_title]').val()==''){
		show_alert('请填写站点标题！');
		return false;
	}
	if($('input[name=s_domain]').val()==''){
		show_alert('请填写站点域名！');
		return false;
	}
	else{
		var tmpInput = $('input[name=s_domain]').val();
		var regex = /^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i;
		if(regex.test(tmpInput)==false){
			show_alert('域名格式无效，请重新输入！');
			return false;
		}
	}
	
	return true;
}
</script>
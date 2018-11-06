<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$node_id = "";
$parent_id = "";
$n_type = "";
$object_type = "";

$title_str = "";
if($_REQUEST['n_id']!=""){
	setcookie("node_id",mysql_escape_string($_REQUEST['n_id']));
	$node_id = mysql_escape_string($_REQUEST['n_id']);
	
	$title_str = "编辑页面";
}
if($_REQUEST['parent_id']!=""){
	$parent_name = "首页";
	
	$parent_id = mysql_escape_string($_REQUEST['parent_id']);	
	$get_parent_query = "SELECT name FROM content_table WHERE id='$parent_id'";	
	$get_parent = mysql_query($get_parent_query);
	if(mysql_num_rows($get_parent)>0){
		$p_row = mysql_fetch_array($get_parent);
		$parent_name = $p_row['name'];
	}	
	
	$title_str = "在 [".$parent_name."] 下创建";
}

$select_field_str = "";
foreach($content_basic as $val){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $val;
}
foreach($content_fields as $val){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= $val;
}

$sql = "SELECT 
			$select_field_str,
			created_time,
			updated_time,
			(SELECT ut.name FROM content_table ct,user ut WHERE ct.created_by=ut.id AND ct.id='".$node_id."') AS created_user,
			(SELECT ut.name FROM content_table ct,user ut WHERE ct.updated_by=ut.id AND ct.id='".$node_id."') AS updated_user,
			(SELECT published_time FROM pub_content_table WHERE id='".$node_id."') AS published_time 
		FROM content_table 
		WHERE id='".$node_id."'";
$get_node = mysql_query($sql);
if(mysql_num_rows($get_node)>0){
	$n_row = mysql_fetch_array($get_node);
	
	$parent_id = $n_row['parent_id'];	
	foreach($content_fields as $val){
		${"n_".$val} = $n_row[$val];
	}
	
	$n_ads_arr = explode("|",$n_ads);
	$n_banner_ads = $n_ads_arr[0];
	$n_promo_ads = $n_ads_arr[1];
	$n_sidebar_ads = $n_ads_arr[2];
	
	$n_created_user = $n_row['created_user'];
	$n_updated_user = $n_row['updated_user'];
	
	$raw_created_time = $n_row['created_time'];	
	$n_created_time = format_time($raw_created_time);
	$n_updated_time = format_time($n_row['updated_time']);
	$published_time = $n_row['published_time'];
	
	foreach($type_control as $key=>$val){
		if($n_type==$key){
			foreach($val as $control){
				${"tc_".$control} = 1;
			}
		}
	}
}

$preview_route = fetch_route($node_id);
?>

<form id="content_form" enctype="multipart/form-data" onsubmit="return false;">

<input type="hidden" name="node_id" id="node_id" value="<?php print $node_id;?>">
<input type="hidden" name="parent_id" id="parent_id" value="<?php print $parent_id;?>">
<input type="hidden" name="action" id="action" value="<?php print ($node_id!="")?"edit":"add";?>">

<table class="ctrl_header top" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"><?php if($title_str!="")print $title_str;?></td>
		<td class="ctrl_holder">
			<?php if($_SESSION['auth_content']==2){ ?>
			<div class="btn_holder">
				<button class="ctrl_btn preview" onclick="window.open('<?php print "../".$preview_route;?>?mode=preview','_blank');">
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
							内容
						</a>
					</li>
					<?php 
					if($tc_topic==1){ ?>
					<li>
						<a id="tab_2" class="tab">
							附属
						</a>					
					</li>
					<?php } ?>
					<?php if($tc_ads_banner==1){ ?>
					<li>
						<a id="tab_3" class="tab">
							横幅
						</a>
					</li>
					<?php } ?>
					<li>
						<a id="tab_4" class="tab">
							属性
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
			<?php include('content_control_tab_1.php');?>
			<?php include('content_control_tab_2.php');?>
			<?php include('content_control_tab_3.php');?>
			<?php include('content_control_tab_4.php');?>
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

<script>
var sim_editor,
	adv_editor;
var targetID = '<?php print $node_id;?>';
var prev_id = '<?php print $_COOKIE['node_id'];?>';
var node_type = '<?php print $n_type;?>';
var banner_type = '<?php print $banner_type;?>';

var draggable_id,
	draggable_type;
var sort_banner_height = '';
var act_tab = '<?php print $_COOKIE['content_tab'];?>';

$(function() {
	if(act_tab=='' || targetID!=prev_id){
		document.cookie = 'content_tab=1';
		$('#tab_panel_1').show();
	}
	else{
		$('form .tab_holder .tab').removeClass('curr');
		$('#tab_'+act_tab).addClass('curr');
		$('#tab_panel_'+act_tab).show();
	}
	
	set_public_attr();
	
	//set_banner_sortable();
	//set_banner_upload();
});

function strip(html){
	var tmp = document.createElement('DIV');
	tmp.innerHTML = html;
	
	var haystack = '';
	var str_arr = tmp.innerText.split('\n');
	for(var i in str_arr){
		if(str_arr[i]!=''){
			str_arr[i] = str_arr[i].split('\t');
			for(var j in str_arr[i]){
				if(str_arr[i][j]!=''){
					if(haystack!='')haystack += ' ';
					haystack += str_arr[i][j];
				}
			}
		}
	}
	
	haystack = haystack.replace(/，/g,',');
	haystack = haystack.replace(/。/g,'.');
	haystack = haystack.replace(/！/g,'!');
	haystack = haystack.replace(/（/g,'(');
	haystack = haystack.replace(/）/g,')');
	haystack = haystack.replace(/：/g,':');
	haystack = haystack.replace(/、/g,',');
	
	return haystack;
}

function build_desc(){
	if($('#n_meta_desc').val()==''){
		if($('#n_summary').length>0 && $('#n_summary').val()!=''){
			$('#n_meta_desc').val(strip($('#n_summary').val()));
		}
		else if($('#n_content').length>0 && $('#n_content').val()!=''){
			$('#n_meta_desc').val(strip($('#n_content').val()));
		}
		
		if($('#n_meta_desc').val().length>200){
			show_alert('注意：页面描述字数过多，建议不超过200字。');
		}
	}
}

function build_kwrd(){
	if($('#n_meta_kwrd').val()==''){
		<?php if(has_child($node_id)){ ?>
		$.ajax({
			type:		'GET',
			url: 		'fetch_children.php?tb=content&parent_id='+targetID,
			dataType:	'xml',
			success: function (xml) {
				var c_kwrd_str = '';
				$(xml).find('node').each(function(){
					var child_name = $(this).find('name').text();
					
					if(c_kwrd_str!='')c_kwrd_str += ',';
					c_kwrd_str += child_name;
				});
				
				$('#n_meta_kwrd').val($('#n_name').val() + ',' + c_kwrd_str);
			}
		});
		<?php }else{ ?>
		$.ajax({
			type:		'GET',
			url: 		'fetch_parents.php?n_id='+targetID,
			dataType:	'xml',
			success: function (xml) {
				var p_kwrd_str = '';
				var i = 1;
				var p_num = $(xml).find('node').length;
				$(xml).find('node').each(function(){
					if(i>=p_num-1){
						var parent_name = $(this).find('parent_name').text();
						
						if(p_kwrd_str!='')p_kwrd_str += ',';
						p_kwrd_str += parent_name;
					}
					i++;
				});
				
				$('#n_meta_kwrd').val(p_kwrd_str + ',' + $('#n_name').val());
			}
		});
		<?php } ?>
	}
}

function check_form(){
	if($('input[name=n_name]').val()==''){
		show_alert('请输入页面名称！');
		return false;
	}
	
	if($('input[name=n_alias]').val()==''){
		show_alert('请输入页面链接！');
		return false;
	}
	else{
		var tmpInput = $('input[name=n_alias]').val();	
		if(tmpInput.indexOf('-')>=0 || tmpInput.indexOf('_')>=0){
			var regex = /^[a-zA-Z0-9][a-zA-Z0-9_\-]+[a-zA-Z0-9]+$/;
		}
		else{
			var regex = /^[a-zA-Z0-9]+$/;
		}
		
		if(regex.test(tmpInput)==false){
			show_alert('页面别名仅支持英文和数字组合，中间分隔符支持“-”和“_”。');
			return false;
		}
		
		//check duplicate alias
		var params = {
			n_id:		$('#node_id').val(),
			parent_id:	$('#parent_id').val(),
			alias:		$('input[name=n_alias]').val()
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
					show_alert('页面别名重复，请重新输入！');
					flag = false;
				}
			}
		});
		
		if(!flag)return false;
	}
	
	return true;
}

function unpublish_page_info(){
	if(confirm('此页面及其子页面将不可见，确定停止发布此页面？')){
		show_alert('正在停止发布页面信息，请稍候 ...','load');
		
		var params = 'action=unpublish&n_id='+$('#node_id').val();
		
		$.ajax({//发布页面信息
			type: 'post',
			url: post_url,
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
		if($('textarea[class=sim_editor]').length>0)sim_editor.sync();
		if($('textarea[class=adv_editor]').length>0)adv_editor.sync();
		
		show_alert('正在保存页面信息，请稍候 ...','load');
		
		var params = $('#content_form').serialize();		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					if(e=='publish'){
						show_alert('正在发布页面信息，请稍候 ...','load');
						
						$('#node_id').val(result.id);
						var params = 'action=publish&n_id='+result.id;
						
						$.ajax({
							type: 'post',
							url: post_url,
							dataType: 'json',
							data: params,
							success: function(result1){
								if(result1.success==1){
									document.cookie = 'node_id='+result1.id;
									show_alert('成功发布页面信息！','reload');
								}
								else{
									show_alert(result1.error);
								}
							}
						});
					}
					else{
						document.cookie = 'node_id='+result.id;
						show_alert('成功保存页面信息！','reload');
					}
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}
</script>
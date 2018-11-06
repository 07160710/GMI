<?php
include("header.php");
?>
<table id="content_holder" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td id="left_col">
			<div id="left_col_holder">
				<input type="hidden" name="post_id" value="<?php print $_COOKIE['node_id'];?>">
				<ul class="node_tree">
					<li id="n_0">						
						<div class="node_holder">
							<span class="node">
								<a class="node_link home" onclick="edit_node('0');">
									首页
								</a>
								<div class="menu_holder">
								<?php
									$home_menu_item = "";
									if($_SESSION['auth_content']==2){
										$home_menu_item .= 	"<a class=\"item add\" onclick=\"add_node('0');\">添加</a>";
									
										if(has_child(0)){
											$home_menu_item .= 	"<a class=\"item sort\" onclick=\"sort_node('0')\">排序</a>";
										}
									}
									
									$home_menu = "";
									if($home_menu_item!=""){
										$home_menu .= 	"<div class=\"ctrl_menu\">".
															$home_menu_item.
														"</div>";
									}
									print $home_menu;
								?>
								</div>
							</span>
						</div>
<?php
$print_col = "";
$level = 1;

$sql = "SELECT id,name,type FROM content_table WHERE parent_id='0' AND level='$level' ORDER BY sort_order";
$get_col = mysql_query($sql);
if(mysql_num_rows($get_col)>0){
	$print_col .= "<ul class=\"branch l_$level\">";
	
	while($c_row = mysql_fetch_array($get_col)){
		$n_id = $c_row['id'];
		$n_name = $c_row['name'];
		$trim_name = $n_name;//trim_node_name($n_name,12);
		$n_type = $c_row['type'];
		
		$publish_class = "";	
		if(check_publish($n_id)==0)$publish_class = "hide";
		
		$toogle_str = "";
		$sort_str = "";
		$child_str = "";
		if(has_child($n_id)){
			$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$n_id');\"></a>";
			$sort_str =	"<a class=\"item sort\" onclick=\"sort_node('$n_id');\">排序</a>";
			$child_str = "<ul class=\"branch l_".($level+1)."\"></ul>";
		}
		
		$ctrl_menu_item = "";
		if($_SESSION['auth_content']==2){
			$ctrl_menu_item .= "<a class=\"item add\" onclick=\"add_node('$n_id');\">添加</a>";
			$ctrl_menu_item .= "<a class=\"item copy\" onclick=\"copy_node('$n_id');\">复制</a>";
			$ctrl_menu_item .= "<a class=\"item move\" onclick=\"move_node('$n_id');\">移动</a>";
			$ctrl_menu_item .= $sort_str;
			$ctrl_menu_item .= "<a class=\"item publish\" onclick=\"publish_node('$n_id');\">发布</a>";
			$ctrl_menu_item .= "<a class=\"item delete\" onclick=\"delete_node($n_id);\">删除</a>";
		}
		
		$ctrl_menu = "";
		if($ctrl_menu_item!=""){
			$ctrl_menu .= 	"<div class=\"ctrl_menu\">".
								$ctrl_menu_item.
							"</div>";
		}	
		
$print_col .= <<<CONTENT
<li id="n_$n_id">
	$toogle_str
	<table class="node_holder $publish_class" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span class="node">
					<a class="node_link $n_type" title="$n_name" onclick="edit_node('$n_id');">$trim_name</a>
					<div class="menu_holder">
						$ctrl_menu
					</div>				
				</span>
			</td>
		</tr>
	</table>
	$child_str
</li>
CONTENT;
	}
	
	$print_col .= "</ul>";
}
print $print_col;
?>
					</li>
				</ul>
			</div>
		</td>
		<td id="right_col">
			<div id="content_panel"></div>
		</td>
	</tr>
</table>

<script>
var post_url = 'content_manage.php',
	post_id = '<?php print $_COOKIE['node_id'];?>',
	id_prefix = 'n_';

$(function() {
	if(post_id>0){
		post_last++;
		expand_node(post_id);
	}
	else{//first load or home page
		curr_id = $('.node_tree li:eq(0)').attr('id').replace(id_prefix,'');		
	
		if(curr_id==0){
			set_curr(curr_id);
			$('#content_panel').html('<div class="loading cover"></div>').load('default_control.php');			
		}
		else{
			edit_node(curr_id);
		}
	}
});

function edit_node(n_id){
	set_curr(n_id);
	
	if(n_id!=0){
		if($('#'+id_prefix+n_id+' .toogle:eq(0)').hasClass('shrink'))toogle_node(n_id);
		$('#content_panel').html('<div class="loading cover"></div>').load('content_control.php?n_id='+n_id);
	}
	else{
		$('#content_panel').html('<div class="loading cover"></div>').load('default_control.php');
	}
	$(window).scrollTop(0);
}

function add_node(n_id){
	set_curr(n_id);
	$('#content_panel').load('content_control.php?parent_id='+n_id);
	$(window).scrollTop(0);
}

function delete_node(n_id){
	var type_str = '页面';
	if($('#'+id_prefix+n_id).parent('ul').hasClass('l_1')){
		type_str = '栏目';
	}
	var str_del_child = '';
	if($('#'+id_prefix+n_id+' .toogle').length>0){
		str_del_child = '及其子页面';
	}
	
	if(confirm('确定删除'+type_str+'['+$('#'+id_prefix+n_id+' .node_link:eq(0)').text()+']'+str_del_child+'?')){
		var params = 'action=delete&n_id='+n_id;
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					document.cookie = 'node_id='+result.parent_id;
					$('#'+id_prefix+n_id).remove();
					if($('#'+id_prefix+result.parent_id+' > ul.branch li').length==0)$('#'+id_prefix+result.parent_id+' .toogle').remove();
					edit_node(result.parent_id);
				}	
				else{
					show_alert(result.error);
				}	
			}
		});
	}	
}
</script>

<?php
include("public_content.php");
include("footer.php");
?>
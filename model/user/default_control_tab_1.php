<!--TAB PANEL 1-->
<table id="tab_panel_1" class="data_holder tab_panel" border="0" cellpadding="0" cellspacing="10">
	<tr>
        <td class="title">
			站点名称
		</td>
        <td>
			<input name="s_name" type="text" id="s_name" maxlength="20" value="<?php print $s_name;?>">
		</td>
	</tr>
    <tr>
        <td class="title">
			站点标题
		</td>
        <td>
			<input name="s_title" type="text" id="s_title" value="<?php print $s_title;?>">
		</td>
    </tr>
    <tr>
        <td class="title">
			站点域名
		</td>
        <td>
			<input name="s_domain" type="text" id="s_domain" value="<?php print $s_domain;?>">
		</td>
    </tr>
	<tr>
        <td class="title">
			布局内容
		</td>
        <td></td>
    </tr>
	<tr>
        <td colspan="4">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr valign="top">
					<td width="492px">
						<div id="layout_preview"></div>
					</td>
					<td id="layout_source">
						<span class="note">拖拽所需的页面至左边虚线框内。</span>
						<ul class="node_tree">
							<li id="n_0">
<?php
$print_col = "";
$level = 1;

$get_col_query = "SELECT id,name,type FROM content_table WHERE parent_id = '0' AND level = '$level' ORDER BY sort_order";
$get_col = mysql_query($get_col_query);

if(mysql_num_rows($get_col)>0){
	$print_col .= "<ul class=\"branch l_$level\">";
	
	while($c_row = mysql_fetch_array($get_col)){
		$n_id = $c_row['id'];
		$n_name = $c_row['name'];
		$n_type = $c_row['type'];
		
		$toogle_str = "";
		$child_str = "";
		if(has_child($n_id,'content')){
			$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_source('$n_id');\"></a>";
			$child_str = "<ul class=\"branch l_".($level+1)."\"></ul>";
		}
		
$print_col .= <<<CONTENT
<li id="n_$n_id" class="$n_type">
	$toogle_str
	<div class="node_holder">		
		<a class="node_link">$n_name</a>
	</div>
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
					</td>
				</tr>
			</table>
		</td>
    </tr>
</table>
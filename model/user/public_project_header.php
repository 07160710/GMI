<table class="header_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="sidebar">
			<div id="hide_arrow_holder">
				<a id="hide_arrow" title="收起侧栏" onclick="display_sidebar();"></a>
			</div>
		</td>
		<td>
			<table class="data_ctrl_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="ctrl_holder">
<div class="ctrl_link">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span>操作</span>
			</td>
			<td>
				<div class="menu_holder">
					<a class="ctrl_arrow"></a>
					<div class="ctrl_menu">	
<?php 
if($page_name=="project"){
	print "	<a class=\"item\" onclick=\"view_project('create');\">创建项目</a>
			<a class=\"item\" onclick=\"multi_edit_project('rename_project');\">批量改项目名</a>
			<a class=\"item\" onclick=\"multi_edit_project('rename_company');\">批量改公司名</a>
			<a class=\"item\" onclick=\"import_approval();\">导入立项信息</a>
			<a class=\"item\" onclick=\"multi_edit_project('project');\">导出项目</a>
			<a class=\"item\" onclick=\"multi_edit_project('delete');\">删除项目</a>";
	if($_SESSION['u_id']==1)
	print "	<div class=\"clear\" style=\"border-top:1px solid #ddd;\"></div>
			<a class=\"item\" onclick=\"import_project('apply');\">导入申报项目</a>
			<a class=\"item\" onclick=\"multi_edit_project('copy_create');\">批量复制创建</a>
			<a class=\"item\" onclick=\"multi_edit_project('move_agreement');\">批量迁移协议</a>
			<a class=\"item\" onclick=\"multi_edit_project('clear_assign');\">批量清除派单</a>
			<a class=\"item\" onclick=\"multi_edit_project('clear_accept');\">批量清除接单</a>";
}
if($page_name=="sales"){ 
	if(strpos($_SESSION['role'],"sm")!==false){
		print "<a class=\"item\" onclick=\"multi_edit_project('assign_sales');\">项目派单</a>";
	}
}
if($page_name=="technology"){ 
	if(strpos($_SESSION['role'],"tm")!==false){ 
		print "<a class=\"item\" onclick=\"multi_edit_project('assign_technology');\">项目派单</a>";
	}
	if(strpos($_SESSION['role'],"ts")!==false){ 
		print "<a class=\"item\" onclick=\"multi_edit_project('accept_technology');\">项目接单</a>";
	} 
	print "<a class=\"item\" onclick=\"multi_edit_project('copy_create');\">批量复制创建</a>
			<a class=\"item\" onclick=\"multi_edit_project('project');\">导出项目</a>";
}
if($page_name=="finance"){ 
	if(strpos($_SESSION['role'],"fm")!==false){
		print "<a class=\"item\" onclick=\"multi_edit_project('assign_finance');\">项目派单</a>";
	}
	if(strpos($_SESSION['role'],"fs")!==false){
		print "<a class=\"item\" onclick=\"multi_edit_project('accept_finance');\">项目接单</a>";
	}
	print "<a class=\"item\" onclick=\"multi_edit_project('copy_create');\">批量复制创建</a>
			<a class=\"item\" onclick=\"multi_edit_project('project');\">导出项目</a>";
}
?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php if($_SESSION['auth_'.$page_name]==2){ ?>
<div class="ctrl_link">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span>标记</span>
			</td>
			<td>
				<div class="menu_holder">
					<a class="ctrl_arrow"></a>
					<div class="ctrl_menu">
						<a class="item" onclick="multi_edit_project('mark_progress');">标记进度</a>
						<a class="item" onclick="multi_edit_project('mark_year');">标记年度</a>
						<a class="item" onclick="multi_edit_project('mark_branch');">标记属地</a>
						<a class="item" onclick="multi_edit_project('mark_level');">标记级别</a>
						<a class="item" onclick="multi_edit_project('mark_category');">标记类型</a>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php } ?>
<div class="ctrl_link">										
	<label for="all_page_project">
		<input type="checkbox" class="check_all" id="all_page_project">
		选取所有分页项目
	</label>
</div>
<div class="data_count_holder">	
	<span id="project_count" class="data_count">	
		<b></b>&nbsp;
		个项目
	</span>
</div>

<select id="per_page">
	<option>20</option>
	<option>50</option>
	<option>100</option>
	<option>300</option>
	<option>500</option>
</select>
&nbsp;条 / 页
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
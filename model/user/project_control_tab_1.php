<!--TAB PANEL 1-->
<table id="tab_panel_1" class="tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
<?php include_once('public_project_header.php'); ?>
<table class="body_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="sidebar" valign="top">
			<table id="sidebar_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td id="filter_holder">
						<div id="cond_holder">
							<b>
								筛选条件
								<button onclick="clear_filter();">
									清空
								</button>
							</b>
							<div id="cond_list"></div>
						</div>
						
						<?php if($_SESSION['u_id']==1){ ?>
						<div id="filter_id" class="list_holder">
							<span class="title">
								项目ID
							</span>
							<input type="text" id="search_id">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						<?php } ?>
						<div id="filter_company" class="list_holder">
							<span class="title">
								公司名称
							</span>
							<input type="text" id="search_company">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						<div id="filter_name" class="list_holder">
							<span class="title">
								项目名称
							</span>
							<input type="text" id="search_name">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						
						<!--sign_period-->
						<div id="filter_sign_period" class="list_holder">
							<span class="title">签约时段</span>
							<input type="text" id="sign_start_date" class="date_input" style="padding:0 3px;" placeholder="开始日期">
							 - 
							<input type="text" id="sign_end_date" class="date_input" style="margin-left:0;padding:0 3px;" placeholder="结束日期">
							<button onclick="filter_search();">筛选</button>
						</div>
						<!--expire_period-->
						<div id="filter_expire_period" class="list_holder">
							<span class="title">到期时段</span>
							<input type="text" id="expire_start_date" class="date_input" style="padding:0 3px;" placeholder="开始日期">
							 - 
							<input type="text" id="expire_end_date" class="date_input" style="margin-left:0;padding:0 3px;" placeholder="结束日期">
							<button onclick="filter_search();">筛选</button>
						</div>
						
						<!--receive-->
						<div id="filter_receive" class="list_holder">
							<span>
								<label for="to_receive">
									<input type="checkbox" id="to_receive">
									已收合同
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
						</div>
						<!--progress-->
						<div id="filter_progress" class="list_holder">
							<span>				
								<label for="all_progress">
									<input type="checkbox" class="check_all" id="all_progress">
									所有进度
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--status_assign-->
						<div id="filter_status_assign" class="list_holder">
							<span>
								<label for="all_status_assign">
									<input type="checkbox" class="check_all" id="all_status_assign">
									派单状态
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<div id="filter_level" class="list_holder">
							<span>								
								<label for="all_level">
									<input type="checkbox" class="check_all" id="all_level">
									所有级别
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--branch-->
						<div id="filter_branch" class="list_holder">
							<span>								
								<label for="all_branch">
									<input type="checkbox" class="check_all" id="all_branch">
									所有属地
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--category-->
						<div id="filter_category" class="list_holder">
							<span>								
								<label for="all_category">
									<input type="checkbox" class="check_all" id="all_category">
									所有类型
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--year_apply-->
						<div id="filter_year_apply" class="list_holder">
							<span>								
								<label for="all_year_apply">
									<input type="checkbox" class="check_all" id="all_year_apply">
									所有年度
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						
					</td>
				</tr>
			</table>
		</td>
		<td id="right_col" valign="top">			
			<table id="project_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table id="project_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr class="header_row">
								<th width="10"><input type="checkbox" class="check_all" id="curr_page_project"></th>
								<th width="10"><a class="btn_preview"></a></th>
								<?php
								if($_SESSION['level']>2)print "<th width=\"10\"><a class=\"btn_receive\"></a></th>";
								
								foreach($GLOBALS['project_list_fields'] as $key=>$val){
									$width_str = "";
									if(strpos($val,"#")!==false){
										$val_arr = explode("#",$val);
										$width_str = 'width="'.$val_arr[1].'"';
										$val = $val_arr[0];
									}
									$sort_arrow = "<i></i>";									
									print "<th $width_str><a class=\"sort_$key\">".$val.$sort_arrow."</a></th>";
								}
								?>
							</tr>
						</table>
						<ul id="pagination"></ul>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

		</td>
	</tr>
</table>

<!--RENAME HOLDER-->
<div id="rename_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					批量重命名
				</span>
				<a class="btn_close" onclick="rename_project('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="rename_panel">
				<input type="hidden" name="rename_type">
				
				<div id="project_holder" class="search_holder">
					<input type="text" id="r_name" name="rename" class="long" placeholder="请输入项目名称">
					<ul id="r_pbase_list" class="result_list"></ul>
				</div>
				
				<div id="company_holder" class="search_holder">
					<input type="text" id="r_company" name="rename" class="long" placeholder="请输入公司名称">
					<ul id="r_company_list" class="result_list"></ul>
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="rename_project('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<?php include("project_control_tab_1_content.php"); ?>
<?php include("public_body_js.php"); ?>

<script>
function import_project(e){
	$('#import_holder .title').text('导入项目列表');
	$('#import_holder').show().setOverlay();
	$('#file_excel').change(function(){
		show_alert('正在导入数据，请稍候 ...','load');
		$.ajaxFileUpload({
			url:'project_import.php?action=import_project&type='+e,
			secureuri :false,
			fileElementId :'file_excel',
			dataType : 'json',
			success : function (result){
				if(result.success==1){
					show_alert(
						'成功导入项目记录！<br/>' + 
						'读取 ' + result.read_num + ' 条记录<br/>' + 
						'插入 ' + result.insert_num + ' 条记录<br/>' + 
						'更新 ' + result.update_num + ' 条记录'
					);
					filter_search(curr_page);
					$('#file_excel').val('');
				}
				else{
					alert(result.error);
				}
			}
		});
	});
}
</script>
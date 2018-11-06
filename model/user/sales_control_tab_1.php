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
						
						<!--expire_period-->
						<div id="filter_expire_period" class="list_holder">
							<span class="title">到期时段</span>
							<input type="text" id="expire_start_date" class="date_input" style="padding:0 3px;" placeholder="开始日期">
							 - 
							<input type="text" id="expire_end_date" class="date_input" style="margin-left:0;padding:0 3px;" placeholder="结束日期">
							<button onclick="filter_search();">筛选</button>
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
						<?php if(strpos($_SESSION['role'],"sm")!==false){ ?>
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
						<?php } ?>
						<!--sales-->
						<div id="filter_sales" class="list_holder">
							<span>
								<label for="all_sales">
									<input type="checkbox" class="check_all" id="all_sales">
									所有销售
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--level-->
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
								
								foreach($GLOBALS['sales_list_fields'] as $key=>$val){
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

<?php include("sales_control_tab_1_content.php"); ?>
<?php include("public_body_js.php"); ?>

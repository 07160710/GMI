<!--MARK HOLDER-->
<div id="mark_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					标记
				</span>
				<a class="btn_close" onclick="mark_project('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="mark_panel">
				<input type="hidden" name="mark_type">
			
				<table class="mark_table progress" width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td class="title">项目进度</td>
						<td>
							<select id="m_progress">
							<?php
							foreach($GLOBALS['project_progress_opt'] as $key=>$val){
								if(strpos($_SESSION['role'],"m")!==false){
									print '<option value="'.$key.'">'.$val.'</option>';
								}
								else{
									if($key==0 || $key>5)print '<option value="'.$key.'">'.$val.'</option>';
								}
							}
							?>
							</select>
						</td>
					</tr>
				</table>
				
				<table class="mark_table year" width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td class="title">申报年度</td>
						<td>
							<select id="m_year">
							<?php
							for($i=date('Y')+1;$i>=2015;$i--){
								print "<option>$i</option>";
							}
							?>
							</select>
						</td>
					</tr>
				</table>
				
				<table class="mark_table branch" width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td class="title">项目属地</td>
						<td>
							<select id="m_branch">
							<?php
							$sql = "SELECT id,name FROM branch";
							$get_branch = mysql_query($sql);
							if(mysql_num_rows($get_branch)>0){
								while($b_row = mysql_fetch_array($get_branch)){
									$id = $b_row[0];
									$name = $b_row[1];
									
									print "<option value=\"$id\">$name</option>";
								}
							}
							?>
							</select>
						</td>
					</tr>
				</table>
				
				<table class="mark_table level" width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td class="title">项目级别</td>
						<td>
							<select id="m_level">
							<?php
							foreach($GLOBALS['project_level_opt'] as $key=>$val){
								print '<option value="'.$key.'">'.$val.'</option>';
							}
							?>
							</select>
						</td>
					</tr>
				</table>
				
				<table class="mark_table category" width="100%" cellpadding="5" cellspacing="0">
					<tr>
						<td class="title">申报类型</td>
						<td>
							<select id="m_category">
							<?php
							foreach($GLOBALS['project_category_opt'] as $key=>$val){
								print '<option value="'.$key.'">'.$val.'</option>';
							}
							?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="mark_project('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<!--ASSIGN HOLDER-->
<div id="assign_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					项目派单
				</span>
				<a class="btn_close" onclick="assign_project('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="assign_panel">
				<table class="assign_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th>人员名单</th>
						<th>分配列表</th>
					</tr>
					<tr>
						<td id="user_list" class="user_list_holder">
							<div class="scroll_holder">
								<ul class="list"></ul>
							</div>
							<div class="assign_filter_holder">
								<input type="text" class="assign_filter" placeholder="输入姓名">
								<a class="btn_clear" onclick="clear_assign_filter();"></a>
							</div>
						</td>
						<td id="assign_list" class="assign_list_holder">
							<ul class="list"></ul>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="assign_project('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<!--IMPORT APPROVE HOLDER-->
<div id="import_approval_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					导入对象
				</span>
				<a class="btn_close" onclick="import_approval('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder">
				导入格式：年度 | 类型 | 批次 | 公司名称 | 市 | 区 | 奖补 | 备注
			</td>
		</tr>		
		<tr>
			<td id="import_panel">
				<div id="project_holder" class="search_holder">
					<input type="text" id="i_name" name="i_name" class="long" placeholder="请输入基础项目名称">
					<ul id="i_pbase_list" class="result_list"></ul>
				</div>
				<div class="clear" style="height:5px;"></div>
				<form name="upload_form" id="upload_form" enctype="multipart/form-data">
					<input type="file" name="file_approval" id="file_approval">
				</form>
			</td>
		</tr>
	</table>
</div>

<div id="project_action_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					标题名
				</span>
				<a class="btn_close" onclick="project_action('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="rename_panel">
				<input type="hidden" name="action_type">
				
				<div id="approve_success_holder" class="search_holder">
					<label class="title">批次：</label>
					<label>
						第
						<input class="short" type="text"  name="m_batch" id="" >批
					</label>
					<label class="title">奖补：</label>
					<label>
						<input type="text" placeholder="请填数字且不大于100" name="m_bonus" id="" >万
					</label>
					<label class="title">其他备注：</label>
					<label>
					<textarea style="margin-left: 3%;width:96%" placeholder="如：省高企培育库入库企业" name="m_remark" id="" ></textarea>
					</label>
				</div>
				<div id="approve_fail_holder" class="search_holder">
					
					<input type="text" name="remark" class="long" placeholder="立项失败的原因">
				</div>
				<div id="check1_fail_holder" class="search_holder">
					
					<input type="text" name="remark" class="long" placeholder="验收失败的原因">
				</div>
				<div id="check2_fail_holder" class="search_holder">
		
					<input type="text" name="remark" class="long" placeholder="验收失败的原因">
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="project_action('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<div id="fund_action_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					标题名
				</span>
				<a class="btn_close" onclick="fund_action('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="rename_panel">
				<input type="hidden" name="fund_type">
				
				<div id="request_fund_holder" style="width:300px;margin:0 13%" class="search_holder">
					<label class="title">请款人：</label>
					<label>
						<input type="text" name="m_request_people" id="" >
					</label>
					<label class="title">请款时间：</label>
					<label>
						<input type="text" name="m_request_date" id="" style="width: 93%!important" class="date_input">
					</label>
					<label class="title">请款金额：</label>
					<label>
						<input type="text" name="m_money" id="" >
					</label>
					<label class="title">其他备注：</label>
					<label>
						<input type="text" name="m_remark">
					</label>
				</div>
				<div id="receive_fund_holder" style="width:300px;margin:0 13%" class="search_holder">
					
					<label class="title">企业收款时间：</label>
					<label>
						<input type="text" name="m_cheques_date" id="" style="width: 93%!important" class="date_input">
					</label>
					<label class="title">企业收款金额：</label>
					<label>
						<input type="text" name="m_money" id="" >
					</label>
					<label class="title">其他备注;</label>
					<label>
						<input type="text" name="m_remark">
					</label>
				</div>
				<div id="receive_fee_holder" style="width:300px;margin:0 13%" class="search_holder">
					
					<label class="title">回款人:</label>
					<label>
						<input type="text" name="m_receive_people" id="" >
					</label>
					<label class="title">回款时间：</label>
					<label>
						<input type="text" name="m_receive_date" id="" style="width: 93%!important" class="date_input">
					</label>
					<label class="title">回款金额：</label>
					<label>
						<input type="text" name="m_money" id="" >
					</label>
					<label class="title">其他备注：</label>
					<label>
						<input type="text" name="m_remark">
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="fund_action('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<script>
var per_page = <?php print ($_COOKIE[$page_name.'_per_page']>0)?$_COOKIE[$page_name.'_per_page']:20;?>,
	page_name = '<?php print $page_name;?>',
	ctrl_id = '';
$(function() {
	$('.check_all').change(function(){
		if($(this).is(':checked')){
			$(this).parents('.list_holder').find('.filter_list input[type=checkbox]').prop('checked',true);
		}
		else{
			$(this).parents('.list_holder').find('.filter_list input[type=checkbox]').prop('checked',false);
		}
	});
	
	$('#per_page').change(function(){
		per_page = $(this).val();
		document.cookie = page_name+'_per_page='+per_page+';path=./';
		filter_search();
	}).val(per_page);
	
	set_date_input();
	set_assign_filter();
	setTimeout(function(){
		filter_search();
	},10);
});

function project_action(e){ //项目立项、第一次验收、第二次验收	
	
	if(e=='close'){
		$('#project_action_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var action_type = $('input[name=action_type]').val();
		var params;
		if(action_type=='approve_success'){
			var batchValue = $('.search_holder:visible input[name=m_batch]').val();
			var bonusValue = $('.search_holder:visible input[name=m_bonus]').val();
			var remarkValue = $('.search_holder:visible textarea[name=m_remark]').val();
			batchValue = '第'+batchValue+'批';
			params = 'action=project_action&type='+action_type+'&remark='+remarkValue+'&bonus='+bonusValue+'&batch='+batchValue+'&project_id='+ctrl_id;
		}else if(action_type=='approve_fail'||action_type=='check1_fail'||action_type=='check2_fail'){
			var remarkValue = $('.search_holder:visible input[name=remark]').val();
			params = 'action=project_action&type='+action_type+'&remark='+remarkValue+'&project_id='+ctrl_id;
		}else{
			alert('无效动作');return;
		}
		
		$.ajax({
			type: 'post',
			url: 'process_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					alert(result.msg);
					$('#project_action_holder').hide();
					view_project(ctrl_id);
					filter_search(curr_page);
					$('.tab_holder .subtab').removeClass('curr');
					$('#subtab_2').addClass('curr');
					$('.subtab_panel').hide();
					$('#subtab_panel_2').show();
				}
				else{
					alert(result.msg);
				}	
			}
		});
	}
	else{
		if(e=='check1_success'||e=='check2_success'){
			if(confirm('是否确认为验收通过？')){
				var params = 'action=project_action&type='+e+'&project_id='+ctrl_id;
				$.ajax({
					type: 'post',
					url: post_url,
					dataType: 'json',
					data: params,
					success: function(result){
						if(result.success==1){
							alert(result.msg);
							view_project(ctrl_id);
							filter_search(curr_page);
							$('.tab_holder .subtab').removeClass('curr');
							$('#subtab_2').addClass('curr');
							$('.subtab_panel').hide();
							$('#subtab_panel_2').show();
						}
						else{
							alert(result.msg);
						}	
					}
				});
			}
				
			return;
		}
		var title,r_remarkValue;
		if(e=='approve_success'){
			title = '立项成功';
			// r_remarkValue = $('.data_row.need_approve .project_approve .reamrk').text();
		}else if(e=='approve_fail'){
			title = '立项失败';
			// r_remarkValue = $('.data_row.need_check .acceptance_check1 .reamrk').text();
		}else if(e=='check1_fail'){
			title = '第一次验收';
			// r_remarkValue = $('.data_row.need_check .acceptance_check1 .reamrk').text();
		}else if(e=='check2_fail'){
			title = '第二次验收';
			// r_remarkValue = $('.data_row.need_check .acceptance_check2 .reamrk').text();
		}
		$('input[name=action_type]').val(e);
		$('#project_action_holder .header_holder .title').text(title)		
		$('#project_action_holder .search_holder').each(function(){
			if($(this).attr('id').indexOf(e)>=0){
				$(this).show();
				// $(this).find('input[name=remark]').val(r_remarkValue);
			}
			else $(this).hide();
		});
		$('#project_action_holder').show().setOverlay();

		$('#'+e+'_holder input[name=result]').click(function(){
			var e_id = $(this).attr('id');
			if($(this).is(":checked")){
				if(e_id.indexOf('fail')>=0){
					$('#'+e+'_holder input[name=remark]').css('display','block');
				}
			}else{
				$('#'+e+'_holder input[name=remark]').css('display','hidden');
			}
		})
		
	}
}

function fund_action(e){ //请款,企业收款,中科回款
	
	if(e=='close'){
		$('#fund_action_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var fund_type = $('input[name=fund_type]').val();
		var params;
		var r_remark = $('.search_holder:visible input[name=fund_date]').val();
		var r_result = $('.search_holder:visible input[name=result]').val();
		if(fund_type=='request_fund'){
			var peopleValue = $('.search_holder:visible input[name=m_request_people]').val();
			var dateValue = $('.search_holder:visible input[name=m_request_date]').val();
			var moneyValue = $('.search_holder:visible input[name=m_money]').val();
			var remarkValue = $('.search_holder:visible input[name=m_remark]').val();
			params = 'action=project_action&type=rf&remark='+remarkValue+'&money='+moneyValue+'&people='+peopleValue+'&date='+dateValue+'&project_id='+ctrl_id;
		}else if(fund_type=='receive_fund'){
			var dateValue = $('.search_holder:visible input[name=m_cheques_date]').val();
			var moneyValue = $('.search_holder:visible input[name=m_money]').val();
			var remarkValue = $('.search_holder:visible input[name=m_remark]').val();
			params = 'action=project_action&type=cf&remark='+remarkValue+'&money='+moneyValue+'&date='+dateValue+'&project_id='+ctrl_id;
		}else{  //receive_fee  
			var peopleValue = $('.search_holder:visible input[name=m_receive_people]').val();
			var dateValue = $('.search_holder:visible input[name=m_receive_date]').val();
			var moneyValue = $('.search_holder:visible input[name=m_money]').val();
			var remarkValue = $('.search_holder:visible input[name=m_remark]').val();
			params = 'action=project_action&type=zf&remark='+remarkValue+'&money='+moneyValue+'&people='+peopleValue+'&date='+dateValue+'&project_id='+ctrl_id;
		}
		$.ajax({
			type: 'post',
			url: 'process_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					alert(result.msg);
					$('#fund_action_holder').hide();
					view_project(ctrl_id);
					filter_search(curr_page);
					$('.tab_holder .subtab').removeClass('curr');
					$('#subtab_2').addClass('curr');
					$('.subtab_panel').hide();
					$('#subtab_panel_2').show();
				}
				else{
					alert(result.msg);
				}	
			}
		});
	}
	else{
		var title,fund_dateValue;
		if(e=='request_fund'){
			title = '请款';
		}else if(e=='receive_fund'){
			title = '企业收款';
		}else if(e=='receive_fee'){
			title = '回款';
		}
		$('input[name=fund_type]').val(e);
		$('#fund_action_holder .header_holder .title').text(title)		
		$('#fund_action_holder .search_holder').each(function(){
			if($(this).attr('id').indexOf(e)>=0)$(this).show();
			else $(this).hide();
		})
		$('#fund_action_holder').show().setOverlay();
	}
}

function set_search_i_pbase(){
	$('#i_name').keyup(function(){
		var keyword = $(this).val();
		if(keyword==''){
			$('#i_pbase_list').html('');
		}
		else{
			var params = 'object=project&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#i_pbase_list').html('');
					$(xml).find('result').each(function(){
						var project = $(this).text();
						$('#i_pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#i_name\').val(\''+project+'\');$(\'#i_pbase_list\').html(\'\');">' + 
									project + 
								'</a>' + 
							'</li>'
						);
					});
				}
			});
		}
	}).focus(function(){
		var keyword = $(this).val();
		if(keyword==''){
			$('#i_pbase_list').html('');
		}
		else{
			var params = 'object=project&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#i_pbase_list').html('');
					$(xml).find('result').each(function(){
						var project = $(this).text();
						$('#i_pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#i_name\').val(\''+project+'\');$(\'#i_pbase_list\').html(\'\');">' + 
									project + 
								'</a>' + 
							'</li>'
						);
					});
				}
			});
		}
	}).blur(function(){
		setTimeout(function(){
			$('#i_pbase_list').html('');
		},300);
	});
}
function import_approval(e){
	if(e=='close'){
		$('#import_approval_holder').hide().setOverlay();
	}
	else{
		set_search_i_pbase();
		$('#import_approval_holder .title').text('导入立项记录');
		$('#import_approval_holder').show().setOverlay();
		$('#file_approval').change(function(){
			var pbase_name = $('#i_name').val();
			if(pbase_name==''){
				alert('基础项目名称不能为空！');
				$('#file_approval').val('');
				return false;
			}
			
			show_alert('正在导入数据，请稍候 ...','load');
			$.ajaxFileUpload({
				url:'process_manage.php?action=import_approval&pbase_name='+pbase_name,
				secureuri :false,
				fileElementId :'file_approval',
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
						$('#file_approval').val('');
					}
					else{
						alert(result.error);
					}
				}
			});
		});
	}
}

function set_date_input(){
	$('.date_input').each(function(){
		$(this).datepicker({
			changeMonth:true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		});
	});
}

function clear_assign_filter(){
	$('.assign_filter').val('');
	$('#user_list .list li').show();
	$('.assign_filter_holder .btn_clear').hide();
}
function set_assign_filter(){
	$('.assign_filter').keyup(function(){
		var keyword = $(this).val();
		if(keyword!=''){
			$('#user_list .list li').each(function(){
				var val = $(this).text();
				if(val.indexOf(keyword)>=0){
					$(this).show();
				}
				else{
					$(this).hide();
				}
			});
			$('.assign_filter_holder .btn_clear').show();
		}
		else{
			$('#user_list .list li').show();
			$('.assign_filter_holder .btn_clear').hide();
		}
	});
}
function set_check_user(){
	$('.user input[type=checkbox]').change(function(){
		var parent = $(this).parents('li.user');
		if($(this).is(':checked'))parent.removeClass('pass').addClass('curr');
		else parent.removeClass('curr').addClass('pass');
	});
}
function assign_user(id,type){
	var name = $('#u_'+id).text(),
		active_class = $('#u_'+id).attr('class').replace('curr ',''),
		assigned_time = $('#u_'+id+' .date_input').val(),
		today = new Date().format('yyyy-MM-dd');
	
	$('.list #u_'+id).remove();
	if(type=='curr'){
		$('#assign_list .list').prepend(
			'<li id="u_'+id+'" class="user curr active">' + 
				'<label class="name" for="ck_u_'+id+'">' + 
					'<input type="checkbox" id="ck_u_'+id+'" title="当前经办" checked>' + 
					name + 
				'</label>' + 
				'<div class="date">' + 
					'<input type="text" class="date_input" placeholder="经办开始日" value="'+today+'">' + 
				'</div>' + 
				'<div class="ctrl">' + 
					'<a class="btn_back" title="移除人员" onclick="assign_user(\''+id+'\',\'back\');"></a>' + 
				'</div>' + 
			'</li>'
		);
		set_date_input();
		set_check_user();
		clear_assign_filter();
	}
	else if(type=='back'){
		$('#user_list .list').append(
			'<li id="u_'+id+'" class="user">' + 
				name + 
				'<a class="btn_move" title="添加人员" onclick="assign_user(\''+id+'\',\'curr\');"></a>' + 
			'</li>'
		);
	}
}
function fetch_user(e){
	var params = 'object='+e;
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_assign_user.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$(xml).find('user').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#user_list .list').append(
					'<li id="u_'+id+'" class="user">' + 
						name + 
						'<a class="btn_move" title="添加人员" onclick="assign_user(\''+id+'\',\'curr\');"></a>' + 
					'</li>'
				);
			});
			
			set_date_input();
			set_check_user();
		}
	});
}

function assign_project(e){
	if(e=='close'){
		$('#assign_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var params = 'action=assign_'+page_name;		
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		var assign_str = '';
		$('#assign_holder #assign_list .list li').each(function(){
			var id = $(this).attr('id').replace('u_',''),
				active = ($(this).hasClass('curr'))?1:0,
				start_date = $(this).find('.date_input').val();
			
			if(active==1)assign_str += '&curr_assign[]='+id+'&curr_start_date_'+id+'='+start_date;
			else assign_str += '&pass_assign[]='+id+'&pass_start_date_'+id+'='+start_date;
		});
		params += assign_str;
		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功保存项目状态！');
					search_project();
				}
				else{
					alert(result.error);
				}	
			}
		});
	}
	else{
		var user_type = e.replace('assign_','');
		$('.assign_holder .list').html('');
		$('#assign_holder').show().setOverlay();
		fetch_user(user_type);
	}
}

function mark_receive(e){
	var params = 'action=mark_receive&project_id='+e+'&u_type=<?php print substr($page_name,0,1);?>';
	$.ajax({
		type: 'post',
		url: 'process_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				filter_search(curr_page);
			}
			else{
				show_alert(result.error);
			}	
		}
	});
}

function accept_task(e){
	if(confirm('确定对所选项目接单？')){
		var params = 'action=accept_task';
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功对所选项目接单！');
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function mark_project(e){
	var mark_type = e.replace('mark_','');
	
	if(e=='close'){
		$('#mark_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var type = '';
		switch(page_name){
			case 'project': type = 'p'; break;
			case 'sales': type = 's'; break;
			case 'technology': type = 't'; break;
			case 'finance': type = 'f'; break;
		}
		
		var params = 'action=mark_project&type='+type+'&mark_type='+$('input[name=mark_type]').val();		
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		$('.mark_table:visible select').each(function(){
			params += '&'+$(this).attr('id')+'='+$(this).val();
		});
		$('.mark_table:visible input[type=text]').each(function(){
			params += '&'+$(this).attr('id')+'='+$(this).val();
		});
		$('.mark_table:visible input[type=checkbox]').each(function(){
			if($(this).is(':checked')){
				params += '&'+$(this).attr('id')+'=1';
			}
			else{
				params += '&'+$(this).attr('id')+'=0';
			}
		});
		
		$.ajax({
			type: 'post',
			url: 'project_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功批量标记项目！');
					filter_search(curr_page);
				}
				else{
					alert(result.error);
				}	
			}
		});
	}
	else{
		$('input[name=mark_type]').val(mark_type);		
		$('.mark_table').each(function(){
			if(!$(this).hasClass(mark_type)){
				$(this).hide();
			}
			else{
				$(this).show();
			}
		});
		$('#mark_holder').show().setOverlay();	
	}
}

function rename_project(e){
	var rename_type = e.replace('rename_','');
	
	if(e=='close'){
		$('#rename_holder').hide().setOverlay();
	}
	else if(e=='save'){
		rename_type = $('input[name=rename_type]').val();
		var action_str = (rename_type=='project')?'批量重命名项目':'批量修改项目所属公司',
			alert_str = (rename_type=='project')?'注意：批量重命名项目将不能撤销！确定重命名所选项目？':'注意：批量修改项目所属公司将不能撤销！确定修改所选项目的所属公司？';
		if(alert_str){
			var params = 'action=rename_'+rename_type+'&rename='+$('.search_holder:visible input[name=rename]').val();
			if($('#all_page_project').is(':checked')){//edit all page projects
				params += '&scope=all';
				params = get_search_cond(params);
			}
			else{//edit current page projects
				$('#project_list .data_row input[type=checkbox]:checked').each(function(){
					var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
					if(params!='')params += '&';
					params += 'project_id[]='+project_id;
				});
			}
			
			$.ajax({
				type: 'post',
				url: post_url,
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功'+action_str+'！');
						filter_search(curr_page);
					}
					else{
						alert(result.error);
					}	
				}
			});
		}
	}
	else{
		$('input[name=rename_type]').val(rename_type);		
		$('#rename_holder .search_holder').each(function(){
			if($(this).attr('id').indexOf(rename_type)>=0)$(this).show();
			else $(this).hide();
		});
		$('#rename_holder').show().setOverlay();
		if(rename_type=='project')set_rename_pbase();
		if(rename_type=='company')set_rename_company();
	}
}

function clear_assign(){
	if(confirm('确定清除所选项目的派单记录？')){
		var params = 'action=clear_assign';
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功清除所选项目的派单记录！');
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function clear_accept(){
	if(confirm('确定清除所选项目的接单记录？')){
		var params = 'action=clear_accept';
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功清除所选项目的接单记录！');
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function copy_create(){
	if(confirm('确定复制创建所选项目？')){
		var params = 'action=copy_create';
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		$.ajax({
			type: 'post',
			url: 'process_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功复制创建所选项目！');
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function move_agreement(){
	if(confirm('确定复制创建所选项目？')){
		var params = 'action=move_agreement';
		if($('#all_page_project').is(':checked')){//edit all page projects
			params += '&scope=all';
			params = get_search_cond(params);
		}
		else{//edit current page projects
			$('#project_list .data_row input[type=checkbox]:checked').each(function(){
				var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
				if(params!='')params += '&';
				params += 'project_id[]='+project_id;
			});
		}
		
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功迁移所选协议！');
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function multi_edit_project(e){
	if($('#project_list .data_row input[type=checkbox]:checked').length==0){
		alert('请选择项目进行操作！');
		return false;
	}
	
	var params = '';
	params += get_sort_params(params,project_sort_arr);
	if($('#all_page_project').is(':checked')){//edit all page projects
		params += '&scope=all';
		params = get_search_cond(params);
	}
	else{//edit current page projects
		$('#project_list .data_row input[type=checkbox]:checked').each(function(){
			var project_id = $(this).parents('.data_row').attr('id').replace('project_','');
			if(params!='')params += '&';
			params += 'project_id[]='+project_id;
		});
	}
	
	if(e=='delete'){
		if(confirm('删除选定的项目将不能恢复，确定删除？')){
			show_alert('正在删除项目，请稍候 ...','load');
			
			params += '&action=delete_project';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_project').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功删除选定的项目！');
							search_project(curr_page);
						}
					}
					else{
						alert(result.error);
					}
				}
			});
		}
	}
	else if(e.indexOf('rename_')>=0){
		rename_project(e);
	}
	else if(e.indexOf('mark_')>=0){
		mark_project(e);
	}
	else if(e.indexOf('assign_')>=0){
		assign_project(e);
	}
	else if(e.indexOf('accept_')>=0){
		accept_task(e);
	}
	else if(e=='clear_assign'){
		clear_assign();
	}
	else if(e=='clear_accept'){
		clear_accept();
	}
	else if(e=='copy_create'){
		copy_create();
	}
	else if(e=='move_agreement'){
		move_agreement();
	}
	else{
		params += '&action=export_'+e;
		window.location.href = post_url+'?'+params;
	}
}

function clear_filter(){
	$('.filter_cond').remove();
	$('.list_holder input[type=checkbox]').prop('checked',false);
	$('.list_holder input[type=text]').val('');
	filter_search();
}

function del_filter_cond(id){
	$('#'+id).remove();
	filter_search();
}

function get_filter_cond(){	
	$('.list_holder').each(function(){
		var id = $(this).attr('id'),
			filter_id = id.replace('filter_','');
			
		var filter_type = '';
		switch(filter_id){
			case 'name': filter_type = '项目'; break;
			case 'company': filter_type = '公司'; break;
			case 'level': filter_type = '级别'; break;
			case 'branch': filter_type = '属地'; break;
			case 'category': filter_type = '类型'; break;
			case 'year_apply': filter_type = '年度'; break;
			case 'progress': filter_type = '进度'; break;
			case 'sign_period': filter_type = '签约'; break;
			case 'expire_period': filter_type = '到期'; break;
			case 'status_assign': filter_type = '派单'; break;
			case 'sales': filter_type = '销售'; break;
			case 'technology': filter_type = '技术'; break;
			case 'finance': filter_type = '财务'; break;
			case 'confirm': filter_type = '确认'; break;
			case 'receive': filter_type = '已收'; break;
			case 'id': filter_type = 'ID'; break;
			case 'sales_name': filter_type = '销售'; break;
		}
		
		if(filter_id=='sign_period'){
			var start_date = $('#sign_start_date').val(),
				end_date = $('#sign_end_date').val();
			
			if(start_date!='' || end_date!=''){
				$('#cond_list').append(
					'<div id="'+filter_id+'" class="filter_cond '+filter_id+'">' + 
						'<input type="hidden" name="start_date" value="'+start_date+'">' + 
						'<input type="hidden" name="end_date" value="'+end_date+'">' + 
						'<span class="type">'+filter_type+'</span>' + 
						'<span class="txt" title="'+start_date+'~'+end_date+'">'+start_date+'~'+end_date+'</span>' + 
						'<span class="del" onclick="del_filter_cond(\''+filter_id+'\');"></span>' + 
					'</div>'
				);
				$('#sign_start_date').val('');
				$('#sign_end_date').val('');
			}
		}
		else if(filter_id=='expire_period'){
			var start_date = $('#expire_start_date').val(),
				end_date = $('#expire_end_date').val();
			
			if(start_date!='' || end_date!=''){
				$('#cond_list').append(
					'<div id="'+filter_id+'" class="filter_cond '+filter_id+'">' + 
						'<input type="hidden" name="start_date" value="'+start_date+'">' + 
						'<input type="hidden" name="end_date" value="'+end_date+'">' + 
						'<span class="type">'+filter_type+'</span>' + 
						'<span class="txt" title="'+start_date+'~'+end_date+'">'+start_date+'~'+end_date+'</span>' + 
						'<span class="del" onclick="del_filter_cond(\''+filter_id+'\');"></span>' + 
					'</div>'
				);
				$('#expire_start_date').val('');
				$('#expire_end_date').val('');
			}
		}
		else{
			$('#'+id+' input[type=checkbox]').each(function(){
				if($(this).is(':checked')){
					var filter_txt = $(this).parent('label').text(),
						filter_val = $(this).val().replace(' ','_');
					
					$('#cond_list').append(
						'<div id="'+filter_id+'_'+filter_val+'" class="filter_cond '+filter_id+'">' + 
							'<input type="hidden" name="'+filter_id+'" value="'+filter_val+'">' + 
							'<span class="type">'+filter_type+'</span>' + 
							'<span class="txt" title="'+filter_txt+'">'+filter_txt+'</span>' + 
							'<span class="del" onclick="del_filter_cond(\''+filter_id+'_'+filter_val+'\');"></span>' + 
						'</div>'
					);
					$(this).prop('checked',false);
				}
			});
			
			$('#'+id+' input[type=text]').each(function(){
				if($(this).val().trim()!=''){
					var filter_txt = $(this).val().trim(),
						filter_val = $(this).val().trim().replace(' ','_');
					
					$('#cond_list').append(
						'<div id="'+filter_id+'_'+filter_val+'" class="filter_cond '+filter_id+'">' + 
							'<input type="hidden" name="'+filter_id+'" value="'+filter_val+'">' + 
							'<span class="type">'+filter_type+'</span>' + 
							'<span class="txt" title="'+filter_txt+'">'+filter_txt+'</span>' + 
							'<span class="del" onclick="del_filter_cond(\''+filter_id+'_'+filter_val+'\');"></span>' + 
						'</div>'
					);					
					$(this).val('');
				}
			});
		}
	});
}

function get_search_cond(params){
	$('#cond_list .filter_cond').each(function(){
		var type = $(this).attr('class').replace('filter_cond','').trim();
		
		if(type=='sign_period'){
			var start_date = $(this).children('input[name=start_date]').val().trim(),
				end_date = $(this).children('input[name=end_date]').val().trim();
			
			if(params)params += '&';
			if(start_date!='')params += 'sign_start_date='+start_date;
			if(params)params += '&';
			if(end_date!='')params += 'sign_end_date='+end_date;
		}
		else if(type=='expire_period'){
			var start_date = $(this).children('input[name=start_date]').val().trim(),
				end_date = $(this).children('input[name=end_date]').val().trim();
			
			if(params)params += '&';
			if(start_date!='')params += 'expire_start_date='+start_date;
			if(params)params += '&';
			if(end_date!='')params += 'expire_end_date='+end_date;
		}
		else{
			var val = $(this).children('input[name='+type+']').val().trim();
			
			if(params)params += '&';
			params += type+'[]='+encodeURIComponent(val);
		}
	});
	return params;
}

function set_project_sort(){
	$('#project_list th a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#project_list .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#project_list .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,project_sort_arr)==-1)project_sort_arr.push(sort_id);
			}
			else if($('#project_list .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#project_list .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,project_sort_arr);
			}
			else{//switch to asc
				$('#project_list .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,project_sort_arr)==-1)project_sort_arr.push(sort_id);
			}
			
			search_project();
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

function filter_search(e){
	$('.body_holder').append('<div class="loading cover"></div>');
	get_filter_cond();	
	
	var params = 'object='+page_name;	
	params = get_search_cond(params);	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_project_filter.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('.check_all').prop('checked',false);			
			<?php
			foreach($GLOBALS[$page_name.'_filter'] as $val){
				print "	$('#filter_".$val." .filter_list').html('');
						$(xml).find('".$val."').each(function(){
							var ".$val." = $(this).text().split('|'),
								key = ".$val."[0],
								val = ".$val."[1];
							
							$('#filter_".$val." .filter_list').append(
								'<li>' + 
									'<label for=\"".$val."_'+key+'\">' + 
										'<input type=\"checkbox\" id=\"".$val."_'+key+'\" value=\"'+key+'\">' + 
										val + 
									'</label>' + 
								'</li>'
							);
						});";
			}
			?>			
			search_project(e);
		}
	});
}

function search_project(e){
	var params = 'object='+page_name+'&per_page='+per_page;
	params = get_search_cond(params);
	
	if(e!=undefined){
		if(e=='find'){
			project_sort_arr.length = 0;
			$('#project_list th a i').attr('class','');
			$('#project_list th a').unbind('click');
		}
		else{
			params += '&page='+e;
		}
	}
	
	params = get_sort_params(params,project_sort_arr);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_project.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('.body_holder .loading.cover').remove();
			$('#project_list #curr_page_project').prop('checked',false);			
			$('#project_list tr.data_row').remove();
			
			$(xml).find('project_num').each(function(){
				var project_num = $(this).text();
				$('#project_count b').html(project_num);
			});
			
			$(xml).find('curr_page').each(function(){
				curr_page = parseInt($(this).text());
			});
			
			$('#project_data #pagination').html('');
			$(xml).find('page_num').each(function(){
				var page_num = parseInt($(this).text());				
				for(var p=1;p<=page_num;p++){
					var curr_class = '';
					if(curr_page==p)curr_class = 'class="curr"';
					
					if(page_num>page_span*3){
						if(p<=page_span || p>(page_num-page_span)){
							$('#project_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
						}	
						else{
							if(curr_page<=page_span){//if curr page at the front
								if(p<=curr_page+Math.floor(page_span/2)){
									$('#project_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p<page_num-page_span){
									if($('#project_data #pagination li.dot').length==0)$('#project_data #pagination').append('<li class="dot">...</li>');
								}
							}
							
							if(curr_page>page_span && curr_page<=page_num-page_span){//if curr page in the middle
								if(p>page_span && p<curr_page-Math.floor(page_span/2)){
									if($('#project_data #pagination li.dot.first').length==0)$('#project_data #pagination').append('<li class="dot first">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2) && p<=curr_page+Math.floor(page_span/2)){
									$('#project_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p>page_num-page_span-Math.floor(page_span/2)){
									if($('#project_data #pagination li.dot.second').length==0)$('#project_data #pagination').append('<li class="dot second">...</li>');
								}
							}
							
							if(curr_page>page_num-page_span){//if curr page at the end
								if(p<curr_page-Math.floor(page_span/2)){
									if($('#project_data #pagination li.dot').length==0)$('#project_data #pagination').append('<li class="dot">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2)){
									$('#project_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
							}
						}
					}
					else{
						$('#project_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
					}
				}
				
				$('#project_data #pagination li').click(function(){
					if(!$(this).hasClass('curr') && !$(this).hasClass('dot')){
						curr_page = parseInt($(this).text());
						search_project(curr_page);
					}
				});
			});
			
			var i = 0;
			$(xml).find('project').each(function(){
				var project_id = $(this).find('project_id').text(),
					receive = $(this).find('receive').text();
				<?php
				foreach($GLOBALS[$page_name.'_list_fields'] as $key=>$val){
					print "	var ".$key." = $(this).find('$key').text();
							var ".$key."_class = '';";
					if($key=="remark"){
						print "	if(remark!=''){
									if(remark.indexOf('|')>=0){
										var remark_arr = remark.split('|'),
											remark_str = '';
										for(var i in remark_arr){
											if(remark_str!='')remark_str += '\\n';
											remark_str += remark_arr[i];
										}
										remark = remark_str;
									}
									remark = '<a class=\"icon_remark\" title=\"'+remark+'\"></a>';
								}";						
					}
					else{
						print "	if(".$key.".indexOf('|')>=0){
									var ".$key."_arr = ".$key.".split('|');
									".$key." = ".$key."_arr[0];
									".$key."_class = ".$key."_arr[1];
								}";
						if($key=="technology" || $key=="finance"){
							print "	var ".substr($key,0,1)."_accepted_time = $(this).find('".substr($key,0,1)."_accepted_time').text(),
										".substr($key,0,1)."_superscript = (".substr($key,0,1)."_accepted_time!='')?'<a class=\"superscript\" title=\"'+".substr($key,0,1)."_accepted_time+'接单\"></a>':'';";
						}
						if($key=="name"){
							print "	var verify = $(this).find('verify').text(),
										verify_superscript = (verify!='')?'<a class=\"superscript\" title=\"'+verify+'\"></a>':'';";
						}
					}
				}
				?>
				
				var alt_class = '';
				if(i%2!=0)alt_class = 'alt';
				
				var receive_str = '<a class="btn_receive" <?php if($_SESSION['level']>2)print "onclick=\"mark_receive(\''+project_id+'\');\""; ?> title="标记已收"></a>';
				if(receive==1)receive_str = '<a class="btn_receive done" <?php if($_SESSION['level']>2)print "onclick=\"mark_receive(\''+project_id+'\');\""; ?> title="取消标记"></a>';
				
				$('#project_list').append(
					'<tr id="project_'+project_id+'" class="data_row '+alt_class+' '+progress_class+'">' + 
						'<td><input type="checkbox"></td>' + 						
						'<td align="center">' + 
							'<a class="btn_preview" onclick="view_project(\''+project_id+'\');" title="查看"></a>' + 
						'</td>' + 
						<?php
						if($_SESSION['level']>2)print "'<td>'+receive_str+'</td>' + ";
						
						foreach($GLOBALS[$page_name.'_list_fields'] as $key=>$val){
							$align_str = 'align="center"';
							if($key=="technology" || $key=="finance"){
								print "'<td $align_str class=\"'+".$key."_class+'\">'+$key+".substr($key,0,1)."_superscript+'</td>' + ";
							}
							else if($key=="name"){
								print "'<td $align_str class=\"'+".$key."_class+'\">'+$key+verify_superscript+'</td>' + ";
							}
							else{
								print "'<td $align_str class=\"'+".$key."_class+'\">'+$key+'</td>' + ";
							}
						}
						?>
					'</tr>'
				);
				i++;
			});
			
			set_project_sort();
			set_check_project();
			set_need_ctrl();
			$('#project_list .loading').fadeOut();
			
			if($('#all_page_project').is(':checked')){
				$('#curr_page_project').prop('checked',true);
				$('#project_list td input[type=checkbox]').prop('checked',true);
				$('#project_list tr').addClass('selected');
			}
		}
	});
}

function set_need_ctrl(){
	$('.btn_need').click(function(){
		var project_id = $(this).parents('.data_row').attr('id').replace('project_',''),
			object = '',
			type = 0;
		if($(this).hasClass('yes')){//取消需要
			object = $(this).attr('class').replace('btn_need','').replace('yes','').trim();
		}
		else{//设置需要
			object = $(this).attr('class').replace('btn_need','').replace('no','').trim();
			type = 1;
		}
		var params = 'action=set_need&u_type=<?php print substr($page_name,0,1);?>&project_id='+project_id+'&object='+object+'&type='+type;
		$.ajax({
			type: 'post',
			url: 'process_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	});
}

function set_subtab(){
	$('.tab_holder .subtab').removeClass('curr');
	$('#subtab_1').addClass('curr');
	$('.subtab_panel').hide();
	$('#subtab_panel_1').show();
	
	$('.tab_holder .subtab').click(function(){
		$('.tab_holder .subtab').removeClass('curr');
		$('.subtab_panel').hide();
		
		var tab_id = $(this).attr('id').replace('subtab_','');
		$(this).addClass('curr');		
		$('#subtab_panel_'+tab_id).show();
	});
}

function display_sidebar(){
	$('.sidebar').each(function(){
		if($(this).hasClass('hide')){
			$(this).removeClass('hide');
			$('#hide_arrow').removeClass('hide');
		}
		else{
			$(this).addClass('hide');
			$('#hide_arrow').addClass('hide');
		}
	});
}
</script>
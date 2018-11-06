<!--VIEW PROJECT HOLDER-->
<div id="view_project_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_project('close');"></a>				
			</td>
		</tr>
		<tr>
			<td>
<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
	<?php include('public_project_tab.php');?>
	<tr valign="top" id="subtab_panel_1" class="subtab_panel">
		<td>
			<form id="project_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
				<input type="hidden" name="action">
				<input type="hidden" name="id">
				<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="form_holder project">
							<div class="scroll_holder">
								<a class="top_anchor"></a>
								<table class="project_holder project" cellpadding="5" cellspacing="0">
									<tr>
										<td class="title required">项目名称</td>
										<td colspan="5">
											<div id="project_holder" class="search_holder">
												<input type="text" id="v_name" name="v_name" class="ex-long">
												<ul id="pbase_list" class="result_list"></ul>
											</div>
										</td>
									</tr>
									<tr>
										<td class="title required">公司名称</td>
										<td colspan="5">
											<div id="company_holder" class="search_holder">
												<input type="text" id="v_company" name="v_company" class="ex-long">
												<ul id="company_list" class="result_list"></ul>
											</div>
										</td>
									</tr>
									<tr>
										<td class="title">级别</td>
										<td>
											<select id="v_level" name="v_level">
											<?php
											foreach($GLOBALS['project_level_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title required">属地</td>
										<td>
											<select id="v_branch" name="v_branch">
											<?php
											$sql = "SELECT id,name FROM branch ORDER BY id";
											$stmt = mysql_query($sql);
											if(mysql_num_rows($stmt)>0){
												while($row = mysql_fetch_array($stmt)){
													print '<option value="'.$row[0].'">'.$row[1].'</option>';
												}
											}
											?>
											</select>
										</td>
										<td class="title required">类型</td>
										<td>
											<select id="v_category" name="v_category">
											<?php
											foreach($GLOBALS['project_category_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="title required">申报年度</td>
										<td>
											<select id="v_year_apply" name="v_year_apply">
											<?php
											for($y=date('Y')+5;$y>=2013;$y--){
												print '<option value="'.$y.'">'.$y.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title required">签约日期</td>
										<td>
											<input type="text" name="v_date_sign" id="v_date_sign" class="date_input">
										</td>
										<td class="title">合同到期</td>
										<td>
											<input type="text" name="v_date_expire" id="v_date_expire" class="date_input">
										</td>
									</tr>
									<tr>
										<td class="title required">项目总进度</td>
										<td>
											<select id="v_progress" name="v_progress">
											<?php
											foreach($GLOBALS['project_progress_opt'] as $key=>$val){
												$opt_class = '';
												if($key==5 || $key==6 || $key==7)$opt_class = 'class="opt_need_approve"';
												if($key==8 || $key==9)$opt_class = 'class="opt_need_check"';
												if($key==10 || $key==11)$opt_class = 'class="opt_need_fund"';
												print '<option value="'.$key.'" '.$opt_class.'>'.$val.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title">服务外包</td>
										<td colspan="3">
											<label for="v_outsource_t">
												<input type="checkbox" id="v_outsource_t" name="v_outsource[]" value="t">
												无需安排技术人员
											</label>
											<label for="v_outsource_f">
												<input type="checkbox" id="v_outsource_f" name="v_outsource[]" value="f">
												无需安排财务人员
											</label>
										</td>
									</tr>
									<tr>	
										<td class="title required">需要立项？</td>
										<td>
											<select id="v_need_approve" name="v_need_approve">
											<?php
											foreach($GLOBALS['need_approve_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title required">需要验收？</td>
										<td>
											<select id="v_need_check" name="v_need_check">
											<?php
											foreach($GLOBALS['need_check_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title required">需要请款？</td>
										<td>
											<select id="v_need_fund" name="v_need_fund">
											<?php
											foreach($GLOBALS['need_fund_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td id="remark_title" class="title">
											项目备注
										</td>
										<td colspan="5">
											<input type="text" id="v_append_remark" name="v_append_remark" class="ex-long" placeholder="备注只增加，不删除，项目取消必须备注原因">
										</td>
									</tr>
									<?php if($_SESSION['level']>2){ ?>
									<tr>
										<th colspan="6">财务信息</th>
									</tr>
									<tr>
										<td colspan="6" style="text-align:center;color:#ff0000;">
											注意：以下金额项目，可填整数或小数，单位“万元”；也可填合同金额的百分比，含“%”符号。
										</td>
									</tr>
									<tr>
										<td class="title">付费类型</td>
										<td>
											<select id="v_pay_type" name="v_pay_type">
											<?php
											foreach($GLOBALS['project_pay_type_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title">签约金额</td>
										<td colspan="3">
											<input type="text" name="v_amt_contract" id="v_amt_contract" class="ex-short number">
										</td>
									</tr>
									<tr>
										<td class="title">预付款</td>
										<td>
											<input type="text" name="v_amt_prepay" id="v_amt_prepay" class="ex-short number">
										</td>
										<td class="title">实际收款</td>
										<td colspan="3">
											<input type="text" name="v_amt_actual" id="v_amt_actual" class="ex-short number">
										</td>
									</tr>
									<tr>
										<td class="title">辛苦费</td>
										<td>
											<input type="text" name="v_amt_commission" id="v_amt_commission" class="ex-short number">
										</td>
										<td class="title">中间人</td>
										<td colspan="3">
											<input type="text" name="v_agent" id="v_agent" class="mid">
										</td>
									</tr>
									<tr>
										<td class="title">财务备注</td>
										<td colspan="5">
											<input type="text" id="v_finance_remark" name="v_finance_remark" class="ex-long" placeholder="备注签约金额构成、代理费构成等">
										</td>
									</tr>
									<?php } ?>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
	<?php include('public_project_tab_panel.php');?>
</table>
			</td>
		</tr>
		<?php if($_SESSION['auth_project']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="view_project('save');">
					保存项目
				</button>
				<button class="ctrl_btn active add" onclick="view_project('add');">
					添加项目
				</button>
				<button class="ctrl_btn verify" onclick="view_project('verify');">
					审核无误
				</button>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>

<script>
$(function() {
	set_search_pbase();
	set_search_company();
});

function set_search_pbase(){
	$('#v_name').keyup(function(){
		var keyword = $(this).val();
		if(keyword==''){
			$('#pbase_list').html('');
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
					$('#pbase_list').html('');
					$(xml).find('result').each(function(){
						var project = $(this).text();
						$('#pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#v_name\').val(\''+project+'\');$(\'#pbase_list\').html(\'\');">' + 
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
			$('#pbase_list').html('');
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
					$('#pbase_list').html('');
					$(xml).find('result').each(function(){
						var project = $(this).text();
						$('#pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#v_name\').val(\''+project+'\');$(\'#pbase_list\').html(\'\');">' + 
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
			$('#pbase_list').html('');
		},300);
	});
}

function set_search_company(){
	$('#v_company').keyup(function(){
		var keyword = $(this).val();
		if(keyword==''){
			$('#company_list').html('');
		}
		else{
			var params = 'object=company&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#company_list').html('');
					$(xml).find('result').each(function(){
						var company = $(this).text();
						$('#company_list').append(
							'<li>' + 
								'<a onclick="$(\'#v_company\').val(\''+company+'\');$(\'#company_list\').html(\'\');">' + 
									company + 
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
			$('#company_list').html('');
		}
		else{
			var params = 'object=company&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#company_list').html('');
					$(xml).find('result').each(function(){
						var company = $(this).text();
						$('#company_list').append(
							'<li>' + 
								'<a onclick="$(\'#v_company\').val(\''+company+'\');$(\'#company_list\').html(\'\');">' + 
									company + 
								'</a>' + 
							'</li>'
						);
					});
				}
			});
		}
	}).blur(function(){
		setTimeout(function(){
			$('#company_list').html('');
		},300);
	});
}

function set_rename_pbase(){
	$('#r_name').keyup(function(){
		var keyword = $(this).val();
		if(keyword==''){
			$('#r_pbase_list').html('');
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
					$('#r_pbase_list').html('');
					$(xml).find('result').each(function(){
						var project = $(this).text();
						$('#r_pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#r_name\').val(\''+project+'\');$(\'#r_pbase_list\').html(\'\');">' + 
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
			$('#r_pbase_list').html('');
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
					$('#r_pbase_list').html('');
					$(xml).find('result').each(function(){
						var project = $(this).text();
						$('#r_pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#r_name\').val(\''+project+'\');$(\'#r_pbase_list\').html(\'\');">' + 
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
			$('#r_pbase_list').html('');
		},300);
	});
}

function set_rename_company(){
	$('#r_company').keyup(function(){
		var keyword = $(this).val();
		if(keyword==''){
			$('#r_company_list').html('');
		}
		else{
			var params = 'object=company&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#r_company_list').html('');
					$(xml).find('result').each(function(){
						var company = $(this).text();
						$('#r_company_list').append(
							'<li>' + 
								'<a onclick="$(\'#r_company\').val(\''+company+'\');$(\'#r_company_list\').html(\'\');">' + 
									company + 
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
			$('#r_company_list').html('');
		}
		else{
			var params = 'object=company&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#r_company_list').html('');
					$(xml).find('result').each(function(){
						var company = $(this).text();
						$('#r_company_list').append(
							'<li>' + 
								'<a onclick="$(\'#r_company\').val(\''+company+'\');$(\'#r_company_list\').html(\'\');">' + 
									company + 
								'</a>' + 
							'</li>'
						);
					});
				}
			});
		}
	}).blur(function(){
		setTimeout(function(){
			$('#r_company_list').html('');
		},300);
	});
}
</script>
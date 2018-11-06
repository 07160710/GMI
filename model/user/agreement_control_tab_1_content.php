<!--VIEW PROJECT HOLDER-->
<div id="view_agreement_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_agreement('close');"></a>				
			</td>
		</tr>
		<tr>
			<td>
<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td class="tab_container" height="35">
			<ul class="tab_holder sub">
				<li><a id="subtab_1" class="subtab curr">详情</a></li>
				<li><a id="subtab_2" class="subtab">日志</a></li>
				<div id="tab_line"></div>
			</ul>
		</td>
	</tr>
	<tr valign="top" id="subtab_panel_1" class="subtab_panel">
		<td>
			<form id="agreement_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
				<input type="hidden" name="action">
				<input type="hidden" name="id">
				<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="form_holder agreement">
							<div class="scroll_holder">
								<a class="top_anchor"></a>
								<table class="agreement_holder agreement" cellpadding="5" cellspacing="0">
									<tr>
										<td class="title required">协议名称</td>
										<td colspan="3">
											<div id="agreement_holder" class="search_holder">
												<input type="text" id="v_name" name="v_name" class="ex-long">
												<ul id="pbase_list" class="result_list"></ul>
											</div>
										</td>
									</tr>
									<tr>
										<td class="title required">公司名称</td>
										<td colspan="3">
											<div id="company_holder" class="search_holder">
												<input type="text" id="v_company" name="v_company" class="ex-long">
												<ul id="company_list" class="result_list"></ul>
											</div>
										</td>
									</tr>
									<tr>
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
										<td class="title required">负责销售</td>
										<td>
											<select id="v_sales_id" name="v_sales_id">
											<?php
											$sql = "SELECT id,name FROM user WHERE role LIKE '%ss%' OR role LIKE '%sm%' ORDER BY CONVERT(name USING GBK)";
											$stmt = mysql_query($sql);
											if(mysql_num_rows($stmt)>0){
												while($row = mysql_fetch_array($stmt)){
													print '<option value="'.$row[0].'">'.$row[1].'</option>';
												}
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
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
										<td id="remark_title" class="title">
											协议备注
										</td>
										<td colspan="3">
											<input type="text" id="v_append_remark" name="v_append_remark" class="ex-long" placeholder="备注只增加，不删除，协议取消必须备注原因">
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
	<tr valign="top" id="subtab_panel_2" class="subtab_panel">
		<td>
			<div class="scroll_holder">
				<table width="99%" id="log_holder" border="0" cellpadding="3" cellspacing="0">
					<tr class="header_row">
						<th width="100">用户</th>
						<th width="100">类别</th>
						<th>操作</th>
						<th width="200">记录时间</th>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
			</td>
		</tr>
		<?php if($_SESSION['auth_agreement']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="view_agreement('save');">
					保存协议
				</button>
				<button class="ctrl_btn active add" onclick="view_agreement('add');">
					添加协议
				</button>
				<button class="ctrl_btn verify" onclick="view_agreement('verify');">
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
			var params = 'object=agreement&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#pbase_list').html('');
					$(xml).find('result').each(function(){
						var agreement = $(this).text();
						$('#pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#v_name\').val(\''+agreement+'\');$(\'#pbase_list\').html(\'\');">' + 
									agreement + 
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
			var params = 'object=agreement&keyword='+keyword;
			$.ajax({
				data: 	params,
				type:		'GET',
				url: 		'fetch_keyword_result.php',
				dataType:	'xml',
				async:		false,
				success: function (xml) {
					$('#pbase_list').html('');
					$(xml).find('result').each(function(){
						var agreement = $(this).text();
						$('#pbase_list').append(
							'<li>' + 
								'<a onclick="$(\'#v_name\').val(\''+agreement+'\');$(\'#pbase_list\').html(\'\');">' + 
									agreement + 
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

function fetch_log(e){
	var params = 'target=<?php print $page_name;?>&object_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_log.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			var is_verified = 0;
			$('#log_holder .data_row').remove();
			$(xml).find('log').each(function(){
				var user = $(this).find('user').text(),
					object = $(this).find('object').text(),
					content = $(this).find('content').text(),
					log_time = $(this).find('log_time').text(),
					row_class = '';
				if(content.indexOf('审核')>=0){
					is_verified = 1;
					row_class = 'verify';
				}
				$('#log_holder').append(
					'<tr class="data_row '+row_class+'">' + 
						'<td>'+user+'</td>' + 
						'<td>'+object+'</td>' + 
						'<td>'+content+'</td>' + 
						'<td>'+log_time+'</td>' + 
					'</tr>'
				);
			});
			if(is_verified==0)$('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.verify').show();
			else $('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.verify').hide();
		}
	});
}

function fetch_agreement_info(e){
	var params = 'agreement_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_agreement_info.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			var agreement_name = '',
				company_name = '';
			<?php
			foreach($GLOBALS['agreement_fields'] as $key){
				if($key=="remark"){
					print "	var remark_str = '';
							$(xml).find('remark').each(function(){
								var remark = $(this).text(),
									remark_arr = remark.split('|');
								for(var i in remark_arr){
									if(remark_str!='')remark_str += '\\n';
									remark_str += remark_arr[i];
								}
							});
							if(remark_str!='')$('#remark_title').prepend('<a id=\"v_remark\" class=\"icon_remark\" title=\"'+remark_str+'\"></a>');";
				}
				else{
					if($key=="company_id"){
						print "	$(xml).find('company').each(function(){
									var company = $(this).text();
									$('#v_company').val(company);
									company_name = company;
								});";
					}
					if($key=="name"){
						print "	$(xml).find('name').each(function(){
									var name = $(this).text();
									$('#v_name').val(name);
									agreement_name = name;
								});";
					}
					
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								$('#v_".$key."').val($key);
							});";
				}
			}
			foreach($GLOBALS['project_finance_fields'] as $key){
				print "	$(xml).find('".$key."').each(function(){
							var ".$key." = $(this).text();
							$('#v_".$key."').val($key);
						});";
			}
			?>
			fetch_log(e);
			
			$('#view_agreement_holder .header_holder .title').text('查看协议 ['+company_name+': '+agreement_name+']');
			$('input[name=action]').val('edit');
			$('input[name=id]').val(e);
			$('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.add').hide();
			$('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.save').show();			
			$('#view_agreement_holder').show().setOverlay();
		}
	});
}

function view_agreement(e){
	if(e=='close'){
		$('#view_agreement_holder').hide().setOverlay();
	}
	else if(e=='add'){
		if(check_form()){
			var params = $('#agreement_form').serialize();
			$.ajax({
				type: 'post',
				url: 'agreement_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功添加协议！');
						filter_search(curr_page);
					}
					else{
						alert(result.error);
					}	
				}
			});
		}
	}
	else if(e=='save'){
		if(check_form()){
			var params = $('#agreement_form').serialize();			
			$.ajax({
				type: 'post',
				url: page_name+'_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						alert('成功保存协议信息！');
						view_agreement(ctrl_id);
						filter_search(curr_page);
					}
					else{
						alert(result.error);
					}	
				}
			});
		}
	}
	else if(e=='verify'){
		if(confirm('通过审核前请确保协议资料准确无误，通过资料审核？')){
			var params = 'action=verify&id='+$('input[name=id]').val();
			$.ajax({
				type: 'post',
				url: 'agreement_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('协议信息通过审核！');
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
		set_subtab();
		<?php
		foreach($GLOBALS['agreement_fields'] as $key){
			if($key=="company_id")print "$('#v_company').val('');";
			else print "$('#v_".$key."').val('');";
		}
		foreach($GLOBALS['project_finance_fields'] as $key){
			print "$('#v_".$key."').val('');";
		}
		?>
		$('#v_remark').remove();
		$('#v_append_remark').val('');
		$('.assign_holder .list').html('');
		
		if(e=='create'){//create agreement
			$('#view_agreement_holder .header_holder .title').text('添加协议');
			$('input[name=action]').val('add');
			$('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.add').show();
			$('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.save').hide();
			$('#view_agreement_holder .ctrl_btn_holder .ctrl_btn.verify').hide();
			$('#view_agreement_holder').show().setOverlay();
		}
		else{//view agreement
			ctrl_id = e;
			fetch_agreement_info(e);
		}
	}
}

function check_form(){
	var check_pass = true;
	$('#agreement_form .title.required').each(function(){
		var input_title = $(this).text(),
			input_field = '';
		
		input_field = $(this).next('td').find('input[type=text],select');
		
		var input_val = input_field.val();
		if(input_val==undefined || input_val==null || input_val==''){
			alert(input_title+'不能为空');
			check_pass = false;
		}
	});
	
	$('input[class*=number]').each(function(){
		var tmp_val = $(this).val(),
			tmp_title = $(this).parent('td').prev('td.title').text(),
			regexp_1 = /^\d+(\.\d+)?$/,
			regexp_2 = /^\d+%$/,
			regexp_3 = /^\d+(\.\d+)%$/,
			regexp_4 = /^\d+[+]+\d+%$/,
			regexp_5 = /^\d+(\.\d+)+[+]+\d+%$/,
			regexp_6 = /^\d+[+]+\d+[+]+\d+%$/
			regexp_7 = /^\d+(\.\d+)+[+]+\d+(\.\d+)+[+]+\d+%$/;
		if(tmp_val!='' && !regexp_1.test(tmp_val) && !regexp_2.test(tmp_val) && !regexp_3.test(tmp_val) && !regexp_4.test(tmp_val) && !regexp_5.test(tmp_val) && !regexp_6.test(tmp_val) && !regexp_7.test(tmp_val)){
			alert(tmp_title+'只能是纯数字、百分比，或数字+百分比！');
			check_pass = false;
		}
	});
	
	return check_pass;
}
</script>
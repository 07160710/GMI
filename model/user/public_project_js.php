<script>
$(function(){
	var raw_progress = 0;
	$('#v_progress').focus(function(){
		raw_progress = $(this).val();
	}).change(function(){
		if($(this).val()>0 && $(this).val()<6){
			show_alert('对不起，你不能选择此进度，请在“流程”标签里面处理。','close');
			$(this).val(raw_progress);
		}
	});

});

function request_help(e, u_type=''){
	if(e=='close'){
		$('#request_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var content = $('textarea[name=request_content]').val();
		if(content==''){
			alert('请求内容不能为空！');
			return false;
		}
		
		show_alert('正在发送请求，请稍候 ...','load');		
		var params = 'action=request_help&project_id='+$('#request_holder input[name=project_id]').val()+'&u_type='+$('#request_holder input[name=u_type]').val()+'&content='+encodeURIComponent(content);
		$.ajax({
			type: 'post',
			url: 'process_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert('成功发送协助请求！','close');
					request_help('close');
					fetch_project_info(ctrl_id);
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
	else{
		$('#request_holder input[name=project_id]').val(e);
		$('#request_holder input[name=u_type]').val(u_type);
		$('#request_holder textarea[name=requet_content]').val('');
		$('#request_holder').show().setOverlay();
	}
}

function process_task(project_id, key, option=''){
	var key_arr = key.split('_'),
		u_type = key_arr[1],
		type = key_arr[0]+((key_arr[2]!=undefined)?'_'+key_arr[2]:''),
		date = $('#'+key+'_date').val();
	var action = '';
	switch(type){
		case 'accept':action = '完成项目接单';break;
		case 'apply_o':action = '完成材料收集';break;
		case 'apply_d':action = '完成项目交稿';break;
		case 'apply_e':
			if(option==1)action = '完成电子材料提交';
			if(option==2)action = '标记免交电子材料';
			break;
		case 'apply_p':
			if(option==1)action = '完成纸质材料提交';
			if(option==2)action = '标记免交纸质材料';
			break;
		case 'reset_o':action = '重置材料收集日期';break;
		case 'reset_d':action = '重置项目交稿日期';break;
		case 'reset_e':action = '重置电子材料提交日期';break;
		case 'reset_p':action = '重置纸质材料提交日期';break;
		case 'reset_c':action = '重置申报确认';break;
		case 'confirm':action = '完成申报确认';break;
	}
	if(confirm('确定'+action+'？')){
		var params = 'action=process_task&project_id='+project_id+'&u_type='+u_type+'&type='+type+'&option='+option+'&date='+date;
		$.ajax({
			type: 'post',
			url: 'process_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					alert('成功'+action+'！');
					fetch_project_process(ctrl_id);
					fetch_log(ctrl_id);
					fetch_platform(ctrl_id);
					filter_search(curr_page);
				}
				else{
					show_alert(result.error);
				}	
			}
		});
	}
}

function fetch_project_process(e){
	var params = 'project_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_project_process.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('#process_holder .data').html('');
			
			$('.data.assign_s').html($(xml).find('assign_s').text());
			$('.data.assign_t').html($(xml).find('assign_t').text());
			$('.data.assign_f').html($(xml).find('assign_f').text());
			
			$('.data.accept_s').html($(xml).find('accept_s').text());
			
			//技术接单
			var accept_t = 0;
			if($(xml).find('accept_t').length>0){
				accept_t = 1;
				$('.data.accept_t').html($(xml).find('accept_t').text());
			}
			else{
				if($(xml).find('assign_t').length>0){
					if(page_name=='technology')$('.data.accept_t').html(
						'<button class="process_btn" onclick="process_task(\''+e+'\',\'accept_t\');">接单</button>'
					);
				}
			}
			//技术申报
			var apply_t_o = 0;
			if($(xml).find('apply_t_o').length>0){
				apply_t_o = 1;
				$('.data.apply_t_o').html($(xml).find('apply_t_o').text());
				if(page_name=='technology')$('.data.apply_t_o').append('<button class="process_btn reset_t" onclick="process_task(\''+e+'\',\'reset_t_o\',\'0\');">重置</button>');
			}
			else{
				if(accept_t==1){
					var request_str = '';
					if($(xml).find('apply_t_request').length>0){
						$('.data.apply_t_o').append($(xml).find('apply_t_request').text());
					}
					else{
						request_str = '<button class="process_btn active" onclick="request_help(\''+e+'\',\'t\');">需协助</button>';
					}
					if(page_name=='technology'){
						$('.data.apply_t_o').append(
							request_str + 
							'<input type="text" class="date_input" id="apply_t_o_date" value="<?php print date('Y-m-d');?>">' + 
							'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_t_o\',\'1\');">保存</button>'
						);
					}
				}
			}
			
			var apply_t_d = 0;
			if($(xml).find('apply_t_d').length>0){
				apply_t_d = 1;
				$('.data.apply_t_d').html($(xml).find('apply_t_d').text());
				if(page_name=='technology')$('.data.apply_t_d').append('<button class="process_btn reset_t" onclick="process_task(\''+e+'\',\'reset_t_d\',\'0\');">重置</button>');
			}
			else{
				if(page_name=='technology' && apply_t_o==1)$('.data.apply_t_d').html(
					'<input type="text" class="date_input" id="apply_t_d_date" value="<?php print date('Y-m-d');?>">' + 
					'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_t_d\',\'1\');">保存</button>'
				);
			}
			
			$('.data.apply_deadline_e').html($(xml).find('apply_deadline_e').text());
			$('.data.apply_deadline_p').html($(xml).find('apply_deadline_p').text());
			
			var apply_t_e = 0;
			if($(xml).find('apply_t_e').length>0){
				apply_t_e = 1;
				$('.data.apply_t_e').html($(xml).find('apply_t_e').text());
				if(page_name=='technology')$('.data.apply_t_e').append('<button class="process_btn reset_t" onclick="process_task(\''+e+'\',\'reset_t_e\',\'0\');">重置</button>');
			}
			else{
				if(page_name=='technology' && apply_t_d==1)$('.data.apply_t_e').html(
					'<button class="process_btn active" onclick="process_task(\''+e+'\',\'apply_t_e\',\'2\');">免提交</button>' + 
					'<input type="text" class="date_input" id="apply_t_e_date" value="<?php print date('Y-m-d');?>">' + 
					'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_t_e\',\'1\');">保存</button>'
				);
			}
			
			var apply_t_p = 0;
			if($(xml).find('apply_t_p').length>0){
				apply_t_p = 1;
				$('.data.apply_t_p').html($(xml).find('apply_t_p').text());
				if(page_name=='technology')$('.data.apply_t_p').append('<button class="process_btn reset_t" onclick="process_task(\''+e+'\',\'reset_t_p\',\'0\');">重置</button>');
			}
			else{
				if(page_name=='technology' && apply_t_e==1)$('.data.apply_t_p').html(
					'<button class="process_btn active" onclick="process_task(\''+e+'\',\'apply_t_p\',\'2\');">免提交</button>' + 
					'<input type="text" class="date_input" id="apply_t_p_date" value="<?php print date('Y-m-d');?>">' + 
					'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_t_p\',\'1\');">保存</button>'
				);
			}
			
			//财务接单
			var accept_f = 0;
			if($(xml).find('accept_f').length>0){
				accept_f = 1;
				$('.data.accept_f').html($(xml).find('accept_f').text());
			}
			else{
				if($(xml).find('assign_f').length>0){
					if(page_name=='finance')$('.data.accept_f').html(
						'<button class="process_btn" onclick="process_task(\''+e+'\',\'accept_f\');">接单</button>'
					);
				}
			}
			//财务申报
			var apply_f_o = 0;
			if($(xml).find('apply_f_o').length>0){
				apply_f_o = 1;
				$('.data.apply_f_o').html($(xml).find('apply_f_o').text());
				if(page_name=='finance')$('.data.apply_f_o').append('<button class="process_btn reset_f" onclick="process_task(\''+e+'\',\'reset_f_o\',\'0\');">重置</button>');
			}
			else{
				if(accept_f==1){
					var request_str = '';
					if($(xml).find('apply_f_request').length>0){
						$('.data.apply_f_o').append($(xml).find('apply_f_request').text())
					}
					else{
						request_str = '<button class="process_btn active" onclick="request_help(\''+e+'\',\'f\');">需协助</button>';
					}
					if(page_name=='finance'){
						$('.data.apply_f_o').append(
							request_str + 
							'<input type="text" class="date_input" id="apply_f_o_date" value="<?php print date('Y-m-d');?>">' + 
							'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_f_o\',\'1\');">保存</button>'
						);
					}
				}
			}
			
			var apply_f_d = 0;
			if($(xml).find('apply_f_d').length>0){
				apply_f_d = 1;
				$('.data.apply_f_d').html($(xml).find('apply_f_d').text());
				if(page_name=='finance')$('.data.apply_f_d').append('<button class="process_btn reset_f" onclick="process_task(\''+e+'\',\'reset_f_d\',\'0\');">重置</button>');
			}
			else{
				if(page_name=='finance' && apply_f_o==1)$('.data.apply_f_d').html(
					'<input type="text" class="date_input" id="apply_f_d_date" value="<?php print date('Y-m-d');?>">' + 
					'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_f_d\',\'1\');">保存</button>'
				);
			}
			
			var apply_f_e = 0;
			if($(xml).find('apply_f_e').length>0){
				apply_f_e = 1;
				$('.data.apply_f_e').html($(xml).find('apply_f_e').text());
				if(page_name=='finance')$('.data.apply_f_e').append('<button class="process_btn reset_f" onclick="process_task(\''+e+'\',\'reset_f_e\',\'0\');">重置</button>');
			}
			else{
				if(page_name=='finance' && apply_f_d==1)$('.data.apply_f_e').html(
					'<button class="process_btn active" onclick="process_task(\''+e+'\',\'apply_f_e\',\'2\');">免提交</button>' + 
					'<input type="text" class="date_input" id="apply_f_e_date" value="<?php print date('Y-m-d');?>">' + 
					'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_f_e\',\'1\');">保存</button>'
				);
			}
			
			var apply_f_p = 0;
			if($(xml).find('apply_f_p').length>0){
				apply_f_p = 1;
				$('.data.apply_f_p').html($(xml).find('apply_f_p').text());
				if(page_name=='finance')$('.data.apply_f_p').append('<button class="process_btn reset_f" onclick="process_task(\''+e+'\',\'reset_f_p\',\'0\');">重置</button>');
			}
			else{
				if(page_name=='finance' && apply_f_e==1)$('.data.apply_f_p').html(
					'<button class="process_btn active" onclick="process_task(\''+e+'\',\'apply_f_p\',\'2\');">免提交</button>' + 
					'<input type="text" class="date_input" id="apply_f_p_date" value="<?php print date('Y-m-d');?>">' + 
					'<button class="process_btn" onclick="process_task(\''+e+'\',\'apply_f_p\',\'1\');">保存</button>'
				);
			}
			
			//技术主管确认
			var apply_t_c = 0;
			if($(xml).find('apply_t_c').length>0){
				apply_t_c = 1;
				//$('.process_btn.reset_t').remove();
				$('.data.apply_t_c').html($(xml).find('apply_t_c').text());
				<?php if(strpos($_SESSION['role'],"tm")!==false){ ?>
				if(page_name=='project' || page_name=='technology')$('.data.apply_t_c').append('<button class="process_btn reset_f" onclick="process_task(\''+e+'\',\'reset_t_c\',\'0\');">重置</button>');
				<?php } ?>
			}
			else{
				if((page_name=='project' || page_name=='technology') && apply_t_p==1){
					<?php if(strpos($_SESSION['role'],"tm")!==false){ ?>
					$('.data.apply_t_c').html(
						'<button class="process_btn confirm" onclick="process_task(\''+e+'\',\'confirm_t\');">主管确认</button>'
					);
					<?php } ?>
				}
			}
			//财务主管确认
			var apply_f_c = 0;
			if($(xml).find('apply_f_c').length>0){
				apply_f_c = 1;
				//$('.process_btn.reset_f').remove();
				$('.data.apply_f_c').html($(xml).find('apply_f_c').text());
				<?php if(strpos($_SESSION['role'],"fm")!==false){ ?>
				if(page_name=='project' || page_name=='finance')$('.data.apply_f_c').append('<button class="process_btn reset_f" onclick="process_task(\''+e+'\',\'reset_f_c\',\'0\');">重置</button>');
				<?php } ?>
			}
			else{
				if((page_name=='project' || page_name=='finance') && apply_f_p==1){
					<?php if(strpos($_SESSION['role'],"fm")!==false){ ?>
					$('.data.apply_f_c').html(
						'<button class="process_btn confirm" onclick="process_task(\''+e+'\',\'confirm_f\');">主管确认</button>'
					);
					<?php } ?>
				}
			}
			//待立项
			if($(xml).find('approve').length>0){
				var approve_arr = $(xml).find('approve').text().split('|'),
					status_approve = approve_arr[0],
					remark = approve_arr[1];
				$('.process_btn.reset_f').remove();
				$('.data.project_approve').append('<span class="reamrk">'+remark+'</span>'); 
				if(status_approve=='1'){
					$('.title.status_approve').html('立项成功');
				}else if(status_approve=='2'){
					$('.title.status_approve').html('立项失败');
					return;
				}else{
					$('.title.status_approve').html('待验收');
				}
			}else{ //立项未标记
				var h_approve = '<button class="progress success ctrl_btn sub progress" onclick="project_action(\'approve_success\');">立项成功</button>'+
				'<button  class="progress error ctrl_btn sub progress" onclick="project_action(\'approve_fail\');">立项失败</button>';
				$('.data.project_approve').html(h_approve);
				return;
			}
			//第一次验收
			if($(xml).find('check1').length>0){
				var check1_arr = $(xml).find('check1').text().split('|'),
					status = check1_arr[0],
					remark = check1_arr[1];
				if(status==2){
					remark = '第一次验收失败，'+remark;
				}else{
					remark = '第一次验收成功，'+remark;
				}
				$('.data.acceptance_check1').append('<span class="reamrk">'+remark+'</span>'); 
				$('.process_btn.reset_f').remove();
				// $('.title.status_approve').html(status);
			}else{
				var h_check1 = '<button class="progress success ctrl_btn sub progress" onclick="project_action(\'check1_success\');" >验收通过</button>'+
				'<button  class="progress error ctrl_btn sub progress" onclick="project_action(\'check1_fail\');">验收失败</button>';
				$('.data.acceptance_check1').html(h_check1);
				return;
			}
			//第二次验收
			if($(xml).find('check2').length>0){
				var check2_arr = $(xml).find('check2').text().split('|'),
					status = check2_arr[0],
					remark = check2_arr[1];
				if(status==2){
					remark = '第二次验收失败，'+remark;
					return;
				}else{
					remark = '第二次验收成功，'+remark;
				}
				$('.process_btn.reset_f').remove();
				// $('.title.status_approve').html(status);
				$('.data.acceptance_check2').append('<span class="reamrk">'+remark+'</span>'); 
			}else{
				var h_check2 = '<button class="progress success ctrl_btn sub progress" onclick="project_action(\'check2_success\');">验收通过</button>'+
				'<button  class="progress error ctrl_btn sub progress" onclick="project_action(\'check2_fail\');">验收失败</button>';
				$('.data.acceptance_check2').html(h_check2);
				return;
			}

			//请款
			if($(xml).find('request_fund').length>0){
				var request_fundValue = $(xml).find('request_fund').text();
				// $('.process_btn.reset_f').remove();
				// $('.title.status_approve').html(status);
				$('.data.request_fund').append('<span class="reamrk">'+request_fundValue+'</span>'); 
			}else{
				var h_request_fund = '<button class="ctrl_btn sub progress" onclick="fund_action(\'request_fund\');">已请款</button>';
				$('.data.request_fund').html(h_request_fund);
				return;
			}

			//企业收款
			if($(xml).find('receive_fund').length>0){
				var receive_fundValue = $(xml).find('receive_fund').text();
				// $('.process_btn.reset_f').remove();
				// $('.title.status_approve').html(status);
				$('.data.receive_fund').append('<span class="reamrk">'+receive_fundValue+'</span>'); 
			}else{
				var h_receive_fund = '<button class="ctrl_btn sub progress" onclick="fund_action(\'receive_fund\');">企业已收款</button>';
				$('.data.receive_fund').html(h_receive_fund);
				return;
			}

			//中科回款
			if($(xml).find('receive_fee').length>0){
				var receive_feeValue = $(xml).find('receive_fee').text();
				$('.data.receive_fee').append('<span class="reamrk">'+receive_feeValue+'</span>'); 
			}else{
				var h_receive_fee = '<button class="ctrl_btn sub progress" onclick="fund_action(\'receive_fee\');">已回款</button>';
				$('.data.receive_fee').html(h_receive_fee);
			}
			
			set_date_input();
		}
	});
}
// function confirm_fund(){
//     $("#subtab_panel_2").on("click",'input[name=request_fund]',function(){
//     	if(confirm('是否确认为已请款？')){
//     		var data = 'action=action_fund&type=request_fund';
//     		$.ajax({
//     			url:post_url,
//     			data:data,
//     			type:'post',
//     			dataType:'json',
//     			success:function(result){

//     			}
//     		})
//     	}
//     })
//     $("#subtab_panel_2").on("click",'input[name=receive_fund]',function(){
//     	if(confirm('是否确认为企业已收款？')){
//     		alert('程序未完善，请稍后重试！QAQ');
//     	}
//     })
//     $("#subtab_panel_2").on("click",'input[name=receive_fee]',function(){
//     	if(confirm('是否确认为已回款？')){
//     		alert('程序未完善，请稍后重试！QAQ');
//     	}
//     })

// }

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
				if(content.indexOf('接单')>=0){
					row_class = 'accept';
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
			if(is_verified==0)$('#view_project_holder .ctrl_btn_holder .ctrl_btn.verify').show();
			else $('#view_project_holder .ctrl_btn_holder .ctrl_btn.verify').hide();
		}
	});
}

function set_platform_delete(){
	$('.data_row.platform .ctrl_link.delete').unbind('click').click(function(){
		$(this).parents('.data_row.platform').fadeOut(function(){
			$(this).remove();
		});
	});
}

function set_platform_select(){
	var platform_arr = [];
	<?php
	$sql = "SELECT id,name FROM platform ORDER BY CONVERT(name USING GBK)";
	$get_platform = mysql_query($sql);
	if(mysql_num_rows($get_platform)>0){
		while($row = mysql_fetch_array($get_platform)){
			print "platform_arr['".$row[0]."']='".$row[1]."';";
		}
	}
	?>
	return platform_arr;
}

function set_type_select(){
	var type_arr = [];
	<?php
	foreach($GLOBALS['platform_type_opt'] as $key=>$val){
		print "type_arr['".$key."']='".$val."';";
	}
	?>
	return type_arr;
}

function add_platform(){
	var platform_arr = set_platform_select(),
		type_arr = set_type_select(),
		platform_option_str = '',
		type_option_str = '';
	for(var key in platform_arr){
		platform_option_str += '<option value="'+key+'">'+platform_arr[key]+'</option>';
	}
	for(var key in type_arr){
		type_option_str += '<option value="'+key+'">'+type_arr[key]+'</option>';
	}
	$('#platform_holder').append(
		'<tr class="data_row platform">' + 
			'<td>' + 
				'<select name="platform">'+platform_option_str+'</select>' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" name="account">' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" name="password">' + 
			'</td>' + 
			'<td>' + 
				'<select name="type">'+type_option_str+'</select>' + 
			'</td>' + 
			<?php if($page_name!="company"){ ?>
			'<td>' + 
				'<input type="text" name="remark">' + 
			'</td>' + 
			<?php }else{ ?>
			'<td class="project_id_holder">' + 
				'<button class="process_btn" onclick="bind_project();">选择关联项目</button>' + 
			'</td>' + 
			<?php } ?>
			'<td></td>' + 
			'<td><a class="ctrl_link delete"></a></td>' + 
		'</tr>'
	);
	set_platform_delete();
}

function fetch_platform(e){
	var params = 'target=<?php print $page_name;?>&id='+e;
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_platform_account.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('#platform_holder .data_row').remove();
			var i = 1;
			$(xml).find('platform_account').each(function(){
				var platform_arr = set_platform_select(),
					type_arr = set_type_select(),
					platform_id = $(this).find('platform_id').text(),
					url = $(this).find('url').text(),
					note = $(this).find('note').text(),
					type = $(this).find('type').text();
				
				var platform_option_str = '';
				for(var key in platform_arr){
					var selected = (key==platform_id)?'selected':'';
					platform_option_str += '<option value="'+key+'" '+selected+'>'+platform_arr[key]+'</option>';
				}
				
				var type_option_str = '';
				for(var key in type_arr){
					var selected = (key==type)?'selected':'';
					type_option_str += '<option value="'+key+'" '+selected+'>'+type_arr[key]+'</option>';
				}
				
				<?php if($page_name=="company"){ ?>
				var project_id_str = '';
				$(this).find('project_id').each(function(){
					var project_id = $(this).text();
					project_id_str += '<input type="hidden" class="project_id" value="'+project_id+'">';
				});
				<?php } ?>
				
				var account = '';
				$(this).find('account').each(function(){
					account = $(this).text();
				});
				var password = '';
				$(this).find('password').each(function(){
					password = $(this).text();
				});
				var remark = '';
				$(this).find('remark').each(function(){
					remark = $(this).text();
				});
				
				$('#platform_holder').append(
					'<tr id="platform_'+platform_id+'_'+type+'" class="data_row platform">' + 
						'<td>' + 
							'<select name="platform">'+platform_option_str+'</select>' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" id="account_'+i+'" name="account" value="'+account+'">' + 
							'<a class="copy_btn" id="copy_account_'+i+'"></a>' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" id="password_'+i+'" name="password" value="'+password+'">' + 
							'<a class="copy_btn" id="copy_password_'+i+'"></a>' + 
						'</td>' + 
						'<td>' + 
							'<select name="type">'+type_option_str+'</select>' + 
						'</td>' + 
						<?php if($page_name!="company"){ ?>
						'<td>' + 
							'<input type="text" name="remark" value="'+remark+'">' + 
						'</td>' + 
						<?php }else{ ?>
						'<td class="project_id_holder">' + 							
							'<button class="process_btn" onclick="bind_project(\''+platform_id+'_'+type+'\');">选择关联项目</button>' + 
							project_id_str + 
						'</td>' + 
						<?php } ?>
						'<td><a class="ctrl_link link" href="'+url+'" target="_blank"></a></td>' + 
						'<td><a class="ctrl_link delete"></a></td>' + 
					'</tr>'
				);
				i++;
			});
			set_platform_delete();
			set_clipboard();
		}
	});
}

function set_clipboard(){
	$('.copy_btn').click(function(){
		var id = $(this).attr('id').replace('copy_','');
		var clipboard = new Clipboard('#copy_'+id,{
			text: function() {  
				return $('#'+id).val();  
			}
		});
	});
}

function fetch_assign_user(e){
	var params = 'project_id='+e+'&object='+page_name;
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
						<?php
						if(strpos($_SESSION['role'],substr($page_name,0,1).'m')!==false)print "'<a class=\"btn_move\" title=\"添加人员\" onclick=\"assign_user(\''+id+'\',\'curr\');\"></a>' + ";
						?>
					'</li>'
				);
			});
			
			$(xml).find('curr').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text(),
					start_date = $(this).find('start_date').text(),
					assigned_by = $(this).find('assigned_by').text(),
					assigned_time = $(this).find('assigned_time').text(),
					active = $(this).find('active').text(),
					active_class = (active=='1')?'active':'',
					icon_info = (assigned_by!='')?'<a class="icon_info" title="'+assigned_by+'于'+assigned_time+'派单"></a>':'';
				$('#assign_list .list').append(
					'<li id="u_'+id+'" class="user curr '+active_class+'">' + 
						'<label class="name" for="ck_u_'+id+'">' + 
							'<input type="checkbox" id="ck_u_'+id+'" title="当前经办" checked>' + 
							name + 						
						'</label>' + 
						'<div class="date">' + 
							<?php if(strpos($_SESSION['role'],substr($page_name,0,1).'m')!==false){ ?>
							'<input type="text" class="date_input" placeholder="经办开始日" value="'+start_date+'">' + 
							<?php }else{ ?>
							'<label><input type="hidden" class="date_input" value="'+start_date+'">'+start_date+'</label>' + 
							<?php } ?>
							icon_info + 
						'</div>' + 
						'<div class="ctrl">' + 
						<?php 
						if(strpos($_SESSION['role'],substr($page_name,0,1).'m')!==false)print "'<a class=\"btn_back\" title=\"移除人员\" onclick=\"assign_user(\''+id+'\',\'back\');\"></a>' + ";
						?>
						'</div>' + 
					'</li>'
				);
			});
			
			$(xml).find('pass').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text(),
					start_date = $(this).find('start_date').text(),
					assigned_by = $(this).find('assigned_by').text(),
					assigned_time = $(this).find('assigned_time').text(),
					active = $(this).find('active').text(),
					active_class = (active=='1')?'active':'inactive',
					icon_info = (assigned_by!='')?'<a class="icon_info" title="'+assigned_by+'于'+assigned_time+'派单"></a>':'';
				$('#assign_list .list').append(
					'<li id="u_'+id+'" class="user pass '+active_class+'">' + 
						'<label class="name" for="ck_u_'+id+'" title="当前经办">' + 
							'<input type="checkbox" id="ck_u_'+id+'">' + 
							name + 						
						'</label>' + 
						'<div class="date">' + 
							<?php if(strpos($_SESSION['role'],substr($page_name,0,1).'m')!==false){ ?>
							'<input type="text" class="date_input" placeholder="经办开始日" value="'+start_date+'">' + 
							<?php }else{ ?>
							'<label><input type="hidden" class="date_input" value="'+start_date+'">'+start_date+'</label>' + 
							<?php } ?>
							icon_info + 
						'</div>' + 
						'<div class="ctrl">' + 
						<?php 
						if($_SESSION['level']>2)print "'<a class=\"btn_back\" title=\"移除人员\" onclick=\"assign_user(\''+id+'\',\'back\');\"></a>' + ";
						?>
						'</div>' + 
					'</li>'
				);
			});
			
			set_check_user();
		}
	});
}

function fetch_project_info(e){
	var params = 'project_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_project_info.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			var project_name = '',
				company_name = '';
			<?php
			foreach($GLOBALS['project_fields'] as $key){
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
				else if($key=="status_assign"){
					print "	$(xml).find('status_assign').each(function(){
								var status_assign = $(this).text();
								if(status_assign=='5')$('#v_outsource_t').prop('checked',true);
								if(status_assign=='6')$('#v_outsource_f').prop('checked',true);
								if(status_assign=='7'){
									$('#v_outsource_t').prop('checked',true);
									$('#v_outsource_f').prop('checked',true);
								}
							});";
				}
				else if($key=="need_approve" || 
						$key=="need_check" || 
						$key=="need_fund"
				){
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								$('#v_".$key."').val($key);
								if(".$key."==1){
									$('#process_holder .data_row.".$key."').show();
									$('#v_progress option.opt_".$key."').show();
								}
								else{
									$('#process_holder .data_row.".$key."').hide();
									$('#v_progress option.opt_".$key."').hide();
								}
							});";
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
									project_name = name;
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
			fetch_project_process(e);
			fetch_log(e);
			fetch_platform(e);
			
			$('#view_project_holder .header_holder .title').text('查看项目 ['+company_name+': '+project_name+']');
			$('input[name=action]').val('edit');
			$('input[name=id]').val(e);
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.add').hide();
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.save').show();			
			$('#view_project_holder').show().setOverlay();
		}
	});
}

function view_project(e){
	if(e=='close'){
		$('#view_project_holder').hide().setOverlay();
	}
	else if(e=='add'){
		if(check_form()){
			var params = $('#project_form').serialize();
			$.ajax({
				type: 'post',
				url: 'project_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功添加项目！');
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
			var params = $('#project_form').serialize();
			var assign_str = '';
			$('#view_project_holder #assign_list .list li').each(function(){
				var id = $(this).attr('id').replace('u_',''),
					active = ($(this).hasClass('curr'))?1:0,
					start_date = $(this).find('.date_input').val();
				
				if(active==1)assign_str += '&curr_assign[]='+id+'&curr_start_date_'+id+'='+start_date;
				else assign_str += '&pass_assign[]='+id+'&pass_start_date_'+id+'='+start_date;
			});
			params += assign_str;
			
			var platform_str = '',
				flag = true,
				i = 1;
			$('.data_row.platform').each(function(){
				var platform = $(this).find('select[name=platform]').val(),
					type = $(this).find('select[name=type]').val(),
					account = $(this).find('input[name=account]').val(),
					password = $(this).find('input[name=password]').val(),
					remark = $(this).find('input[name=remark]').val();
					
				if(platform=='' || type=='' || account=='' || password==''){
					alert('平台名称、账号类型、账号、密码不能为空！');
					flag = false;
				}
				
				platform_str += '&platform[]='+i+
								'&platform_'+i+'='+platform+
								'&type_'+i+'='+type+
								'&account_'+i+'='+account+
								'&password_'+i+'='+password+
								'&remark_'+i+'='+remark;
				i++;
			});
			if(!flag)return flag;
			else params += platform_str;
			
			$.ajax({
				type: 'post',
				url: page_name+'_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						alert('成功保存项目信息！');
						view_project(ctrl_id);
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
		if(confirm('通过审核前请确保项目资料准确无误，通过资料审核？')){
			var params = 'action=verify&id='+$('input[name=id]').val();
			$.ajax({
				type: 'post',
				url: 'project_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('项目信息通过审核！');
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
		foreach($GLOBALS['project_fields'] as $key){
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
		$('input[name*=v_outsource]').prop('checked',false);
		
		if(e=='create'){//create project
			$('#view_project_holder .header_holder .title').text('添加项目');
			$('input[name=action]').val('add');
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.add').show();
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.save').hide();
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.verify').hide();
			$('#view_project_holder').show().setOverlay();
		}
		else{//view project
			ctrl_id = e;
			fetch_project_info(e);
			fetch_assign_user(e);
		}
	}
}

function set_check_project(){
	$('#project_list #curr_page_project').change(function(){
		if($(this).is(':checked')){
			$('#project_list td input[type=checkbox]').prop('checked',true);
			$('#project_list tr').addClass('selected');
		}
		else{
			$('#all_page_project').prop('checked',false);
			$('#project_list td input[type=checkbox]').prop('checked',false);
			$('#project_list tr').removeClass('selected');
		}
	});
	
	$('#all_page_project').change(function(){
		if($(this).is(':checked')){
			$('#curr_page_project').prop('checked',true);
			$('#project_list td input[type=checkbox]').prop('checked',true);
			$('#project_list tr').addClass('selected');
		}
		else{
			$('#curr_page_project').prop('checked',false);
			$('#project_list td input[type=checkbox]').prop('checked',false);
			$('#project_list tr').removeClass('selected');
		}
	});
	
	$('#project_list td input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('selected');
		}
		else{
			$('#all_page_project').prop('checked',false);
			$('#project_list #curr_page_project').prop('checked',false);
			$(this).closest('tr').removeClass('selected');
		}
	});
}

function check_form(){
	var check_pass = true;
	$('#project_form .title.required').each(function(){
		var input_title = $(this).text(),
			input_field = '';
		
		if(input_title.indexOf('市')>=0){
			input_field = $(this).next('input[type=text],select');
		}
		else{
			input_field = $(this).next('td').find('input[type=text],select');
		}
		
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
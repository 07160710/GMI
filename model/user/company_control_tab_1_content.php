<!--VIEW COMPANY HOLDER-->
<div id="view_company_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_company('close');"></a>				
			</td>
		</tr>		
		<tr>
			<td>
<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td class="tab_container" height="35">
			<ul class="tab_holder sub">
				<li><a id="subtab_1" class="subtab curr">详情</a></li>
				<li><a id="subtab_2" class="subtab">员工</a></li>
				<li><a id="subtab_3" class="subtab">账号</a></li>
				<li><a id="subtab_4" class="subtab">日志</a></li>
				<div id="tab_line"></div>
			</ul>
		</td>
	</tr>
	<tr valign="top" id="subtab_panel_1" class="subtab_panel">
		<td>
			<form id="company_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
				<input type="hidden" name="action">
				<input type="hidden" name="id">
				<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="form_holder company">
							<div class="scroll_holder">
								<a class="top_anchor"></a>
								<table class="company_holder company" cellpadding="5" cellspacing="0">
									<tr>
										<td class="title required">公司名称</td>
										<td colspan="3">
											<input type="text" name="v_name" class="ex-long" id="v_name">
										</td>
									</tr>
									<tr>
										<td class="title required">省</td>
										<td>
											<select id="v_province" name="v_province">
											<?php
											$sql = "SELECT DISTINCT id,name FROM region WHERE parent_id=0 ORDER BY id";
											$stmt = mysql_query($sql);
											if(mysql_num_rows($stmt)>0){
												while($row = mysql_fetch_array($stmt)){
													print '<option value="'.$row[0].'">'.$row[1].'</option>';
												}
											}
											?>
											</select>
											<span class="title required">市</span>
											<select id="v_city" name="v_city"></select>
											<span class="title">区</span>
											<select id="v_district" name="v_district"></select>
										</td>
									</tr>
									<tr>
										<td class="title">地址</td>
										<td colspan="3">
											<input type="text" name="v_address" id="v_address" class="ex-long">
										</td>
									</tr>
									<tr>										
										<td class="title">财务状况</td>
										<td colspan="3">
											<textarea name="v_finance_info" id="v_finance_info"></textarea>
										</td>
									</tr>
									<tr>
										<td class="title">人员状况</td>
										<td colspan="3">
											<textarea name="v_employee_info" id="v_employee_info"></textarea>
										</td>
									</tr>
									<tr class="additional_row">	
										<td class="title">绑定码</td>
										<td colspan="3">
											<input type="hidden" name="v_code" id="v_code">
											<label id="binding_code"></label>
											<a class="ctrl_btn active reset" onclick="reset_code();">重设绑定码</a>
										</td>
									</tr>
									<tr class="additional_row">
										<td class="title">相关附件</td>
										<td colspan="3">
											<table class="upload_panel" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td class="left-column">
														<table id="fileUploader" cellpadding="0" cellspacing="0">
															<tr>
																<td align="center" valign="middle">
																	<div id="drag-and-drop-zone" class="uploader">
																		<div class="caption">
																			<h3>拖放文件到这里</h3>
																			<b class="or">- 或 -</b>
																			<div class="browser">
																				<label>
																					<span>点击选择上传文件</span>
																					<input type="file" name="files[]" multiple="multiple" title="Click to add Files">
																				</label>
																			</div>
																		</div>
																	</div>
																</td>
															</tr>
														</table>
													</td>
													<td class="right-column">
														<table id="fileList_holder" cellpadding="0" cellspacing="0">
															<tr>
																<td class="header">文件列表</td>
															</tr>
															<tr>
																<td>
																	<div id="fileList"></div>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
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
				<table width="99%" id="employee_holder" border="0" cellpadding="0" cellspacing="0">
					<tr class="header_row">
						<th width="30">负责</th>
						<th width="80">姓名</th>
						<th width="100">手机</th>
						<th width="150">职位</th>
						<th>电子邮箱</th>
						<th width="100">绑定码</th>
						<th width="20"><a class="ctrl_link link"></a></th>
						<th width="20"><a class="ctrl_link delete"></a></th>
					</tr>
				</table>
				<button class="process_btn" onclick="add_employee();">添加员工资料</button>
			</div>
		</td>
	</tr>
	<tr valign="top" id="subtab_panel_3" class="subtab_panel">
		<td>
			<div class="scroll_holder">
				<table width="99%" id="platform_holder" border="0" cellpadding="0" cellspacing="0">
					<tr class="header_row">
						<th>平台名称</th>						
						<th width="20%">账号</th>
						<th width="20%">密码</th>
						<th width="50">类型</th>
						<th width="20%">关联项目</th>
						<th width="20"><a class="ctrl_link link"></a></th>
						<th width="20"><a class="ctrl_link delete"></a></th>
					</tr>
				</table>
				<button class="process_btn" onclick="add_platform();">添加平台账号</button>
			</div>
		</td>
	</tr>
	<tr valign="top" id="subtab_panel_4" class="subtab_panel">
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
		<?php if($_SESSION['auth_company']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="view_company('save');">
					保存公司信息
				</button>
				<button class="ctrl_btn active add" onclick="view_company('add');">
					添加公司
				</button>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>

<!--BIND PROJECT HOLDER-->
<div id="bind_project_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">关联项目</span>
				<a class="btn_close" onclick="bind_project('close');"></a>				
			</td>
		</tr>		
		<tr>
			<td>
				<div class="scroll_holder">
					<ul class="bp_list"></ul>
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="bind_project('confirm');">
					确定关联
				</button>
			</td>
		</tr>
	</table>
</div>

<script>
var ctrl_id = '';
$(function() {
	set_v_province_change();
	set_v_city_change();
	set_subtab();
});

function reset_code(){
	if(confirm('提示：重设绑定码后你需要将新绑定码告知该企业未绑定的用户，已绑定的用户可忽略。确定重设？')){
		var params = 'action=reset_code&id='+$('input[name=id]').val();
		$.ajax({
			type: 'post',
			url: 'company_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					alert('成功解绑用户！');
					fetch_company_info(ctrl_id);
					filter_search(curr_page);
				}
				else{
					alert(result.error);
				}	
			}
		});
	}
}

function set_employee_ctrl(){
	$('.data_row.employee .ctrl_link.link').unbind('click').click(function(){
		var parent = $(this).parents('.data_row.employee'),
			mobile = parent.attr('id').replace('employee_','');
		
		if(confirm('解绑后此员工将不能查看该企业的项目进度和接收相关通知。确定解绑？')){
			var params = 'action=unbind_member&company_id='+ctrl_id+'&mobile='+mobile;
			$.ajax({
				type: 'post',
				url: 'company_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						alert('成功重设绑定码！');
						fetch_employee(ctrl_id);
					}
					else{
						alert(result.error);
					}	
				}
			});
		}
	});
	
	$('.data_row.employee .ctrl_link.delete').unbind('click').click(function(){
		$(this).parents('.data_row.employee').fadeOut(function(){
			$(this).remove();
		});
	});
}

function add_employee(){
	$('#employee_holder').append(
		'<tr class="data_row employee">' + 
			'<td>' + 
				'<input type="checkbox" name="is_leader[]">' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" name="name">' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" name="mobile">' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" name="position">' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" name="email">' + 
			'</td>' + 
			'<td>-</td>' + 
			'<td>-</td>' + 
			'<td><a class="ctrl_link delete"></a></td>' + 
		'</tr>'
	);
	set_employee_ctrl();
}

function fetch_employee(e){
	var params = 'id='+e;
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_employee_account.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('#employee_holder .data_row').remove();
			var i = 1;
			$(xml).find('employee_account').each(function(){
				var is_leader = $(this).find('is_leader').text()
					name = $(this).find('name').text(),
					mobile = $(this).find('mobile').text(),
					position = $(this).find('position').text(),
					email = $(this).find('email').text(),
					code = $(this).find('code').text(),
					link_str = (code!='')?'<a class="ctrl_link link" title="解绑"></a>':'-',
					is_checked = (is_leader=='1')?'checked':'';
				code = (code!='')?code:'-';
				$('#employee_holder').append(
					'<tr id="employee_'+mobile+'" class="data_row employee">' + 
						'<td>' + 
							'<input type="checkbox" name="is_leader[]" '+is_checked+'>' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" name="name" value="'+name+'">' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" name="mobile" value="'+mobile+'">' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" name="position" value="'+position+'">' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" name="email" value="'+email+'">' + 
						'</td>' + 
						'<td>'+code+'</td>' + 
						'<td>'+link_str+'</td>' + 
						'<td><a class="ctrl_link delete" title="删除"></a></td>' + 
					'</tr>'
				);
				i++;
			});
			set_employee_ctrl();
			set_clipboard();
		}
	});
}

function fetch_v_district(){
	var params = 'action=get_district&province='+$('#v_province').val()+'&city='+$('#v_city').val();
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_region.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('#v_district').html('<option value=""></option>');
			$(xml).find('district').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#v_district').append('<option value="'+id+'">'+name+'</option>');
			});
		}
	});
}
function set_v_city_change(){
	$('#v_city').change(function(){
		fetch_v_district();
	});
}
function fetch_v_city(){
	var params = 'action=get_city&province='+$('#v_province').val();	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_region.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('#v_city').html('');
			$(xml).find('city').each(function(){
				var id = $(this).find('id').text(),
					name = $(this).find('name').text();
				$('#v_city').append('<option value="'+id+'">'+name+'</option>');
			});
			fetch_v_district();
		}
	});
}
function set_v_province_change(){
	$('#v_province').change(function(){
		fetch_v_city();
	});
}

function fetch_company_info(e){
	var params = 'company_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_company_info.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			var company_name = '';
			<?php
			foreach($GLOBALS['company_fields'] as $key){
				if($key=="name"){
					print "	$(xml).find('name').each(function(){
								var name = $(this).text();
								$('#v_name').val(name);
								company_name = name;
							});";
				}
				else if($key=="code"){
					print "	$(xml).find('code').each(function(){
								var code = $(this).text();
								$('#v_code').val(code);
								$('#binding_code').html('<a class=\"copy_btn\" id=\"copy_v_code\" title=\"复制到粘贴板\">'+code+'</a>');
							});";
				}
				else{
					if($key=="city")print "fetch_v_city();";
					if($key=="district")print "fetch_v_district();";
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								$('#v_".$key."').val($key);
							});";
				}
			}
			?>
			$(xml).find('file').each(function(){
				var file_id = $(this).find('file_id').text(),
					file_name = $(this).find('file_name').text(),
					file_ext = $(this).find('file_ext').text(),
					file_url = $(this).find('file_url').text();
					
				$('#fileList').append(
					'<div class="file" id="uploadFile_'+file_id+'">' + 
						'<div class="info">' + 
							'<a class="filename '+file_ext+'" href="<?php print _ROOT_URL_;?>'+file_url+'" target="_blank" title="'+file_name+'">'+file_name+'</a>' + 
						'</div>' + 
						'<div class="ctrl">' + 	
							<?php if($_SESSION['level']>2){ ?>
							'<a class="btn_delete" onclick="delete_file(\''+e+'\',\''+file_id+'\');" title="删除"></a>' + 
							<?php } ?>
						'</div>' + 
					'</div>'
				);
			});
			
			$(xml).find('project').each(function(){
				var project_id = $(this).find('project_id').text(),
					project_name = $(this).find('project_name').text();
				$('.bp_list').append(
					'<li>' + 
						'<label for="bp_'+project_id+'">' + 
							'<input type="checkbox" id="bp_'+project_id+'" class="bp_checkbox" value="'+project_id+'">' + 
							project_name + 
						'</label>' + 
					'</li>'
				);
			});	
			fetch_log(e);
			fetch_platform(e);
			fetch_employee(e);
			
			$('#view_company_holder .header_holder .title').text('查看公司 ['+company_name+']');
			$('input[name=action]').val('edit');
			$('input[name=id]').val(e);
			$('#view_company_holder .ctrl_btn_holder .ctrl_btn.add').hide();
			$('#view_company_holder .ctrl_btn_holder .ctrl_btn.save').show();
			$('#view_company_holder .additional_row').show();
			$('#view_company_holder').show().setOverlay();
		}
	});
}

var ctrl_platform_id = '';
function bind_project(e){
	if(e=='close'){
		$('#bind_project_holder').hide().setOverlay();
	}
	else if(e=='confirm'){
		$('#platform_'+ctrl_platform_id+' .project_id_holder .project_id').remove();		
		$('#bind_project_holder .bp_list .bp_checkbox').each(function(){
			if($(this).is(':checked')){
				var tmp_id = $(this).val();
				$('#platform_'+ctrl_platform_id+' .project_id_holder').append(
					'<input type="hidden" class="project_id" value="'+tmp_id+'">'
				);
			}
		});
		$('#bind_project_holder').hide().setOverlay();
	}
	else{
		ctrl_platform_id = e;
		
		var project_id_arr = [];
		$('#platform_'+ctrl_platform_id+' .project_id_holder .project_id').each(function(){
			project_id_arr.push($(this).val());
		});
		
		$('#bind_project_holder .bp_list .bp_checkbox').each(function(){
			var tmp_id = $(this).val();
			if(in_array(tmp_id, project_id_arr)>=0){
				$(this).prop('checked',true);
			}
			else{
				$(this).prop('checked',false);
			}
		});
		$('#bind_project_holder').show().setOverlay();
	}
}

function view_company(e){
	if(e=='close'){
		$('#view_company_holder').hide().setOverlay();
		$('#drag-and-drop-zone').dmUploader('destroy');
	}
	else if(e=='add'){
		if(check_form()){
			var params = $('#company_form').serialize();
			$.ajax({
				type: 'post',
				url: 'company_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功添加公司！','reload');
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
			var params = $('#company_form').serialize();
			
			var platform_str = '',
				flag = true,
				i = 1;
			$('.data_row.platform').each(function(){
				var platform = $(this).find('select[name=platform]').val(),
					type = $(this).find('select[name=type]').val(),
					account = $(this).find('input[name=account]').val(),
					password = $(this).find('input[name=password]').val();
					
				if(platform=='' || type=='' || account=='' || password==''){
					alert('平台名称、账号类型、账号、密码不能为空！');
					flag = false;
				}
				
				$(this).find('.project_id').each(function(){
					platform_str += '&platform[]='+i+
									'&platform_'+i+'='+platform+
									'&project_id_'+i+'='+$(this).val()+
									'&type_'+i+'='+type+
									'&account_'+i+'='+account+
									'&password_'+i+'='+password;
					i++;
				});
			});
			if(!flag)return flag;
			else params += platform_str;
			
			var employee_str = '',
				flag = true,
				j = 1;
			$('.data_row.employee').each(function(){
				var is_leader = ($(this).find('input[name*=is_leader]').is(':checked'))?1:0,
					name = $(this).find('input[name=name]').val(),
					mobile = $(this).find('input[name=mobile]').val(),
					position = $(this).find('input[name=position]').val(),
					email = $(this).find('input[name=email]').val();
				
				if(name=='' || mobile==''){
					alert('员工姓名、手机不能为空！');
					flag = false;
				}
				
				employee_str += '&employee[]='+j+
								'&is_leader_'+j+'='+is_leader+
								'&name_'+j+'='+name+
								'&mobile_'+j+'='+mobile+
								'&position_'+j+'='+position+
								'&email_'+j+'='+email;
				j++;
			});
			if(!flag)return flag;
			else params += employee_str;
			
			$.ajax({
				type: 'post',
				url: 'company_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						alert('成功保存公司信息！');
						fetch_log(ctrl_id);
						fetch_platform(ctrl_id);
						fetch_employee(ctrl_id);
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
		<?php
		foreach($GLOBALS['company_fields'] as $key=>$val){
			if(	$key!="province" && 
				$key!="city" && 
				$key!="district"
			){
				print "$('#v_".$key."').val('');";
			}
			else{
				print "$('#v_".$key." option:eq(0)').prop('selected', true);";
			}
		}
		?>
		$('#fileList').html('');
		
		setTimeout(function(){
			$('.scroll_holder').animate({
				scrollTop: $('.top_anchor').offset().top - $('.scroll_holder').offset().top + $('.scroll_holder').scrollTop()
			});
		},200);
		
		if(e=='create'){//create company
			$('#view_company_holder .header_holder .title').text('添加公司');
			$('#view_company_holder select').each(function(){
				$(this).children('option:eq(0)').prop('selected',true);
			});
			fetch_v_city();
			$('input[name=action]').val('add');
			$('#view_company_holder .ctrl_btn_holder .ctrl_btn.add').show();
			$('#view_company_holder .ctrl_btn_holder .ctrl_btn.save').hide();
			$('#view_company_holder .additional_row').hide();
			$('#view_company_holder').show().setOverlay();
		}
		else{//view company
			ctrl_id = e;
			fetch_company_info(e);
			set_upload(e);
		}
	}
}

function check_form(){
	var check_pass = true;
	$('#company_form .title.required').each(function(){
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
	
	return check_pass;
}
</script>

<script type="text/javascript" src="js/dmuploader.js"></script>
<script type="text/javascript">
function delete_file(company_id, id){
	var params = 'action=delete_file&company_id='+company_id+'&media_id='+id;
	$.ajax({
		type: 'post',
		url: 'company_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				$('#uploadFile_'+id).fadeOut(function(){
					$(this).remove();
				});
				filter_search(curr_page);
			}
			else{
				alert(result.error);
			}	
		}
	});
}

function add_file(id, file, company_id){
	var template = 	'<div class="file" id="uploadFile_'+id+'">' + 
						'<div class="info">' + 
							'<span class="filename" title="'+file.name+'">'+file.name+'</span>' + 
						'</div>' + 
						'<div class="bar">' + 
							'<div class="progress" style="width:0%"></div>' + 
						'</div>' + 
					'</div>';	
	$('#fileList').append(template);
}

function update_file_progress(id, percent){
	$('#uploadFile_'+id).find('div.progress').width(percent).html(percent);
}

function set_upload(company_id){
	$('#drag-and-drop-zone').dmUploader({
		url: 'media_upload.php?company_id='+company_id,
		dataType: 'json',
		extFilter: ['pdf'],
		onInit: function(){
		},
		onBeforeUpload: function(id){
		},
		onNewFile: function(id, file){
			add_file(id, file, company_id);
		},
		onComplete: function(){
			view_company(company_id);
			filter_search(curr_page);
		},
		onUploadProgress: function(id, percent){
			var percentStr = percent + '%';
			update_file_progress(id, percentStr);
		},
		onUploadSuccess: function(id, data){
			if(data.success==1){
				update_file_progress(id, '100%');
				$('#uploadFile_'+id).find('div.progress').addClass('success');
			}
			else{
				update_file_progress(id, 'Upload file failed');
				$('#uploadFile_'+id).find('div.progress').addClass('error');
			}
		},
		onUploadError: function(id, message){
		},
		onFileTypeError: function(file){
		},
		onFileSizeError: function(file){
		},
		onFallbackMode: function(message){
			alert('Browser not supported: ' + message);
		}
	});
}
</script>
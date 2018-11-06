<!--VIEW REGISTRATION HOLDER-->
<div id="view_nbase_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_nbase('close');"></a>
			</td>
		</tr>		
		<tr>
			<td>
				<form id="nbase_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
				<input type="hidden" name="action">
				<input type="hidden" name="v_id" id="v_id">
				<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="form_holder nbase">
							<div class="scroll_holder">
								<a class="top_anchor"></a>
								<table class="nbase_holder nbase" cellpadding="5" cellspacing="0">
									<tr>
										<td class="title required">通知名称</td>
										<td colspan="3">
											<input type="text" class="ex-long" id="v_name" name="v_name">
										</td>
									</tr>
									<tr>
										<td class="title">头图</td>
										<td id="img_ctrl_area">
											<div id="img_ctrl_holder " class="upload_area image_show_type1">
												<input name="n_image" class="btn_file" type="file" id="n_image">
												<span class="btn_upload">上传头图
													<dt class="lang en">Upload Image</dt>
													<dt class="lang zh">上传头图</dt>
												</span>
											</div>
											<div id="show_image " class="upload_area image_show_type2"></div>
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
													$id = $row[0];
													$name = $row[1];
													$selected = "";
													if($name=="广东")$selected = "selected";
													
													print '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
												}
											}
											?>
											</select>
											
											<span class="title">市</span>
											<select id="v_city" name="v_city"></select>
											
											<span class="title">区</span>
											<select id="v_district" name="v_district"></select>
										</td>
										<td class="title required">部委名称</td>
										<td>
											<input type="text" id="v_bureau" name="v_bureau">
										</td>
									</tr>
									<tr>
										<td class="title required">发文时间</td>
										<td>
											<input type="text" class="date_input" id="v_release_date" name="v_release_date">
										</td>
										<td class="title">申报截止时间</td>
										<td>
											<input type="text" class="date_input" id="v_apply_deadline" name="v_apply_deadline">
										</td>
									</tr>
									<tr>
										<td class="title required">政策类别</td>
										<td colspan="3">
											<?php
											foreach($GLOBALS['policy_type_opt'] as $key){
												$rand = rand(10000,99999);
												print 	"<label for=\"sm_$rand\" style=\"width:18%;\">
															<input type=\"checkbox\" name=\"v_policy_type[]\" id=\"sm_$rand\" value=\"$key\">
															$key
														</label>";
											}
											?>
										</td>
									</tr>
									<tr>
										<td class="title">通知类别</td>
										<td colspan="3">
											<label for="v_is_recommend" style="width:30%;">
												<input type="checkbox" name="v_is_recommend" id="v_is_recommend">
												推荐(若勾选则发通知信息)
											</label>											
											<label for="v_is_hot" style="width:30%;">
												<input type="checkbox" name="v_is_hot" id="v_is_hot">
												热点(若勾选则放在首页热点列表)
											</label>
											<label for="v_is_top" style="width:30%;">
												<input type="checkbox" name="v_is_top" id="v_is_top">
												置顶(若勾选则在通知列表置顶)
											</label>											
										</td>
									</tr>
									<tr>
										<td class="title">通知正文</td>
										<td colspan="3">
											<textarea id="v_content" name="v_content" style="height:350px;"></textarea>
										</td>
									</tr>
									
									<!--<tr>
										<td class="title">备注</td>
										<td colspan="3">
											<div id="v_remark"></div>
											<textarea id="v_append_remark" name="v_append_remark"></textarea>
											<span class="note">(备注只增加，不删除。)</span>
										</td>
									</tr>-->
									<?php if($_SESSION['auth_nbase']==2){ ?>
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
									<?php } ?>
									<tr class="additional_row">
										<td class="title ">创建者</td>
										<td>
											<label id="v_created_by">
										</td>
										<td class="title">创建时间</td>
										<td>
											<label id="v_created_time">
										</td>
									</tr>
									<tr class="additional_row">							
										<td class="title ">更新者</td>
										<td>
											<label id="v_updated_by">
										</td>
										<td class="title">更新时间</td>
										<td>
											<label id="v_updated_time">
										</td>
									</tr>
									<tr class="additional_row">							
										<td class="title ">发布者</td>
										<td>
											<label id="v_published_by">
										</td>
										<td class="title">发布时间</td>
										<td>
											<label id="v_published_time">
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
		<?php if($_SESSION['auth_nbase']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn active save" onclick="view_nbase('save');">
					保存通知
				</button>
				<button class="ctrl_btn sub active publish" onclick="view_nbase('publish');">
					发布通知
				</button>
				<button class="ctrl_btn active add" onclick="view_nbase('add');">
					创建通知
				</button>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>

<script>
var ctrl_id = '';
var myEditor;
$(function() {	
	$('.date_input').each(function(){
		$(this).datepicker({
			changeMonth:true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		});
	});
	
	myEditor = KindEditor.create('#v_content', {
		width: '100%',
		height: '350px',
		resizeType : 1,
		filterMode: false,//是否开启过滤模式
		items : [
		'source', '|', 'plainpaste', 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 
		//'emoticons', 'image', 
		'link'
		]
	});
	
	set_v_province_change();
	set_v_city_change();
});

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
			$('#v_city').html('<option value=""></option>');
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

function check_form(){
	var check_pass = true;
	$('#nbase_form .title.required').each(function(){
		var input_title = $(this).text(),
			input_field = '',
			input_val = '';
		
		if($(this).next('td').find('input[type=text],select').length>0){
			input_field = $(this).next('td').find('input[type=text],select');
			input_val = input_field.val();
		}
		if($(this).next('td').find('input[type=checkbox]').length>0){
			$(this).next('td').find('input[type=checkbox]').each(function(){
				if($(this).is(':checked'))input_val += '1';
			});
		}
		
		if(input_val==undefined || input_val==null || input_val==''){
			alert(input_title+'不能为空');
			check_pass = false;
		}
	});
	
	return check_pass;
}

function fetch_nbase_info(e){
	var params = 'n_id='+e;
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_nbase_info.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			<?php
			foreach($GLOBALS['nbase_fields'] as $key){					
				if($key=="remark"){
					print "	$(xml).find('remark').each(function(){
								var remark = $(this).text(),
									remark_arr = remark.split('|');
								for(var i in remark_arr){
									$('#v_remark').prepend('<div>'+remark_arr[i]+'</div>');
								}
							});";
				}
				else if($key=="policy_type"){
					print "	$(xml).find('policy_type').each(function(){
								var policy_type = $(this).text(),
									policy_type_arr = policy_type.split(',');
								for(var i in policy_type_arr){
									$('input[name*=policy_type]').each(function(){
										if($(this).val()==policy_type_arr[i])$(this).prop('checked',true);
									});
								}
							});";
				}
				else if($key=="is_top"||$key=="is_recommend"||$key=="is_hot"){
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								if(".$key."=='1'){
									$('#v_".$key."').prop('checked',true);
								}else{
									$('#v_".$key."').prop('checked',false);
								}
							});";
				}
				else{
					if($key=="region"){
						print "	$(xml).find('province').each(function(){
									var province = $(this).text();				
									$('#v_province').val(province);
								});
								
								fetch_v_city();
								$(xml).find('city').each(function(){
									var city = $(this).text();				
									$('#v_city').val(city);
								});
								
								fetch_v_district();
								$(xml).find('district').each(function(){
									var district = $(this).text();				
									$('#v_district').val(district);
								});";
					}
					else{
						print "	$(xml).find('".$key."').each(function(){
									var ".$key." = $(this).text();				
									$('#v_".$key."').val($key);
								});";
					}
				}
			}
			?>
			$(xml).find('img_url').each(function(){
				var img_url = $(this).text();				
				if(img_url!='<?php print _BASE_URL_;?>'){
					var h = '<img width="182" height="132" src="'+img_url+'">'+
							'<div id="img_ctrl" class="menu_holder">'+
								'<a class="ctrl_arrow"></a>' + 
								'<div class="ctrl_menu">' + 
									'<input name="n_image" class="btn_file" type="file" id="n_image">' + 
									'<a class="item replace">替换</a>' + 
									'<a class="item crop" onclick="crop_img(\'<?php print _BASE_URL_;?>'+img_url+'\');">裁切</a>' + 
									'<a class="item delete" onclick="delete_img(\''+$('#v_id').val()+'\')">删除</a>' + 
								'</div>'+
							'</div>';
					$('.image_show_type2').html(h);
					$('.image_show_type1').css('display','none')
					$('.image_show_type2').css('display','block')
				}else{
					$('.image_show_type1').css('display','block')
					$('.image_show_type2').css('display','none')
				}
			});

			$(xml).find('created_by').each(function(){
				var created_by = $(this).text();				
				$('#v_created_by').text(created_by);
			});
			
			$(xml).find('created_time').each(function(){
				var created_time = $(this).text();				
				$('#v_created_time').text(created_time);
			});
			
			$(xml).find('updated_by').each(function(){
				var updated_by = $(this).text();				
				$('#v_updated_by').text(updated_by);
			});
			
			$(xml).find('updated_time').each(function(){
				var updated_time = $(this).text();				
				$('#v_updated_time').text(updated_time);
			});
			
			$(xml).find('published_by').each(function(){
				var published_by = $(this).text();				
				$('#v_published_by').text(published_by);
			});
			
			$(xml).find('published_time').each(function(){
				var published_time = $(this).text();				
				$('#v_published_time').text(published_time);
			});
			
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
							'<a class="btn_delete" onclick="delete_file(\''+e+'\',\''+file_id+'\');" title="删除"></a>' + 
						'</div>' + 
					'</div>'
				);
			});
			
			myEditor.html($('#v_content').val());
		}
	});
	
	$('#view_nbase_holder .header_holder .title').text('查看通知记录 ['+$('#n_'+e+' .name').text()+']');
	$('input[name=action]').val('edit');
	$('#view_nbase_holder .ctrl_btn_holder .ctrl_btn.add').hide();
	$('#view_nbase_holder .ctrl_btn_holder .ctrl_btn.save').show();
	$('#view_nbase_holder .ctrl_btn_holder .ctrl_btn.publish').show();
	$('#view_nbase_holder .additional_row').show();
	$('#view_nbase_holder').show().setOverlay();
}

function view_nbase(e){
	if(e=='close'){
		$('#view_nbase_holder').hide().setOverlay();
		$('#drag-and-drop-zone').dmUploader('destroy');
	}
	else if(e=='add'){
		if(check_form()){
			myEditor.sync();
			var params = $('#nbase_form').serialize();
			$.ajax({
				type: 'post',
				url: 'nbase_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功创建通知！');
						filter_search(curr_page);
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	else if(e=='save'){
		if(check_form()){
			myEditor.sync();
			var params = $('#nbase_form').serialize();			
			$.ajax({
				type: 'post',
				url: 'nbase_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功保存通知！');
						filter_search(curr_page);
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	else if(e=='publish'){
		show_alert('正在发布通知，请稍候 ...','load');
		if(check_form()){
			myEditor.sync();
			var params = $('#nbase_form').serialize();			
			$.ajax({
				type: 'post',
				url: 'nbase_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						var params = 'action=publish_notice&id='+result.nbase_id+'&type=1';
						$.ajax({
							data: 	params,
							type:	'post',
							url: 	'nbase_manage.php',
							dataType: 'json',
							success: function(result){
								if(result.success==1){
									show_alert('成功发布通知！');
									filter_search(curr_page);
								}
								else{
									show_alert(result.error);
								}
							}
						});
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	else{
		<?php
		foreach($GLOBALS['nbase_fields'] as $key){
			if($key=="policy_type"){
				print "$('input[name*=policy_type').prop('checked',false);";
			}
			else if($key=="region"){
				print "	$('#v_province option').each(function(){
							if($(this).val()=='广东')$(this).prop('selected',true);
						});";
			}
			else{
				print "$('#v_".$key."').val('');";
			}
		}
		?>
		myEditor.html($('#v_content').val());
		//$('#v_remark').html('');
		//$('#v_append_remark').val('');
		$('#fileList').html('');
		$('#v_img_url').text('');
		$('#v_created_by').text('');
		$('#v_created_time').text('');
		$('#v_updated_by').text('');
		$('#v_updated_time').text('');
		$('#v_published_by').text('');
		$('#v_published_time').text('');
		$('.image_show_type1').css('display','block')
		$('.image_show_type2').css('display','none')
		
		if(e=='create'){//create nbase
			$('#view_nbase_holder .header_holder .title').text('添加通知记录');
			fetch_v_city();
			$('input[name=action]').val('add');
			$('#view_nbase_holder .ctrl_btn_holder .ctrl_btn.add').show();
			$('#view_nbase_holder .ctrl_btn_holder .ctrl_btn.save').hide();
			$('#view_nbase_holder .ctrl_btn_holder .ctrl_btn.publish').hide();
			$('#view_nbase_holder .additional_row').hide();
			$('#view_nbase_holder').show().setOverlay();
		}
		else{//view nbase
			ctrl_id = e;
			fetch_nbase_info(e);
			set_upload(e);
		}
	}
}
</script>

<script type="text/javascript" src="../../publlic/js/dmuploader.js"></script>
<script type="text/javascript">
function delete_file(n_id, id){
	var params = 'action=delete_file&nbase_id='+n_id+'&media_id='+id;
	$.ajax({
		type: 'post',
		url: 'nbase_manage.php',
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

function add_file(id, file, n_id){
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

function set_upload(n_id){
	$('#drag-and-drop-zone').dmUploader({
		url: 'media_upload.php?nbase_id='+n_id,
		dataType: 'json',
		allowedTypes: '*',
		onInit: function(){
		},
		onBeforeUpload: function(id){
		},
		onNewFile: function(id, file){
			add_file(id, file, n_id);
		},
		onComplete: function(){
			view_nbase(n_id);
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
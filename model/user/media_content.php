<!--FILE HOLDER-->
<div id="file_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					编辑文档
				</span>				
				<a class="btn_close" onclick="display_file('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="file_panel">
				<div class="scroll_holder">
				
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="display_file('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>
<script>
function display_file(e){
	if(e=='close'){
		$('#file_holder div.scroll_holder').html('');
		$('#file_holder').hide().setOverlay();
	}
	else if(e=='save'){
		var params = '';
		$.ajax({
			data: 	params,
			type:	'post',
			url: 	post_url,
			dataType: 'json',
			success: function(result){
				if(result.success==1){
					document.cookie = 'node_id='+result.id;
					window.location.reload();
				}
				else{
					$('#'+ctrl_action+'_holder .alert_holder').html(result.error);
				}	
			}
		});
	}
	else{
		ctrl_id = e;
		$('#file_holder').show().setOverlay();		
	}
}
</script>

<!--UPLOAD HOLDER-->
<div id="upload_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					上传文件
				</span>				
				<a class="btn_close" onclick="display_upload('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder">
				注意：只接受中文、英文、数字组合，以及 “-” 或 “_” 符号作为连接符的文件名。
			</td>
		</tr>
		<tr>
			<td valign="top">
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
									<td class="header">上传文件列表</td>
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

<script type="text/javascript" src="js/dmuploader.js"></script>
<script type="text/javascript">
function add_file(id, file){
	var template = 	'<div class="file" id="uploadFile' + id + '">' +
						'<div class="info">' +
							'<span class="filename" title="Size: ' + file.size + 'bytes - Mimetype: ' + file.type + '">' + 
								file.name + 
							'</span>' +
						'</div>' +
						'<div class="bar">' +
							'<div class="progress" style="width:0%"></div>' +
						'</div>' +
					'</div>';
	
	$('#fileList').append(template);
}

function update_file_status(id, status, message){
	$('#uploadFile' + id).find('span.status').html(message).addClass(status);
}

function update_file_progress(id, percent){
	$('#uploadFile' + id).find('div.progress').width(percent).html(percent);
}

function set_upload(m_id){
	$('#drag-and-drop-zone').dmUploader({
		url: 'media_upload.php?m_id='+m_id,
		dataType: 'json',
		allowedTypes: '*',
		onInit: function(){
		},
		onBeforeUpload: function(id){
		},
		onNewFile: function(id, file){
			add_file(id, file);
			toogle_node(m_id);
		},
		onComplete: function(){
		},
		onUploadProgress: function(id, percent){
			var percentStr = percent + '%';
			update_file_progress(id, percentStr);
		},
		onUploadSuccess: function(id, data){
			if(data.success==1){
				update_file_progress(id, '100%');
				$('#uploadFile'+id).find('div.progress').addClass('success');
				get_file_list();
			}
			else{
				update_file_progress(id, 'Upload file failed');
				$('#uploadFile'+id).find('div.progress').addClass('error');
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
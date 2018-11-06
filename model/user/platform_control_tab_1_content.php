<!--VIEW COMPANY HOLDER-->
<div id="view_platform_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_platform('close');"></a>				
			</td>
		</tr>		
		<tr>
			<td>
				<form id="platform_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
				<input type="hidden" name="action">
				<input type="hidden" name="id">
				<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="form_holder platform">
							<div class="scroll_holder">
								<a class="top_anchor"></a>
								<table class="platform_holder platform" cellpadding="5" cellspacing="0">
									<tr>
										<td class="title required">平台名称</td>
										<td>
											<input type="text" name="v_name" class="ex-long" id="v_name">
										</td>
									</tr>
									<tr>
										<td class="title">平台链接</td>
										<td>
											<input type="text" name="v_url" id="v_url" class="ex-long">
										</td>
									</tr>
									<tr>										
										<td class="title">备注</td>
										<td>
											<input type="text" id="v_remark" name="v_remark" class="ex-long">
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
		<?php if($_SESSION['auth_platform']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="view_platform('save');">
					保存平台信息
				</button>
				<button class="ctrl_btn active add" onclick="view_platform('add');">
					添加平台
				</button>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>

<script>
var ctrl_id = '';
$(function() {
});

function check_form(){
	var check_pass = true;
	$('#platform_form .title.required').each(function(){
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

function fetch_platform_info(e){
	var params = 'platform_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_platform_info.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			var platform_name = '';
			<?php
			foreach($GLOBALS['platform_fields'] as $key){
				if($key=="name"){
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								$('#v_".$key."').val($key);
								platform_name = name;
							});";
				}
				else{
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								$('#v_".$key."').val($key);
							});";
				}
			}
			?>
			
			$('#view_platform_holder .header_holder .title').text('查看平台 ['+platform_name+']');
			$('input[name=action]').val('edit');
			$('input[name=id]').val(e);
			$('#view_platform_holder .ctrl_btn_holder .ctrl_btn.add').hide();
			$('#view_platform_holder .ctrl_btn_holder .ctrl_btn.save').show();
			$('#view_platform_holder').show().setOverlay();
		}
	});
}

function view_platform(e){
	if(e=='close'){
		$('#view_platform_holder').hide().setOverlay();
	}
	else if(e=='add'){
		if(check_form()){
			var params = $('#platform_form').serialize();
			$.ajax({
				type: 'post',
				url: 'platform_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功添加平台！','reload');
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
			var params = $('#platform_form').serialize();
			$.ajax({
				type: 'post',
				url: 'platform_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功保存平台信息！');
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
		foreach($GLOBALS['platform_fields'] as $key){
			print "$('#v_".$key."').val('');";
		}
		?>
		
		if(e=='create'){//create platform
			$('#view_platform_holder .header_holder .title').text('添加平台');
			$('#view_platform_holder select').each(function(){
				$(this).children('option:eq(0)').prop('selected',true);
			});
			$('input[name=action]').val('add');
			$('#view_platform_holder .ctrl_btn_holder .ctrl_btn.add').show();
			$('#view_platform_holder .ctrl_btn_holder .ctrl_btn.save').hide();
			$('#view_platform_holder').show().setOverlay();
		}
		else{//view platform
			ctrl_id = e;
			fetch_platform_info(e);
		}
	}
}
</script>
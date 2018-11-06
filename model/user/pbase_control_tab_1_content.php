<!--VIEW PBASE HOLDER-->
<div id="view_project_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_pbase('close');"></a>				
			</td>
		</tr>		
		<tr>
			<td>
				<form id="pbase_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
					<input type="hidden" name="action">
					<input type="hidden" name="id">
					<table style="width:100%;height:100%;" border="0" cellpadding="5" cellspacing="0">
						<tr height="30">
							<td class="title required">基础项目名称</td>
							<td>
								<input type="text" name="v_name" class="ex-long" id="v_name">
							</td>
						</tr>
						<tr height="30">								
							<td class="title">备注</td>
							<td>
								<input type="text" id="v_remark" name="v_remark" class="ex-long">
							</td>
						</tr>
						<tr>
							<td class="title">申报截止日期</td>
							<td>
								<div class="scroll_holder" style="width:99%;height:99%;margin:.5%;">
									<table width="99%" id="pbase_info_holder" border="0" cellpadding="0" cellspacing="0">
										<tr class="header_row">					
											<th width="50">年度</th>
											<th width="80">级别</th>
											<th>区域</th>
											<th width="90">电子提交截至</th>
											<th width="90">纸质提交截至</th>
											<th width="20"><a class="ctrl_link delete"></a></th>
										</tr>
									</table>
									<a class="ctrl_btn active" onclick="add_pbase_info();">添加记录</a>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<?php if($_SESSION['auth_pbase']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="view_pbase('save');">
					保存基础项目信息
				</button>
				<button class="ctrl_btn active add" onclick="view_pbase('add');">
					添加基础项目
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

function set_date_input(){
	$('.date_input').each(function(){
		$(this).datepicker({
			changeMonth:true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		});
	});
}

function set_info_delete(){
	$('.data_row.info .ctrl_link.delete').unbind('click').click(function(){
		$(this).parents('.data_row.info').fadeOut(function(){
			$(this).remove();
		});
	});
}

function add_pbase_info(){
	var timestamp = Math.round(new Date().getTime()/1000);
	$('#pbase_info_holder').append(
		'<tr id="info_'+timestamp+'" class="data_row info">' + 
			'<td>' + 
				'<input type="text" class="number" name="year_apply">' + 
			'</td>' + 
			'<td>' + 
				'<select name="level" id="level_'+timestamp+'">' + 
				<?php
				foreach($GLOBALS['project_level_opt'] as $key=>$val){
					print "'<option value=\"".$key."\">".$val."</option>' + ";
				}
				?>
				'</select>' + 
			'</td>' + 
			'<td>' + 
				'<select name="province" id="province_'+timestamp+'">' + 
				<?php
				$sql = "SELECT DISTINCT id,name FROM region WHERE parent_id=0 ORDER BY id";
				$stmt = mysql_query($sql);
				if(mysql_num_rows($stmt)>0){
					while($row = mysql_fetch_array($stmt)){
						$id = $row[0];
						$name = $row[1];
						$selected = "";  
						if($name=="广东")$selected = "selected";
						
						print "'<option value=\"".$id."\" ".$selected.">".$name."</option>' + ";
					}
				}
				?>
				'</select>' + 
				'<select name="city" id="city_'+timestamp+'"></select>' + 
				'<select name="district" id="district_'+timestamp+'"></select>' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" class="date_input" name="apply_deadline_e">' + 
			'</td>' + 
			'<td>' + 
				'<input type="text" class="date_input" name="apply_deadline_p">' + 
			'</td>' + 
			'<td><a class="ctrl_link delete"></a></td>' + 
		'</tr>'
	);
	set_date_input();
	set_info_delete();
	fetch_v_city(timestamp);
	set_v_province_change();
	set_v_city_change();
}

function fetch_v_district(e){
	var params = 'action=get_district&province='+$('#province_'+e).val()+'&city='+$('#city_'+e).val();
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_region.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			if(e==undefined){
				$('select[name=district]').html('<option value=""></option>');
				$(xml).find('district').each(function(){
					var id = $(this).find('id').text(),
						name = $(this).find('name').text();
					$('select[name=district]').append('<option value="'+id+'">'+name+'</option>');
				});
			}
			else{
				$('#district_'+e).html('<option value=""></option>');
				$(xml).find('district').each(function(){
					var id = $(this).find('id').text(),
						name = $(this).find('name').text();
					$('#district_'+e).append('<option value="'+id+'">'+name+'</option>');
				});
			}
		}
	});
}
function set_v_city_change(){
	$('select[name=city]').change(function(){
		var id = $(this).attr('id').replace('city_','');
		fetch_v_district(id);
	});
}
function fetch_v_city(e){
	var params = 'action=get_city&province='+$('#province_'+e).val();
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_region.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			if(e==undefined){
				$('select[name=city]').html('<option value=""></option>');
				$(xml).find('city').each(function(){
					var id = $(this).find('id').text(),
						name = $(this).find('name').text();
					$('select[name=city]').append('<option value="'+id+'">'+name+'</option>');
				});
			}
			else{
				$('#city_'+e).html('<option value=""></option>');
				$(xml).find('city').each(function(){
					var id = $(this).find('id').text(),
						name = $(this).find('name').text();
					$('#city_'+e).append('<option value="'+id+'">'+name+'</option>');
				});
			}
		}
	});
}
function set_v_province_change(){
	$('select[name=province]').change(function(){
		var id = $(this).attr('id').replace('province_','');
		fetch_v_city(id);
	});
}

function fetch_pbase_info(e){
	var params = 'pbase_id='+e;	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_pbase_info.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			var pbase_name = '';
			<?php
			foreach($GLOBALS['pbase_fields'] as $key){
				if($key=="name"){
					print "	$(xml).find('".$key."').each(function(){
								var ".$key." = $(this).text();
								$('#v_".$key."').val($key);
								pbase_name = name;
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
			$('#pbase_info_holder .data_row').remove();
			$(xml).find('info').each(function(){
				var tmp_id = $(this).find('tmp_id').text(),
					year_apply = $(this).find('year_apply').text(),
					level = $(this).find('level').text(),
					province = $(this).find('province').text(),
					city = $(this).find('city').text(),
					district = $(this).find('district').text(),
					apply_deadline_e = $(this).find('apply_deadline_e').text(),
					apply_deadline_p = $(this).find('apply_deadline_p').text(),
					i_remark = $(this).find('i_remark').text();
				
				$('#pbase_info_holder').append(
					'<tr id="info_'+tmp_id+'" class="data_row info">' + 
						'<td>' + 
							'<input type="text" class="number" name="year_apply" value="'+year_apply+'">' + 
						'</td>' + 
						'<td>' + 
							'<select name="level" id="level_'+tmp_id+'">' + 
							<?php
							foreach($GLOBALS['project_level_opt'] as $key=>$val){
								print "'<option value=\"".$key."\">".$val."</option>' + ";
							}
							?>
							'</select>' + 
						'</td>' + 
						'<td>' + 
							'<select name="province" id="province_'+tmp_id+'">' + 
							<?php
							$sql = "SELECT DISTINCT id,name FROM region WHERE parent_id=0 ORDER BY id";
							$stmt = mysql_query($sql);
							if(mysql_num_rows($stmt)>0){
								while($row = mysql_fetch_array($stmt)){
									$id = $row[0];
									$name = $row[1];
									$selected = "";  
									if($name=="广东")$selected = "selected";
									
									print "'<option value=\"".$id."\" ".$selected.">".$name."</option>' + ";
								}
							}
							?>
							'</select>' + 
							'<select name="city" id="city_'+tmp_id+'"></select>' + 
							'<select name="district" id="district_'+tmp_id+'"></select>' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" class="date_input" name="apply_deadline_e" value="'+apply_deadline_e+'">' + 
						'</td>' + 
						'<td>' + 
							'<input type="text" class="date_input" name="apply_deadline_p" value="'+apply_deadline_p+'">' + 
						'</td>' + 
						'<td><a class="ctrl_link delete"></a></td>' + 
					'</tr>'
				);
				$('#level_'+tmp_id).val(level);
				$('#province_'+tmp_id).val(province);
				fetch_v_city(tmp_id);
				$('#city_'+tmp_id).val(city);
				fetch_v_district(tmp_id);
				$('#district_'+tmp_id).val(district);
				
				set_v_province_change();
				set_v_city_change();
			});
			set_date_input();
			set_info_delete();
			
			$('#view_project_holder .header_holder .title').text('查看基础项目 ['+pbase_name+']');
			$('input[name=action]').val('edit');
			$('input[name=id]').val(e);
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.add').hide();
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.save').show();
			$('#view_project_holder').show().setOverlay();
		}
	});
}

function view_pbase(e){
	if(e=='close'){
		$('#view_project_holder').hide().setOverlay();
	}
	else if(e=='add'){
		if(check_form()){
			var params = $('#pbase_form').serialize();
			$.ajax({
				type: 'post',
				url: 'pbase_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('成功添加基础项目！','reload');
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
			var params = $('#pbase_form').serialize();
			
			var info_str = '',
				flag = true,
				j = 1;
			$('.data_row.info').each(function(){
				var year_apply = $(this).find('input[name=year_apply]').val(),
					level = $(this).find('select[name=level]').val(),
					province = $(this).find('select[name=province]').val(),
					city = $(this).find('select[name=city]').val(),
					district = $(this).find('select[name=district]').val(),
					apply_deadline_e = $(this).find('input[name=apply_deadline_e]').val(),
					apply_deadline_p = $(this).find('input[name=apply_deadline_p]').val();
					
				if(year_apply==''){
					alert('申报年度不能为空！');
					flag = false;
				}
				
				info_str += '&info[]='+j+
							'&year_apply_'+j+'='+year_apply+
							'&level_'+j+'='+level+
							'&province_'+j+'='+((province!=undefined)?province:'')+
							'&city_'+j+'='+((city!=undefined)?city:'')+
							'&district_'+j+'='+((district!=undefined)?district:'')+
							'&apply_deadline_e_'+j+'='+apply_deadline_e+
							'&apply_deadline_p_'+j+'='+apply_deadline_p;
				j++;
			});
			if(!flag)return flag;
			else params += info_str;
			
			$.ajax({
				type: 'post',
				url: 'pbase_manage.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						alert('成功保存基础项目信息！');
						fetch_pbase_info(ctrl_id);
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
		foreach($GLOBALS['pbase_fields'] as $key){
			print "$('#v_".$key."').val('');";
		}
		?>
		
		if(e=='create'){//create pbase
			$('#view_project_holder .header_holder .title').text('添加基础项目');
			$('#view_project_holder select').each(function(){
				$(this).children('option:eq(0)').prop('selected',true);
			});
			$('input[name=action]').val('add');
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.add').show();
			$('#view_project_holder .ctrl_btn_holder .ctrl_btn.save').hide();
			$('#view_project_holder').show().setOverlay();
		}
		else{//view pbase
			ctrl_id = e;
			fetch_pbase_info(e);
		}
	}
}

function check_form(){
	var check_pass = true;
	$('#pbase_form .title.required').each(function(){
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
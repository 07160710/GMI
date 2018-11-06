<!--TAB PANEL 1-->
<table id="tab_panel_1" class="tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
<table class="header_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="sidebar">
			<div id="hide_arrow_holder">
				<a id="hide_arrow" title="收起侧栏" onclick="display_sidebar();"></a>
			</div>
		</td>
		<td>
			<?php if($_SESSION['auth_company']==2){ ?>
			<table class="data_ctrl_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="ctrl_holder">
<div class="ctrl_link">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span>操作</span>
			</td>
			<td>
				<div class="menu_holder">
					<a class="ctrl_arrow"></a>
					<div class="ctrl_menu">
						<a class="item" onclick="view_company('create');">
							创建公司
						</a>
						<a class="item" onclick="multi_edit_company('company');">
							导出公司
						</a>
						<?php if($_SESSION['level']>2){ ?>
						<a class="item" onclick="import_company();">
							导入公司
						</a>
						<a class="item" onclick="multi_edit_company('delete');">
							删除公司
						</a>
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="ctrl_link">										
	<label for="all_page_company">
		<input type="checkbox" class="check_all" id="all_page_company">
		选取所有分页公司
	</label>
</div>
<div class="data_count_holder">	
	<span id="company_count" class="data_count">	
		<b></b>&nbsp;
		个公司
	</span>
</div>

<select id="per_page">
	<option>20</option>
	<option>50</option>
	<option>100</option>
	<option>300</option>
	<option>500</option>
</select>
&nbsp;条 / 页
					</td>
				</tr>
			</table>
			<?php } ?>
		</td>
	</tr>
</table>
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
						
						<div id="filter_name" class="list_holder">
							<span class="title">
								公司名称
							</span>
							<input type="text" id="search_name">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						
						<!--province-->
						<div id="filter_province" class="list_holder">
							<span>								
								<label for="all_province">
									<input type="checkbox" class="check_all" id="all_province">
									所有省
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--city-->
						<div id="filter_city" class="list_holder">
							<span>								
								<label for="all_city">
									<input type="checkbox" class="check_all" id="all_city">
									所有市
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--district-->
						<div id="filter_district" class="list_holder">
							<span>								
								<label for="all_district">
									<input type="checkbox" class="check_all" id="all_district">
									所有区
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
			<table id="company_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table id="company_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr class="header_row">
								<th width="10"><input type="checkbox" class="check_all" id="curr_page_company"></th>
								<th width="30">
									查看
								</th>
								<?php
								foreach($GLOBALS['company_list_fields'] as $key=>$val){
									$width_str = "";
									if(strpos($val,"#")!==false){
										$val_arr = explode("#",$val);
										$width_str = 'width="'.$val_arr[1].'"';
										$val = $val_arr[0];
									}
									$sort_arrow = "";
									if($key=="name" || $key=="province" || $key=="city" || $key=="district")$sort_arrow = "<i></i>";
									
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

<?php include("company_control_tab_1_content.php"); ?>
<?php include("public_project_js.php"); ?>
<?php include("public_body_js.php"); ?>

<script>
function import_company(){
	$('#import_holder .title').text('导入公司列表');
	$('#import_holder').show().setOverlay();
	$('#file_excel').change(function(){console.log('here');
		show_alert('正在导入数据，请稍候 ...','load');
		$.ajaxFileUpload({
			url:'company_manage.php?action=import_company',
			secureuri :false,
			fileElementId :'file_excel',
			dataType : 'json',
			success : function (result){
				if(result.success==1){
					show_alert(
						'成功导入公司记录！<br/>' + 
						'读取 ' + result.read_num + ' 条记录<br/>' + 
						'插入 ' + result.insert_num + ' 条记录<br/>' + 
						'更新 ' + result.update_num + ' 条记录'
					);
					filter_search(curr_page);
				}
				else{
					alert(result.error);
				}
			}
		});
	});
}

function get_filter_cond(){	
	$('.list_holder').each(function(){
		var id = $(this).attr('id'),
			filter_id = id.replace('filter_','');
			
		var filter_type = '';
		switch(filter_id){
			case 'name': filter_type = '公司'; break;
			case 'province': filter_type = '省'; break;
			case 'city': filter_type = '市'; break;
			case 'district': filter_type = '区'; break;
		}
		
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
	});
}

function filter_search(e){
	$('.body_holder').append('<div class="loading cover"></div>');
	get_filter_cond();	
	
	var params = '';	
	params = get_search_cond(params);	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_company_filter.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('.check_all').prop('checked',false);
			
			<?php
			foreach($GLOBALS['company_filter'] as $val){
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
			
			search_company(e);
		}
	});
}

function multi_edit_company(e){
	if($('#company_list .data_row input[type=checkbox]:checked').length==0){
		alert('请选择公司进行操作！');
		return false;
	}
	
	var params = '';
	if($('#all_page_company').is(':checked')){//edit all page companys
		params += '&scope=all';
		params = get_search_cond(params);
	}
	else{//edit current page companys
		$('#company_list .data_row input[type=checkbox]:checked').each(function(){
			var company_id = $(this).parents('.data_row').attr('id').replace('company_','');
			if(params!='')params += '&';
			params += 'company_id[]='+company_id;
		});
	}
	
	if(e=='delete'){
		if(confirm('删除选定的公司将不能恢复，确定删除？')){
			show_alert('正在删除公司，请稍候 ...','load');
			
			params += '&action=delete_company';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_company').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功删除选定的公司！');
							search_company(curr_page);
						}
					}
					else{
						alert(result.error);
					}	
				}
			});
		}
	}
	else{
		params += '&action=export_'+e;
		window.location.href = 'company_manage.php?'+params;
	}
}

function set_company_sort(){
	$('#company_list th a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#company_list .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#company_list .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,company_sort_arr)==-1)company_sort_arr.push(sort_id);
			}
			else if($('#company_list .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#company_list .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,company_sort_arr);
			}
			else{//switch to asc
				$('#company_list .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,company_sort_arr)==-1)company_sort_arr.push(sort_id);
			}
			
			search_company();
		}
	});
}

function search_company(e){
	var params = 'per_page='+per_page;
	params = get_search_cond(params);
	
	if(e!=undefined){
		if(e=='find'){
			company_sort_arr.length = 0;
			$('#company_list th a i').attr('class','');
			$('#company_list th a').unbind('click');
		}
		else{
			params += '&page='+e;
		}
	}
	
	params = get_sort_params(params,company_sort_arr);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_company.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('.body_holder .loading.cover').remove();
			$('#company_list #curr_page_company').prop('checked',false);			
			$('#company_list tr.data_row').remove();
			
			$(xml).find('company_num').each(function(){
				var company_num = $(this).text();
				$('#company_count b').html(company_num);
			});
			
			$(xml).find('curr_page').each(function(){
				curr_page = parseInt($(this).text());
			});
			
			$('#company_data #pagination').html('');
			$(xml).find('page_num').each(function(){
				var page_num = parseInt($(this).text());				
				for(var p=1;p<=page_num;p++){
					var curr_class = '';
					if(curr_page==p)curr_class = 'class="curr"';
					
					if(page_num>page_span*3){						
						if(p<=page_span || p>(page_num-page_span)){
							$('#company_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
						}	
						else{
							if(curr_page<=page_span){//if curr page at the front
								if(p<=curr_page+Math.floor(page_span/2)){
									$('#company_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p<page_num-page_span){
									if($('#company_data #pagination li.dot').length==0)$('#company_data #pagination').append('<li class="dot">...</li>');
								}
							}
							
							if(curr_page>page_span && curr_page<=page_num-page_span){//if curr page in the middle
								if(p>page_span && p<curr_page-Math.floor(page_span/2)){
									if($('#company_data #pagination li.dot.first').length==0)$('#company_data #pagination').append('<li class="dot first">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2) && p<=curr_page+Math.floor(page_span/2)){
									$('#company_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p>page_num-page_span-Math.floor(page_span/2)){
									if($('#company_data #pagination li.dot.second').length==0)$('#company_data #pagination').append('<li class="dot second">...</li>');
								}
							}
							
							if(curr_page>page_num-page_span){//if curr page at the end
								if(p<curr_page-Math.floor(page_span/2)){
									if($('#company_data #pagination li.dot').length==0)$('#company_data #pagination').append('<li class="dot">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2)){
									$('#company_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
							}
						}
					}
					else{
						$('#company_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
					}
				}
				
				$('#company_data #pagination li').click(function(){
					if(!$(this).hasClass('curr') && !$(this).hasClass('dot')){
						curr_page = parseInt($(this).text());
						search_company(curr_page);
					}
				});
			});
			
			var i = 0;
			$(xml).find('company').each(function(){
				var company_id = $(this).find('company_id').text();
				<?php
				foreach($GLOBALS['company_list_fields'] as $key=>$val){
					print "	var ".$key." = $(this).find('$key').text();
							var ".$key."_status = '';
							if(".$key.".indexOf('|')>=0){
								var ".$key."_arr = ".$key.".split('|');
								".$key." = ".$key."_arr[0];
								".$key."_status = ".$key."_arr[1];
								if(".$key."_status=='alarm')alarm_class = 'alarm';
							}";
				}
				?>
				
				var alt_class = '';
				if(i%2!=0)alt_class = 'alt';
				
				$('#company_list').append(
					'<tr id="company_'+company_id+'" class="data_row '+alt_class+'">' + 
						'<td>' + 
							'<input type="checkbox">' + 
						'</td>' + 
						'<td align="center">' + 
							'<a onclick="view_company(\''+company_id+'\');">查看</a>' + 
						'</td>' + 
						<?php
						foreach($GLOBALS['company_list_fields'] as $key=>$val){
							$align_str = 'align="center"';
							if($key=="code")print "'<td $align_str class=\"'+".$key."_status+'\"><input type=\"hidden\" id=\"code_'+company_id+'\" value=\"'+$key+'\"><a class=\"copy_btn\" id=\"copy_code_'+company_id+'\" title=\"复制到粘贴板\">'+$key+'</a></td>' + ";
							else print "'<td $align_str class=\"'+".$key."_status+'\">'+$key+'</td>' + ";
						}
						?>
					'</tr>'
				);
				i++;
			});
			set_clipboard();
			set_company_sort();
			set_check_company();
			$('#company_list .loading').fadeOut();
			
			if($('#all_page_company').is(':checked')){
				$('#curr_page_company').prop('checked',true);
				$('#company_list td input[type=checkbox]').prop('checked',true);
				$('#company_list tr').addClass('selected');
			}
		}
	});
}

function set_check_company(){
	$('#company_list #curr_page_company').change(function(){
		if($(this).is(':checked')){
			$('#company_list td input[type=checkbox]').prop('checked',true);
			$('#company_list tr').addClass('selected');
		}
		else{
			$('#all_page_company').prop('checked',false);
			$('#company_list td input[type=checkbox]').prop('checked',false);
			$('#company_list tr').removeClass('selected');
		}
	});
	
	$('#all_page_company').change(function(){
		if($(this).is(':checked')){
			$('#curr_page_company').prop('checked',true);
			$('#company_list td input[type=checkbox]').prop('checked',true);
			$('#company_list tr').addClass('selected');
		}
		else{
			$('#curr_page_company').prop('checked',false);
			$('#company_list td input[type=checkbox]').prop('checked',false);
			$('#company_list tr').removeClass('selected');
		}
	});
	
	$('#company_list td input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('selected');
		}
		else{
			$('#all_page_company').prop('checked',false);
			$('#company_list #curr_page_company').prop('checked',false);
			$(this).closest('tr').removeClass('selected');
		}
	});
}
</script>
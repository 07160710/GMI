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
			<?php if($_SESSION['auth_nbase']==2){ ?>
			<table class="data_ctrl_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="ctrl_holder">
<div class="ctrl_link">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span>
					操作
				</span>
			</td>
			<td>
				<div class="menu_holder">
					<a class="ctrl_arrow"></a>
					<div class="ctrl_menu">
						<a class="item" onclick="view_nbase('create');">
							创建通知
						</a>
						<a class="item" onclick="multi_edit_nbase('publish');">
							发布通知
						</a>
						<?php if($_SESSION['level']>2){ ?>
						<a class="item" onclick="import_nbase();">
							导入通知
						</a>
						<a class="item" onclick="multi_edit_nbase('nbase');">
							导出通知
						</a>
						<a class="item" onclick="multi_edit_nbase('delete');">
							删除通知
						</a>
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="ctrl_link">										
	<label for="all_page_nbase">
		<input type="checkbox" class="check_all" id="all_page_nbase">
		选取所有分页通知
	</label>
</div>
<div class="data_count_holder">	
	<span id="nbase_count" class="data_count">	
		<b></b>&nbsp;
		个通知
	</span>
</div>

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
								通知名称
							</span>
							<input type="text" id="search_name">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						<div id="filter_content" class="list_holder">
							<span class="title">
								通知正文
							</span>
							<input type="text" id="search_content">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						
						<!--发文时段-->
						<div id="filter_period" class="list_holder">
							<span class="title">发文时段</span>
							<input type="text" id="release_start_date" class="date_input" style="padding:0 3px;" placeholder="开始日期">
							 - 
							<input type="text" id="release_end_date" class="date_input" style="margin-left:0;padding:0 3px;" placeholder="结束日期">
							<button onclick="filter_search();">筛选</button>
						</div>
						
						<!--通知类型-->
						<div id="filter_notice_type" class="list_holder">
							<span>								
								<label for="all_notice_type">
									<input type="checkbox" class="check_all" id="all_notice_type">
									所有通知类别
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>

						<!--创建者-->
						<div id="filter_created_by" class="list_holder">
							<span>								
								<label for="all_created_by">
									<input type="checkbox" class="check_all" id="all_created_by">
									所有创建者
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--省-->
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
						<!--市-->
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
						<!--区-->
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
						<!--部委-->
						<div id="filter_bureau" class="list_holder">
							<span>
								<label for="all_bureau">
									<input type="checkbox" class="check_all" id="all_bureau">
									所有部委
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--支持方式-->
						<div id="filter_policy_type" class="list_holder">
							<span>								
								<label for="all_policy_type">
									<input type="checkbox" class="check_all" id="all_policy_type">
									所有支持方式
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
			<table id="nbase_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table id="nbase_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr class="header_row">
								<th width="10"><input type="checkbox" class="check_all" id="curr_page_nbase"></th>
								<th width="10"><a class="btn_preview" title="查看/编辑"></a></th>
								<?php
								foreach($GLOBALS['nbase_list_fields'] as $key=>$val){
									$width_str = "";
									if(strpos($val,"#")!==false){
										$val_arr = explode("#",$val);
										$width_str = 'width="'.$val_arr[1].'"';
										$val = $val_arr[0];
									}
									
									$sort_arrow = "<i></i>";
									if($key=="transfer")$sort_arrow = "";
									
									print "<th $width_str><a class=\"sort_$key\">".$val.$sort_arrow."</a></th>";
								}
								?>
								<?php if($_SESSION['auth_nbase']==2){ ?>
								<th width="10"><a class="btn_display" title="发布/隐藏"></a></th>
								<?php } ?>
							</tr>
						</table>
						<ul id="pagination"></ul>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php include("nbase_control_tab_1_content.php"); ?>

<script>
$(function() {

	$('.check_all').change(function(){
		if($(this).is(':checked')){
			$(this).parents('.list_holder').find('.filter_list input[type=checkbox]').prop('checked',true);
		}
		else{
			$(this).parents('.list_holder').find('.filter_list input[type=checkbox]').prop('checked',false);
		}
	});
	set_img_upload();
	filter_search();
});

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

function set_btn_import(){	
	$('#file_excel').change(function(){
		show_alert('正在导入数据，请稍候 ...','load');
		$.ajaxFileUpload({
			url:'nbase_manage.php?action=import_nbase',
			secureuri :false,
			fileElementId :'file_excel',
			dataType : 'json',
			success : function (result){
				if(result.success==1){
					show_alert(
						'成功导入政策通知通知！<br/>' + 
						'读取 ' + result.read_num + ' 条通知<br/>' + 
						'插入 ' + result.insert_num + ' 条通知<br/>' + 
						'更新 ' + result.update_num + ' 条通知'
					);
					filter_search();
					set_btn_import();
				}
				else{
					show_alert(result.error);
				}
			}
		});
	});
}

function import_nbase(){
	var import_title = '导入政策通知',
		alert_str = '导入表头格式：序号 | 区域 | 部委名称 | 发文时间(年/月/日) | 通知名称 | 申报截止时间 | 政策类别 | 支持领域 | 申报条件 | 公示名单链接 | 网址 | 备注';
	
	$('#import_holder .title').text(import_title);
	$('#import_holder .alert_holder').text(alert_str);
	$('#import_holder').show().setOverlay();
	set_btn_import();
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
			case 'name': filter_type = '名称'; break;
			case 'content': filter_type = '正文'; break;
			case 'period': filter_type = '时段'; break;
			case 'created_by': filter_type = '创建'; break;
			case 'province': filter_type = '省'; break;
			case 'city': filter_type = '市'; break;
			case 'district': filter_type = '区'; break;
			case 'bureau': filter_type = '部委'; break;
			case 'policy_type': filter_type = '类别'; break;
			case 'notice_type': filter_type = '推荐'; break;
		}
		
		if(filter_id=='period'){
			var start_date = $('#release_start_date').val(),
				end_date = $('#release_end_date').val();
			
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
				$('#enroll_start_date').val('');
				$('#enroll_end_date').val('');
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
		
		if(type=='period'){
			var start_date = $(this).children('input[name=start_date]').val().trim(),
				end_date = $(this).children('input[name=end_date]').val().trim();
			
			if(params)params += '&';
			if(start_date!='')params += 'release_start_date='+start_date;
			if(params)params += '&';
			if(end_date!='')params += 'release_end_date='+end_date;
		}
		else{
			var val = $(this).children('input[name='+type+']').val().trim();
			
			if(params)params += '&';
			params += type+'[]='+encodeURIComponent(val);
		}
	});
	return params;
}

function filter_search(e){
	get_filter_cond();	
	
	var params = '';	
	params = get_search_cond(params);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_nbase_filter.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('.check_all').prop('checked',false);
			
			<?php
			foreach($GLOBALS['nbase_filter'] as $val){
				if($val=="region"){
					print "	$('#filter_province .filter_list').html('');
							$(xml).find('province').each(function(){
								var province = $(this).text().split('|'),
									key = province[0],
									val = province[1];
								
								$('#filter_province .filter_list').append(
									'<li>' + 
										'<label for=\"province_'+key+'\">' + 
											'<input type=\"checkbox\" id=\"province_'+key+'\" value=\"'+key+'\">' + 
											val + 
										'</label>' + 
									'</li>'
								);
							});
							
							$('#filter_city .filter_list').html('');
							$(xml).find('city').each(function(){
								var city = $(this).text().split('|'),
									key = city[0],
									val = city[1];
								
								$('#filter_city .filter_list').append(
									'<li>' + 
										'<label for=\"city_'+key+'\">' + 
											'<input type=\"checkbox\" id=\"city_'+key+'\" value=\"'+key+'\">' + 
											val + 
										'</label>' + 
									'</li>'
								);
							});
							
							$('#filter_district .filter_list').html('');
							$(xml).find('district').each(function(){
								var district = $(this).text().split('|'),
									key = district[0],
									val = district[1];
								
								$('#filter_district .filter_list').append(
									'<li>' + 
										'<label for=\"district_'+key+'\">' + 
											'<input type=\"checkbox\" id=\"district_'+key+'\" value=\"'+key+'\">' + 
											val + 
										'</label>' + 
									'</li>'
								);
							});";
				}
				else{
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
			}
			?>
			
			$('#filter_notice_type .filter_list').html('');
			var is_top = $(xml).find('is_top').text();
			var is_hot = $(xml).find('is_hot').text();
			var is_recommend = $(xml).find('is_recommend').text();
			$('#filter_notice_type .filter_list').append(
				'<li>' + 
					'<label for=\"is_top\">' + 
						'<input type=\"checkbox\" id=\"is_top\" value=\"is_top\">' + 
						'置顶（'+is_top+'）'+
					'</label>' + 
				'</li>'+
				'<li>' + 
					'<label for=\"is_hot\">' + 
						'<input type=\"checkbox\" id=\"is_hot\" value=\"is_hot\">' + 
						'热点（'+is_hot+'）'+
					'</label>' + 
				'</li>'+'<li>' + 
					'<label for=\"is_recommend\">' + 
						'<input type=\"checkbox\" id=\"is_recommend\" value=\"is_recommend\">' + 
						'推荐（'+is_recommend+'）'+
					'</label>' + 
				'</li>'
			);

			search_nbase(e);
		}
	});
}

function multi_edit_nbase(e){
	if($('#nbase_list .data_row input[type=checkbox]:checked').length==0){
		alert('请选择通知进行操作！');
		return false;
	}
	
	var params = '';
	if($('#all_page_nbase').is(':checked')){//edit all page nbases
		params += '&scope=all';
		params = get_search_cond(params);
	}
	else{//edit current page nbases
		$('#nbase_list .data_row input[type=checkbox]:checked').each(function(){
			var n_id = $(this).parents('.data_row').attr('id').replace('n_','');
			if(params!='')params += '&';
			params += 'n_id[]='+n_id;
		});
	}
	
	if(e=='delete'){
		if(confirm('删除选定的通知将不能恢复，确定删除？')){
			show_alert('正在删除通知，请稍候 ...','load');
			
			params += '&action=delete_nbase';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_nbase').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功删除选定的通知！');
							filter_search(curr_page);
						}
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	else if(e=='publish'){
		if(confirm('确定发布所选通知？')){
			show_alert('正在发布通知，请稍候 ...','load');
			
			params += '&action=publish_nbase';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_nbase').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功发布选定的通知！');
							filter_search(curr_page);
						}
					}
					else{
						show_alert(result.error);
					}	
				}
			});
		}
	}
	else{
		params += '&action=export_'+e;		
		window.location.href = 'nbase_manage.php?'+params;
	}
}

function set_nbase_sort(){
	$('#nbase_list th a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#nbase_list .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#nbase_list .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,nbase_sort_arr)==-1)nbase_sort_arr.push(sort_id);
			}
			else if($('#nbase_list .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#nbase_list .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,nbase_sort_arr);
			}
			else{//switch to asc
				$('#nbase_list .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,nbase_sort_arr)==-1)nbase_sort_arr.push(sort_id);
			}
			
			search_nbase();
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

function search_nbase(e){
	$('#nbase_list').append('<div class="loading cover"></div>');
	
	var params = '';
	params = get_search_cond(params);
	
	if(e!=undefined){
		if(e=='find'){
			nbase_sort_arr.length = 0;
			$('#nbase_list th a i').attr('class','');
			$('#nbase_list th a').unbind('click');
		}
		else{
			params += '&page='+e;
		}
	}
	
	params = get_sort_params(params,nbase_sort_arr);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_nbase.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('#nbase_list #curr_page_nbase').prop('checked',false);			
			$('#nbase_list tr.data_row').remove();
			
			$(xml).find('nbase_num').each(function(){
				var nbase_num = $(this).text();
				$('#nbase_count b').html(nbase_num);
			});
			
			$(xml).find('enroll_sum').each(function(){
				var power_sum = $(this).text();
				$('#enroll_sum b').html(power_sum);
			});
			
			$(xml).find('leave_sum').each(function(){
				var power_sum = $(this).text();
				$('#leave_sum b').html(power_sum);
			});
			
			$(xml).find('curr_page').each(function(){
				curr_page = parseInt($(this).text());
			});
			
			$('#nbase_data #pagination').html('');
			$(xml).find('page_num').each(function(){
				var page_num = parseInt($(this).text());				
				for(var p=1;p<=page_num;p++){
					var curr_class = '';
					if(curr_page==p)curr_class = 'class="curr"';
					
					if(page_num>page_span*3){						
						if(p<=page_span || p>(page_num-page_span)){
							$('#nbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
						}	
						else{
							if(curr_page<=page_span){//if curr page at the front
								if(p<=curr_page+Math.floor(page_span/2)){
									$('#nbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p<page_num-page_span){
									if($('#nbase_data #pagination li.dot').length==0)$('#nbase_data #pagination').append('<li class="dot">...</li>');
								}
							}
							
							if(curr_page>page_span && curr_page<=page_num-page_span){//if curr page in the middle
								if(p>page_span && p<curr_page-Math.floor(page_span/2)){
									if($('#nbase_data #pagination li.dot.first').length==0)$('#nbase_data #pagination').append('<li class="dot first">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2) && p<=curr_page+Math.floor(page_span/2)){
									$('#nbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p>page_num-page_span-Math.floor(page_span/2)){
									if($('#nbase_data #pagination li.dot.second').length==0)$('#nbase_data #pagination').append('<li class="dot second">...</li>');
								}
							}
							
							if(curr_page>page_num-page_span){//if curr page at the end
								if(p<curr_page-Math.floor(page_span/2)){
									if($('#nbase_data #pagination li.dot').length==0)$('#nbase_data #pagination').append('<li class="dot">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2)){
									$('#nbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
							}
						}
					}
					else{
						$('#nbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
					}
				}
				
				$('#nbase_data #pagination li').click(function(){
					if(!$(this).hasClass('curr') && !$(this).hasClass('dot')){
						curr_page = parseInt($(this).text());
						search_nbase(curr_page);
					}
				});
			});
			
			var i = 0;
			$(xml).find('nbase').each(function(){
				var n_id = $(this).find('n_id').text();
				<?php
				foreach($GLOBALS['nbase_list_fields'] as $key=>$val){
					print "var ".$key." = $(this).find('$key').text();";
				}
				?>
				var publish = $(this).find('publish').text();
				
				var alt_class = '';
				if(i%2!=0)alt_class = 'alt';
				
				$('#nbase_list').append(
					'<tr id="n_'+n_id+'" class="data_row '+alt_class+'">' + 
						'<td>' + 
							'<input type="checkbox">' + 
						'</td>' + 
						'<td align="center">' + 
							'<a class="btn_preview" title="查看/编辑" onclick="view_nbase(\''+n_id+'\');"></a>' + 
						'</td>' + 
						<?php
						foreach($GLOBALS['nbase_list_fields'] as $key=>$val){
							$align_str = 'align="center"';
							if($key=="name")$align_str = '';
							
							print "'<td class=\"".$key."\" $align_str>'+$key+'</td>' + ";
						}
						?>
						<?php if($_SESSION['auth_nbase']==2){ ?>
						'<td style="width:30px;">' + 
							'<a class="btn_display '+((publish==0)?'hide':'')+'" onclick="publish_notice(\''+n_id+'\',\''+((publish==0)?1:0)+'\');" target="_blank" title="'+((publish==1)?'已发布':'未发布')+'"></a>' + 
						'</td>' + 
						<?php } ?>
					'</tr>'
				);
				i++;
			});
			
			set_nbase_sort();
			set_check_nbase();
			$('#nbase_list .loading').fadeOut();
			
			if($('#all_page_nbase').is(':checked')){
				$('#curr_page_nbase').prop('checked',true);
				$('#nbase_list td input[type=checkbox]').prop('checked',true);
				$('#nbase_list tr').addClass('selected');
			}
		}
	});
}

function publish_notice(e,type){
	var params = 'action=publish_notice&id='+e+'&type='+type;
	$.ajax({
		data: 	params,
		type:	'post',
		url: 	post_url,
		dataType: 'json',
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

function set_check_nbase(){
	$('#nbase_list #curr_page_nbase').change(function(){
		if($(this).is(':checked')){
			$('#nbase_list td input[type=checkbox]').prop('checked',true);
			$('#nbase_list tr').addClass('selected');
		}
		else{
			$('#all_page_nbase').prop('checked',false);
			$('#nbase_list td input[type=checkbox]').prop('checked',false);
			$('#nbase_list tr').removeClass('selected');
		}
	});
	
	$('#all_page_nbase').change(function(){
		if($(this).is(':checked')){
			$('#curr_page_nbase').prop('checked',true);
			$('#nbase_list td input[type=checkbox]').prop('checked',true);
			$('#nbase_list tr').addClass('selected');
		}
		else{
			$('#curr_page_nbase').prop('checked',false);
			$('#nbase_list td input[type=checkbox]').prop('checked',false);
			$('#nbase_list tr').removeClass('selected');
		}
	});
	
	$('#nbase_list td input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('selected');
		}
		else{
			$('#all_page_nbase').prop('checked',false);
			$('#nbase_list #curr_page_nbase').prop('checked',false);
			$(this).closest('tr').removeClass('selected');
		}
	});
}

function delete_img(n_id){
	if($('#img_ctrl_area img').length>0){
		if(confirm('确定删除此图片？')){
			show_alert('正在删除图片，请稍候 ...','load');
			
			$.ajax({
				type: 'post',
				url: 'img_delete.php?id='+n_id,
				dataType: 'json',
				//data: params,
				success: function(result){
					if(result.success==1){
						$('.image_show_type1').html(
							'<input name="n_image" class="btn_file" type="file" id="n_image">' + 
							'<span class="btn_upload"><?php print ($_SESSION['u_lang']==0)?"Upload Image":"上传图片";?></span>' 
						);
						set_public_attr();
						set_img_upload();
						alert('成功	图片！');
					}
					else{
						show_alert(result.error);
					}
				}
			});
		}
	}
}

function upload_img(){	
	$.ajaxFileUpload({
		url:'img_upload.php?object=content&id='+$('#v_id').val(),
		secureuri :false,
		fileElementId :'n_image',
		dataType : 'json',
		success : function (result){
			if(result.success==1){
				var delete_ctrl_str = 	'<a class="ctrl_arrow"></a>' + 
										'<div class="ctrl_menu">' + 
											'<input name="n_image" class="btn_file" type="file" id="n_image">' + 
											'<a class="item replace"><?php print ($_SESSION['u_lang']==0)?"替换":"替换";?></a>' + 
											'<a class="item crop" onclick="crop_img(\'<?php print _BASE_URL_;?>'+result.file_url+'\');"><?php print ($_SESSION['u_lang']==0)?"裁切":"裁切";?></a>' + 
											'<a class="item delete" onclick="delete_img(\''+$('#v_id').val()+'\')"><?php print ($_SESSION['u_lang']==0)?"	":"删除";?></a>' + 
										'</div>';
				
				$('.image_show_type2').html(
					'<img width="182" height="132" src="<?php print _BASE_URL_;?>'+result.file_url+'">' + 
					'<div id="img_ctrl" class="menu_holder">' + 
						delete_ctrl_str + 
					'</div>'
				);
				set_public_attr();
				set_img_upload();
				show_alert('成功上传图片！');
			}
			else{
				show_alert(result.error);
			}
		}
	});
}

function set_img_upload(){
	$('#n_image').change(function(){
		show_alert('正在上传图片，请稍候 ...','load');
		upload_img();
	});
}
</script>
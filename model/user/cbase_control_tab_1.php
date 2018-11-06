<!--TAB PANEL 1-->
<table id="tab_panel_1" class="data_holder tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td id="left_col" valign="top">
			<div id="hide_arrow_holder">
				<a id="hide_arrow" title="收起侧栏" onclick="display_sidebar();"></a>
			</div>
			<table id="sidebar" width="100%" border="0" cellpadding="0" cellspacing="0">
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
						
						<div id="filter_company" class="list_holder">
							<span class="title">
								企业名称
							</span>
							<input type="text" id="search_company">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						
						<!--认定类型-->
						<div id="filter_type" class="list_holder">
							<span>								
								<label for="all_type">
									<input type="checkbox" class="check_all" id="all_type">
									所有认定类型
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--年份-->
						<div id="filter_year" class="list_holder">
							<span>								
								<label for="all_year">
									<input type="checkbox" class="check_all" id="all_year">
									所有年份
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--批次-->
						<div id="filter_batch" class="list_holder">
							<span>								
								<label for="all_batch">
									<input type="checkbox" class="check_all" id="all_batch">
									所有批次
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
						
					</td>
				</tr>
			</table>
		</td>
		<td id="right_col" valign="top">			
			<table id="cbase_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table id="cbase_ctrl_holder" class="data_ctrl_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
							<?php if($_SESSION['level']>1){ ?>
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
						<a class="item" onclick="multi_edit_cbase('cbase');">
							导出记录
						</a>
						<?php if($_SESSION['level']>2){ ?>
						<a class="item" onclick="import_cbase('0');">
							导入认定库(通用)
						</a>
						<a class="item" onclick="import_cbase('1');">
							导入高培认定
						</a>
						<a class="item" onclick="import_cbase('2');">
							导入小巨人入库
						</a>
						<a class="item" onclick="multi_edit_cbase('delete');">
							删除记录
						</a>
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="ctrl_link">										
	<label for="all_page_cbase">
		<input type="checkbox" class="check_all" id="all_page_cbase">
		选取所有分页记录
	</label>
</div>
<div class="data_count_holder">	
	<span id="cbase_count" class="data_count">	
		<b></b>&nbsp;
		个记录
	</span>
</div>

								</td>
							</tr>
							<?php } ?>
							<tr>
								<td>									
									<table id="cbase_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr class="header_row">
											<th width="10"><input type="checkbox" class="check_all" id="curr_page_cbase"></th>
											<?php
											foreach($GLOBALS['cbase_list_fields'] as $key=>$val){
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
										</tr>
									</table>
									<ul id="pagination"></ul>
								</td>								
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

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
	
	filter_search();
});

function display_sidebar(){
	$('#sidebar').each(function(){
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

function set_btn_import(e){
	var import_title = '';
	if(e==0)import_title = '导入高新认定';
	if(e==1)import_title = '导入高培认定';
	if(e==2)import_title = '导入小巨人入库';
	
	$('#file_excel').change(function(){
		show_alert('正在导入数据，请稍候 ...','load');
		$.ajaxFileUpload({
			url:'cbase_manage.php?action=import_cbase&type='+e,
			secureuri :false,
			fileElementId :'file_excel',
			dataType : 'json',
			success : function (result){
				if(result.success==1){
					show_alert(
						'成功'+import_title+'记录！<br/>' + 
						'读取 ' + result.read_num + ' 条记录<br/>' + 
						'插入 ' + result.insert_num + ' 条记录<br/>' + 
						'更新 ' + result.update_num + ' 条记录'
					);
					filter_search();
				}
				else{
					show_alert(result.error);
				}
			}
		});
	});
}

function import_cbase(e){
	var import_title = '',
		alert_str = '';
	if(e==0){
		import_title = '导入高新认定';
		alert_str = '导入表头格式：年份 | 类型(高新) | 批次(第X批) | 市 | 区';
	}
	if(e==1){
		import_title = '导入高培认定';
		alert_str = '导入表头格式：年份 | 类型(高培) | 批次(第X批) | 市 | 区 | 奖金';
	}
	if(e==2){
		import_title = '导入小巨人入库';
		alert_str = '导入表头格式：年份 | 类型(小巨人) | 市(广州市) | 区 | 备注';
	}
	
	$('#import_holder .title').text(import_title);
	$('#import_holder .alert_holder').text(alert_str);
	$('#import_holder').show().setOverlay();
	set_btn_import(e);
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
			case 'company': filter_type = '公司'; break;
			case 'type': filter_type = '类型'; break;
			case 'year': filter_type = '年份'; break;
			case 'batch': filter_type = '批次'; break;
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

function get_search_cond(params){
	$('#cond_list .filter_cond').each(function(){
		var type = $(this).attr('class').replace('filter_cond','').trim();
		
		var val = $(this).children('input[name='+type+']').val().trim();
		
		if(params)params += '&';
		params += type+'[]='+encodeURIComponent(val);
	});
	return params;
}

function filter_search(){
	get_filter_cond();	
	
	var params = '';	
	params = get_search_cond(params);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_cbase_filter.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('.check_all').prop('checked',false);
			
			<?php
			foreach($GLOBALS['cbase_filter'] as $val){
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
			
			search_cbase();
		}
	});
}

function multi_edit_cbase(e){
	if($('#cbase_list .data_row input[type=checkbox]:checked').length==0){
		alert('请选择记录进行操作！');
		return false;
	}
	
	var params = '';
	if($('#all_page_cbase').is(':checked')){//edit all page cbases
		params += '&scope=all';
		params = get_search_cond(params);
	}
	else{//edit current page cbases
		$('#cbase_list .data_row input[type=checkbox]:checked').each(function(){
			var c_id = $(this).parents('.data_row').attr('id').replace('c_','');
			if(params!='')params += '&';
			params += 'c_id[]='+c_id;
		});
	}
	
	if(e=='delete'){
		if(confirm('删除选定的记录将不能恢复，确定删除？')){
			show_alert('正在删除记录，请稍候 ...','load');
			
			params += '&action=delete_cbase';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_cbase').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功删除选定的记录！');
							search_cbase(curr_page);
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
		window.location.href = 'cbase_manage.php?'+params;
	}
}

function set_cbase_sort(){
	$('#cbase_list th a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#cbase_list .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#cbase_list .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,cbase_sort_arr)==-1)cbase_sort_arr.push(sort_id);
			}
			else if($('#cbase_list .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#cbase_list .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,cbase_sort_arr);
			}
			else{//switch to asc
				$('#cbase_list .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,cbase_sort_arr)==-1)cbase_sort_arr.push(sort_id);
			}
			
			search_cbase();
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

function search_cbase(e){
	$('#cbase_list').append('<div class="loading cover"></div>');
	
	var params = '';
	params = get_search_cond(params);
	
	if(e!=undefined){
		if(e=='find'){
			cbase_sort_arr.length = 0;
			$('#cbase_list th a i').attr('class','');
			$('#cbase_list th a').unbind('click');
		}
		else{
			params += '&page='+e;
		}
	}
	
	params = get_sort_params(params,cbase_sort_arr);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_cbase.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('#cbase_list #curr_page_cbase').prop('checked',false);			
			$('#cbase_list tr.data_row').remove();
			
			$(xml).find('cbase_num').each(function(){
				var cbase_num = $(this).text();
				$('#cbase_count b').html(cbase_num);
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
			
			$('#cbase_data #pagination').html('');
			$(xml).find('page_num').each(function(){
				var page_num = parseInt($(this).text());				
				for(var p=1;p<=page_num;p++){
					var curr_class = '';
					if(curr_page==p)curr_class = 'class="curr"';
					
					if(page_num>page_span*3){						
						if(p<=page_span || p>(page_num-page_span)){
							$('#cbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
						}	
						else{
							if(curr_page<=page_span){//if curr page at the front
								if(p<=curr_page+Math.floor(page_span/2)){
									$('#cbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p<page_num-page_span){
									if($('#cbase_data #pagination li.dot').length==0)$('#cbase_data #pagination').append('<li class="dot">...</li>');
								}
							}
							
							if(curr_page>page_span && curr_page<=page_num-page_span){//if curr page in the middle
								if(p>page_span && p<curr_page-Math.floor(page_span/2)){
									if($('#cbase_data #pagination li.dot.first').length==0)$('#cbase_data #pagination').append('<li class="dot first">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2) && p<=curr_page+Math.floor(page_span/2)){
									$('#cbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p>page_num-page_span-Math.floor(page_span/2)){
									if($('#cbase_data #pagination li.dot.second').length==0)$('#cbase_data #pagination').append('<li class="dot second">...</li>');
								}
							}
							
							if(curr_page>page_num-page_span){//if curr page at the end
								if(p<curr_page-Math.floor(page_span/2)){
									if($('#cbase_data #pagination li.dot').length==0)$('#cbase_data #pagination').append('<li class="dot">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2)){
									$('#cbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
							}
						}
					}
					else{
						$('#cbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
					}
				}
				
				$('#cbase_data #pagination li').click(function(){
					if(!$(this).hasClass('curr') && !$(this).hasClass('dot')){
						curr_page = parseInt($(this).text());
						search_cbase(curr_page);
					}
				});
			});
			
			var i = 0;
			$(xml).find('cbase').each(function(){
				var c_id = $(this).find('c_id').text();
				<?php
				foreach($GLOBALS['cbase_list_fields'] as $key=>$val){
					print "var ".$key." = $(this).find('$key').text();";
				}
				?>
				
				var alt_class = '';
				if(i%2!=0)alt_class = 'alt';
				
				$('#cbase_list').append(
					'<tr id="c_'+c_id+'" class="data_row '+alt_class+'">' + 
						'<td>' + 
							'<input type="checkbox">' + 
						'</td>' + 
						<?php
						foreach($GLOBALS['cbase_list_fields'] as $key=>$val){
							$align_str = 'align="center"';
							print "'<td $align_str>'+$key+'</td>' + ";
						}
						?>
					'</tr>'
				);
				i++;
			});
			
			set_cbase_sort();
			set_check_cbase();
			$('#cbase_list .loading').fadeOut();
			
			if($('#all_page_cbase').is(':checked')){
				$('#curr_page_cbase').prop('checked',true);
				$('#cbase_list td input[type=checkbox]').prop('checked',true);
				$('#cbase_list tr').addClass('selected');
			}
		}
	});
}

function set_check_cbase(){
	$('#cbase_list #curr_page_cbase').change(function(){
		if($(this).is(':checked')){
			$('#cbase_list td input[type=checkbox]').prop('checked',true);
			$('#cbase_list tr').addClass('selected');
		}
		else{
			$('#all_page_cbase').prop('checked',false);
			$('#cbase_list td input[type=checkbox]').prop('checked',false);
			$('#cbase_list tr').removeClass('selected');
		}
	});
	
	$('#all_page_cbase').change(function(){
		if($(this).is(':checked')){
			$('#curr_page_cbase').prop('checked',true);
			$('#cbase_list td input[type=checkbox]').prop('checked',true);
			$('#cbase_list tr').addClass('selected');
		}
		else{
			$('#curr_page_cbase').prop('checked',false);
			$('#cbase_list td input[type=checkbox]').prop('checked',false);
			$('#cbase_list tr').removeClass('selected');
		}
	});
	
	$('#cbase_list td input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('selected');
		}
		else{
			$('#all_page_cbase').prop('checked',false);
			$('#cbase_list #curr_page_cbase').prop('checked',false);
			$(this).closest('tr').removeClass('selected');
		}
	});
}
</script>
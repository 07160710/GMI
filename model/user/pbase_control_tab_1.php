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
			<?php if($_SESSION['auth_pbase']==2){ ?>
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
						<a class="item" onclick="view_pbase('create');">
							创建基础项目
						</a>
						<?php if($_SESSION['level']>2){ ?>
						<a class="item" onclick="multi_edit_pbase('delete');">
							删除基础项目
						</a>
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="ctrl_link">										
	<label for="all_page_pbase">
		<input type="checkbox" class="check_all" id="all_page_pbase">
		选取所有分页基础项目
	</label>
</div>
<div class="data_count_holder">	
	<span id="pbase_count" class="data_count">	
		<b></b>&nbsp;
		个基础项目
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
								基础项目名称
							</span>
							<input type="text" id="search_name">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						
						<!--type-->
						<div id="filter_type" class="list_holder">
							<span>								
								<label for="all_type">
									<input type="checkbox" class="check_all" id="all_type">
									所有类型
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
			<table id="pbase_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table id="pbase_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr class="header_row">
								<th width="10"><input type="checkbox" class="check_all" id="curr_page_pbase"></th>
								<th width="30">
									查看
								</th>
								<?php
								foreach($GLOBALS['pbase_list_fields'] as $key=>$val){
									$width_str = "";
									if(strpos($val,"#")!==false){
										$val_arr = explode("#",$val);
										$width_str = 'width="'.$val_arr[1].'"';
										$val = $val_arr[0];
									}
									$sort_arrow = "";
									if($key=="name" || $key=="type")$sort_arrow = "<i></i>";									
									
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

<?php include("pbase_control_tab_1_content.php"); ?>
<?php include("public_body_js.php"); ?>

<script>
function get_filter_cond(){	
	$('.list_holder').each(function(){
		var id = $(this).attr('id'),
			filter_id = id.replace('filter_','');
			
		var filter_type = '';
		switch(filter_id){
			case 'name': filter_type = '名称'; break;
			case 'type': filter_type = '类型'; break;
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
		url: 		'fetch_pbase_filter.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('.check_all').prop('checked',false);
			
			<?php
			foreach($GLOBALS['pbase_filter'] as $val){
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
			
			search_pbase(e);
		}
	});
}

function multi_edit_pbase(e){
	if($('#pbase_list .data_row input[type=checkbox]:checked').length==0){
		alert('请选择基础项目进行操作！');
		return false;
	}
	
	var params = '';
	if($('#all_page_pbase').is(':checked')){//edit all page pbases
		params += '&scope=all';
		params = get_search_cond(params);
	}
	else{//edit current page pbases
		$('#pbase_list .data_row input[type=checkbox]:checked').each(function(){
			var pbase_id = $(this).parents('.data_row').attr('id').replace('pbase_','');
			if(params!='')params += '&';
			params += 'pbase_id[]='+pbase_id;
		});
	}
	
	if(e=='delete'){
		if(confirm('删除选定的基础项目将不能恢复，确定删除？')){
			show_alert('正在删除基础项目，请稍候 ...','load');
			
			params += '&action=delete_pbase';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_pbase').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功删除选定的基础项目！');
							search_pbase(curr_page);
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
		window.location.href = 'pbase_manage.php?'+params;
	}
}

function set_pbase_sort(){
	$('#pbase_list th a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#pbase_list .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#pbase_list .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,pbase_sort_arr)==-1)pbase_sort_arr.push(sort_id);
			}
			else if($('#pbase_list .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#pbase_list .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,pbase_sort_arr);
			}
			else{//switch to asc
				$('#pbase_list .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,pbase_sort_arr)==-1)pbase_sort_arr.push(sort_id);
			}
			
			search_pbase();
		}
	});
}

function search_pbase(e){
	var params = 'per_page='+per_page;
	params = get_search_cond(params);
	
	if(e!=undefined){
		if(e=='find'){
			pbase_sort_arr.length = 0;
			$('#pbase_list th a i').attr('class','');
			$('#pbase_list th a').unbind('click');
		}
		else{
			params += '&page='+e;
		}
	}
	
	params = get_sort_params(params,pbase_sort_arr);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_pbase.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('.body_holder .loading.cover').remove();
			$('#pbase_list #curr_page_pbase').prop('checked',false);			
			$('#pbase_list tr.data_row').remove();
			
			$(xml).find('pbase_num').each(function(){
				var pbase_num = $(this).text();
				$('#pbase_count b').html(pbase_num);
			});
			
			$(xml).find('curr_page').each(function(){
				curr_page = parseInt($(this).text());
			});
			
			$('#pbase_data #pagination').html('');
			$(xml).find('page_num').each(function(){
				var page_num = parseInt($(this).text());				
				for(var p=1;p<=page_num;p++){
					var curr_class = '';
					if(curr_page==p)curr_class = 'class="curr"';
					
					if(page_num>page_span*3){						
						if(p<=page_span || p>(page_num-page_span)){
							$('#pbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
						}	
						else{
							if(curr_page<=page_span){//if curr page at the front
								if(p<=curr_page+Math.floor(page_span/2)){
									$('#pbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p<page_num-page_span){
									if($('#pbase_data #pagination li.dot').length==0)$('#pbase_data #pagination').append('<li class="dot">...</li>');
								}
							}
							
							if(curr_page>page_span && curr_page<=page_num-page_span){//if curr page in the middle
								if(p>page_span && p<curr_page-Math.floor(page_span/2)){
									if($('#pbase_data #pagination li.dot.first').length==0)$('#pbase_data #pagination').append('<li class="dot first">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2) && p<=curr_page+Math.floor(page_span/2)){
									$('#pbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p>page_num-page_span-Math.floor(page_span/2)){
									if($('#pbase_data #pagination li.dot.second').length==0)$('#pbase_data #pagination').append('<li class="dot second">...</li>');
								}
							}
							
							if(curr_page>page_num-page_span){//if curr page at the end
								if(p<curr_page-Math.floor(page_span/2)){
									if($('#pbase_data #pagination li.dot').length==0)$('#pbase_data #pagination').append('<li class="dot">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2)){
									$('#pbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
							}
						}
					}
					else{
						$('#pbase_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
					}
				}
				
				$('#pbase_data #pagination li').click(function(){
					if(!$(this).hasClass('curr') && !$(this).hasClass('dot')){
						curr_page = parseInt($(this).text());
						search_pbase(curr_page);
					}
				});
			});
			
			var i = 0;
			$(xml).find('pbase').each(function(){
				var pbase_id = $(this).find('pbase_id').text();
				<?php
				foreach($GLOBALS['pbase_list_fields'] as $key=>$val){
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
				
				$('#pbase_list').append(
					'<tr id="pbase_'+pbase_id+'" class="data_row '+alt_class+'">' + 
						'<td>' + 
							'<input type="checkbox">' + 
						'</td>' + 
						'<td align="center">' + 
							'<a onclick="view_pbase(\''+pbase_id+'\');">查看</a>' + 
						'</td>' + 
						<?php
						foreach($GLOBALS['pbase_list_fields'] as $key=>$val){
							$align_str = 'align="center"';
							print "'<td $align_str class=\"'+".$key."_status+'\">'+$key+'</td>' + ";
						}
						?>
					'</tr>'
				);
				i++;
			});
			
			set_pbase_sort();
			set_check_pbase();
			$('#pbase_list .loading').fadeOut();
			
			if($('#all_page_pbase').is(':checked')){
				$('#curr_page_pbase').prop('checked',true);
				$('#pbase_list td input[type=checkbox]').prop('checked',true);
				$('#pbase_list tr').addClass('selected');
			}
		}
	});
}

function set_check_pbase(){
	$('#pbase_list #curr_page_pbase').change(function(){
		if($(this).is(':checked')){
			$('#pbase_list td input[type=checkbox]').prop('checked',true);
			$('#pbase_list tr').addClass('selected');
		}
		else{
			$('#all_page_pbase').prop('checked',false);
			$('#pbase_list td input[type=checkbox]').prop('checked',false);
			$('#pbase_list tr').removeClass('selected');
		}
	});
	
	$('#all_page_pbase').change(function(){
		if($(this).is(':checked')){
			$('#curr_page_pbase').prop('checked',true);
			$('#pbase_list td input[type=checkbox]').prop('checked',true);
			$('#pbase_list tr').addClass('selected');
		}
		else{
			$('#curr_page_pbase').prop('checked',false);
			$('#pbase_list td input[type=checkbox]').prop('checked',false);
			$('#pbase_list tr').removeClass('selected');
		}
	});
	
	$('#pbase_list td input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('selected');
		}
		else{
			$('#all_page_pbase').prop('checked',false);
			$('#pbase_list #curr_page_pbase').prop('checked',false);
			$(this).closest('tr').removeClass('selected');
		}
	});
}
</script>
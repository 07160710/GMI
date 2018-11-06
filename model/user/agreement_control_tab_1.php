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
			<?php if($_SESSION['auth_agreement']==2){ ?>
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
						<a class="item" onclick="view_agreement('create');">
							创建协议
						</a>
						<a class="item" onclick="multi_edit_agreement('agreement');">
							导出协议
						</a>
						<?php if($_SESSION['level']>2){ ?>
						<a class="item" onclick="multi_edit_agreement('delete');">
							删除协议
						</a>
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="ctrl_link">										
	<label for="all_page_agreement">
		<input type="checkbox" class="check_all" id="all_page_agreement">
		选取所有分页协议
	</label>
</div>
<div class="data_count_holder">	
	<span id="agreement_count" class="data_count">	
		<b></b>&nbsp;
		个协议
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
						
						<?php if($_SESSION['u_id']==1){ ?>
						<div id="filter_id" class="list_holder">
							<span class="title">
								协议ID
							</span>
							<input type="text" id="search_id">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						<?php } ?>
						<div id="filter_company" class="list_holder">
							<span class="title">
								公司名称
							</span>
							<input type="text" id="search_company">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						<div id="filter_name" class="list_holder">
							<span class="title">
								协议名称
							</span>
							<input type="text" id="search_name">
							<button onclick="filter_search();">
								筛选
							</button>
						</div>
						
						<!--sign_period-->
						<div id="filter_sign_period" class="list_holder">
							<span class="title">签约时段</span>
							<input type="text" id="sign_start_date" class="date_input" style="padding:0 3px;" placeholder="开始日期">
							 - 
							<input type="text" id="sign_end_date" class="date_input" style="margin-left:0;padding:0 3px;" placeholder="结束日期">
							<button onclick="filter_search();">筛选</button>
						</div>
						<!--expire_period-->
						<div id="filter_expire_period" class="list_holder">
							<span class="title">到期时段</span>
							<input type="text" id="expire_start_date" class="date_input" style="padding:0 3px;" placeholder="开始日期">
							 - 
							<input type="text" id="expire_end_date" class="date_input" style="margin-left:0;padding:0 3px;" placeholder="结束日期">
							<button onclick="filter_search();">筛选</button>
						</div>
						
						<!--receive-->
						<div id="filter_receive" class="list_holder">
							<span>
								<label for="to_receive">
									<input type="checkbox" id="to_receive">
									已收合同
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
						</div>
						<!--branch-->
						<div id="filter_branch" class="list_holder">
							<span>								
								<label for="all_branch">
									<input type="checkbox" class="check_all" id="all_branch">
									所有属地
								</label>
								<button onclick="filter_search();">
									筛选
								</button>
							</span>
							<ul class="filter_list"></ul>
						</div>
						<!--sales-->
						<div id="filter_sales_name" class="list_holder">
							<span>
								<label for="all_sales_name">
									<input type="checkbox" class="check_all" id="all_sales_name">
									所有销售
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
			<table id="agreement_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table id="agreement_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr class="header_row">
								<th width="10"><input type="checkbox" class="check_all" id="curr_page_agreement"></th>
								<th width="10"><a class="btn_preview"></a></th>
								<?php
								if($_SESSION['level']>2)print "<th width=\"10\"><a class=\"btn_receive\"></a></th>";
								
								foreach($GLOBALS['agreement_list_fields'] as $key=>$val){
									$width_str = "";
									if(strpos($val,"#")!==false){
										$val_arr = explode("#",$val);
										$width_str = 'width="'.$val_arr[1].'"';
										$val = $val_arr[0];
									}
									$sort_arrow = "<i></i>";									
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

<!--RENAME HOLDER-->
<div id="rename_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					批量重命名
				</span>
				<a class="btn_close" onclick="rename_agreement('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="rename_panel">
				<input type="hidden" name="rename_type">
				
				<div id="agreement_holder" class="search_holder">
					<input type="text" id="r_name" name="rename" class="long" placeholder="请输入协议名称">
					<ul id="r_pbase_list" class="result_list"></ul>
				</div>
				
				<div id="agreement_holder" class="search_holder">
					<input type="text" id="r_agreement" name="rename" class="long" placeholder="请输入协议名称">
					<ul id="r_agreement_list" class="result_list"></ul>
				</div>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="rename_agreement('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<?php include("agreement_control_tab_1_content.php"); ?>
<?php include("public_body_js.php"); ?>

<script>
function filter_search(e){
	$('.body_holder').append('<div class="loading cover"></div>');
	get_filter_cond();	
	
	var params = '';	
	params = get_search_cond(params);	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_agreement_filter.php',
		dataType:	'xml',
		async:		false,
		success: function (xml) {
			$('.check_all').prop('checked',false);
			
			<?php
			foreach($GLOBALS['agreement_filter'] as $val){
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
			
			search_agreement(e);
		}
	});
}

function multi_edit_agreement(e){
	if($('#agreement_list .data_row input[type=checkbox]:checked').length==0){
		alert('请选择协议进行操作！');
		return false;
	}
	
	var params = '';
	if($('#all_page_agreement').is(':checked')){//edit all page agreements
		params += '&scope=all';
		params = get_search_cond(params);
	}
	else{//edit current page agreements
		$('#agreement_list .data_row input[type=checkbox]:checked').each(function(){
			var agreement_id = $(this).parents('.data_row').attr('id').replace('agreement_','');
			if(params!='')params += '&';
			params += 'agreement_id[]='+agreement_id;
		});
	}
	
	if(e=='delete'){
		if(confirm('删除选定的协议将不能恢复，确定删除？')){
			show_alert('正在删除协议，请稍候 ...','load');
			
			params += '&action=delete_agreement';
			$.ajax({
				data: 	params,
				type:	'post',
				url: 	post_url,
				dataType: 'json',
				success: function(result){
					if(result.success==1){
						if($('#all_page_agreement').is(':checked')){
							window.location.reload();
						}
						else{
							show_alert('成功删除选定的协议！');
							search_agreement(curr_page);
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
		window.location.href = 'agreement_manage.php?'+params;
	}
}

function set_agreement_sort(){
	$('#agreement_list th a').unbind('click').click(function(){
		if($(this).children('i').length>0){
			var sort_id = $(this).attr('class');
			
			if($('#agreement_list .'+sort_id+' i').hasClass('asc')){//switch to desc
				$('#agreement_list .'+sort_id+' i').removeClass('asc').addClass('desc');
				if(in_array(sort_id,agreement_sort_arr)==-1)agreement_sort_arr.push(sort_id);
			}
			else if($('#agreement_list .'+sort_id+' i').hasClass('desc')){//switch to no sort
				$('#agreement_list .'+sort_id+' i').removeClass('desc');
				remove_sort(sort_id,agreement_sort_arr);
			}
			else{//switch to asc
				$('#agreement_list .'+sort_id+' i').addClass('asc');
				if(in_array(sort_id,agreement_sort_arr)==-1)agreement_sort_arr.push(sort_id);
			}
			
			search_agreement();
		}
	});
}

function search_agreement(e){
	var params = 'per_page='+per_page;
	params = get_search_cond(params);
	
	if(e!=undefined){
		if(e=='find'){
			agreement_sort_arr.length = 0;
			$('#agreement_list th a i').attr('class','');
			$('#agreement_list th a').unbind('click');
		}
		else{
			params += '&page='+e;
		}
	}
	
	params = get_sort_params(params,agreement_sort_arr);
	
	$.ajax({
		data: 	params,
		type:		'GET',
		url: 		'fetch_agreement.php',
		dataType:	'xml',
		async:		true,
		success: function (xml) {
			$('.body_holder .loading.cover').remove();
			$('#agreement_list #curr_page_agreement').prop('checked',false);			
			$('#agreement_list tr.data_row').remove();
			
			$(xml).find('agreement_num').each(function(){
				var agreement_num = $(this).text();
				$('#agreement_count b').html(agreement_num);
			});
			
			$(xml).find('curr_page').each(function(){
				curr_page = parseInt($(this).text());
			});
			
			$('#agreement_data #pagination').html('');
			$(xml).find('page_num').each(function(){
				var page_num = parseInt($(this).text());				
				for(var p=1;p<=page_num;p++){
					var curr_class = '';
					if(curr_page==p)curr_class = 'class="curr"';
					
					if(page_num>page_span*3){						
						if(p<=page_span || p>(page_num-page_span)){
							$('#agreement_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
						}	
						else{
							if(curr_page<=page_span){//if curr page at the front
								if(p<=curr_page+Math.floor(page_span/2)){
									$('#agreement_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p<page_num-page_span){
									if($('#agreement_data #pagination li.dot').length==0)$('#agreement_data #pagination').append('<li class="dot">...</li>');
								}
							}
							
							if(curr_page>page_span && curr_page<=page_num-page_span){//if curr page in the middle
								if(p>page_span && p<curr_page-Math.floor(page_span/2)){
									if($('#agreement_data #pagination li.dot.first').length==0)$('#agreement_data #pagination').append('<li class="dot first">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2) && p<=curr_page+Math.floor(page_span/2)){
									$('#agreement_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
								if(p>curr_page+Math.floor(page_span/2) && p>page_num-page_span-Math.floor(page_span/2)){
									if($('#agreement_data #pagination li.dot.second').length==0)$('#agreement_data #pagination').append('<li class="dot second">...</li>');
								}
							}
							
							if(curr_page>page_num-page_span){//if curr page at the end
								if(p<curr_page-Math.floor(page_span/2)){
									if($('#agreement_data #pagination li.dot').length==0)$('#agreement_data #pagination').append('<li class="dot">...</li>');
								}
								if(p>=curr_page-Math.floor(page_span/2)){
									$('#agreement_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
								}
							}
						}
					}
					else{
						$('#agreement_data #pagination').append('<li '+curr_class+'>'+p+'</li>');
					}
				}
				
				$('#agreement_data #pagination li').click(function(){
					if(!$(this).hasClass('curr') && !$(this).hasClass('dot')){
						curr_page = parseInt($(this).text());
						search_agreement(curr_page);
					}
				});
			});
			
			var i = 0;
			$(xml).find('agreement').each(function(){
				var agreement_id = $(this).find('agreement_id').text(),
					receive = $(this).find('receive').text();
				<?php
				foreach($GLOBALS['agreement_list_fields'] as $key=>$val){
					print "	var ".$key." = $(this).find('$key').text();
							var ".$key."_class = '';";
					if($key=="remark"){
						print "	if(remark!=''){
									if(remark.indexOf('|')>=0){
										var remark_arr = remark.split('|'),
											remark_str = '';
										for(var i in remark_arr){
											if(remark_str!='')remark_str += '\\n';
											remark_str += remark_arr[i];
										}
										remark = remark_str;
									}
									remark = '<a class=\"icon_remark\" title=\"'+remark+'\"></a>';
								}";						
					}
					else{
						print "	if(".$key.".indexOf('|')>=0){
									var ".$key."_arr = ".$key.".split('|');
									".$key." = ".$key."_arr[0];
									".$key."_class = ".$key."_arr[1];
								}";
						if($key=="name"){
							print "	var verify = $(this).find('verify').text(),
										verify_superscript = (verify!='')?'<a class=\"superscript\" title=\"'+verify+'\"></a>':'';";
						}
					}
				}
				?>
				
				var alt_class = '';
				if(i%2!=0)alt_class = 'alt';
				
				var receive_str = '<a class="btn_receive" onclick="mark_receive(\''+agreement_id+'\');" title="标记已收"></a>';
				if(receive==1)receive_str = '<a class="btn_receive done" onclick="mark_receive(\''+agreement_id+'\');" title="取消标记"></a>';
				
				$('#agreement_list').append(
					'<tr id="agreement_'+agreement_id+'" class="data_row '+alt_class+'">' + 
						'<td>' + 
							'<input type="checkbox">' + 
						'</td>' + 
						'<td align="center">' + 
							'<a class="btn_preview" onclick="view_agreement(\''+agreement_id+'\');"></a>' + 
						'</td>' + 
						<?php
						if($_SESSION['level']>2)print "'<td>'+receive_str+'</td>' + ";
						
						foreach($GLOBALS['agreement_list_fields'] as $key=>$val){
							$align_str = 'align="center"';
							if($key=="name"){
								print "'<td $align_str class=\"'+".$key."_class+'\">'+$key+verify_superscript+'</td>' + ";
							}
							else{
								print "'<td $align_str class=\"'+".$key."_class+'\">'+$key+'</td>' + ";
							}
						}
						?>
					'</tr>'
				);
				i++;
			});
			set_agreement_sort();
			set_check_agreement();
			$('#agreement_list .loading').fadeOut();
			
			if($('#all_page_agreement').is(':checked')){
				$('#curr_page_agreement').prop('checked',true);
				$('#agreement_list td input[type=checkbox]').prop('checked',true);
				$('#agreement_list tr').addClass('selected');
			}
		}
	});
}

function set_check_agreement(){
	$('#agreement_list #curr_page_agreement').change(function(){
		if($(this).is(':checked')){
			$('#agreement_list td input[type=checkbox]').prop('checked',true);
			$('#agreement_list tr').addClass('selected');
		}
		else{
			$('#all_page_agreement').prop('checked',false);
			$('#agreement_list td input[type=checkbox]').prop('checked',false);
			$('#agreement_list tr').removeClass('selected');
		}
	});
	
	$('#all_page_agreement').change(function(){
		if($(this).is(':checked')){
			$('#curr_page_agreement').prop('checked',true);
			$('#agreement_list td input[type=checkbox]').prop('checked',true);
			$('#agreement_list tr').addClass('selected');
		}
		else{
			$('#curr_page_agreement').prop('checked',false);
			$('#agreement_list td input[type=checkbox]').prop('checked',false);
			$('#agreement_list tr').removeClass('selected');
		}
	});
	
	$('#agreement_list td input[type=checkbox]').change(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('selected');
		}
		else{
			$('#all_page_agreement').prop('checked',false);
			$('#agreement_list #curr_page_agreement').prop('checked',false);
			$(this).closest('tr').removeClass('selected');
		}
	});
}
</script>
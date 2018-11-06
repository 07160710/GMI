if(post_id==undefined)var post_id = '';
var curr_id = post_id;

var ctrl_action = '',
	ctrl_id = '',
	ctrl_curr_id = '',
	ctrl_panel_str = '#left_col_holder',
	ctrl_str = '';

$(function() {
	set_public_attr();
});

function set_input(){
	$('.edit_block strong').unbind('click').bind('click', function(){
		var parent = $(this),
			tmp_val = parent.text(),
			type = $(this).attr('class').replace('design_input ','');
			
		parent.unbind('click');
		if(type=='remark'){
			parent.html('<textarea class="design_input">'+tmp_val+'</textarea>');			
		}
		else{
			parent.html('<input type="text" class="design_input" value="'+tmp_val+'">');
		}
		
		var child = parent.children('.design_input');
		child.focus().blur(function(){
			parent.html($(this).val());
			set_input();
		});
	});
}

function set_curr(set_curr_id){
	curr_id = set_curr_id;
	$(ctrl_panel_str+' .node_tree .node_holder').removeClass('curr');
	var prefix = ($('#move_holder:visible').length>0 && id_prefix=='u_')?'g_':id_prefix;
	set_curr_id = prefix+set_curr_id;
	$(ctrl_panel_str+' #'+set_curr_id+' .node_holder:eq(0)').addClass('curr');
}

function set_move_item(){
	$('.check_all').change(function(){
		if($(this).is(':checked')){
			$(this).closest('td').find('ul input[type=checkbox]').prop('checked',true);
			$(this).closest('td').find('ul li').addClass('selected');
		}
		else{
			$(this).closest('td').find('ul input[type=checkbox]').prop('checked',false);
			$(this).closest('td').find('ul li').removeClass('selected');
		}
	});
	
	$('input[type=checkbox]').change(function(){
		if($(this).is(':checked'))$(this).parents('li').addClass('selected');
		else{
			$(this).parents('li').removeClass('selected');
			if($('.check_all').is(':checked'))$('.check_all').prop('checked',false);
		}
	});
	
	$('.move_item').unbind('click').click(function(){
		var move_action = $(this).attr('class').replace('move_item ',''),
			parent_table = $(this).closest('table'),
			item_haystack = '';
		
		if(move_action=='add'){
			parent_table.find('.left_col ul input[type=checkbox]:checked').each(function(){
				$(this).parents('li').removeClass('selected');
				item_haystack += $(this).parents('li')[0].outerHTML;
				$(this).parents('li').remove();
			});
			
			parent_table.find('.right_col ul').append(item_haystack);
			set_move_item();
			parent_table.find('.left_col input[type=checkbox]').prop('checked',false);
		}
		else{
			parent_table.find('.right_col ul input[type=checkbox]:checked').each(function(){
				$(this).parents('li').removeClass('selected');
				item_haystack += $(this).parents('li')[0].outerHTML;
				$(this).parents('li').remove();
			});
			
			parent_table.find('.left_col ul').append(item_haystack);
			set_move_item();
			parent_table.find('.right_col input[type=checkbox]').prop('checked',false);
		}
	});
}

function set_public_attr(){
	$('.tab_holder .tab').click(function(){
		var tab_id = $(this).attr('id').replace('tab_','');
		$('.tab_holder .tab').removeClass('curr');
		$(this).addClass('curr');
		
		$('.tab_panel').hide();
		$('#tab_panel_'+tab_id).show();
		document.cookie = page_name+'_tab='+tab_id;
	});
	
	$('.ctrl_link .ctrl_arrow, #img_ctrl .ctrl_arrow').click(function(){
		$(this).parent('.menu_holder').children('.ctrl_menu').show();
	});
	
	$('body').click(function(event){//for cell phone
		var $target = $(event.target);
		if($target.is('.ctrl_link .ctrl_arrow') || $target.is('#img_ctrl .ctrl_arrow')){
		}
		else{
			$('.ctrl_link .ctrl_menu').hide();
			$('#img_ctrl .ctrl_menu').hide();
		}
	});
	
	$('.ctrl_menu .btn_file').mouseenter(function(){
		$(this).parent('.ctrl_menu').children('.item:eq(0)').css('background','#d5effc');
	}).mouseleave(function(){
		$(this).parent('.ctrl_menu').children('.item:eq(0)').css('background','transparent');
	});
}

function in_array(needle, haystack){
    var found = 0;
    for (var i=0, len=haystack.length;i<len;i++) {
        if (haystack[i] == needle) return i;
            found++;
    }
    return -1;
}

function remove_sort(sort_id,sort_arr){
	for(var i = sort_arr.length - 1; i >= 0; i--) {
		if(sort_arr[i] === sort_id) {
		   sort_arr.splice(i, 1);
		}
	}
}

Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}
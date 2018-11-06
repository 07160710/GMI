<?php
include("header.php");
?>
<table class="ctrl_header top" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="header"></td>
		<td class="ctrl_holder"></td>
	</tr>
	<tr>
		<td class="tab_list" style="padding:0;" colspan="2">
			<div id="tab_container">
				<ul class="tab_holder">
					<li>
						<a id="tab_1" class="tab curr">
							概览
						</a>
					</li>
				</ul>
			</div>
			<div id="tab_line"></div>
		</td>
	</tr>
</table>
	
<table id="content_holder" class="ctrl_content" style="margin:50px 0 0 0;" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>
			<?php include('sales_control_tab_1.php');?>
		</td>
	</tr>
</table>

<script>
var act_tab = '<?php print $_COOKIE['project_tab'];?>';
var ctrl_id = '';
	ctrl_action = '';	
var post_url = 'sales_manage.php';
var curr_tab_panel = '';
var project_sort_arr = new Array;
var curr_page = 1,
	page_span = 5;

$(function() {	
	if(act_tab==''){
		document.cookie = 'project_tab=1';
		$('#tab_panel_1').show();
		curr_tab_panel = '#tab_panel_1';
	}
	else{
		$('.tab').removeClass('curr');
		$('#tab_'+act_tab).addClass('curr');
		$('#tab_panel_'+act_tab).show();
		curr_tab_panel = '#tab_panel_'+act_tab;
	}
});
</script>

<?php
include("public_content.php");
include("footer.php");
?>
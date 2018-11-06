<?php
include("header.php");

$curr_ctrl = $_SESSION['home_ctrl'];
?>
<table id="content_holder" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td id="left_col">
			<div id="left_col_holder">
				<input type="hidden" name="ctrl" id="ctrl" value="">
				<a class="ctrl_link <?php print ($curr_ctrl=="basic_info" || $curr_ctrl=="")?"curr":"";?>" onclick="ctrl_info('basic_info');">
					基本信息
				</a>
				<a class="ctrl_link <?php print ($curr_ctrl=="edit_profile")?"curr":"";?>" onclick="ctrl_info('edit_profile');">	
					修改资料
				</a>
				<a class="ctrl_link <?php print ($curr_ctrl=="reset_pass")?"curr":"";?>" onclick="ctrl_info('reset_pass');">	
					修改密码
				</a>
				<?php if($_SESSION['level']>2){ ?>
				<a class="ctrl_link <?php print ($curr_ctrl=="site_setting")?"curr":"";?>" onclick="ctrl_info('site_setting');">
					站点设置
				</a>
				<?php } ?>
			</div>
		</td>
		<td id="right_col">
			<div id="content_panel"></div>			
		</td>
	</tr>
</table>

<script>
var curr_ctrl = '<?php print ($curr_ctrl!="")?$curr_ctrl:"basic_info";?>';
$(function() {
	$('.ctrl_link').click(function(){
		$('.ctrl_link').removeClass('curr');
		$(this).addClass('curr');
	});
	
	$('#content_panel').html('<div class="loading cover"></div>').load('home_control.php?ctrl='+curr_ctrl);
});

function ctrl_info(e){
	$('#ctrl').val(e);
	$('#content_panel').html('<div class="loading cover"></div>').load('home_control.php?ctrl='+e);
}
</script>

<?php
include("public_content.php");
include("footer.php");
?>
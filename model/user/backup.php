<?php
include("header.php");

$curr_ctrl = $_SESSION['backup_ctrl'];
?>
<table id="content_holder" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td id="left_col">
			<div id="left_col_holder">
				<input type="hidden" name="ctrl" id="ctrl" value="">				
				<a class="ctrl_link <?php print ($curr_ctrl=="restore_backup" || $curr_ctrl=="")?"curr":"";?>" onclick="ctrl_info('restore_backup');">					
					还原备份
				</a>
				<a class="ctrl_link <?php print ($curr_ctrl=="manual_backup")?"curr":"";?>" onclick="ctrl_info('manual_backup');">
					手动备份
				</a>
			</div>
		</td>
		<td id="right_col">
			<div id="content_panel"></div>
		</td>
	</tr>
</table>

<script>
var curr_ctrl = '<?php print ($curr_ctrl!="")?$curr_ctrl:"restore_backup";?>';
$(function() {
	$('.ctrl_link').click(function(){
		$('.ctrl_link').removeClass('curr');
		$(this).addClass('curr');
	});
	
	$('#content_panel').html('<div class="loading cover"></div>').load('backup_control.php?ctrl='+curr_ctrl);
});

function ctrl_info(e){
	$('#ctrl').val(e);
	$('#content_panel').html('<div class="loading cover"></div>').load('backup_control.php?ctrl='+e);
}
</script>

<?php
include("public_content.php");
include("footer.php");
?>
<!--TAB PANEL 2-->
<table id="tab_panel_2" class="data_holder tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>		
			<table width="100%" border="0" cellpadding="0" cellspacing="10px">
				<?php if($n_type=="topic")	include('content_control_tab_2_topic.php');?>
				<?php if($tc_document==1)	include('content_control_tab_2_document.php');?>
				<?php if($tc_promo_ads==1)	include('content_control_tab_2_promo_ads.php');?>
				<?php if($tc_upload_file==1)include('content_control_tab_2_faq.php');?>
			</table>
		</td>
	</tr>
</table>
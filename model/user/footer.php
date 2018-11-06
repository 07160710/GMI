</div>

<footer>
	EZ-MAN &bullet; 2013-<?php print date('Y');?> &bullet; Designed by 
	<span id="lnk_contact">Kevin Xian
		<div id="contact_info_panel">
			<i class="arrow"></i>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="header" colspan="2">Contact me</td>
				</tr>
				<tr>
					<td class="title email"></td>
					<td><a href="mailto:37207030@qq.com">37207030@qq.com</a></td>
				</tr>
				<tr>
					<td class="title wechat"></td>
					<td><img src="images/qrcode_kevin.jpg"></td>
				</tr>
			</table>
		</div>
	</span>
</footer>

<!--TO TOP-->
<div class="float" id="to_top">
	<a class="link" href="#top"></a>
</div>

<div id="alert_panel" class="overlay">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="middle" align="center" class="alert_holder"></td>
		</tr>
	</table>
</div>

</body>
</html>

<script>
function show_alert(msg,type){
	if(msg!=''){
		var msg_str = '',
			btn_str = '';	
		switch(type){
			case 'load': 
				msg_str = '<div class="loading"><i></i>'+msg+'</div>'; 
				break;
			case 'reload':
				msg_str = '<p>'+msg+'</p>';
				btn_str = '<p><button class="ctrl_btn active" onclick="window.location.reload();">确定</button></p>';
				break;
			default: 
				msg_str = '<p>'+msg+'</p>'; 
				btn_str = '<p><button class="ctrl_btn active" onclick="document.cookie=\'response=;path=./\';$(\'.overlay\').hide().setOverlay();">确定</button></p>';
		}
		
		$('#alert_panel .alert_holder').html(msg_str+btn_str);
		if(!$('#alert_panel').is(':visible'))$('#alert_panel').show().setOverlay();
	}
}
</script>
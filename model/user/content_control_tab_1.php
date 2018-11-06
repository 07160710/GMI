<!--TAB PANEL 1-->
<table id="tab_panel_1" class="data_holder tab_panel" border="0" cellpadding="0" cellspacing="10px">
	<tr>
        <td class="title">
			页面名称
		</td>
        <td>
			<input name="n_name" type="text" id="n_name" value="<?php print $n_name;?>">
		</td>
		<td colspan="2" rowspan="3" valign="top">
<?php if($node_id!="" && $tc_image==1){ ?>		
			<table border="0" cellpadding="0" cellspacing="0">
				<tr valign="top">
					<td class="title">
						页面图片
					</td>
					<td id="img_ctrl_area">
						
<?php
$print_img_ctrl = "";

if($n_image!="" && file_exists(_ROOT_PATH_.$n_image)){
	$n_image_src = _ROOT_URL_.$n_image;
	$n_full_image = str_replace(_THUMB_FOLDER_,"",$n_image);
	$n_full_image_src = _ROOT_URL_.$n_full_image;
	list($width, $height, $type, $attr) = getimagesize(_ROOT_PATH_.$n_image);
	$height_str = "";
	if($height>120)$height_str = "height=\"120\"";
	
	$ctrl_menu_str = "";
	if($_SESSION['auth_delete']==1){
$ctrl_menu_str = <<<CTRL_MENU
<a class="ctrl_arrow"></a>
<div class="ctrl_menu">
	<input name="n_image" class="btn_file" type="file" id="n_image">
	<a class="item replace">
		替换
	</a>
	<a class="item delete" onclick="delete_img('$node_id');">
		删除
	</a>
</div>
CTRL_MENU;
	}
	
$print_img_ctrl = <<<IMG
	<div id="img_ctrl_holder" class="display_area">
		<img src="$n_image_src" $height_str>
		<div id="img_ctrl" class="menu_holder">
			$ctrl_menu_str
		</div>
	</div>	
IMG;
	
}
else{
$print_img_ctrl = <<<IMG
	<div id="img_ctrl_holder" class="upload_area">							
		<input name="n_image" class="btn_file" type="file" id="n_image">
		<span class="btn_upload">			
			上传
			$recomm_str
		</span>
	</div>
IMG;
}
print $print_img_ctrl;

if($n_type=="topic"){
print <<<SIZE
<div class="clear"></div>
<i>
	建议尺寸：640*360
</i>
SIZE;
}
?>						
						
					</td>
				</tr>
			</table>
<?php } ?>			
		</td>
    </tr>
    <tr>
        <td class="title">
			页面标题
		</td>
        <td>
			<input name="n_title" type="text" id="n_title" value="<?php print $n_title;?>">
		</td>
    </tr>
    <tr>
        <td class="title">
			页面别名
		</td>
        <td>
			<input name="n_alias" type="text" id="n_alias" value="<?php print $n_alias;?>"> 
			<span class="note">
				限英文字母，数字，"-"，"_"的组合
			</span>
		</td>
    </tr>
	<tr>
        <td class="title">
			页面类型
		</td>
        <td>
			<select name="n_type" id="n_type">
			<?php
			$get_parent_type = "SELECT type FROM content_table WHERE id='$parent_id'";
			$parent_type = mysql_result(mysql_query($get_parent_type),0);
			if($parent_type=="article_list"){
				foreach($article_type_arr as $key=>$val){
					$selected = "";
					if($n_type==$key)$selected = "selected";		
					print "<option value=\"$key\" $selected>$val</option>";
				}
			}
			if($parent_type=="base_center" || $parent_type=="base"){
				foreach($base_type_arr as $key=>$val){
					$selected = "";
					if($n_type==$key)$selected = "selected";
					print "<option value=\"$key\" $selected>$val</option>";
				}
			}
			else if($parent_type=="news_list"){
				foreach($news_type_arr as $key=>$val){
					$selected = "";
					if($n_type==$key)$selected = "selected";
					print "<option value=\"$key\" $selected>$val</option>";
				}
			}
			else{
				foreach($node_type_arr as $key=>$val){
					$selected = "";
					if($n_type==$key)$selected = "selected";
					print "<option value=\"$key\" $selected>$val</option>";
				}
			}
			?>
			</select>          
		</td>
		<td colspan="2" width="50%">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="title">
						在导航显示
					</td>
					<td>
						<label for="n_show_navi">
							<input name="n_show_navi" type="checkbox" id="n_show_navi" <?php print ($n_show_navi==1)?"checked":"";?>>
							在导航显示
						</label>
					</td>
				</tr>
			</table>	
		</td>
	</tr>
	
	<?php 
	if($n_type=="news"){
		$sql = "SELECT * FROM content_info WHERE c_id='$node_id'";
		$stmt = mysql_query($sql);
		if(mysql_num_rows($stmt)>0){
			$row = mysql_fetch_array($stmt);
			foreach($content_info_fields as $val){
				${"t_".$val} = $row[$val];
			}
		}
	?>
	<tr>
        <td class="title">
			开始日期
		</td>
        <td>
			<input name="t_start_date" type="text" class="date_input trip" value="<?php print ($t_start_date!="" && $t_start_date!="0000-00-00")?date('Y-m-d',strtotime($t_start_date)):"";?>"> 
			如设为长期有效，请留空
		</td>
		<td class="title">
			结束日期
		</td>
		<td>
			<input name="t_end_date" type="text" class="date_input trip" value="<?php print ($t_end_date!="" && $t_end_date!="0000-00-00")?date('Y-m-d',strtotime($t_end_date)):"";?>"> 
			如设为长期有效，请留空
		</td>       
    </tr>	
	<?php } ?>
	
	<?php if($tc_summary==1){ ?>
	<tr valign="top">
		<td class="title">
			页面摘要
		</td>
		<td colspan="3">
			<textarea name="n_summary" class="sim_editor" id="n_summary"><?php print $n_summary;?></textarea>
		</td>
	</tr>
	<?php } ?>
	<?php if($tc_content==1){ ?>
    <tr valign="top">
        <td class="title">
			页面正文
		</td>
		<td colspan="3">
			<textarea name="n_content" class="adv_editor" id="n_content"><?php print $n_content;?></textarea>
		</td>
    </tr>
	<?php } ?>
</table>

<script>
$(function() {
	sim_editor = KindEditor.create('textarea[class=sim_editor]', {
		width: '100%',
		height: '200px',
		resizeType : 1,
		filterMode: false,//是否开启过滤模式
		items : [
		'source', '|', 'plainpaste', 'clearhtml', '|', 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 
		//'emoticons', 'image', 
		'link'
		]
	});
	
	adv_editor = KindEditor.create('textarea[class=adv_editor]', {
		width: '100%',
		height: '450px',
		resizeType : 1,
		uploadJson : 'JSON_upload.php',
		filterMode: false,//是否开启过滤模式
		items : [
        'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste', 'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/', 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage', 'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 
		//'baidumap', 
		'pagebreak', 'anchor', 'link', 'unlink'
		//, '|', 'about'
		]
	});
	
	$('#n_title').focus(function(){
		if($(this).val()==''){
			$(this).val($('#n_name').val());
		}
	});
	
	$('.date_input').each(function(){
		if(!$(this).hasClass('trip')){
			$(this).datepicker({
				dateFormat:'yy-mm-dd'
			});
		}
		else{
			$(this).datepicker({
				changeMonth:true,
				changeYear: true,
				dateFormat:'yy-mm-dd',
				yearRange: '2011:<?php print date('Y')+1;?>'
			});
		}
	});
	
	set_img_upload();
});

function delete_img(n_id){
	if($('#img_ctrl_area img').length>0){
		if(confirm('确定删除此图片？')){
			show_alert('正在删除图片，请稍候 ...','load');
			
			$.ajax({
				type: 'post',
				url: 'img_delete.php?n_id='+n_id,
				dataType: 'json',
				//data: params,
				success: function(result){
					if(result.success==1){
						$('#img_ctrl_holder').html(
							'<div id="img_ctrl_holder" class="upload_area">' + 
								'<input name="n_image" class="btn_file" type="file" id="n_image">' + 
								'<span class="btn_upload"><?php print ($_SESSION['u_lang']==0)?"Upload Image":"上传图片";?></span>' + 
							'</div>'
						);
						set_public_attr();
						set_img_upload();
						show_alert('成功删除图片！');
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
		url:'img_upload.php?object=content&object_type=<?php print $object_type;?>&n_id='+$('#node_id').val()+'&targetID='+$('#node_id').val(),
		secureuri :false,
		fileElementId :'n_image',
		dataType : 'json',
		success : function (result){
			if(result.success==1){
				var delete_ctrl_str = 	'<a class="ctrl_arrow"></a>' + 
										'<div class="ctrl_menu">' + 
											'<input name="n_image" class="btn_file" type="file" id="n_image">' + 
											'<a class="item replace"><?php print ($_SESSION['u_lang']==0)?"Replace":"替换";?></a>' + 
											'<a class="item crop" onclick="crop_img(\'<?php print _ROOT_URL_;?>'+result.file_url+'\');"><?php print ($_SESSION['u_lang']==0)?"Crop":"裁切";?></a>' + 
											'<a class="item delete" onclick="delete_img(\'<?php print $node_id;?>\');"><?php print ($_SESSION['u_lang']==0)?"Delete":"删除";?></a>' + 
										'</div>';
				
				$('#img_ctrl_area').html(
					'<div id="img_ctrl_holder" class="display_area">' + 
						'<img src="<?php print _ROOT_URL_;?>'+result.file_url+'">' + 
						'<div id="img_ctrl" class="menu_holder">' + 
							delete_ctrl_str + 
						'</div>' + 
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
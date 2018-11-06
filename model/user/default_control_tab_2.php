<!--TAB PANEL 2-->
<table id="tab_panel_2" width="100%" class="data_holder tab_panel banner_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td id="banner_holder">
<?php
$sql = "SELECT * FROM banner_table WHERE type='0' ORDER BY sort_order";
$get_banner = mysql_query($sql);
$print_banner = "";
$banner_count = mysql_num_rows($get_banner);
if($banner_count>0){
	$print_banner .= "<div id=\"sort_banner\">";
	while($b_row = mysql_fetch_array($get_banner)){
		$b_id = $b_row['id'];
		$b_image = $b_row['image'];
		$b_img_title = $b_row['img_title'];
		$b_img_desc = $b_row['img_desc'];
		$b_img_link = $b_row['img_link'];
		$b_full_image = str_replace(_THUMB_FOLDER_,"",$b_image);
		
		if($_SESSION['auth_content']==2){
			$publish_class = "";
			$publish_ctrl_str = "<a class=\"item publish hide\" onclick=\"publish_banner('$b_id');\">隐藏</a>";
			if(check_publish($b_id,"banner")==0){
				$publish_class = "hide";
				$publish_ctrl_str = "<a class=\"item publish show\" onclick=\"publish_banner('$b_id');\">显示</a>";
			}
			
			$delete_ctrl_str = "<a class=\"icon delete\" onclick=\"delete_banner('$b_id');\"></a>";
		}
		
		if($b_image!="" && file_exists(_ROOT_PATH_.$b_image)){
			$b_image = _ROOT_URL_.$b_image."?".TIMESTAMP;
			$b_full_image = _ROOT_URL_.$b_full_image;
			
			if($_SESSION['auth_content']==2){	
$delelte_ctrl_str = <<<DELETE_CTRL
<a class="ctrl_arrow"></a>
<div class="ctrl_menu">
	<input name="b_image_$b_id" class="btn_file upload_banner" type="file" id="b_image_$b_id">
	<a class="item replace">
		替换
	</a>
	$publish_ctrl_str
</div>
DELETE_CTRL;
			}
			
$print_img_ctrl = <<<IMG
<div id="img_ctrl_holder" class="display_area">
	<img src="$b_image">
	<div id="img_ctrl" class="menu_holder">
		$delelte_ctrl_str
	</div>
</div>
IMG;
		}
		else{
$print_img_ctrl = <<<IMG
<div id="img_ctrl_holder" class="upload_area">							
	<input name="b_image_$b_id" class="btn_file upload_banner" type="file" id="b_image_$b_id">
	<span class="btn_upload">
		上传
	</span>
</div>
IMG;
		}
		
$print_banner .= <<<BANNER
<div id="banner_$b_id" class="banner_info $publish_class">
	<i class="icon handle"></i>
	$delete_ctrl_str
	<input type="hidden" name="b_id[]" id="b_id[]" value="$b_id">
	<table border="0" cellpadding="0" cellspacing="10">
		<tr>
			<td class="title">
				图片标题
			</td>
			<td width="250px">
				<input name="b_img_title[]" type="text" id="b_img_title" maxlength="20" value="$b_img_title">
			</td>
			<td id="img_ctrl_area" valign="top" rowspan="3">
				$print_img_ctrl
			</td>
		</tr>
		<tr valign="top">
			<td class="title">
				图片描述
			</td>
			<td>
				<textarea name="b_img_desc[]" class="txt_area" id="b_image_desc">$b_img_desc</textarea>
			</td>
		</tr>
		<tr valign="top">
			<td class="title">
				图片链接
			</td>
			<td>
				<input type="text" name="b_img_link[]" id="b_img_link" value="$b_img_link">
				<a class="ctrl_link" onclick="choose_link('$b_id');">
					选择页面
				</a>
			</td>
		</tr>	
	</table>
</div>
BANNER;
	}
	$print_banner .= "</div>";
}

print $print_banner;
?>
			
		</td>
	</tr>
	<tr>
		<td class="banner_ctrl">
			<button id="btn_add" class="ctrl_btn sub active" onclick="add_banner();">
				创建横幅
			</button>
			<button id="btn_save" class="ctrl_btn sub active" onclick="save_banner();">
				保存横幅
			</button>
			<button id="btn_cancel" class="ctrl_btn" style="display:none;" onclick="add_banner('cancel');">
				取消
			</button>
		</td>
	</tr>
</table>
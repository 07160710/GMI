<?php 
if($tc_core==1){	
?>
<!--TAB PANEL 3-->
<table id="tab_panel_3" class="data_holder tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">		
			<table width="100%" border="0" cellpadding="0" cellspacing="10px">
				<tr valign="top">
					<td class="title">
						<dt class="lang en">Time Info</dt>
						<dt class="lang zh">时间信息</dt>
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_template_content('time_info');">
							<dt class="lang en">Insert Template</dt>
							<dt class="lang zh">插入模板</dt>
						</button>
					</td>
					<td>
						<textarea name="t_time_info" class="tti_editor"><?php print $t_time_info;?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<td class="title">
						<dt class="lang en">Itinerary</dt>
						<dt class="lang zh">具体行程</dt>
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_template_content('itinerary');">
							<dt class="lang en">Insert Template</dt>
							<dt class="lang zh">插入模板</dt>
						</button>
					</td>
					<td>
						<textarea name="t_itinerary" class="ti_editor"><?php print $t_itinerary;?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<td class="title">
						<dt class="lang en">Attention</dt>
						<dt class="lang zh">注意事项</dt>
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_template_content('attention');">
							<dt class="lang en">Insert Template</dt>
							<dt class="lang zh">插入模板</dt>
						</button>
					</td>
					<td>
						<textarea name="t_attention" class="ta_editor"><?php print $t_attention;?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<td class="title">
						<dt class="lang en">Fare Info</dt>
						<dt class="lang zh">费用信息</dt>
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_template_content('fare_info');">
							<dt class="lang en">Insert Template</dt>
							<dt class="lang zh">插入模板</dt>
						</button>
					</td>
					<td>
						<textarea name="t_price_info" class="tfi_editor"><?php print $t_price_info;?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<td class="title">
						<dt class="lang en">Signup Method</dt>
						<dt class="lang zh">报名方式</dt>
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_template_content('signup_method');">
							<dt class="lang en">Insert Template</dt>
							<dt class="lang zh">插入模板</dt>
						</button>
					</td>
					<td>
						<textarea name="t_signup_method" class="tsm_editor"><?php print $t_signup_method;?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<td class="title">
						<dt class="lang en">Signup Content</dt>
						<dt class="lang zh">报名内容</dt>
						<?php if($tc_insert_dt==1){ ?>
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="insert_dt('<?php print $node_id;?>','signup');"">
							<dt class="lang en">Insert Form</dt>
							<dt class="lang zh">插入表单</dt>
						</button>
						<?php } ?>
					</td>
					<td>
						<textarea name="t_signup_content" class="tsc_editor"><?php print $t_signup_content;?></textarea>
					</td>
				</tr>
			</table>
		</td>
    </tr>
</table>
<?php } ?>
<?php if($tc_ads_banner==1){ ?>
<!--TAB PANEL 3-->
<table id="tab_panel_3" width="100%" class="tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td id="banner_holder">
<?php
$banner_type = 1;
if($n_type=="slide_show")$banner_type = 2;
if($n_type=="custom_home")$banner_type = 3;

$get_banner_query = "SELECT * FROM banner_table WHERE type='$banner_type' AND c_id='$node_id' ORDER BY sort_order";
$get_banner = mysql_query($get_banner_query);
$print_banner = "";
if(mysql_num_rows($get_banner)>0){
	$print_banner .= "<div id=\"sort_banner\">";
	while($b_row = mysql_fetch_array($get_banner)){
		$b_id = $b_row['id'];
		$b_image = $b_row['image'];
		$b_img_title = $b_row['img_title'];
		$b_img_desc = $b_row['img_desc'];
		$b_img_link = $b_row['img_link'];
		$b_full_image = str_replace(_THUMB_FOLDER_,"",$b_image);
		
		$publish_class = "";
		$publish_ctrl_str = "<a class=\"item publish hide\" onclick=\"publish_banner('$b_id');\">
								<dt class=\"lang en\">Hide</dt>
								<dt class=\"lang zh\">隐藏</dt>
							</a>";
		if(check_publish($b_id,"banner")==0){
			$publish_class = "hide";
			$publish_ctrl_str = "<a class=\"item publish show\" onclick=\"publish_banner('$b_id');\">
									<dt class=\"lang en\">Show</dt>
									<dt class=\"lang zh\">显示</dt>
								</a>";
		}
		
		$delete_ctrl_str = "";
		if($_SESSION['auth_delete']==1)
			$delete_ctrl_str = "<a class=\"icon delete\" onclick=\"delete_banner('$b_id');\" title=\"删除\"></a>";
		
		if($b_image!="" && file_exists(_ROOT_PATH_.$b_image)){
			$b_image = _ROOT_URL_.$b_image."?".TIMESTAMP;
			$b_full_image = _ROOT_URL_.$b_full_image;
			
			if($_SESSION['auth_delete']==1){	
$delelte_ctrl_str = <<<DELETE_CTRL
<a class="ctrl_arrow"></a>
<div class="ctrl_menu">
	<input name="b_image_$b_id" class="btn_file upload_banner" type="file" id="b_image_$b_id">
	<a class="item replace">
		<dt class="lang en">Replace</dt>
		<dt class="lang zh">替换</dt>
	</a>
	<a class="item view" onclick="view_img('$b_id','banner_img');">
		<dt class="lang en">View</dt>
		<dt class="lang zh">查看</dt>
	</a>
	$publish_ctrl_str
	<a class="item crop" onclick="crop_img('$b_full_image');">
		<dt class="lang en">Crop</dt>
		<dt class="lang zh">裁切</dt>
	</a>
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
		<dt class="lang en">Upload Banner</dt>
		<dt class="lang zh">上传横幅</dt>
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
				<dt class="lang en">Image Title</dt>
				<dt class="lang zh">图片标题</dt>
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
				<dt class="lang en">Image Desc</dt>
				<dt class="lang zh">图片描述</dt>
			</td>
			<td>
				<textarea name="b_img_desc[]" class="txt_area" id="b_image_desc">$b_img_desc</textarea>
			</td>
		</tr>
		<tr valign="top">
			<td class="title">
				<dt class="lang en">Image Link</dt>
				<dt class="lang zh">图片链接</dt>
			</td>
			<td>
				<input type="text" name="b_img_link[]" id="b_img_link" value="$b_img_link">
				<a class="ctrl_link" onclick="choose_link('$b_id');">
					<dt class="lang en">Choose Page</dt>
					<dt class="lang zh">选择页面</dt>
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
				<dt class="lang en">Create Banner</dt>
				<dt class="lang zh">创建横幅</dt>
			</button>
			<button id="btn_save" class="ctrl_btn sub active" onclick="save_banner();">
				<dt class="lang en">Save Banner</dt>
				<dt class="lang zh">保存横幅</dt>
			</button>
			<button id="btn_cancel" class="ctrl_btn" style="display:none;" onclick="add_banner('cancel');">
				<dt class="lang en">Cancel</dt>
				<dt class="lang zh">取消</dt>
			</button>		
		</td>
	</tr>
</table>
<?php } ?>
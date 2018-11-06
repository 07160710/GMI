<!--TAB PANEL 4-->
<table id="tab_panel_4" class="data_holder tab_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">		
			<table width="100%" border="0" cellpadding="0" cellspacing="10px">
				<tr valign="top">
					<td class="title">
						发布状态
					</td>
					<td>
						<?php
						if($published_time==0){
						?>
							<label>
								未发布
							</label>
						<?php
						}
						else{
						?>
							<label>
								发布时间: 
								<?php print format_time($published_time);?>
							</label>
							<button class="ctrl_btn" onclick="unpublish_page_info();">
								停止发布
							</button>
							<div class="clear" style="height:10px;"></div>
							<label style="line-height:26px;">
								页面链接：
								<a href="<?php print _ROOT_URL_.fetch_route($node_id);?>" target="_blank"><?php print _ROOT_URL_.fetch_route($node_id);?></a>
							</label>							
						<?php } ?>
					</td>
				</tr>
				<?php if($tc_redirect==1){ ?>
				<tr>
					<td class="title">
						跳转链接
					</td>
					<td>
						<input name="n_redirect" type="text" id="n_redirect" class="long_input" style="float:left;" value="<?php print $n_redirect;?>">
						<a class="ctrl_link" onclick="choose_link('<?php print $node_id;?>');">
							选择页面
						</a>
						<div class="clear note">
							如需跳转至外部网页，请输入绝对地址（例：http://www.abc.com）。
						</div>
					</td>
				</tr>
				<?php } ?>
				<?php if($tc_meta_desc==1){ ?>
				<tr valign="top">
					<td class="title">
						页面描述
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_desc();">
							生成描述
						</button>
					</td>
					<td>
						<textarea name="n_meta_desc" class="txt_area" id="n_meta_desc"><?php print $n_meta_desc;?></textarea>
					</td>
				</tr>
				<?php } ?>
				<?php if($tc_meta_kwrd==1){ ?>
				<tr valign="top">
					<td class="title">
						页面关键词
						<div class="clear" style="height:5px;"></div>
						<button class="ctrl_btn sub active insert" onclick="build_kwrd();">
							生成关键词
						</button>
					</td>
					<td>
						<textarea name="n_meta_kwrd" class="txt_area" id="n_meta_kwrd"><?php print $n_meta_kwrd;?></textarea>
					</td>
				</tr>
				<?php } ?>
			</table>
		</td>		
<?php
$info_print = <<<INFO
<td class="info_holder" width="130px" valign="top">
	<h1>页面信息</h1>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<b>创建时间</b><br/>$n_created_time
			</td>
		</tr>
		<tr>
			<td>
				<b>创建用户</b><br/>$n_created_user
			</td>
		</tr>
		<tr>
			<td>
				<b>最后更新时间</b><br/>$n_updated_time
			</td>
		</tr>
		<tr>
			<td>
				<b>最后更新用户</b><br/>$n_updated_user
			</td>
		</tr>	
	</table>
</td>
INFO;

if($node_id!="")print $info_print;
?>		
	</tr>
</table>
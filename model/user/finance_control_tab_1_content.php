<!--VIEW COMPANY HOLDER-->
<div id="view_project_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title"></span>
				<a class="btn_close" onclick="view_project('close');"></a>				
			</td>
		</tr>		
		<tr>
			<td>
<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
	<?php include('public_project_tab.php');?>
	<tr valign="top" id="subtab_panel_1" class="subtab_panel">
		<td>
			<form id="project_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
				<input type="hidden" name="action">
				<input type="hidden" name="id">
				<table style="width:100%;height:100%;" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="form_holder project">
							<div class="scroll_holder">
								<a class="top_anchor"></a>
								<table class="project_holder project" cellpadding="5" cellspacing="0">
									<tr>
										<td class="title" rowspan="4">人员安排</td>
										<td rowspan="4">
<table class="assign_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th>人员列表</th>
		<th>分配列表</th>
	</tr>
	<tr>
		<td id="user_list" class="user_list_holder">
			<div class="scroll_holder">
				<ul class="list"></ul>
			</div>
			<div class="assign_filter_holder">
				<input type="text" class="assign_filter" placeholder="输入姓名">
				<a class="btn_clear" onclick="clear_assign_filter();"></a>
			</div>
		</td>
		<td id="assign_list" class="assign_list_holder">
			<div class="scroll_holder">
				<ul class="list"></ul>
			</div>
		</td>
	</tr>
</table>
										</td>
										<td class="title required">项目总进度</td>
										<td>
											<select id="v_progress" name="v_progress">
											<?php
											foreach($GLOBALS['project_progress_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
									</tr>
									<tr>	
										<td class="title required">需要立项？</td>
										<td>
											<select id="v_need_approve" name="v_need_approve">
											<?php
											foreach($GLOBALS['need_approve_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="title required">需要验收？</td>
										<td>
											<select id="v_need_check" name="v_need_check">
											<?php
											foreach($GLOBALS['need_check_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="title required">需要请款？</td>
										<td>
											<select id="v_need_fund" name="v_need_fund">
											<?php
											foreach($GLOBALS['need_fund_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="title">服务外包</td>
										<td colspan="3">
											<label for="v_outsource_t">
												<input type="checkbox" id="v_outsource_t" name="v_outsource[]" value="t">
												无需安排技术人员
											</label>
											<label for="v_outsource_f">
												<input type="checkbox" id="v_outsource_f" name="v_outsource[]" value="f">
												无需安排财务人员
											</label>
										</td>
									</tr>
									<tr>
										<td id="remark_title" class="title">
											项目备注
										</td>
										<td colspan="3">
											<input type="text" id="v_append_remark" name="v_append_remark" class="ex-long" placeholder="备注只增加，不删除，项目取消必须备注原因">
										</td>
									</tr>
									<tr>
										<th colspan="4">财务信息</th>
									</tr>
									<tr>
										<td colspan="4" style="text-align:center;color:#ff0000;">
											注意：以下金额项目，可填整数或小数，单位“万元”；也可填合同金额的百分比，含“%”符号。
										</td>
									</tr>
									<tr>
										<td class="title">付费类型</td>
										<td>
											<select id="v_pay_type" name="v_pay_type" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
											<?php
											foreach($GLOBALS['project_pay_type_opt'] as $key=>$val){
												print '<option value="'.$key.'">'.$val.'</option>';
											}
											?>
											</select>
										</td>
										<td class="title">签约金额</td>
										<td>
											<input type="text" name="v_amt_contract" id="v_amt_contract" class="ex-short number" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
										</td>
									</tr>
									<tr>
										<td class="title">预付款</td>
										<td>
											<input type="text" name="v_amt_prepay" id="v_amt_prepay" class="ex-short number" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
										</td>
										<td class="title">实际收款</td>
										<td>
											<input type="text" name="v_amt_actual" id="v_amt_actual" class="ex-short number" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
										</td>
									</tr>
									<tr>
										<td class="title">辛苦费</td>
										<td>
											<input type="text" name="v_amt_commission" id="v_amt_commission" class="ex-short number" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
										</td>
										<td class="title">中间人</td>
										<td>
											<input type="text" name="v_agent" id="v_agent" class="mid" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
										</td>
									</tr>
									<tr>
										<td class="title">财务备注</td>
										<td colspan="3">
											<input type="text" id="v_finance_remark" name="v_finance_remark" class="ex-long" placeholder="备注签约金额构成、代理费构成等" <?php print ($_SESSION['level']<3)?"readonly":"";?>>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
	<?php include('public_project_tab_panel.php');?>
</table>
			</td>
		</tr>
		<?php if($_SESSION['auth_finance']==2){ ?>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active save" onclick="view_project('save');">
					保存项目
				</button>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>

<div id="request_holder" class="overlay">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					请求协助
				</span>
				<a class="btn_close" onclick="request_help('close');"></a>				
			</td>
		</tr>
		<tr>
			<td class="alert_holder">
				注意：以下消息将会发给此项目相关人
			</td>
		</tr>
		<tr>
			<td id="request_panel">
				<input type="hidden" name="project_id">
				<input type="hidden" name="u_type">
				<textarea name="request_content" placeholder="请描述请求协助的内容，例如：仍缺XX资料"></textarea>
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="request_help('save');">
					发送请求
				</button>
			</td>
		</tr>
	</table>
</div>
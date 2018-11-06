<tr valign="top" id="subtab_panel_2" class="subtab_panel">
	<td>
		<div class="scroll_holder">
			<table width="99%" id="process_holder" border="0" cellpadding="3" cellspacing="0">
				<tr>
					<th>主程序</th>
					<th width="80">子程序</th>
					<th width="26.5%">销售</th>
					<th width="26.5%">技术</th>
					<th width="26.5%">财务</th>						
				</tr>
				<tr>
					<td rowspan="2">项目派单</td>
					<td class="title">派单</td>
					<td class="data assign_s"></td>
					<td class="data assign_t"></td>
					<td class="data assign_f"></td>
				</tr>
				<tr>
					<td class="title">接单</td>
					<td class="data accept_s"></td>
					<td class="data accept_t"></td>
					<td class="data accept_f"></td>
				</tr>
				<tr>
					<td rowspan="7">项目申报</td>
					<td class="title">收集申报材料</td>
					<td></td>
					<td class="data apply_t_o"></td>
					<td class="data apply_f_o"></td>
				</tr>
				<tr>
					<td class="title">申报材料完稿</td>
					<td></td>
					<td class="data apply_t_d"></td>
					<td class="data apply_f_d"></td>
				</tr>
				<tr>
					<td rowspan="2" class="title">提交电子材料</td>
					<td rowspan="2"></td>
					<td colspan="2" class="data apply_deadline_e highlight"></td>
				</tr>
				<tr>
					<td class="data apply_t_e"></td>
					<td class="data apply_f_e"></td>
				</tr>
				<tr>
					<td rowspan="2" class="title">提交纸质材料</td>
					<td rowspan="2"></td>
					<td colspan="2" class="data apply_deadline_p highlight"></td>
				</tr>
				<tr>
					<td class="data apply_t_p"></td>
					<td class="data apply_f_p"></td>
				</tr>
				<tr>
					<td class="title">申报提交确认</td>
					<td></td>
					<td class="data apply_t_c"></td>
					<td class="data apply_f_c"></td>
				</tr>
				<tr class="data_row need_approve">
					<td>项目立项</td>
					<td class="title status_approve">待立项</td>
					<td colspan="3" style="line-height: 32px" class="data project_approve"></td>
				</tr>
				<tr class="data_row need_check">
					<td rowspan="2">项目验收</td>
					<td class="title">第一次验收</td>
					<td colspan="3" style="line-height: 32px" class="data acceptance_check1"></td>
				</tr>
				<tr class="data_row need_check">
					<td class="title">第二次验收</td>
					<td colspan="3" style="line-height: 32px" class="data acceptance_check2"></td>
				</tr>
				<tr class="data_row need_fund">
					<td rowspan="2">项目请款</td>
					<td class="title">请款</td>
					<td colspan="3" style="line-height: 32px" class="data request_fund"></td>
				</tr>
				<tr class="data_row need_fund">
					<td class="title">企业收款</td>
					<td colspan="3" style="line-height: 32px" class="data receive_fund"></td>
				</tr>
				<tr>
					<td>项目回款</td>
					<td class="title">中科回款</td>
					<td colspan="3" style="line-height: 32px" class="data receive_fee"></td>
				</tr>
			</table>
		</div>
	</td>
</tr>
<tr valign="top" id="subtab_panel_3" class="subtab_panel">
	<td>
		<div class="scroll_holder">
			<table width="99%" id="log_holder" border="0" cellpadding="3" cellspacing="0">
				<tr class="header_row">
					<th width="100">用户</th>
					<th width="100">类别</th>
					<th>操作</th>
					<th width="200">记录时间</th>
				</tr>
			</table>
		</div>
	</td>
</tr>
<tr valign="top" id="subtab_panel_4" class="subtab_panel">
	<td>
		<div class="scroll_holder">
			<table width="99%" id="platform_holder" border="0" cellpadding="0" cellspacing="0">
				<tr class="header_row">
					<th>平台名称</th>						
					<th width="20%">账号</th>
					<th width="20%">密码</th>
					<th width="50">类型</th>
					<th width="20%">备注</th>
					<th width="20"><a class="ctrl_link link"></a></th>
					<th width="20"><a class="ctrl_link delete"></a></th>
				</tr>
			</table>
			<button class="process_btn" onclick="add_platform();">添加平台账号</button>
		</div>
	</td>
</tr>

<?php include("public_project_js.php"); ?>
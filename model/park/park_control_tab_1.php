<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7 0007
 * Time: 11:23
 */
?>

<!--TAB PANEL 1-->
<table id="tab_panel_1" class="tab_panel" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
<table class="header_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="sidebar">
            <div id="hide_arrow_holder">
                <a id="hide_arrow" title="收起侧栏" onclick="display_sidebar();"></a>
            </div>
        </td>
        <td>
            <!--auth_company-->
            <table class="data_ctrl_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="ctrl_holder">
                        <div class="ctrl_link">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <span>操作</span>
                                    </td>
                                    <td>
                                        <div class="menu_holder">
                                            <a class="ctrl_arrow"></a>
                                            <div class="ctrl_menu">
                                                <a class="item" onclick="view_company('create');">
                                                    创建园区
                                                </a>
                                                <a class="item" onclick="multi_edit_company('company');">
                                                    导出园区
                                                </a>
                                                <?php if($_SESSION['level']>2){ ?>
                                                    <a class="item" onclick="import_company();">
                                                        导入园区
                                                    </a>
                                                    <a class="item" onclick="multi_edit_company('delete');">
                                                        删除园区
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="ctrl_link">
                            <label for="all_page_company">
                                <input type="checkbox" class="check_all" id="all_page_company">
                                选取所有分页园区
                            </label>
                        </div>
                        <div class="data_count_holder">
                        <span id="company_count" class="data_count">
                            <b></b>&nbsp;
                            个园区
                        </span>
                        </div>

                        <select id="per_page">
                            <option>20</option>
                            <option>50</option>
                            <option>100</option>
                            <option>300</option>
                            <option>500</option>
                        </select>
                        &nbsp;条 / 页
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<table class="body_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="sidebar" valign="top">
            <table id="sidebar_holder" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td id="filter_holder">
                        <div id="cond_holder">
                            <b>
                                筛选条件
                                <button onclick="clear_filter();">
                                    清空
                                </button>
                            </b>
                            <div id="cond_list"></div>
                        </div>

                        <div id="filter_name" class="list_holder">
                            <span class="title">
                            园区名称
                            </span>
                            <input type="text" id="search_name">
                            <button onclick="filter_search();">
                                筛选
                            </button>
                        </div>

                        <!--province-->
                        <div id="filter_province" class="list_holder">
                        <span>
                        <label for="all_province">
                            <input type="checkbox" class="check_all" id="all_province">
                            所有省
                        </label>
                        <button onclick="filter_search();">
                            筛选
                        </button>
                        </span>
                            <ul class="filter_list"></ul>
                        </div>
                        <!--city-->
                        <div id="filter_city" class="list_holder">
                        <span>
                        <label for="all_city">
                            <input type="checkbox" class="check_all" id="all_city">
                            所有市
                        </label>
                        <button onclick="filter_search();">
                            筛选
                        </button>
                        </span>
                            <ul class="filter_list"></ul>
                        </div>
                        <!--district-->
                        <div id="filter_district" class="list_holder">
                        <span>
                        <label for="all_district">
                            <input type="checkbox" class="check_all" id="all_district">
                            所有区
                        </label>
                        <button onclick="filter_search();">
                            筛选
                        </button>
                        </span>
                            <ul class="filter_list"></ul>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td id="right_col" valign="top">
            <table id="company_data" class="data_holder list" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table id="company_list" class="data_list" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="header_row">
                                <th width="10"><input type="checkbox" class="check_all" id="curr_page_company"></th>
                                <th width="30">
                                    查看
                                </th>
                                <?php
                                foreach($GLOBALS['company_list_fields'] as $key=>$val){
                                    $width_str = "";
                                    if(strpos($val,"#")!==false){
                                        $val_arr = explode("#",$val);
                                        $width_str = 'width="'.$val_arr[1].'"';
                                        $val = $val_arr[0];
                                    }
                                    $sort_arrow = "";
                                    if($key=="name" || $key=="province" || $key=="city" || $key=="district" || $key=="cancelled")$sort_arrow = "<i></i>";

                                    print "<th $width_str><a class=\"sort_$key\">".$val.$sort_arrow."</a></th>";
                                }
                                ?>
                            </tr>
                        </table>
                        <ul id="pagination"></ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


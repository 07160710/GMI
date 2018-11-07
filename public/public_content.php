<script>
    var node_arr = new Array();
    if(id_prefix==undefined)var id_prefix = '';
    if(post_id==undefined)var post_id = '';
    var curr_id = '',
        ctrl_action = '',
        ctrl_id = '',
        ctrl_curr_id = '',
        ctrl_panel_str = '#left_col_holder',
        ctrl_str = '',
        post_last = (id_prefix=='n_')?0:false;

    function select_node(n_id){
        if(ctrl_action=='move'){
            if(n_id==ctrl_id){//cannot move to self
                alert('不能移动元素到自身元素之下！');
                return false;
            }
            if($(ctrl_panel_str+' #'+id_prefix+n_id).parents('li[id='+id_prefix+ctrl_id+']').length>0){//cannot move to child
                alert('不能移动元素到其子元素之下！');
                return false;
            }
            if($('#'+id_prefix+n_id+' ul.branch:eq(0) > li[id='+id_prefix+ctrl_id+']').length>0){//cannot move to same parent
                alert('此元素已在其父元素之下！');
                return false;
            }
        }

        ctrl_curr_id = n_id;
        set_curr(ctrl_curr_id);

        var str_ctrl_child = '';
        if($(ctrl_panel_str+' #'+id_prefix+ctrl_id+' .node_holder:eq(0) .toogle').length>0){
            str_ctrl_child = '及其子元素';
        }
        var prefix = (id_prefix=='u_')?'g_':id_prefix;

        $('#'+ctrl_action+'_holder .alert_holder').html('元素['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']'+str_ctrl_child+'将会'+ctrl_str+'到父元素['+$(ctrl_panel_str+' #'+prefix+ctrl_curr_id+' .node_link:eq(0)').text()+']之下。');
    }

    function expand_node(e){
        post_id = e;
        curr_id = e;
        $.ajax({
            type:		'GET',
            url: 		'fetch_parents.php?'+id_prefix+'id='+e,
            dataType:	'xml',
            success: function (xml) {
                var n_id = '';
                $(xml).find('node').each(function(){
                    if(n_id!='')n_id += '|';
                    n_id += $(this).find('parent_id').text();
                });

                toogle_node(n_id);
            }
        });
    }

    function toogle_node(n_id){
        var prefix = (id_prefix=='u_')?'g_':id_prefix;
        if(n_id.indexOf('|')<0){//single
            if($(ctrl_panel_str+' #'+prefix+n_id+' .toogle').hasClass('expand')){
                $(ctrl_panel_str+' #'+prefix+n_id+' .toogle').removeClass('expand').addClass('shrink');
                $(ctrl_panel_str+' #'+prefix+n_id+' .branch').html('');
            }
            else{
                node_arr.push(n_id);
            }
        }
        else{//multiple
            var n_id_arr = n_id.split('|');
            for(var i=0;i<n_id_arr.length;i++){
                node_arr.push(n_id_arr[i]);
            }
        }
        fetch_children(0);
    }

    var auth_add = '<?php print $_SESSION['auth_add'];?>',
        auth_publish = '<?php print $_SESSION['auth_publish'];?>',
        auth_move = '<?php print $_SESSION['auth_move'];?>',
        auth_sort = '<?php print $_SESSION['auth_sort'];?>',
        auth_delete = '<?php print $_SESSION['auth_delete'];?>';
    function fetch_children(i){
        var index = i,
            parent_id = node_arr[index],
            set_curr_id = 0;

        var prefix = (id_prefix=='u_')?'g_':id_prefix;
        if((ctrl_panel_str.indexOf('#left_col_holder')>=0 && parent_id>0) || ctrl_action!=''){
            if($(ctrl_panel_str+' #'+prefix+parent_id+' .toogle:eq(0)').hasClass('shrink')){
                $(ctrl_panel_str).append('<div class="loading_overlay"></div>');
            }
            $(ctrl_panel_str+' #'+prefix+parent_id+' .toogle:eq(0)').removeClass('shrink').addClass('expand');
            $(ctrl_panel_str+' #'+prefix+parent_id+' .branch:eq(0)').append('<div class="loading"><i></i></div>');
        }

        var tb_name = 'content_table';
        switch(id_prefix){
            case 'm_':tb_name = 'media_table';break;
            case 'g_':tb_name = 'user_group';break;
            case 'u_':tb_name = 'user_group';break;
        }

        if(parent_id!=undefined)
            $.ajax({
                type:		'GET',
                url: 		'fetch_children.php?tb='+tb_name+'&parent_id='+parent_id,
                dataType:	'xml',
                async:		true,
                success: function (xml) {
                    $(ctrl_panel_str+' #'+prefix+parent_id+' .branch .loading').remove();
                    $('.loading_overlay').remove();

                    $(xml).find('node').each(function(){
                        var id = $(this).find('id').text(),
                            name = $(this).find('name').text(),
                            type = $(this).find('type').text(),
                            level = $(this).find('level').text(),
                            publish = $(this).find('publish').text(),
                            has_child = $(this).find('has_child').text();

                        if(id==post_id)post_last = (id_prefix=='n_')?(post_last+1):true;

                        var publish_class = (publish!=1 && page_name!='media')?'hide':'';

                        var toogle_str = '',
                            sort_str = '',
                            branch_str = '';
                        if(has_child==1){
                            toogle_str = '<a class="toogle shrink" onclick="toogle_node(\''+id+'\');"></a>';
                            sort_str = '<a class="item sort" onclick="sort_node(\''+id+'\');">排序</a>';
                            branch_str = '<ul class="branch l_'+(parseInt(level)+1)+'"></ul>';
                        }

                        if(ctrl_panel_str.indexOf('#left_col_holder')>=0){
                            if(parent_id>0){
                                var ctrl_menu_item = '';

                                ctrl_menu_item += '<a class="item add" onclick="add_node(\''+id+'\');">添加</a>';

                                if(page_name=='media'){
                                    ctrl_menu_item += '<a class="item upload" onclick="display_upload(\''+id+'\');">上传</a>';
                                }

                                ctrl_menu_item += '<a class="item move" onclick="move_node(\''+id+'\');">移动</a>';
                                ctrl_menu_item += sort_str;

                                if(page_name=='content'){
                                    ctrl_menu_item += '<a class="item publish" onclick="publish_node(\''+id+'\');">发布</a>';
                                }
                                if(page_name=='media'){
                                    ctrl_menu_item += '<a class="item auth" onclick="auth_manage(\'folder\',\''+id+'\');">权限</a>';
                                }

                                ctrl_menu_item += '<a class="item delete" onclick="delete_node('+id+');">删除</a>';

                                var ctrl_menu = '';
                                if(ctrl_menu_item!=''){
                                    ctrl_menu = '<div class="ctrl_menu">' +
                                        ctrl_menu_item +
                                        '</div>';
                                }

                                $(ctrl_panel_str+' #'+id_prefix+parent_id+' .l_'+level).append(
                                    '<li id="'+id_prefix+id+'">' +
                                    toogle_str +
                                    '<div class="node_holder '+publish_class+'">' +
                                    '<span class="node">' +
                                    '<a class="node_link '+type+'" title="'+name+'" onclick="edit_node(\''+id+'\');">'+name+'</a>' +
                                    '<div class="menu_holder">' +
                                    ctrl_menu +
                                    '</div>' +
                                    '</span>' +
                                    '</div>' +
                                    branch_str +
                                    '</li>'
                                );
                            }
                            set_curr_id = curr_id;
                        }
                        else{//if ctrl holder shows
                            $(ctrl_panel_str+' #'+prefix+parent_id+' .branch:eq(0)').append(
                                '<li id="'+prefix+id+'">' +
                                toogle_str +
                                '<div class="node_holder">' +
                                '<a class="node_link" title="'+name+'" onclick="select_node(\''+id+'\');">'+name+'</a>' +
                                '</div>' +
                                branch_str +
                                '</li>'
                            );
                            set_curr_id = ctrl_curr_id;
                        }
                    });

                    if(index+1<node_arr.length){
                        fetch_children(index+1);
                    }
                    else{
                        set_public_attr();
                        node_arr.length = 0;
                    }

                    if(ctrl_panel_str.indexOf('#left_col_holder')>=0){
                        if(	id_prefix=='n_' && post_last==2 ||
                            id_prefix!='n_' && post_last==true
                        ){
                            edit_node(post_id);
                            post_last = (id_prefix=='n_')?0:false;
                        }
                    }
                    set_curr(set_curr_id);
                }
            });
    }
</script>

<!--IMPORT HOLDER-->
<div id="import_holder" class="overlay">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header_holder">
				<span class="title">
					导入对象
				</span>
                <a class="btn_close" onclick="import_close();"></a>
            </td>
        </tr>
        <tr>
            <td class="alert_holder"></td>
        </tr>
        <tr>
            <td id="import_panel">
                <form name="upload_form" id="upload_form" enctype="multipart/form-data">
                    <input type="file" name="file_excel" id="file_excel">
                </form>
            </td>
        </tr>
    </table>
</div>
<script>
    function import_close(){
        $('#import_holder').hide();
    }
</script>

<!--COPY HOLDER-->
<div id="copy_holder" class="overlay">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header_holder">
				<span class="title">
					复制
				</span>
                <a class="btn_close" onclick="ctrl_node('close');"></a>
            </td>
        </tr>
        <tr>
            <td class="alert_holder"></td>
        </tr>
        <tr>
            <td id="copy_panel">
                <div class="scroll_holder">
                    <ul class="node_tree">
                        <?php if($_SESSION['level']>=2){ ?>
                        <li id="n_0" class="home">
                            <div class="node_holder">
                                <a class="toogle expand" onclick="toogle_node('0');"></a>
                                <a class="node_link" onclick="select_node('0');" title="首页">首页</a>
                            </div>
                            <?php } ?>
                            <?php
                            $print_col = "";
                            $level = 1;

                            $get_col_query = "SELECT id,name FROM content_table WHERE parent_id = '0' AND level = '$level' $col_cond_str ORDER BY sort_order";
                            $get_col = mysql_query($get_col_query);

                            if(mysql_num_rows($get_col)>0){
                                $print_col .= "<ul class=\"branch l_$level\">";

                                while($c_row = mysql_fetch_array($get_col)){
                                    $n_id = $c_row['id'];
                                    $n_name = $c_row['name'];

                                    $toogle_str = "";
                                    $sort_str = "";
                                    $child_str = "";
                                    if(has_child($n_id)){
                                        $toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$n_id');\"></a>";
                                        $sort_str = "<a class=\"item\" onclick=\"sort_node('$n_id');\"><i class=\"sort\"></i>排序</a>";
                                        $child_str = "<ul class=\"branch l_".($level+1)."\"></ul>";
                                    }

                                    $print_col .= <<<CONTENT
<li id="n_$n_id">
	$toogle_str
	<div class="node_holder">
		<a class="node_link" title="$n_name" onclick="select_node('$n_id');">$n_name</a>
	</div>
	$child_str
</li>
CONTENT;
                                }

                                $print_col .= "</ul>";
                            }
                            print $print_col;
                            ?>
                            <?php if($_SESSION['level']>=2){ ?>
                        </li>
                    <?php } ?>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td class="ctrl_btn_holder">
                <button class="ctrl_btn sub active" onclick="ctrl_node('save');">
                    保存
                </button>
            </td>
        </tr>
    </table>
</div>

<!--MOVE HOLDER-->
<div id="move_holder" class="overlay">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header_holder">
				<span class="title">
					移动
				</span>
                <a class="btn_close" onclick="ctrl_node('close');"></a>
            </td>
        </tr>
        <tr>
            <td class="alert_holder"></td>
        </tr>
        <tr>
            <td id="move_panel">
                <div class="scroll_holder">
                    <?php
                    $print_col = "";
                    $level = 1;
                    if($page_name=="account"){
                        $sql = "SELECT id,name FROM user_group WHERE parent_id='0'";
                        $get_group = mysql_query($sql);
                        if(mysql_num_rows($get_group)>0){
                            $print_col .= "<ul class=\"node_tree\">";
                            while($row = mysql_fetch_array($get_group)){
                                $id = $row[0];
                                $name = $row[1];

                                $toogle_str = "";
                                $child_str = "";
                                if(has_child($id,'user_group')){
                                    $toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$id');\"></a>";
                                    $child_str = "<ul class=\"branch l_".$level."\"></ul>";
                                }

                                $print_col .= <<<CONTENT
<li id="g_$id">
	$toogle_str
	<div class="node_holder">
		<a class="node_link" title="$name" onclick="select_node('$id');">$name</a>
	</div>
	$child_str
</li>
CONTENT;
                            }
                            $print_col .= "</ul>";
                        }
                    }
                    else{
                        $sql = "SELECT id,name 
			FROM content_table 
			WHERE parent_id='0' AND 
				level='$level' 
				$col_cond_str 
			ORDER BY sort_order";
                        $get_col = mysql_query($sql);
                        if(mysql_num_rows($get_col)>0){
                            $print_col .= "<ul class=\"node_tree\">";

                            while($c_row = mysql_fetch_array($get_col)){
                                $n_id = $c_row['id'];
                                $n_name = $c_row['name'];

                                $toogle_str = "";
                                $child_str = "";
                                if(has_child($n_id)){
                                    $toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$n_id');\"></a>";
                                    $child_str = "<ul class=\"branch l_".$level."\"></ul>";
                                }

                                $print_col .= <<<CONTENT
<li id="n_$n_id">
	$toogle_str
	<div class="node_holder">
		<a class="node_link" title="$n_name" onclick="select_node('$n_id');">$n_name</a>
	</div>
	$child_str
</li>
CONTENT;
                            }

                            $print_col .= "</ul>";
                        }
                    }
                    print $print_col;
                    ?>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td class="ctrl_btn_holder">
                <button class="ctrl_btn sub active" onclick="ctrl_node('save');">
                    保存
                </button>
            </td>
        </tr>
    </table>
</div>
<script>
    function copy_node(id){
        ctrl_action = 'copy';
        ctrl_str = '复制';
        ctrl_node(id);
    }

    function move_node(id){
        ctrl_action = 'move';
        ctrl_str = '移动';
        ctrl_node(id);
    }

    var ctrl_panel_html = '';
    function ctrl_node(e){
        if(e=='close'){
            ctrl_curr_id = '';
            ctrl_panel_str = '#left_col_holder';
            $('#'+ctrl_action+'_holder').hide();
            $('#'+ctrl_action+'_panel').html(ctrl_panel_html);
        }
        else if(e=='save'){
            if(ctrl_curr_id==''){
                alert('没有父页面被选择！');
                return false;
            }

            var object = 'content';
            switch(id_prefix){
                case 'm_':object = 'media';break;
                case 'g_':object = 'group';break;
                case 'u_':object = 'user';break;
            }
            var params = {
                action:	ctrl_action,
                object: object,
                id:	ctrl_id,
                parent_id:ctrl_curr_id
            };

            $('#'+ctrl_action+'_holder .alert_holder').html('<div class="loading"><i></i>正在'+ctrl_str+'页面，请稍候 ...</div>');

            $.ajax({
                data: 	params,
                type:	'post',
                url: 	post_url,
                dataType: 'json',
                success: function(result){
                    if(result.success==1){
                        document.cookie = 'node_id='+result.id;
                        window.location.reload();
                    }
                    else{
                        $('#'+ctrl_action+'_holder .alert_holder').html(result.error);
                    }
                }
            });
        }
        else{
            if(e.indexOf('_')>0){
                var id_arr = e.split('_');
                id_prefix = id_arr[0]+'_';
                ctrl_id = id_arr[1];
            }
            else{
                ctrl_id = e;
            }

            ctrl_panel_str = '#'+ctrl_action+'_panel';
            ctrl_panel_html = $('#'+ctrl_action+'_panel').html();

            if(page_name=='account')toogle_node('1');

            $('#'+ctrl_action+'_holder .alert_holder').html('请选择元素['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']所'+ctrl_str+'到的父元素。');
            $('#'+ctrl_action+'_holder').show();
        }
    }
</script>

<!--SORT HOLDER-->
<div id="sort_holder" class="overlay">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header_holder">
				<span class="title">
					排序
				</span>
                <a class="btn_close" onclick="sort_node('close');"></a>
            </td>
        </tr>
        <tr>
            <td class="alert_holder"></td>
        </tr>
        <tr>
            <td id="sort_panel"><div class="scroll_holder"></div></td>
        </tr>
        <tr>
            <td class="ctrl_btn_holder">
                <button class="ctrl_btn sub active" onclick="sort_node('save');">
                    保存
                </button>
            </td>
        </tr>
    </table>
</div>
<script>
    function sort_node(e,o){
        if($('#sort_holder').is(':visible')){
            if(e=='close'){
                $('#sort_panel div.scroll_holder').html('');
                $('#sort_holder').hide();
            }
            if(e=='save'){//save order
                $('#sort_holder .alert_holder').html('<div class="loading"><i></i>正在保存页面排序，请稍候 ...</div>');

                var params = $('#sort_panel div.scroll_holder').sortable('serialize');
                params = 'action=sort&' + params;
                $.ajax({
                    data: 	params,
                    type:	'post',
                    url: 	post_url,
                    dataType: 'json',
                    success: function(result){
                        if(result.success==1){
                            document.cookie = 'node_id='+ctrl_id;
                            window.location.reload();
                        }
                        else{
                            $('#sort_holder .alert_holder').html(result.error);
                        }
                    }
                });
            }
        }
        else{
            ctrl_id = e;
            var tb_name = '';
            switch(page_name){
                case 'media': tb_name = 'media_table'; break;
                case 'account': tb_name = 'user_group'; break;
                default: tb_name = 'content_table';
            }

            var obj_param = '';
            if(o!=undefined)obj_param = '&obj='+o;

            //fetch node
            $.ajax({
                type:		'GET',
                url: 		'fetch_children.php?tb='+tb_name+'&parent_id='+ctrl_id+obj_param,
                dataType:	'xml',
                success: function (xml) {
                    var node_str = '';
                    $(xml).find('node').each(function(){
                        var id = $(this).find('id').text();
                        var name = $(this).find('name').text();

                        node_str += '<a id="sort_'+id+'">'+name+'</a>';
                    });

                    $('#sort_panel div.scroll_holder').html(node_str).sortable({
                        containment: 'parent',
                        axis: 'y',
                        placeholder: 'node-placeholder'
                    }).disableSelection();

                    $('#sort_holder').show();
                }
            });
        }
    }
</script>

<!--PUBLISH HOLDER-->
<div id="publish_holder" class="overlay">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header_holder">
				<span class="title">
					发布
				</span>
                <a class="btn_close" onclick="publish_node('close');"></a>
            </td>
        </tr>
        <tr>
            <td class="alert_holder"></td>
        </tr>
        <tr>
            <td id="publish_panel">
                <input type="checkbox" name="publish_child" id="publish_child">
                <label for="publish_child">发布子页面</label>
            </td>
        </tr>
        <tr>
            <td class="ctrl_btn_holder">
                <button class="ctrl_btn sub active" onclick="publish_node('save');">
                    发布
                </button>
            </td>
        </tr>
    </table>
</div>
<script>
    function publish_node(e){
        if($('#publish_holder').is(':visible')){
            if(e=='close'){
                $('#publish_child').prop('checked',false);
                $('#publish_holder').hide();
            }
            if(e=='save'){
                //save order
                var params = 'action=publish&n_id='+ctrl_id+'&publish_child=' + $('#publish_child').prop('checked');

                var publish_child_str = '';
                if($('#publish_child').prop('checked')==true){
                    publish_child_str = '及其所有子页面';
                }

                $('#publish_holder .alert_holder').html('<span class="loading"><i></i>正在发布页面'+publish_child_str+'，请稍候 ...</span>');

                $.ajax({
                    data: 	params,
                    type:	'post',
                    url: 	post_url,
                    dataType: 'json',
                    success: function(result){
                        if(result.success==1){
                            document.cookie = 'node_id='+ctrl_id;
                            window.location.reload();
                        }
                        else{
                            $('#publish_holder .alert_holder').html(result.error);
                        }
                    }
                });
            }
        }
        else{
            ctrl_id = e;

            $('#publish_child').change(function(){
                if($(this).prop('checked')==true){
                    $('#publish_holder .alert_holder').html('发布页面['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']及其所有子页面');
                }
                else{
                    $('#publish_holder .alert_holder').html('发布页面['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']');
                }
            });

            $('#publish_holder .alert_holder').html('发布页面['+$('#'+id_prefix+ctrl_id+' .node_link:eq(0)').text()+']');
            $('#publish_holder').show();
        }
    }
</script>

<script>
    function view_form(e,tb,dt_id,rec_id){
        if(e=='close'){
            $('#view_form_panel .scroll_holder').html('');
            $('#view_form_holder').hide();
        }
        else{
            $('#view_form_panel .scroll_holder').html('<div class="loading cover"></div>').load(tb+'.php?dt_id='+dt_id+'&rec_id='+rec_id);
            $('#view_form_holder').show();
        }
    }
</script>

<!--CROP HOLDER-->
<div id="crop_holder" class="overlay">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header_holder">
                <span class="title">裁切图片</span>
                <a class="btn_close" onclick="crop_img('close');"></a>
            </td>
        </tr>
        <tr>
            <td class="alert_holder"></td>
        </tr>
        <tr>
            <td id="crop_panel">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="title" width="50%">裁切图片</td>
                        <td class="title">预览图片</td>
                    </tr>
                    <tr valign="top" id="crop_ctrl_area">
                        <td id="crop_area"></td>
                        <td>
                            <div id="preview_area"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="ctrl_area">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr valign="top">
                                    <td width="80px">按比例:</td>
                                    <td width="100px">
                                        <label for="by_ratio_yes">
                                            <input type="radio" name="by_ratio" id="by_ratio_yes" value="1">
                                            是
                                        </label>
                                        <div class="clear" style="height:5px;"></div>
                                        <label for="by_ratio_no">
                                            <input type="radio" name="by_ratio" id="by_ratio_no" value="0">
                                            否
                                        </label>
                                    </td>
                                    <td width="200px">
                                        <label style="width:50px;" for="width">宽度: </label>
                                        <input type="text" name="width" id="width" class="crop_input">
                                        <span>px</span>
                                        <div class="clear" style="height:5px;"></div>
                                        <label style="width:50px;" for="height">高度: </label>
                                        <input type="text" name="height" id="height" class="crop_input">
                                        <span>px</span>
                                    </td>
                                    <td>
                                        <button class="ctrl_btn active crop" onclick="crop_img('save');">
                                            裁切图片
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<script>
    var crop_path,
        img_x = 0,
        img_y = 0,
        img_w = 0,
        img_h = 0,
        c_min_w = 16,
        c_min_h = 16,
        img_max_w = 300,
        img_max_h = 300,
        jcrop_api,
        box_w = 0,
        box_h = 0,
        a_ratio = 0
    p_ratio = 1,
        tmp_w = '',
        tmp_h = '';
    function crop_img(e){
        if(e!='save' && e!='close')crop_path = e;

        $('.ke-dialog').remove();
        $('.ke-dialog-mask').remove();
        if(e=='close'){
            $('#crop_area').html('');
            $('#preview_area').html('');
            $('.crop_input[name=width]').val('');
            $('.crop_input[name=height]').val('');
            $('#crop_ctrl_area').hide();
            $('#crop_holder').hide();
        }
        else if(e=='save'){
            //save crop
            var crop_x = Math.round(img_x / p_ratio);
            var crop_y = Math.round(img_y / p_ratio);
            var crop_w = $('.crop_input[name=width]').val();
            var crop_h = $('.crop_input[name=height]').val();

            var params = {
                action:		'crop',
                img_path:	crop_path,
                img_x:		crop_x,
                img_y:		crop_y,
                img_w:		crop_w,
                img_h:		crop_h
            };

            $('#crop_holder .alert_holder').html('<div class="loading"><i></i>正在裁切图片，请稍候 ...</div>');

            $.ajax({
                data: 	params,
                type:	'post',
                url: 	'img_crop.php',
                dataType: 'json',
                success: function(result){
                    if(result.success==1){
                        d = new Date();
                        $('#img_ctrl_holder img').each(function(){
                            var tmp_src = $(this).attr('src');
                            $(this).attr('src',tmp_src+'?'+d.getTime());
                        });

                        if(page_name=='home')$('#user_img').attr('src',$('#user_img').attr('src')+'?'+d.getTime());

                        show_alert('图片已成功裁切！');
                    }
                    else{
                        $('#crop_holder .alert_holder').html(result.error);
                    }
                }
            });
        }
        else{
            $('#crop_holder .alert_holder').html('<div class="loading"><i></i>正在载入图片，请稍候 ...</div>');
            $('#crop_holder').show();

            var params = 'action=load&img_path='+crop_path;
            $.ajax({
                data: 	params,
                type:	'post',
                url: 	'img_crop.php',
                dataType: 'json',
                success: function(result){
                    if(result.success==1){
                        img_w = parseInt(result.width);
                        img_h = parseInt(result.height);
                        a_ratio = 0;
                        if(img_w>img_max_w){
                            box_w = img_max_w;
                            box_h = box_w * img_h / img_w;
                            p_ratio = img_max_w / img_w;
                        }
                        else{
                            box_h = img_max_h;
                            box_w = box_h * img_w / img_h;
                            p_ratio = img_max_h / img_h;
                        }

                        $('#crop_area').append('<img id="crop" width="'+box_w+'" height="'+box_h+'" src="'+crop_path+'?'+new Date().getTime()+'">');
                        $('#preview_area').append('<img id="preview" width="'+box_w+'" height="'+box_h+'" src="'+crop_path+'?'+new Date().getTime()+'">');

                        $('#preview_area img').load(function(){
                            $('#crop_holder .alert_holder .loading').remove();
                            $('#crop_ctrl_area').show();

                            $('#crop_area img').Jcrop({
                                onSelect: updatePreview,
                                onChange: updatePreview,
                                aspectRatio: a_ratio,
                                minSize: [c_min_w,c_min_h]
                            },function(){
                                jcrop_api = this;

                                $('#crop_holder .alert_holder').html('注意：图片一经裁切，不能返回操作。');
                                $('#by_ratio_no').prop('checked', true);

                                $('input[name=by_ratio]').change(function(){
                                    setRatio($(this).val());
                                });

                                $('.crop_input[name=width]').focus(function(){
                                    tmp_w = $(this).val();
                                }).blur(function(){
                                    if($(this).val()!=tmp_w)
                                        setBoxSize('w',parseInt($(this).val()));
                                });

                                $('.crop_input[name=height]').focus(function(){
                                    tmp_h = $(this).val();
                                }).blur(function(){
                                    if($(this).val()!=tmp_h)
                                        setBoxSize('h',parseInt($(this).val()));
                                });

                                jcrop_api.setSelect([0,0,box_w,box_h]);
                            });
                        });
                    }
                    else{
                        $('#crop_holder .alert_holder').html(result.error);
                    }
                }
            });
        }
    }

    function setBoxSize(d,e){
        if($('input[name=by_ratio]:checked').val()==1){
            if(d=='w'){
                box_w = e;
                box_h = box_w / a_ratio;
            }
            else if(d=='h'){
                box_h = e;
                box_w = box_h * a_ratio;
            }
        }
        else{
            box_w = parseInt($('.crop_input[name=width]').val());
            box_h = parseInt($('.crop_input[name=height]').val());
        }

        jcrop_api.setSelect([img_x,img_y,img_x + (box_w * p_ratio),img_y + (box_h * p_ratio)]);
        $('.crop_input[name=width]').val(box_w);
        $('.crop_input[name=height]').val(box_h);
    }

    function setRatio(e){
        if(e==1){
            a_ratio = img_w / img_h;
        }
        else{
            a_ratio = 0;
        }
        jcrop_api.setOptions({
            aspectRatio: a_ratio
        });
    }

    function showCoords(c){
        $('.crop_input[name=width]').val(Math.floor(c.w / p_ratio));
        $('.crop_input[name=height]').val(Math.floor(c.h / p_ratio));
    };

    function updatePreview(c){
        if (parseInt(c.w)>0){
            img_x = c.x;
            img_y = c.y;

            $('#preview_area').css({
                width: c.w + 'px',
                height: c.h + 'px'
            });

            $('#preview').css({
                width: img_w * p_ratio + 'px',
                height: img_h * p_ratio + 'px',
                marginLeft: '-' + c.x + 'px',
                marginTop: '-' + c.y + 'px'
            });

            showCoords(c);
        }
    }
</script>
<?php
session_start();
$_SESSION['site_id'] = 0;
$_SESSION['wx_token'] = "";
$_SESSION['wx_info'] = "";

/* GENERALS */
define('SITE_NAME','中科索顿智库管理平台');
define('SESSION_TIMEOUT',60*60*4);//4 hours
define('SALT','ZK-GD');

define('_ROOT_URL_','../');
define('_BASE_URL_','http://www.park.com/');
define('_WF_URL_','http://wf.chinatech.org.cn/');
define('_PORTAL_URL_','http://wf.chinatech.org.cn/portal/');
define('_ADMIN_PATH_',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
define('_ROOT_PATH_',str_replace('/model','',_ADMIN_PATH_));
define('_URL_PATH_','/');
define('_LOG_FOLDER_','log/');
define('_UPLOAD_FOLDER_','files/');

define('_SAVE_PATH_',_ROOT_PATH_._UPLOAD_FOLDER_);
define('_SAVE_URL_',_URL_PATH_._UPLOAD_FOLDER_);
define('_MEDIA_FOLDER_','media/');
define('_MEDIA_PATH_',_ROOT_PATH_._UPLOAD_FOLDER_._MEDIA_FOLDER_);
define('_MEDIA_URL_',_ROOT_URL_._UPLOAD_FOLDER_._MEDIA_FOLDER_);

define('_THUMB_FOLDER_','thumb/');
define('_BK_DB_FOLDER_','db/');

$GLOBALS['zhiku_appid'] = "wwc1c7f33e11d5d711";//企业号
$GLOBALS['zhiku_appsecret'] = "tRSgcKM_NN5Mlep8S9IRIOURkZNlGyeVbbsP9YUBmR0";
$GLOBALS['zhiku_agentid'] = "1000003";

$GLOBALS['qy_appid'] = "wwc1c7f33e11d5d711";//企业号
$GLOBALS['qy_appsecret'] = "KEe5xyVnqi0cRH3vSRv-VcqCSZmn33Vg7dSUYJnFCiM";//企业号
$GLOBALS['agentid'] = "1000004";
$GLOBALS['redirect_uri'] = _BASE_URL_."backoffice/manage_login.php";

$GLOBALS['fw_appid'] = "wx45f2afae962a64b6";//服务号
$GLOBALS['fw_appsecret'] = "b5a613f466d20fe782fa3b8cdeec99f9";//服务号

/* MANAGE IMAGE */
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);

$imagecreatefrom = array(
	'image/gif'	=> 'imagecreatefromgif',
	'image/jpeg'=> 'imagecreatefromjpeg',
	'image/png'	=> 'imagecreatefrompng',
	'image/bmp'	=> 'imagecreatefromwbmp'
);
$imageto = array(
	'image/gif'	=> 'imagegif',
	'image/jpeg'=> 'imagejpeg',
	'image/png'	=> 'imagepng',
	'image/bmp'	=> 'imagewbmp'
);

define('MAX_UPLOAD_SIZE',20*1024*1024);//max file size 10M
define('FULL_MAX_WIDTH',980);
define('THUMB_MAX_WIDTH',320);
define('THUMB_MAX_HEIGHT',120);
define('IMAGE_QUALITY',100);
define('THUMB_QUALITY',100);

$GLOBALS['user_level'] = array(
	1 => '用户',
	2 => '管理员',
	3 => '超级管理员'
);

$GLOBALS['auth_ctrl_opt'] = array(
	0 => '隐藏',
	1 => '仅可见',
	2 => '可编辑',
);

$GLOBALS['media_type'] = array(
	"folder"	=>	"文件夹",
);

$GLOBALS['user_fields'] = array(
	'id',
	'password',//密码
	'level',//身份
	'auth_ctrl',//权限
);
$GLOBALS['user_auth'] = array(
	'auth_content',//内容
	'auth_cbase',//认定库
	'auth_nbase',//通知库
	'auth_media',//附件
);
$GLOBALS['user_fields_wx'] = array(
	'userid',
	'name',
	'mobile',
	'department',
	'is_leader',
	'position',
	'gender',
	'email',
	'avatar',
	'status',//激活状态
);
$GLOBALS['user_fields_ext'] = array(
	'last_login',
	'last_logout',
);

$GLOBALS['user_group_fields'] = array(
	'id',
	'parent_id',
	'name',
	'sort_order',
	'route',
);

$node_type_arr = array(	
	"article"		=>	"文章",
	"article_list"	=>	"文章列表",
	"base_center"	=>	"智库中心",
	"news_list"		=>	"新闻列表",	
	"contact_list"	=>	"通讯录",
	"favorite"		=>	"收藏",
	"message"		=>	"消息",
	"search"		=>	"搜索",
);

$article_type_arr = array(
	"article"		=>	"文章",
	"article_list"	=>	"文章列表",
);

$base_type_arr = array(
	"base"			=>	"智库",
	"base_object"	=>	"智库对象",
);

$news_type_arr = array(
	"news"		=>	"新闻",
	"news_list"	=>	"新闻列表",
);

$type_control = array(
	'article' => array(
	    'image',
        'summary',
        'content',
        'document',
        'redirect',
        'meta_desc',
        'meta_kwrd'
    ),
	'article_list' => array(
	    'summary',
        'redirect',
        'meta_desc',
        'meta_kwrd'
    ),
	'base' => array(
	    'image',
        'meta_desc',
        'meta_kwrd'
    ),
	'base_center' => array(
	    'redirect',
        'meta_desc',
        'meta_kwrd'
    ),
	'news' => array(
	    'image',
        'summary',
        'content',
        'meta_desc',
        'meta_kwrd'
    ),
	'guide' => array(
	    'content',
        'meta_desc',
        'meta_kwrd'
    ),
);

/* CONTENT */
$content_basic = array(
	'id',
	'parent_id',
	'sort_order',
	'level'
);
$content_fields = array(
	'name',
	'title',
	'alias',
	'route',
	'type',
	'image',
	'summary',
	'content',
	'show_navi',
	'redirect',
	'meta_desc',
	'meta_kwrd'
);
$content_info = array(
	'created_by',
	'created_time',
	'updated_by',
	'updated_time'
);
$pub_content_fields = array(
	'id',
	'parent_id',
	'sort_order',
	'level',
	'name',
	'title',
	'alias',
	'route',
	'type',
	'image',
	'summary',
	'content',
	'show_navi',
	'redirect',
	'meta_desc',
	'meta_kwrd',
	'created_time'
);

/* DEFAULT */
$default_fields = array(
	'name',
	'title',
	'domain',
	'layout_header',
	'layout_body',
	'layout_footer',
);
$default_layout = array(
	'one_col'=>'无分栏',
	'two_col_50_50'=>'两栏50/50',
	'two_col_30_70'=>'两栏30/70',
	'two_col_70_30'=>'两栏70/30',
	'one_two_col_30_70'=>'一行两栏30/70',
	'one_two_col_70_30'=>'一行两栏70/30',
	'two_two_col'=>'两行两栏',
	'three_col'=>'三栏'
);
$pub_banner_fields = array(
	'id',
	'c_id',
	'type',
	'sort_order',
	'image',
	'img_title',
	'img_desc',
	'img_link'
);

$GLOBALS['company_fields'] = array(
    'id',
    'name',
    'short_name',
    'nature',
    'trade',
    'province',
    'city',
    'district',
    'address',
    'scale',
    'registered_fund',
    'contact',
    'telephone',
    'fixed_phones',
    'email',
    'remark',
);

$GLOBALS['company_list_fields'] = array(
    'name'=>'公司名称#200',
    'province'=>'省#40',
    'city'=>'市#40',
    'district'=>'区#60',
    'nature' => '公司性质#80',
    'trade' => '所属行业#80',
    'scale' => '公司规模#80',
    'remark'=>'企业详细#300',
);

$GLOBALS['company_filter'] = array(
    'province',
    'city',
    'district',
);

/* MEDIA */
$GLOBALS['media_fields'] = array(
	'parent_id',
	'sort_order',
	'level',
	'name',
	'alias',
	'route',
	'type',
	'file_url',
);
$media_info = array(
	'created_by',
	'created_time',
);
$file_fields = array(
	'name'=>'文件名',
	'ext'=>'类型#50',
	'size'=>'大小#60',
	'created_by'=>'创建者#60',
	'created_time'=>'创建时间#100',
	'uploaded_by'=>'上传者#60',
	'uploaded_time'=>'上传时间#100',
);

/* NOTIFICATION BASE */
$GLOBALS['nbase_fields'] = array(
	'id',
	'region',
	'bureau',
	'release_date',
	'name',
	'apply_deadline',
	'policy_type',
	'is_top',
	'is_hot',
	'is_recommend',
	'content',
	//'remark',
);
$GLOBALS['nbase_list_fields'] = array(
	'region'=>'区域#120',
	'bureau'=>'部委#150',
	'release_date'=>'发文时间#70',
	'name'=>'通知名称',
	'apply_deadline'=>'截止时间#70',
	'policy_type'=>'政策类别#60',
	'file_count' => '附件#40',
	'created_by' => '创建者#50',
	'published_by' => '发布者#50',
);
$GLOBALS['nbase_filter'] = array(
	'created_by',
	'region',
	'bureau',
	'policy_type',
);

$GLOBALS['policy_type_opt'] = array(
	'认定奖补',
	'入库备案',
	'税收优惠',
	'专项资金',
	'资质认定',
	'立项公示',
);

/* CERTIFIED BASE */
$GLOBALS['cbase_fields'] = array(
	'year',
	'type',
	'batch',
	'company',
	'city',
	'district',
	'bonus',
	'remark',
);
$GLOBALS['cbase_list_fields'] = array(
	'year'=>'年份#50',
	'type'=>'认定类型#150',
	'batch'=>'批次#60',
	'company'=>'公司名称#200',
	'city'=>'市#60',
	'district'=>'区#60',
	'bonus'=>'拟奖补金额(万元)#120',
	'remark'=>'备注',
);
$GLOBALS['cbase_filter'] = array(
	'type',
	'year',
	'batch',
	'city',
	'district',
);
$GLOBALS['category_nature'] = array(
    1 => '国企',
    2 => '民营',
    3 => '合资',
    4 => '外商独资',
    5 => '股份制企业',
    6 => '上市公司',
    7 => '国家机关',
    8 => '事业单位',
    9 => '其它',
);

$GLOBALS['category_trade'] = array(
    1 => '计算机软件/硬件',
    2 => '计算机系统/维修',
    3 =>'通信(设备/运营/服务)',
    4 =>'互联网/电子商务',
    6 =>'电子/半导体/集成电路',
    7 =>'仪器仪表/工业自动化',
    8 =>'会计/审计',
    9 =>'金融(投资/证券',
    10 =>'金融(银行/保险)',
    11 => '贸易/进出口',
    12 => '批发/零售',
    13 => '消费品(食/饮/烟酒)',
    14 => '服装/纺织/皮革',
    15 => '家具/家电/工艺品/玩具',
    16 => '办公用品及设备',
    17 => '机械/设备/重工',
    18 => '汽车/摩托车/零配件',
    19 => '制药/生物工程',
    20 => '医疗/美容/保健/卫生',
    21 => '医疗设备/器械',
    22 => '广告/市场推广',
    23 => '会展/博览',
    24 => '影视/媒体/艺术/出版',
    25 => '印刷/包装/造纸',
    26 => '房地产开发',
    27 => '建筑与工程',
    28 => '家居/室内设计/装潢',
    29 => '物业管理/商业中心',
    30 => '中介服务/家政服务',
    31 => '专业服务/财会/法律',
    32 => '检测/认证',
    33 => '教育/培训',
    34 => '学术/科研',
    35 => '餐饮/娱乐/休闲',
    36 => '酒店/旅游',
    37 => '交通/运输/物流',
    38 => '航天/航空',
    39 => '能源(石油/化工/矿产)',
    40 => '能源(采掘/冶炼/原材料)',
    41 => '电力/水利/新能源',
    42 => '政府部门/事业单位',
    43 => '非盈利机构/行业协会',
    44 => '农业/渔业/林业/牧业',
    45 => '其他行业'
);

$GLOBALS['category_scale'] = array(
    1 => '20人以下',
    2 => '20-99人',
    3 => '100-499人',
    4 => '500-999人',
    5 => '1000-9999人',
    6 => '10000人以上',
);

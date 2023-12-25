<?php //updated:2020-01-29 20:20:34 globals- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>
<?php
/*
  *  page is for public page, when ==dsh ?  dashboard
  *  mode is the second level of public and dsh/mode
  *  http://domain.com/[page]/[mode]
  *
  * */
$TEMPLATE=$this->db->f("SELECT en FROM globs WHERE name='template_active'")['en'];
//PRESERVED page URIS
$GLOBAL_URI=array("index","app","post","tax","tag","search","user","login","register","dsh","archieve","404","gaia");
$POST_URI=$this->db->fl("uri","post");
$TAX_URI=$this->db->fl("name","tax");
$USER_URI=$this->db->fl("name","user");
//$PAGE_URI=$this->db->fl("uri","page");
foreach(glob(SITE_ROOT."templates/".$TEMPLATE."/*.php") as $file){$pages[]=explode('.',basename($file))[0];};
//SETUP.JSON
//$CONF= config('',true);
$DOMAIN= HTTP_HOST;
//$DOMAINS= $CONF['doms'];
$PAGESURI=SITE_ROOT."templates/".$TEMPLATE."/";
$PAGESPATH=SITE_URL."templates/".$TEMPLATE."/";
//	check if exist in templates> page folder and include template page
$app_folder = SITE_ROOT . "apps/";
$apps=read_folder($app_folder);
//$pages = read_folder($PAGESURI);
//$urimap=@jsonget(SITE_ROOT."urimap.json");
//$uris=@array_keys($urimap);
//xecho($uris);
$ismobile =  ismobile() || $_COOKIE['display']=="mob";

//*********************START GLOBAL GS************************		
$G= array(
//*********************URI PATHS************************
'LIB'=> "https://".SERVERNAME."/gaia/lib/", //lib
'IMG'=> SITE_URL."gaia/img/", //GAIA images
'SITE_ROOT'=>SITE_ROOT,
'SITE_URL'=>SITE_URL,
'GAIAROOT'=>GAIAROOT,
'ROOTSETUP'=>ROOTSETUP,
'GAIABASE'=>GAIABASE,
'REFERER'=>REFERER,
'server'=>$_SERVER,
'HTTP_HOST'=>HTTP_HOST,
'SERVEROOT'=>SERVEROOT,
//'URIMAP'=> $urimap,
//'URIS'=> $uris,

//*********************API*********************************
'API_ROOT' => '/var/www/api/',
'API_TEMPLATESURI' => '/var/www/templates/',
'API_TEMPLATESREPOPATH' => 'https://parapera.gr/repo/templates/',
'API_TEMPLATESPATH' => 'https://parapera.gr/store/templates/',

//*********************BASIC HIERARCHY domain > globs > template > page > uris(2nd level) > widget*************
'DOMAIN'=> $DOMAIN,
//'DOMAINS'=> $DOMAINS,
'SUBDOM_EXIST'=>SUBDOM_EXIST,
'DOM_ARRAY'=>DOM_ARRAY,
'SUBDOM'=>SUBDOM,
//**lang**
'lang'=>isset($_GET['lang']) ? $_GET['lang'] : (!empty($_COOKIE['lang']) ? $_COOKIE['lang']: 'en'),
'langprefix'=>isset($_GET['lang']) && $_GET['lang']!='en' ? $_GET['lang'] : (!empty($_COOKIE['lang']) ? $_COOKIE['lang']: ''),

'is'=> $this->db->fl(array("name",LOC),"globs"),
'globs_tags'=>array_values(array_unique($this->db->fl('tag','globs'))),

'APPSROOT'=> SITE_ROOT."apps/",
'APPSPATH'=> SITE_URL."apps/",
'apps'=>$apps,
'TEMPLATESURI'=> SITE_ROOT."templates/",
'TEMPLATESPATH'=> SITE_URL."templates/",
'template'=>$TEMPLATE,
'templates'=>read_folder(TEMPLATESURI),
'template_rootpath'=>TEMPLATESURI.$TEMPLATE."/",
'PAGESURI'=> $PAGESURI,
'PAGESPATH'=> $PAGESPATH,
'pages'=>$pages,
'page' => $getpage,
'globs_types'=>array(0=>'text',1=>'img',2=>'html',3=>'boolean',4=>'integer',5=>'decimal2',6=>'textarea',7=>'url',8=>'color',9=>'read'),
//preserver uris array 2nd level SITE_URL/[uri]
'global_uri'=>$GLOBAL_URI,
'static_pages'=>!empty($pages) ? array_values(array_diff($pages,$GLOBAL_URI)):array(),
'post_uri'=>$POST_URI,
'tax_uri'=>$TAX_URI,
'user_uri'=>$USER_URI,
//'page_uri'=>$PAGE_URI,
'app_uri'=>$APP_URI,
'WIDGETLOCALPATH'=> SITE_URL."templates/".$TEMPLATE."/widgets/",
'WIDGETPATH'=> "/gaia/widgets/",
'WIDGETLOCALURI'=> SITE_ROOT."templates/".$TEMPLATE."/widgets/",
'WIDGETURI'=> GAIAROOT."widgets/",
'localwidgets'=>glob(TEMPLATESURI.$TEMPLATE.'/widgets/*.php'),
'widgets'=>glob(GAIAROOT.'widgets/*.php'),
'localwidgets_json'=>glob(TEMPLATESURI.$TEMPLATE.'/widgets/*.json'),
'widgets_json'=>glob(GAIAROOT.'widgets/*.json'),
//*************** SETUP*********************
'CONF'=> $CONF,
'REDISDB'=> $CONF[$DOMAIN]['redisdb'],
//*************** MISC*********************
'mobile'=> $ismobile,
'LOC'=> LOC,
//*********************MEDIA**********************
'UPLOADS'=> SITE_URL."media/",
'UPLOADS_ICON'=> SITE_URL."media/thumbs",
'UPLOADS_ROOTPATH'=> SITE_ROOT."media/",
'UPLOADS_ROOTPATH_ICON'=> SITE_ROOT."media/thumbs/",
//*********************BACKUPS -STORE *********************
'BACKUPURI'=> SERVEROOT.'repo/',
//*********************CRONS*********************
'CRON'=> SITE_ROOT."cron/",
//*********************maria DATA user,post,tax,menus (and grps)*********************
'postgrps'=> $this->db->fl(array("id","name"),"postgrp","WHERE status=1"),
'usergrps'=> $this->db->fl(array("id","name"),"usergrp"),
'taxgrps'=> $this->db->fl(array("name","parenting"),"taxgrp","WHERE status=1"),
'taxgrp'=> $this->db->fl(array("id","name"),"taxgrp","WHERE status=1"),
'posts'=>$this->db->fl(array("id","title"),"post"),
'users'=>$this->db->fl(array("id","name"),"user"),
'menus'=>$this->db->fl(array("id","title"),"menu"),
'supusers'=>$this->db->fl(array("id","name"),"user","WHERE grp > 1"),

//************DYNAMIC FORMS**********************************
'taxname'=>$this->db->fl(array("id","name"),"tax"),
'menutitle'=>$this->db->fl(array("id","title"),"menu"),
//*********************class system*******************************
'mymethods'=>get_class_methods_noparent('My'),
//*********************errors*******************************
'error'=>array(
    1 => 'already exists',
    2 => 'query did not executed'
),
//seo
'xmls'=> array('sitemap','atom','rss'),
//**********************GLOBAL FORMS
'authentication'=>array('1'=>'Account Active','2'=>'Account Suspended. Proceed to Payment Page.','3'=>'Account Registration Invoice Pending. Proceed to Payment Page.','4'=> 'Account Proactivated. Proceed to Registration Confirmation Page.','5'=> 'Account is banned.Contact with Administrator.'),
'authen'=>array('1'=>'Active','2'=>'Suspended','3'=>'Not Activated','4'=> 'Proactivated','5'=> 'Banned'),

'orient'=>array(1=>'horizontal',2=>'vertical'),
'status'=>array(0=>'closed',1=>'inactive',2=>'active'),
'langs'=>array(1=>'en',2=>'gr'),
'privacy'=>array(0=>'hidden',1=>'visible'),
'colorstatus'=>array(0=>'red',1=>'orange',2=>'green'),
'phase'=>array(0=>'logged out',1=>'sleepy',2=>'logged in'),
'icons'=>array(
        "admin"=>"alert",
        "apps"=>"leaf",
        "backup"=>"duplicate",
        "categories"=>"list-alt",
        "console"=>"scale",
        "documentation"=>"question-sign",
        "fileerrors"=>"alert",
        "global"=>"record",
        "home"=>"dashboard",
        "local"=>"globe",
        "logout"=>"hand-right",
        "manage"=>"edit",
        "media"=>"film",
        "gallery"=>"film",
        "modules"=>"th",
        "menu"=>"list",
        "new"=>"new-window",
        "notifications"=>"hand-right",
        "page"=>"th-large",
        "pagevar"=>"equalizer",
        "permissions"=>"filter",
        "post"=>"file",
        "redis"=>"road",
        "seo"=>"bullhorn",
        "simulate"=>"record",
        "sync"=>"tree-conifer",
        "setup"=>"cog",
        "stats"=>"stats",
        "superboard"=>"list-alt",
        "tags"=>"tags",
        "taxonomy"=>"tags",
        "templates"=>"th-large",
        "widget"=>"th-large",
        "groups"=>"briefcase",
        "user"=>"user"
    ),
	//mongo database setup
	//setup 1:templates 2:modules 3: dsh_pages
	//$pages=$DASHPAGES[$G['my']['status']];
	'apages'=>array(
    //    "local",
	    "setup",		 
		"templates",
		"global",
	    "media",
	    "user",
		"seo", 
        "taxonomy",
		"post",        
      //  "apps",
        "widget",
        "menu",        
    //    "doc",
//	"comments",
//	"ads"=>array('customers','bar','history','pricing','settings'),
              
       // "admin"
    ),

    'ampages'=>array(
        "setup",
        "media",
        "user",
        "post",
        "taxonomy",
        "templates",
        "widget",
        "seo"
    ),

'sucolors'=>array(
'1'=>'rgba(265,118,267,0.3)',
'2'=>'rgba(85,155,195,0.5)',
'3'=>'rgba(165,175,95,0.3)',
'4'=>'rgba(85,45,95,0.3)',
'5'=>'rgba(85,45,95,0.3)',
'6'=>'rgba(85,45,95,0.3)',
'7'=>'rgba(85,45,95,0.3)',
'8'=>'rgba(85,45,95,0.3)'
),
'post_status'=>array(
0=>'Closed',
1=>'Inactive',
2=>'Active'
),
'bool'=>array('y' => 'YES','n'=>'NO'),
'greekMonths' => array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου')
);
$this->G=array_merge($G,$this->G);
?>
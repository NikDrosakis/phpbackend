<?php //updated:2020-01-29 20:20:33 bootstrap- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>

<?php 
//GaiaCMS configuration
ini_set('display_errors', 0);
ini_set('short_open_tag', 1);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
date_default_timezone_set('Europe/Athens');
//session_cache_limiter('private, must-revalidate');
//session_cache_expire(60);
setlocale(LC_ALL, 'el_GR.UTF-8');
$time=time();
//setcookie("PHPSESSID","",$time-3600,"/"); // delete cookie
//if(!ob_start("ob_gzhandler")) ob_start();
@define('AJAXREQUEST',$_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest");
$_SERVER['HTTP_HOST']=implode('.',explode('.',explode('/',$_SERVER['HTTP_REFERER'])[2]));
$_SERVER['db']="gs_".implode('',explode('.',$_SERVER['HTTP_HOST']));
$_SERVER['DOCUMENT_ROOT']="/var/www/".$_SERVER['SERVER_NAME'];
@define('SITE_ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
@define('SERVERNAME',$_SERVER['SERVER_NAME']);
@define('SERVEROOT',dirname(SITE_ROOT).'/');
@define('HTTP_HOST',rtrim(str_replace(SERVEROOT,'',SITE_ROOT),'/'));
@define('REFERER',$_SERVER['HTTPS']=='on' ? 'https://' : 'http://'); //http or https
@define('AIMD5_API','/var/www/api/');
@define('GAIAROOT',__DIR__.'/');
@define('GAIABASE',str_replace(basename(__DIR__), '', __DIR__));
@define('G',GAIAROOT);
@define('SITE_URL',REFERER.HTTP_HOST.'/'); //the full url
@define('SITE',$_SERVER['HTTP_HOST']);
@define('SUBDOM_EXIST', substr_count($_SERVER['HTTP_HOST'], '.')==2 ? 1 : 0);
@define('DOM_EXT', pathinfo($_SERVER['SERVER_NAME'], PATHINFO_EXTENSION));
@define('DOM_ARRAY', explode('.',$_SERVER['HTTP_HOST']));
@define('DOMAINAME',SUBDOM_EXIST ? DOM_ARRAY[1] : DOM_ARRAY[0]);
@define('SITE',DOMAINAME);
@define('EXT',DOM_EXT);
@define('SERVERBASE',DOMAINAME.'.'.DOM_EXT);
@define('SUBDOM',SUBDOM_EXIST ? DOM_ARRAY[0]: '');
@define('LOC','en');
@define('LANG','en');
@define('ROOTSETUP',$_SERVER['REQUEST_URI']=='/gaia/' || $_SERVER['REQUEST_URI']=='/gaia/index.php' ? true :false);

//START THE SESSION
if(!isset($_SESSION))session_start();
include_once GAIAROOT."generic.php";
@define('UPLOADS_ROOTPATH', GAIABASE.SERVERBASE.'/media/');
@define('UPLOADS', SITE_URL.'media/');
@define('UPLOADS_ROOTPATH_ICON', GAIABASE.SERVERBASE."/media/thumbs/");
@define('SERVERALIAS',explode('.',$_SERVER['HTTP_HOST'])[0]);
@define('URL_FILE',basename($_SERVER['PHP_SELF']) );
@define('IMG',SITE_URL."media/");
@define ('TEMPLATESURI',SITE_ROOT."templates/");
@define('SUBDOM_EXIST', substr_count($_SERVER['HTTP_HOST'], '.')==2 ? 1 : 0);
@define('DOM_ARRAY', explode('.',$_SERVER['HTTP_HOST']));
@define('SUBDOM',SUBDOM_EXIST ? DOM_ARRAY[0]: '');
spl_autoload_register(function ($className) {
  if(file_exists(GAIAROOT."class/".$className.".php"))
        include (GAIAROOT."class/".$className.".php");
});

if(AJAXREQUEST || php_sapi_name()=='cli'){  //OR $this->G['CUR_DIR']!='cron'
    $cms=new Gaia($_SERVER['db']);
}



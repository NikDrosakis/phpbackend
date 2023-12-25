<?php //updated:2020-01-29 20:20:33 Gaia- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>
<?php 
/*
GAIACMS ROOT CLASS 
CMS PAGE,DSH PAGE, SETUP PAGE STARTER 
*/
class Gaia extends DB{
	public $template;
	public $redis;
	public $connect;
	public $agent;
	public $tax; //instantiate Taxonomy Class
	public $user; //instantiate User Class
	public $page; //instantiate Page Class
    public $conf;
    public $confd;
    public $db;
	public $G;
	public $my;
	public $globalfile;

	public function __construct($database=DOMAINAME.DOM_EXT,$dbt='maria'){
				$this->my=$_COOKIE;						
				//$globalfile=SITE_ROOT."globals.json";
				//if(file_exists($globalfile)){
					//$this->G = jsonget($globalfile);
				//}elseif($_SERVER['PHP_SELF']!='/gaia/dsh/setupajax.php' && !AJAXREQUEST){	
				
				$this->G=array();				
				$this->G['aconf']=json_decode(file_get_contents("/var/www/".$_SERVER['SERVER_NAME'].'/config.json'),true);
				$this->G['CURRENT']= getcwd();
				$this->G['URL']= php_sapi_name() !='cli' ? SITE_URL.$_SERVER['REQUEST_URI']:'';
				$this->G['URL_FILE']= URL_FILE;
				$this->G['URL_PAGE']= basename(URL_FILE, ".php");
				$this->G['SELF']= php_sapi_name() !='cli' ? (SITE_URL.$_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING']!="" ? '?'.$_SERVER['QUERY_STRING']:'')):'';
				$this->G['SELF_NONURL']= php_sapi_name() !='cli' ? $_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING']!="" ? '?'.$_SERVER['QUERY_STRING']:''):'';
				$this->G['QUERY_STRING']= php_sapi_name() !='cli' ? $_SERVER['QUERY_STRING'] :'';
				$this->G['server']= $_SERVER;
				$this->G['CUR_DIR']= basename(dirname($_SERVER['PHP_SELF']));				

					if(file_exists(GAIABASE.'gaia.json')){
//xecho($_SERVER['SERVER_NAME']);						
					$this->db=new DB($database,$dbt);
					include GAIAROOT."globals.php";		
					}
					
				//echo(json_encode($this->G,JSON_PRETTY_PRINT));
				//	file_put_contents($globalfile,json_encode($this->G,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND | LOCK_EX);
				//}		
				foreach($_GET as $get =>$getval){
					$this->G[$get] =trim($getval);
				}
				$this->G['page']= isset($_GET['page']) ? trim($_GET['page']):'';
				$this->G['mode']= isset($_GET['mode']) ? trim($_GET['mode']):'';
				$this->G['sub']= isset($_GET['sub']) ? trim($_GET['sub']):'';
				$this->G['id']= isset($_GET['id']) ? trim($_GET['id']):'';
				$this->G['uid']= isset($_GET['uid']) ? trim($_GET['uid']):'';
				$getpage= $this->G['page']!='' ?  $this->G['page'] : 'index';
				//URI RULES
				//global pages
				if(in_array($getpage,array('login','register'))) {
					$this->G['paget']= GAIAROOT.$getpage;
					$this->G['pagetype'] ="global";
				//template default pages
				}elseif($getpage=="index"){
					$this->G['paget']= $this->G['PAGESURI'].$getpage;
					$this->G['pagetype'] =$getpage;
                }elseif($this->G['mode']=='post'){
                    $this->G['paget']= $this->G['PAGESURI']."page";
                    if($this->G['page']!="") {
                        $this->G['data'] = $this->db->f("SELECT * FROM post where uri=?", array($this->G['page']));
                        $this->is['logo'] = $this->G['data']['img'];
                    }
				}elseif(in_array($getpage,array("app","tax","tag","search","user"))){
					if($this->G['mode']!=""){
					$this->G['paget']= $this->G['PAGESURI'].$getpage;
					}else{
					$this->G['paget']= $this->G['PAGESURI']."archieve";
					}
					$this->G['pagetype']=$getpage;
				}elseif(in_array($getpage,$this->G['tax_uri'])){
					$this->G['paget']= $this->G['PAGESURI']."archieve";
				//}elseif(in_array($getpage,$this->G['static_pages'])){
//					$this->G['paget']= $this->G['PAGESURI']."$getpage";
					//$this->G['pagetype'] ="static";
				}else{
					$this->G['paget']= $this->G['PAGESURI']."page";
					if($this->G['page']!=""){$this->G['data']= $this->db->f("SELECT * FROM post where uri=?",array($this->G['page']));
					$this->is['logo']=$this->G['data']['img'];
					}
					$this->G['pagetype'] ="404";
				}
				//RUN every GS key as public var of Gaia
				foreach($this->G as $gsname =>$gsval){
					$this->$gsname = $gsval;
				}
    }
	/*
     *
     * SET TEMPLATE VARS AND INCLUDE TEMPLATE FILES public and dashboard
     * htaccess providing awesome uri
	 * $this->G['GET']['page']==dsh or not (public)
	 * provide module autopage
	 apps 
     * */
	public function start_dsh()	{		
	$getpage = $this->page!='' ? $this->page : 'home';
			$folder_page = GAIAROOT . "dsh/";
			$app_folder = SITE_ROOT . "apps/";
        	$pages = read_folder($folder_page);
			include GAIAROOT."dsh/header.php";
			include $this->inside()
                ? ($this->mode!="" ? $folder_page.$this->mode.'.php' : $folder_page.'home.php')
                : GAIAROOT.$getpage.'.php';
			include GAIAROOT."dsh/footer.php";	
	}
	
	public function start_page()	{						
		if(ROOTSETUP && !AJAXREQUEST){		
		include_once GAIAROOT.'dsh/setup.php';
		}else{
        $getpage = $this->page;	
//APPS CASE
		if(!empty($this->apps) && in_array($getpage,$this->apps)){			
			include GAIAROOT . 'gaiaheader.php';						
			$homebase=SITE_ROOT."apps/$getpage/";
			include file_exists($homebase."index.php") ? $homebase."index.php":$homebase."index.html";
			
			include GAIAROOT . 'dsh/footer.php';		
		}else{					
			if(!in_array($this->page,array('login','register'))){
				include GAIAROOT."gaiaheader.php";    		
			}
			//	$this->include_file($this->G['PAGESURI']."header");
				$this->include_file($this->paget);
			//	$this->include_file($this->G['PAGESURI']."footer");
		}
		}
	}

/*
 * without extension
 * */
    public function include_file($file){
        if(file_exists($file.".php")){
            include $file.".php";
        }elseif(file_exists($file.".html")){
        include $file.".html";
        }else{
            return;
        }
    }
   /*
 * crossdomain
 * */
    public function widgets(){
        $widgets=read_folder($this->TEMPLATES.$this->template."/widgets",array('js'));
        sort($widgets);
        return $widgets;
    }



    /*
 * IS LOGGED
 * */
    public function inside(){
        if(!empty($_COOKIE['GSID'])){
            $phase=$this->db->f("SELECT phase FROM user WHERE id=?",array($_COOKIE['GSID']))['phase'];
            return $phase==2 ? true : false;
//             }
        }else{
            return false;
        }
    }

    public function is_logged()
    {
        if (!empty($_COOKIE['sp']) && !empty($_COOKIE['GSID'])) {
                $user = $this->db->f("SELECT phase,sp FROM user WHERE id=?", array($_COOKIE['GSID']));
                return !empty($user) && $user['phase'] != 0 && $user['sp'] == $_COOKIE['sp'] ? true : false;
        } else {
            return false;
        }
    }

    /*
     * LOGIN EVENT DATA
     * */
    public function login($pass,$name){
        $fetch=$this->db->f("SELECT * FROM user WHERE name=? AND pass=?",array($name,$pass));
        //case 1:  pass incorrect or account does not exist
		
        if (empty($fetch)){
            return 'no_account';

            //case 2:  authentication incorrect, account does not exist
        }elseif (!in_array($fetch['auth'],array('1','2','3','4','5'))){
            return 'Authentication Pending';

            //case 3: account active proceed to loggedin
        } elseif($fetch['auth'] !='1') {
            return $fetch;
        } else {
            /*
            * security code set new if phase ==0 use old one if connected from different browser/device
             * */
            if ($fetch['phase'] == 2) {
                $hash = $fetch['sp'] != 0 ? $fetch['sp'] : hash("sha256", $fetch['id'] . time());
            } else {
                $hash = hash("sha256", $fetch['id'] . time());
                $this->db->q("UPDATE user SET sp=? WHERE id=?", array($hash, $fetch['id']));
//                $this->cache()->replace('sp_'.$fetch['id'],$hash);
            }
			$hash = hash("sha256", $fetch['id'] . time());
            $fetch['sp']= $hash;
            $updatePhase=$this->db->q("UPDATE user SET phase=?,last_login=? WHERE id=?",array(2,time(),$fetch['id']));
            //update cache
//            $this->cache()->replace('isactive_'.$fetch['id'],2);
            if($updatePhase){return $fetch;}else{return "mistake";}
        }
    }

    public function logout(){
        $logout=$this->db->q("UPDATE user SET phase=0 WHERE id=?",array($_COOKIE['GSID']));
        if ($logout){
//            $this->cache()->replace('isactive_'.$_SESSION['GSID'],0);
            return true;
        }
    }
    /*
 * used to access dsh
 * */
    public function is_authorised($level = array()){
        if ($this->inside()) {
            if (in_array($_COOKIE['GSGRP'], $level)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function validate($pa){
        if (filter_var($pa, FILTER_VALIDATE_EMAIL)) {
            $mailExist=$this->db->count_("id","user"," WHERE mail='$pa'");
            if ($mailExist !=0){
                return  false;
            }else{
                return true;
            }
            return true;
        } else {
            return false;
        }
    }

    public function name_not_exist($pa){
        $nameExist = $this->db->count_("id", "user", " WHERE name='$pa'");
        if ($nameExist == 0) {return true;} else {return false;}
    }

	public function template_new($template,$onlyinsert=false){
        //folder system
        $folder= $this->TEMPLATES . $template;
        mkdir($folder, 0777, true);

        $sfolders=array('pages','widgets','css','js','images','html');
        foreach($sfolders as $f){
            mkdir("$folder/$f", 0777, true);
        }

        system ("chown -R dros:dros $folder");
        system ("chmod -R 777 $folder");
        file_put_contents("$folder/header.php",'');
        file_put_contents("$folder/footer.php",'');
        $head= '<link rel="stylesheet" href="/templates/'.$template.'/css/style.css" />';
        file_put_contents("$folder/head.php",$head);

        //page system
        $pagefolder= $this->TEMPLATES.$template."/";
        $htmls= glob($this->TEMPLATES.$template."/*.html");
            foreach($htmls as $html){
                $filename= explode('.',$html)[0];
                copy($html,$pagefolder.$filename.'.php');
            }
        system ("chmod -R 777 $folder");
        return true;
    }

    public function template_activate($name){        
		//1 !routine of htmls => page/php is performed at the first step of gaia template integration
		if(!file_exists($this->TEMPLATESURI . $name ."")){
		mkdir($folder, 0777, true);
		//2 copy *.html to pages
		$htmls = array_merge(glob($this->TEMPLATESURI . $name . '/*.html'),glob($this->TEMPLATESURI . $name . '/*.php'));
        if (!empty($htmls)) {
            foreach ($htmls as $html) {
                $htmlname = explode('.', basename($html))[0];
                $phpname = dirname($html) . "/$htmlname.php";
                //copy php to pages
                copy($html, $phpname);chmod($phpname, 0777);
                //move *.html to html
                rename($html, $this->TEMPLATESURI . "$name/html/" . basename($html)); //copy to preview
            }            
        }
		}
		//3 create a list of widgets in template.json 
		$templatejsonfile=$this->TEMPLATESURI . $name."/template.json";
		foreach(glob($this->TEMPLATESURI . $name . '/widgets/*.php') as $widget){
		$allwidgets[]=explode('.',basename($widget))[0];
		}
		if(!file_exists($templatejsonfile)){
			$array=array(
			"name"=>$name,
			"version"=>"1.0",
			"summary"=>"Template details",
			"designer"=>"Nikos Drosakis",
			"developer"=>"Nikos Drosakis",
			"widgets"=>$allwidgets);
			file_put_contents($templatejsonfile,json_encode($array,JSON_PRETTY_PRINT));
		}
		//4 update globs		
		$q=$this->db->q("UPDATE globs SET en=? WHERE name='template_active'",array($name));
		return !$q ? 'NO':'OK';		
    }

    public function icon($title){
        if (array_key_exists($title,$this->icons)) {
            return $this->icons[$title];
        }else{
            return "asterisk";
        }
    }

/*
 * FORM edit && new
 *
 * */
    public function form($table,$cols=array(),$form=false,$res=array()){
        $return = '';
        $schema=jsonget(GAIAROOT."schema.json");
		//$type= $this->db->comments($table);
		$type=$schema[$table];
        $id= $res['id'];
        $img= !$res['img'] ?  "/gaia/img/post.jpg" : UPLOADS.$res['img'];

        //html form and basic hidden for new forms
        if($form){
            $return .= "<form id='form_$table'><input type='hidden' name='a' value='new'>";
            $return .= "<input type='hidden' name='table' value='$table'>";
        }

        foreach($cols as $colid =>$col) {

         $typ= explode('-',$type[$col]);
         $typ1=!empty($typ) ? $typ[0] : $type[$col];
           $typ2=!empty($typ) ? $typ[1] : false;
            if(contains($typ2,'.')){
                $typ2e= explode('.',$typ2);
                $typ2table= $typ2e[0];
                $typ2row= $typ2e[1];
                $res[$col]= $this->db->f("SELECT $typ2row FROM $typ2table WHERE id=?",array($res[$col]))[$typ2row];
//                $col= $typ2table.' '.$typ2row;
            }

                if($typ1=='text') {
                    $return .= "<div class='gs-span'><label for='$col'>$col</label>
                    <input class='gs-input' name='$col' placeholder='$col' id='$col' type='text' value='$res[$col]'></div>";

                }elseif($typ1=='number') {
                        $return .= "<div class='gs-span'><label for='$col'>$col</label>
                    <input class='gs-input' name='$col' placeholder='$col' id='$col' type='number' value='$res[$col]'></div>";

                }elseif($typ1=='img') {
                    $return .=" <script src='/gaia/lib/jquery.ui.widget.js'></script>
                <script src='/gaia/lib/jquery.form.js'></script>
                <script src='/gaia/lib/load-image.all.min.js'></script>
                <script src='/gaia/lib/canvas-to-blob.min.js'></script>
                <script src='/gaia/lib/jquery.fileupload.js'></script>
                <script src='/gaia/lib/jquery.fileupload-process.js'></script>
                <script src='/gaia/lib/jquery.fileupload-image.js'></script>
                <script src='/gaia/lib/jquery.fileupload-audio.js'></script>
                <script src='/gaia/lib/jquery.fileupload-video.js'></script>
                <script src='/gaia/lib/jquery.fileupload-validate.js'></script>
                <link href='/gaia/lib/uploader/css/jquery.fileupload.css' rel=\"stylesheet\" type=\"text/css\" />                
                  <div class=\"imgBox\">
                  <div id=\"files\" class=\"files\" style=\"\"><img src='$img' style=\"height:250px;width: 229px;margin: -21px 0 0 -21px;\"></div>
                  <span class=\"btn btn-success btn-sm fileinput-button\">
                  <i class=\"glyphicon glyphicon-plus\"></i>
                  <span>Add file</span>
                  <input id=\"fileupload\" type=\"file\" name=\"files\">
                  </span>
                  <div id=\"progress\" class=\"progress\" style=\"display:none\">
                  <div class=\"progress-bar progress-bar-success\"></div>				  
                  </div>
				  <button id=\"selectimage\" onclick=\"s.media.select(this)\" class=\"btn btn-info btn-sm\">Select</button>
                  </div>";
                }elseif($typ1=='select') {
                    $return .= "<div class='gs-span'><label for='$col'>$col</label><select class='gs-input' name='$col' id='$col'>";
                        if($type!=false)
                        if(!empty($this->$typ2)){
                        foreach($this->$typ2 as $li =>$liname){
                            $selected= $res[$col]==$li ? "selected=selected":"";
                            $return .= "<option value='$li' $selected>$liname</option>";
                        }}
                    $return .= "</select></div>";

                }elseif($typ1=='textarea') {
                    $return .= "<div class='gs-span'><label for='$col'>$col</label>";
                    if($typ2=='editor') {
                        $return .= "<textarea class='wysiwyg' name='$col'  id='$col' placeholder='$col'>" . ($res[$col]!='' ? html_entity_decode($res[$col]):'') . "</textarea>";
                        $return .= $form==false ? "<button style='display:block' class='btn btn-default' id='submit_$col'>Save</button>" : "";
                    }else {
                        $return .= "<textarea class='form-control' name='$col' id='$col'>$res[$col]</textarea>";
                    }
                        $return .= "</div>";

                }elseif($typ1=='decimal') {
                    $return .= "<div class='gs-span'><label for='$col'>$col</label>
                    <input table='post' id='$col' value='$res[$col]' type='number' step='0.1' min='0.1' max='1' class='form-control input-sm' style='width: 60px;'></div>";

                }elseif($typ1=='checkbox') {
                
				}elseif($typ1=='hidden') {

                }elseif($typ1=='radio') {

                }elseif($typ1=='date') {

                    $return .= "<div class='gs-span'><label for='$col'>$col</label>";
                    if($typ2=='read') {
                        $return .= date('Y-m-d H:i', $res[$col]);
                    }else {
                        $return .= "<input class='gs-input' name='$col' placeholder='$col' id='$col' type='date' value='$res[$col]'>";
                    }
                    $return .= "</div>"; 
                }elseif($typ1=='auto') {
                    $return .= "<div class='gs-span'><label for='$col'>$col</label>";
                    $return .= $res[$col]."</div>";
                }
        }
        //submit button for new forms
        $return .= $form ? "<button style='display:block' class='btn btn-default' id='submit_$table'>Save new $table</button></form>" : "";

        return $return;
    }
}
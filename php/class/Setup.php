<?php //updated:2020-01-29 20:20:33 Setup- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>

<?php 
/*
Install Class
@deprecated rewrite to bash install.sh file
*/
class Setup extends DB{  
  protected $connection;
  public $os;
  public $vhostfile;
  public $hostfile;
  
 /*
 tested installation for 
 1) windows wamp server64 (localserver)
 2) ubuntu server 16  (public server)
 */ 
 public function __construct(){
		$os=$this->is_os();
		if($os=='WIN'){
		//for wampserver64
		$this->vhostfile=	'C:\wamp64\bin\apache\apache2.4.41\conf\extra\httpd-vhosts.conf';	
		$this->hostfile=	'C:\Windows\System32\drivers\etc\hosts';	
		}elseif($os=='Linux'){
		$this->vhostfile="/var/www/apache";
		}		
 }
 /*
 sudo apt-get install php7.0-cli -y
sudo apt-get install libssh2-1 php-ssh2 -y
apt install php-ssh2 && service php7.2-fmp restart
 */ 
 public function copysshfile($fileremote,$filelocal){
	// phpinfo();
	//	$this->connection = @ssh2_connect('62.38.140.132', 22);
	//@ssh2_auth_password($this->connection, 'root', 'n130177!');
	if(extension_loaded("ssh2")){	
		$connection = ssh2_connect('192.168.2.2', 22);
		ssh2_auth_password($connection, 'dros', 'n130177!');
		return ssh2_scp_recv($connection, $fileremote,$filelocal);		
	}else{
		return "nossh";	
	}
 }
 
	public function create_domain_folders($siteroot){
		//domain
		if (!file_exists ($siteroot)){
			if(mkdir($siteroot,0777, true)){
			chmod($siteroot, 0777);
			}else{
				return "problem creating domain folder";
			}
		}
		
		//folders
		$folder_list=array('apps','media','templates');
		foreach($folder_list as $folder){
			if (!file_exists ($siteroot.$folder))
			if(mkdir($siteroot.$folder,0777, true)){
			chmod($siteroot.$folder, 0777);			
			//place empty index files to all folders
			}else{			
				return "problem creating domain subfolder";			
			}
		}

		if (!file_exists ($siteroot.'media/thumbs')){
			if(mkdir($siteroot.'media/thumbs',0777, true)){
			chmod($siteroot.'media/thumbs', 0777);
			return true;
			}else{
				return "problem creating domain folder";
			}
		}
	}
	
	public function is_webserver(){
	if(!empty($_SERVER['SERVER_SOFTWARE'])){
		$server['name'] = strtolower(trim(explode('/',$_SERVER['SERVER_SOFTWARE'])[0]));
		$server['version']= trim(explode(' ',explode('/',$_SERVER['SERVER_SOFTWARE'])[1])[0]);
		
	}else{
		$server['name'] = shell_exec('nginx -v 2>&1');
		$server['name'] = shell_exec('apache2 -v 2>&1');	
	}	
	return $server;
	}
	
	public function is_os(){
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return 'WIN';
		} else {
		return PHP_OS;
		}
	}
	/*
	public function setup_zip($name){
	//copy	
	$remoteFolder='/var/www/gaia/public_html/code/modules/';	
	$localFolder='/var/www/gaia/public_html/code/modules/';	
	ssh2_scp_send($this->connection, $remoteFolder."$name/$name.zip", SITE_ROOT, 0777);
	//unzip file		
	//unzip
	if (unzip(SITE_ROOT.$name.'.zip',SITE_ROOT.$name.'.zip')){
	//delete	
	if (unlink(SITE_ROOT.$name.'.zip')){
	return true;
	}
	}		
	}

	SETUP zip
	*/
	public function recurse_scp($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                $this->recurse_scp($this->connection, $src . '/' . $file,$dst . '/' . $file,0777); 
            } 
            else { 
                ssh2_scp_send($this->connection, $src.'/'.$file, $dst.'/'.$file, 0777); 
				chmod($dst . '/' . $file, 0777);				
            } 
        } 
    } 
    closedir($dir); 
	}

public function check_db_classes(){	
	$modules_list=array('mysql','PDO','pdo_mysql','gd','redis','sqlite3','ssh2','memcached');
	$problem=array();
		foreach($modules_list as $module){
		if(!extension_loaded($module)){
			$problem[]=$module;
		}
		}
	return $problem;
}

/*
public function create_mongo($mongodb){
	if (!class_exists("Mongo") && !class_exists("MongoClient")) {
	echo ("php_mongo module not installed.");	
	return;
	}else{
	$m = new MongoClient();
	$m->selectDb($mongodb)->execute("function(){}");
	echo 'GaiaCMS <b>Mongo setup database</b> installed correctly.<br/>';			
	return true;
	}	
}
*/
/*
public function create_mysql($root,$root_password,$user,$pass,$db){
    try {
        $dbh = new PDO("mysql:host=localhost", $root, $root_password);

        $dbh->exec("CREATE DATABASE $db;
                CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
                GRANT ALL ON $db.* TO '$user'@'localhost';
                FLUSH PRIVILEGES;") 
        or die(print_r($dbh->errorInfo(), true));
		

    } catch (PDOException $e) {
        echo ("DB ERROR: ". $e->getMessage());
    }
}	

public function insert_mysql_tables($username,$password,$db){	
	try {
		 $db = new PDO("mysql:dbname=$db;host=localhost", $username, $password);
		 $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
		 
		 include 'mysql.php';
		 
		 foreach ($query as $qname => $q){
		 $db->exec($q);	 
		 print("mysql <b>$qname</b> executed.<br/>");
		 }		 
	return true;
	} catch(PDOException $e) {
		echo $e->getMessage();//Remove or change message in production code
	}
}
*/
//ram in kb
public function mem(){
	if(file_exists('/proc/meminfo')){
	 $fh = fopen('/proc/meminfo','r');
	  $mem = 0;
	  while ($line = fgets($fh)) {
		$pieces = array();
		if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
		  $mem = $pieces[1];
		  break;
		}
	  }
	  fclose($fh);
		return $mem;  
	}
}
/*
creates local domain in wamp server64
!update for ubuntu server lamp
check if wamp
returns errors
*/
public function setup_domain($jsonsetup,$url){
		//$json=htmlspecialchars_decode($jsonsetup);	
		$json=json_decode($jsonsetup,true);	
		$domain=$_POST['domain'];
		$dbhost=$json[$domain]['dbhost'];
		$dbuser=$json[$domain]['dbuser'];
		$dbname=$json[$domain]['dbname'];
		$dbpass=$json[$domain]['dbpass'];	
		$email=$json[$domain]['email'];	
	//1	create domain  folder	
		$gaiabase=$_POST['folder'];
		$gaiaroot=$_POST['folder'].'gaia/';
		@define('SITEROOT',$gaiabase.$domain.'/');
		$folder_install=$this->create_domain_folders(SITEROOT);

	//2	move myblog to template
	 @rename($gaiaroot.'myblog', SITEROOT.'templates/myblog');
	 
	//3	create domain index.php && .htaccess (if apache)
		$indexfile="<?php define('GAIAROOT',dirname(dirname(__FILE__)).'/gaia/'); include GAIAROOT.'bootstrap.php'; ?>";
		@file_put_contents(SITEROOT."index.php",$indexfile);		
		$htaccess="RewriteEngine On\n";
		$htaccess.="RewriteBase /\n";
		$htaccess.="DirectoryIndex index.php index.html\n";
		$htaccess.="RewriteRule ^([A-Za-z0-9_-]+)/?$ index.php?page=$1&dsh=$2 [QSA] \n";
		$htaccess.="RewriteRule ^([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/?$	index.php?page=$1&mode=$2 [QSA] \n";
		@file_put_contents(SITEROOT.".htaccess",$htaccess);
	//4	create virtual host
			$vhost="\n<VirtualHost *:80>\n";
			$vhost.="ServerName $domain\n";
			$vhost.="DocumentRoot '{$gaiabase}{$domain}'\n";
			$vhost.="Alias '/gaia' '{$gaiaroot}'\n";
			$vhost.="<Directory  '{$gaiabase}$domain/'>\n";
			$vhost.="Options +Indexes +Includes +FollowSymLinks +MultiViews\n";
			$vhost.="AllowOverride All\n";
			$vhost.="Require local\n";
			$vhost.="</Directory>\n";
			$vhost.="</VirtualHost>";
	if(!file_put_contents($this->vhostfile, $vhost.PHP_EOL , FILE_APPEND | LOCK_EX)){
		$error[]="Problem creating vhost";
	}
	//5	update hosts file if windows	
			$host="127.0.0.1 $domain\n";
			$host.="::1	$domain\n";
	if(!file_put_contents($this->hostfile, $host.PHP_EOL , FILE_APPEND | LOCK_EX)){
		$error[]="Problem modifying host file";
	}
	//6 create newdb in maria 
		$newdb = new PDO("mysql:host=$dbhost", $dbuser, $dbpass);
		$newdb->exec("CREATE DATABASE `$dbname`;
				CREATE USER '$dbuser'@'localhost' IDENTIFIED BY '$dbpass';
				GRANT ALL ON `$dbname`.* TO '$dbuser'@'localhost';
				FLUSH PRIVILEGES;"); 
	//7 install maria db --$this->create_db($dbname,$dbhost,$dbuser,$dbpass);
	//	$this->_db = $this->mysql_con($dbhost,$dbname,$dbuser,$dbpass);
		$db= new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass);
	//import basic database
	
	$sqlines=file($gaiaroot."install.sql");
	foreach($sqlines as $line){
		if(substr($line,0,2)=='--' || $line=='')
			continue;
		$templine .=$line;
		if(substr(trim($line),-1,1)==";"){
		//$this->q($templine);
		 $db->query($templine);
		$templine='';
		}
	}
	//8 insert superuser in db
	$db->query("INSERT INTO user(name,pass,mail,grp,auth) VALUES(?,?,?,?,?)",
	array($dbuser,$dbpass,$email,7,1));
	
	//9	create/update setup.json in www folder (same level with Gaia and domain folder)
	$setupjson=urldecode($url);
	//htmlspecialchars_decode($jsonsetup)
	if(file_exists($setupjson)){		
		$newjson= jsonget($setupjson);
		$newjson[$domain]= json_decode($json,true)[$domain];	
		$setup=file_put_contents($setupjson, json_encode($newjson,JSON_PRETTY_PRINT));	
	}else{
		//$setup=file_put_contents($setupjson, $json);
		$setup=file_put_contents($setupjson, $jsonsetup);
	}
		if (!$setup){
			$error[]="Problem creating/updating setupjson";
		}
		return !empty($error) ? $error:  true;
}

    public function mysqldump($domainame,$replica,$type='dom'){
        $host = $this->CONF[$domainame]['dbhost'];
        $db = $this->CONF[$domainame]['dbname'];
        $dbuser = $this->CONF[$domainame]['dbuser'];
        $dbpass = $this->CONF[$domainame]['dbpass'];

//dump mysql
        $dump= $type=='dom'
            ? $this->BACKUP_DIR.$type."/sql/".$db."-".$replica.".sql"
            : $this->BACKUP_DIR.$type."/sql/gs-".$replica.".sql";
        //if type==gaia set only default settings and from one demo user,post,page record
        if($type=='dom'){
            $mysqldump = "mysqldump --user=$dbuser --password=$dbpass --host=$host $db > $dump";
        }else{
            $mysqldump = "mysqldump --no-data --user=$dbuser --password=$dbpass --host=$host $db > $dump";
        }


        @exec($mysqldump);
        @chmod($dump, 0777);
    }

	public function domain_backup($domainame, $replica, $log){
        $domainbase= explode('.',$domainame)[0];
		$dom_folder = $this->BACKUP_DIR . "dom/".$domainbase."-".$replica;

		if (!file_exists("$dom_folder.tar.gz")) {
			mkdir($dom_folder);
			chmod($dom_folder, 0777);

			//rewrite update.log.txt
			write_onfile($this->BACKUP_DIR."dom/log/updatelog-" . $domainbase . "-" . $replica . ".md", $log);

            //mysqldump
            $this->mysqldump($domainame,$replica);

//copy the system to backup folder
            // and domain to domain_folder
			recurse_copy(SERVERBASE . $domainame."/", $dom_folder);
            //unlink gaia from folder
            xrmdir($dom_folder.'/gaia');
//create tar.gz for domains_folder
			$drepo = new PharData("$dom_folder.tar");
			$drepo->buildFromDirectory($dom_folder);
			$drepo->compress(Phar::GZ);

//remove old
			system("chmod -R 777 $dom_folder");
			system("chmod -R 777 $dom_folder.tar");
			unlink("$dom_folder.tar");
			xrmdir($dom_folder);
//permissions
			system("chmod -R 777 $dom_folder.tar.bz2");

			//update version database
			$updateVersion = $this->db($domainame)->q("UPDATE globs SET value=? WHERE name=?", array($replica, 'domain-version'));
			if (!$updateVersion) {return $this->error[2];}else{return 'yes';}
		} else {
			return $this->error[1];
		}
	}

    public function system_backup($domainame, $replica, $log){
           $sys_folder = BACKUP_DIR.'gaia/'.$replica;
        if (!file_exists("$sys_folder.tar.gz")) {
            mkdir($sys_folder);
            chmod($sys_folder, 0777);

            //rewrite update.log.txt
            write_onfile(BACKUP_DIR."gaia/log/updatelog-sys-" . $replica . ".md", $log);

            //mysqldump
            $this->mysqldump($domainame,$replica,'gaia');

////copy the system to backup folder
            //update copy gaia to sys_folder
            recurse_copy(SERVERBASE . $domainame.'/gaia/', $sys_folder);

//create tar.gz for sys_folder
            $srepo = new PharData("$sys_folder.tar");
            $srepo->buildFromDirectory($sys_folder);
            $srepo->compress(Phar::GZ);
//remove old
            system("chmod -R 777 $sys_folder");
            system("chmod -R 777 $sys_folder.tar");
            unlink("$sys_folder.tar");
            xrmdir($sys_folder);
//permissions
            system("chmod -R 777 $sys_folder.tar.gz");

            //update version database
            $updateVersion = $this->db($domainame)->q("UPDATE varglobal SET value=? WHERE name=?", array($replica, 'system-version'));
            if (!$updateVersion) {return $this->error[2];}else{return 'yes';}
        } else {
            return $this->error[1];
        }
    }
	
	public function templates_similar($new,$old){
		$filesnew=rglob("$new/*");
		$filesold=rglob("$old/*");
		$array=array();
		$templatenew=basename($new);
		$basenew= explode('templates/',$new)[0].'templates/';		
		$baseold= explode('templates/',$old)[0].'templates/';				
		foreach($filesnew as $filen){
			$newfilenames[]=explode('/templates/',$filen)[1];			
		}
		foreach($filesold as $fileo){
			$oldfilenames[]=explode('/templates/',$fileo)[1];		
		}		
		//1 find files added new local installed template than in store
		$diff=array_diff($newfilenames,$oldfilenames); //new files 
		
		//2 compare hash files it's ok with hash but we need to compare all folders	
			$common=array_intersect($newfilenames,$oldfilenames);			
			//break common to two arrays with hashes and then compare them
			foreach($common as $comfile){
			if(hash_file('md5', $basenew.$comfile)!=hash_file('md5', $baseold.$comfile)){
				$count_changed +=1;
			}
			}
			$sim['new_files_rate']=number_format((count($diff)/(count($newfilenames)+count($oldfilenames)))*100, 2, '.', ''); 	
			$sim['changed_files_rate']=number_format(($count_changed/count($common))*100, 2, '.', ''); 						
		return $sim;
	}
}
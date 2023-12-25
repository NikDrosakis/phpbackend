<?php //updated:2020-01-29 20:20:33 DB- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>
<?php
/*
DB class
Database Connector (Redis, mariadb(mysql), postgresql(not ready),sqlite)
  v.2 UPDATED multiple mysql database connection and sqlite3
  v.3 UPDATED support ALL APPLICATION stores even to json and jsonb
1  PDO MYSQL
2 PDO SQLITE3
3  REDIS
  MONGODB
  CASSANDRA DB
  PDO POSTGRES

    $this->_db = new PDO("sqlite:" . SQLITE . $database . ".db");
  @define('DBTYPE', 'mysql');
    @define('CURRENT_TIMESTAMP', "UNIX_TIMESTAMP(CURDATE())");
/* $draft= new PDO('sqlite:sys.db');
$table="userFirstLast";
$column="AR";
$draft->beginTransaction();
$draft->query("CREATE TEMPORARY TABLE {$table}_backup({$column})");
$draft->query("INSERT INTO {$table}_backup SELECT {$column} FROM {$table}");
$draft->query("DROP TABLE {$table}");
$draft->query("CREATE TABLE {$table}({$column} )");
$draft->query("INSERT INTO {$table} SELECT {$column}  FROM {$table}_backup");
$draft->query("DROP TABLE {$table}_backup");
$draft->commit();

REDIS
  $this->redis->set("NN".$uid,$nb);
        $this->redis->del($keys);
		    public function update($key, $array_key, $array_new_value)
    {
//        $db = $this->redisdb($key);
//        if ($db != false) {
//            $this->redis->select($db);
        $array = $this->get($key);
        $array[$array_key] = $array_new_value;
        $this->replace($key, json_encode($array, JSON_UNESCAPED_UNICODE));
//        }else{
//            return false;
//        }
    }
	    /*  delete
         * a) multiple keys according to prefix
         * b) all keys without setting param
         * @prefix string
         * Need to add param @service 1 | 2
    
    function delp($prefix = '', $service = 1)
    {
        foreach ($this->redis->keys($prefix . '*') as $key) {
            $this->redis->del($key);
        }
    }
	   /*
    set the key
    $service is 1: Memcached, 2: Redis
    !check array keys for redis

    public function set($key, $fetch){
//        $db = $redisdb!='' ? $redisdb :(int)$this->redisdb($key);
//        if ($db != false) {
//            $this->redis->select($db);
        if (!$fetch) {
            return false;
        } else {
            if (is_int($fetch)) {
                $this->redis->set($key, $fetch);
            } elseif (is_array($fetch)) {
                $fetch = json_encode($fetch, JSON_UNESCAPED_UNICODE);
//                $this->redis->rawCommand('json.set', $key, '.', json_encode($fetch, JSON_UNESCAPED_UNICODE));
                $this->redis->setex($key, 1000, $fetch);
            } elseif (is_json($fetch)) {
                $this->redis->set($key, $fetch);
            } else {
                $this->redis->set($key, $fetch);
            }

            //pubsub mongo/counters
//            if ($this->redis->object("encoding", $key)=='int'){
//                if(substr($key, 0, 1)=="N"){
//                    $this->redis->publish('N','set.'.$key.'.'.$fetch);
//                }
//            }
            $this->redis->persist($key);
        }
    }

    public function append($key,$value){
        $state=false;
        $res = $this->redis->exists($key);
        if ($res) {
            if($this->redis->append($key, $value)){
                $state=true;
//                $this->publish($key, $value);
            };
        }else{
            if($this->redis->set($key, $value)){
                $state= true;
//                $this->publish($key, $value);
            };
        }
        return $state;
    }

    /*
    $service is Redis
    read from cache
  
    public function get($key, $format = 'arraystring'){
        $res = $this->redis->exists($key);
        $type = $this->redis->object("encoding", $key);
        if ($res) {
            $get = $this->redis->get($key);
            if ($type == 'int') {
                return (int)$get;
            } elseif (is_json($get)) {
                return json_decode($get, true);
            } else {
                return !$get ? false : $get;
            }
        } else {
            return false;
        }
    }

    public function incr($field,$uid=SPID){
//        $this->redis->incr('N'.$uid.'.'.$field);
        $this->redis->incr($field);
//        $this->redis->publish('N',"incr.N".$uid.'.'.$field);
    }
    public function decr($field,$uid=SPID){
//        $this->redis->decr('N'.$uid.'.'.$field);
        $this->redis->decr($field);
//        $this->redis->publish('N',"decr.N".$uid.'.'.$field);
    }
    //update saving keys to list with lrange
    public function keys($criteria){
        //
        return $this->redis->keys($criteria);
    }

 */

class DB {
    public $_db;
    public $dbt;
    public $confd;

    public function __construct($database = '',$dbt='maria',$DOMAIN=''){
        //configuration file
		$this->confd = config($DOMAIN)['dbs'];	
	//	xecho($this->confd);
	//	xecho(array_keys($this->confd));
      //  $this->dbt = $relationaldb;			
        $this->dbt = $dbt;			
		$dbname = $database != '' ? $database : $dbname;
        if($dbt=='maria' && in_array("maria",array_keys($this->confd))) {
		$this->confd=$this->confd['maria'];
		
		extract($this->confd);
			//connect to maria/mysql
			$this->_db = $this->mysql_con($dbhost,$dbname,$dbuser,$dbpass);	
			//check if database exists else install gaia.sql
			if($this->_db=="1049"){			//database not exist => create
				$this->create_db($dbname,$dbhost,$dbuser,$dbpass);
				//try {				
				//exec("mysql --user=$dbuser --password=$dbpass --host=$dbhost $dbname < ".GAIAROOT."gaia.sql");
				//} catch (PDOException $e) {
                    //xecho($e);
                //}
				//create maria 
			//exec("mysql --user=$dbuser --password=$dbpass --host=$dbhost $dbname < {$gaiabase}gaia.sql");
			//connect again
			$this->_db = $this->mysql_con($dbhost,$dbname,$dbuser,$dbpass);
			//import basic database			
			$sqlines=file(GAIAROOT."gaia.sql");
			foreach($sqlines as $line){
				if(substr($line,0,2)=='--' || $line=='')
					continue;
				$templine .=$line;
				if(substr(trim($line),-1,1)==';'){
				$this->q($templine);
				$templine='';
				}
			}
			}
        }elseif($dbt=='sqlite' && in_array("sqlite",array_keys($this->confd))) {
			//xecho(SITE);
            $datapath=  "/var/www/sqlite/" . $database . ".db";
			//xecho($datapath);
            if ($this->sqlite_version($datapath)!= -1) {
                try {
                    $this->_db = new PDO("sqlite:$datapath");
                } catch (PDOException $e) {
                    xecho($e);
                }
            }else{
                echo 'sqlite database is not correct';
            }
        }elseif($dbt=='redis' && class_exists('Redis')) {
            //ONE INSTANCE OF REDIS
			        //$DOMAIN= $database=='' ? $_SERVER['HTTP_HOST'] : $database;  
		//$this->confd= config($DOMAIN);	
		//$dbname = $database != 'sys' ? $database : $this->confd['dbname']; 		
        //this domain's system database //remove l from local domain                      
            $this->redis = new Redis();
            $this->redis->pconnect('0.0.0.0', 6379);
            $this->redis->auth("n130177!");
			$database=$database!='' ? $database : $this->confd['redis'];
            $this->redis->select($database);
            $this->redis->redis_running=true;   
			
        }elseif($dbt=='pg'){
            $this->_db = $this->pgsql_connect(
                $this->confd['postgresql']['db'],
                $this->confd['postgresql']['user'],
                $this->confd['postgresql']['pass']
            );
        }else{
			return;
		}		
    }
	//setting table
	public function is($name){
		$fetch = $this->db->f("SELECT en FROM globs WHERE name=?", array($name));
		if (!empty($fetch)) {
			return urldecode($fetch['en']);
		} else {
			return false;
		}
	}
	
	 /********************************   MARIA/MYSQL FUNCTIONS*********************************************/
    /*
     *	Fetch MANY result
     *	Updated with memcache
     */
    public function fjsonlist($query){
        $res=$this->fa($query);		
		if (!$res) {
			return FALSE;
		}else{
			$tags=array();
			for($i=0;$i<count($res);$i++){	
				if($res[$i]['json']!='[]'){
				$jsdecod=json_decode($res[$i]['json'],true);
			if(!empty($jsdecod)){
				foreach($jsdecod as $jsid => $jsval){		
					$tags[]=trim($jsval);
						}
			}
					}		
			}
		return $tags;
		}
        $res->closeCursor();
    }
    /*
   * INSERT WITH RETURN ID ,
   * UPDATE
   * A) RETURNS FALSE,
   * B) Autoincrement with NULL or insert $id NO NEED FOR fetchMax function
   * c) NO NEED FOR QUESTIONMARKS
     * This function works only if we insert all params except id
     * sequential array = array('apple', 'orange', 'tomato', 'carrot');
     * associative array = array('fruit1' => 'apple',
                    'fruit2' => 'orange',
                    'veg1' => 'tomato',
                    'veg2' => 'carrot');
     * if we want to insert specified number of params we need array('uid'=>$uid,'content'=>$content,etc)
   * */
    public function inse($table, $params = array(),$id=NULL){
        $qmk = implode(',', array_fill(0, count($params), '?'));
        if (is_assoc($params)) {
//            $rows= $k= '("'.implode('","',array_keys($params)).'")';
            $rows = $k = '(' . implode(',', array_keys($params)) . ')';
            $values = "$rows VALUES ($qmk)";
            $params = array_values($params);
        } else {
            $values = count($params) != count($this->columns($table)) && $id != NULL ? "VALUES ($id,$qmk)" : "VALUES ($qmk)";
        }
        $sql= "INSERT INTO $table $values";
        $res = $this->_db->prepare($sql);
        $res->execute($params);
        if (!$res){return false;}else{
        return !$this->_db->lastInsertId() ? true: $this->_db->lastInsertId(); //CASE OF CORRECT INSERT BUT WITH NO RETURN VALUE (eg NO ID table)
		}
        $res->closeCursor();
    }
    /*
get max value from table
*/
    public function fetchMax($row, $table, $clause = ''){
        $selecti = $this->f("SELECT MAX($row) as max FROM $table $clause");
        return $selecti['max'];
    }

    public function fetchList1($rows){
        if(is_array($rows)){
            $fetch=$this->fa("SELECT {$rows[0]} FROM {$rows[1]} {$rows[2]}");
            for($i=0;$i<count($fetch);$i++){
                $list[]=strpos($rows[0], '.') !== false	? $fetch[$i][explode('.',$rows[0])[1]] : $fetch[$i][$rows[0]];
            }
        }
        return $list;
    }


    public function column_primary($table){
        $q = $this ->_db->prepare("SHOW columns FROM $table WHERE Key_name = 'PRIMARY'");
        $q->execute();
        return  $q->fetchAll(PDO::FETCH_COLUMN);
        $q->closeCursor();
    }

    /*
     *
     * meta retuns table all columns and types
     * LONG -> int
     * TINY ->tinyint
     * VAR_STRING ->varchar
     * STRING -> char
     * INT24 -> mediumint
     * */
	 public function list_tables(){
        $query = $this->_db->query('SHOW TABLES');
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function types($table){
        $sel=array();
        $select = $this ->_db->query("SELECT * FROM $table");
        foreach($this->columns($table) as $colid => $col) {
            $meta= $select->getColumnMeta($colid);
            $sel[$meta['name']] = $meta['native_type'];
        }
        return $sel;
    }

    public function comments($table){
        $sel=array();
        foreach($this->columns($table) as $colid => $col) {
            $select = $this->f("SHOW full columns from $table WHERE Field='$col'");
            $sel[$select['Field']] = $select['Comment'];
        }
        return $sel;
    }
    /*
     * RETURN TABLE char, varchar, text types
     *
     * */
    public function char_types($table){
        $res = $this->types($table);
        foreach($res as $col => $type){
            if(in_array($type,array('VAR_STRING','STRING','BLOB'))){
                $cols[] = $col;
            }
        }
        return $cols;
    }

    public function sqlite_version($datapath){
        if(file_exists($datapath)) //make sure file exists before getting its contents
        {
            $content = strtolower(file_get_contents($datapath, NULL, NULL, 0, 40)); //get the first 40 characters of the database file
            $p = strpos($content, "** this file contains an sqlite 2"); //this text is at the beginning of every SQLite2 database
            if($p!==false) //the text is found - this is version 2
                return 2;
            else
                return 3;
        }
        else //return -1 to indicate that it does not exist and needs to be created
        {
            return -1;
        }
    }
	
    public function mysql_con($dbhost,$dbname,$dbuser,$dbpass){
        try	{
			//mysql:unix_socket=/var/run/mysqld/mysqld.sock;charset=utf8mb4;dbname=$dbname
            return new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass,
                array(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION,
                    PDO::ERRMODE_WARNING,
                    PDO::ATTR_EMULATE_PREPARES => FALSE,
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                    PDO::ATTR_PERSISTENT => false
                ));

        }	catch(PDOException $error)	{
            return $error->getCode();
//            return false;
        }

    }

    public function pgsql_connect($dbname,$dbuser,$dbpass){
        try	{
            return new PDO("pgsql:host=localhost;port=5432;dbname=$dbname;user=$dbuser;password=$dbpass");
        }	catch(PDOException $error)	{
            echo $error->getMessage();
            return false;
        }
    }

    //key listing exists in one db return which db
    public function redisdb($key){
        global $GLOBAL;
        $key= is_array($key) ? $key[0] : $key;
        $fkey= strpos($key, '_') !== false ? explode('_',$key)[0] : $key;
        $r= array_key_exists($key,$GLOBAL['rservices']) ? $GLOBAL['rservices'][$key] : (array_key_exists($fkey,$GLOBAL['rservices']) ? $GLOBAL['rservices'][$fkey] :'false');
        return (int)$r;
    }
	
	public function create_db($dbname,$dbhost,$dbuser,$dbpass){		
	try {
		$this->_db = new PDO("mysql:host=$dbhost", $dbuser, $dbpass);
		$this->_db->exec("CREATE DATABASE `$dbname`;
				CREATE USER '$dbuser'@'localhost' IDENTIFIED BY '$dbpass';
				GRANT ALL ON `$dbname`.* TO '$dbuser'@'localhost';
				FLUSH PRIVILEGES;") 
		or die(print_r($this->_db->errorInfo(), true));

	} catch (PDOException $e) {
		die("DB ERROR: ". $e->getMessage());
	}
}
    /*
     * BASIC FUNCTIONS USED TO ALL DBT
     * f FETCH
     * fa FETCH ALL
     * q QUERY (INSERT AND UPDATE)
     * INS
     * exec
    */
    public function exec($q){
		 $s= $this->_db->exec($q);
		 return $s;
	}	
    public function f($q, $params = array()){
        if(in_array($this->dbt,array('maria','sqlite'))) {
            $res = $this->_db->prepare($q);
            $res->execute($params);
            if (!$res) return FALSE;
            return $res->fetch(PDO::FETCH_ASSOC);
            $res->closeCursor();

        }elseif ($this->dbt=='redis'){
            $format='json';
            if($this->redis->exists($q)) {
                $get= $this->redis->get($q);
                if (is_json($get)) {
                    $encoded= $format=='json' ? $get : json_decode($get, true);
                    return !empty($encoded) || is_json($encoded) ?  $encoded: false;
                } else {
                    return $get!='' ? $get: false;
                }
            }else{return false;}
        }
    }
    /*
    *	Fetch MANY result
    *	Updated with memcache
    */
    public function fa($query, $params = array())
    {
		$res = $this->_db->prepare($query);
        if(in_array($this->dbt,array('maria','sqlite'))) {            
            $res->execute($params);
        }elseif(in_array($this->dbt,array('pg'))) {
			$res->execute();
		}
		if(!$res) return FALSE;
            return $res->fetchAll(PDO::FETCH_ASSOC);
            $res->closeCursor();
    }

    /*
    *Query Method replaces standard pdo query method
    Usage: with	INSERT, UPDATE, DELETE queries
    Updated with memcache
    */
    public function q($q, $params = array()) {
        if(in_array($this->dbt,array('maria','sqlite'))) {
            $res = $this->_db->prepare($q);
            $res->execute($params);
            if (!$res)return FALSE;            
            //return $this->_db->lastInsertId();
            return true;
            
            $res->closeCursor();

        }elseif($this->dbt=='redis'){
            $key=$q[0];
            $fetch=$q[1];
            if (is_array($fetch)) {
                $this->redis->setex($key, 1000, json_encode($fetch, JSON_UNESCAPED_UNICODE));
            } else {
                $this->redis->set($key, $fetch);
            }
        }
    }
    /*
   * INSERT WITH RETURN ID ,
   * UPDATE
   * A) RETURNS FALSE,
   * B) Autoincrement with NULL or insert $id NO NEED FOR fetchMax function
   * c) NO NEED FOR QUESTIONMARKS
   * */
       public function ins($table, $params = array(),$id='NULL'){
         if(in_array($this->dbt,array('maria','sqlite'))) {
		$qmk= implode(',', array_fill(0, count($params), '?'));
         if(is_assoc($params)){
            $rows= $k= '("'.implode('","',array_keys($params)).'")';
            $values = "$rows VALUES ($qmk)";
            $params= array_values($params);
        }else {
            $values = count($params) != count($this->columns($table)) ? "VALUES ($id,$qmk)" : "VALUES ($qmk)";
        }
        $res = $this->_db->prepare("INSERT INTO $table $values");
        $res->execute($params);
        if (!$res) {return false;} else {return $this->_db->lastInsertId();}
        $res->closeCursor();
		 }
		}

    //count_ results
    public function count_($rowt, $table, $clause = null, $params = array()){
        if(in_array($this->dbt,array('maria','sqlite'))) {
            $result = $this->_db->prepare("SELECT COUNT($rowt) FROM $table $clause");
            $result->execute($params);
            if (!$result) return FALSE;
            return $result->fetchColumn();
            $result->closeCursor();
        }
    }

    //count_ results
    public function counter($query = null, $params = array()){
        if(in_array($this->dbt,array('maria','sqlite'))) {
            $result = $this->_db->prepare($query);
            $result->execute($params);
            if (!$result) return FALSE;
            return $result->fetchColumn();
            $result->closeCursor();
        }
    }

    //only for dbt maria, sqlite
    public function columns($table){
		return array_keys(jsonget(GAIAROOT."schema.json")[$table]);
		//if($this->dbt=="maria"){
        //$q = $this->_db->prepare("DESCRIBE $table");
		//}elseif($this->dbt=="sqlite"){
		//$q = $this->_db->prepare("PRAGMA table_info($table)");		
		//}
        //$q->execute();
        //return $q->fetchAll(PDO::FETCH_COLUMN);
    }
    /*
create key->value list with two rows from database
    fPairs to replace fetchCoupleList
    UPDATE WITH PDO::FETCH_KEY_PAIR
    NEW METHOD 1
*/
    public function fPairs($row1, $row2, $table, $clause = ''){
        return $this->_db->query("SELECT $row1,$row2 FROM $table $clause")->fetchAll(PDO::FETCH_KEY_PAIR);
    }

/*
  fUnique SELECT uid,cv.* FROM cv returns [uid]=>array(id=1,title=asdfdsf)
  for cases we want unique id to avoid for loops
  NEW METHOD 2
 * */
    public function fUnique($query){
        return $this->_db->query($query)->fetchAll(PDO::FETCH_UNIQUE);
    }
    /*
      fGroup SELECT uid,id,title FROM cv returns
      [uid]=>array(
             [0]=>(id=1,title=asdfdsf)
             [1]=>
      good for nested arrays to avoid for loops
      NEW METHOD 3
     * */
    public function fGroup($query){
        return $this->_db->query($query)->fetchAll(PDO::FETCH_GROUP);
    }
    /*
      fPairs to replace fetchList and fetchRowList
      returns a simple array list
      NEW METHOD 4
     * */
    public function fList($rows, $table, $clause = ''){
        return $this->_db->query("SELECT $rows from $table $clause")->fetchAll(PDO::FETCH_COLUMN);
    }

    //FAST NEW FUNCTION FROM CMS CLASS
    //update of fetchRowList and fetchCoupleList
    public function fetchList($rows, $table, $clause=''){
        $list=array();
        //fetchRowList
        if(is_array($rows)){

            $row1=$rows[0];$row2=$rows[1];
            $fetch=$this->fa("SELECT $row1,$row2 FROM $table $clause");
            if(!empty($fetch)) {
                $row1 = strpos($row1, '.') !== false ? explode('.', $row1)[1] : $row1;
                $row2 = strpos($row2, '.') !== false ? explode('.', $row2)[1] : $row2;
                for ($i = 0; $i < count($fetch); $i++) {
                    $list[$fetch[$i][$row1]] = $fetch[$i][$row2];
                }
            }else{return false;}
            //fetchCoupleList
        }else{
            $fetch=$this->fa("SELECT $rows FROM $table $clause");
            if(!empty($fetch)) {
                for ($i = 0; $i < count($fetch); $i++) {
                    $list[] = $fetch[$i][$rows];
                }
            }else{return false;}
        }
        return $list;
    }

    public function truncate($table){
        if(in_array($this->dbt,array('maria','sqlite'))) {
            $q = $this->_db->exec("TRUNCATE TABLE $table");
        }
    }

    //update of fetchRowList and fetchCoupleList
    public function fl($rows, $table, $clause=''){
        if(in_array($this->dbt,array('maria','sqlite'))) {
            $list = array();
            //fetchRowList
            if (is_array($rows)) {
                //fetchCoupleList
                $row1 = $rows[0];
                $row2 = $rows[1];
                $fetch = $this->fa("SELECT $row1,$row2 FROM $table $clause");
                if (!empty($fetch)) {
                    for ($i = 0; $i < count($fetch); $i++) {
                        $list[$fetch[$i][$row1]] = $fetch[$i][$row2];
                    }
                    return $list;
                } else {
                    return false;
                }
            } else {
                //FETCHrOWLIST
                $fetch = $this->fa("SELECT $rows FROM $table $clause");
                if (!empty($fetch)) {
                    for ($i = 0; $i < count($fetch); $i++) {
                        $list[] = $fetch[$i][$rows];
                    }
                    return $list;
                } else {
                    return false;
                }
            }
        }
    }

    //only for maria
    function trigger_list(){
        $triggers = $this->fetchAll("SHOW TRIGGERS");
        $list=array();
        if(!empty($triggers)) {
            for ($i = 0; $i < count($triggers); $i++) {
                $list[] = $triggers[$i]['Trigger'];
            }
        }
        return $list;
    }

	
	   
    /********************************REDIS CACHE FUNCTIONS*********************************************/
    /*
    check if key exist in cache
    */
    public function cexist($key,$dbr='')	{
        $db= $dbr!='' ? $dbr : $this->redisdb($key);
        if($db!=false) {
            $this->redis->select($db);
            $res = $this->redis->exists($key);
            if (!$res || empty($this->get($key))) {
                return false;
            } else {
                return true;
            }
        }else{
            return false;
        }
    }

    public function set($key, $fetch)
    {
//        $db = $redisdb!='' ? $redisdb :(int)$this->redisdb($key);
//        if ($db != false) {
//            $this->redis->select($db);
        if (!$fetch) {
            return false;
        } elseif (is_int($fetch)) {
            $this->redis->set($key, $fetch);
        } elseif (is_array($fetch)) {
//                $this->redis->rawCommand('json.set', $key, '.', json_encode($fetch, JSON_UNESCAPED_UNICODE));
            $this->redis->setex($key, 1000, json_encode($fetch, JSON_UNESCAPED_UNICODE));
        } elseif (is_json($fetch)) {
            $this->redis->set($key, $fetch);
        } else {
            $this->redis->set($key, $fetch);
        }
//        } else {
//            return false;
//        }
    }
    public function append($key,$value){
        $state=false;
        $res = $this->redis->exists($key);
        if ($res) {
            if($this->redis->append($key, $value)){
                $state=true;
            };
        }else{
            if($this->redis->set($key, $value)){
                $state= true;
            };
        }
        return $state;
    }

    /*
    $service is 1: Memcached, 2: Redis
    read from cache
    */
    public function get($key, $format = 'arraystring')
    {
        $res = $this->redis->exists($key);
        $type = $this->redis->object("encoding", $key);
        if ($res) {
            $get = $this->redis->get($key);
            if ($type == 'int') {
                return (int)$get;
            } elseif (is_json($get)) {
                return json_decode($get, true);
            } else {
                return !$get ? false : $get;
            }
        } else {
            return false;
        }
    }

    /*  delete
     * a) multiple keys according to an array del(array())
     * b) one $key as a string.
     * @keys array | string
     * */
    public function del($keys){
        $db = is_array($keys) ? $this->redisdb($keys[0]): $this->redisdb($keys);
        if ($db != false) {
            $this->redis->select($db);
            $this->redis->delete($keys);
        } else {
            return false;
        }
    }
    /*  delete
         * a) multiple keys according to prefix
         * b) all keys without setting param
         * @prefix string
         * Need to add param @service 1 | 2
         * */
    function delp($prefix='',$service=1) {
        foreach ($this->redis->keys($prefix.'*') as $key){
            $this->redis->del($key);
        }
    }

    /*
    update from cache
    UPDATE with multiple array_key with array('key'=>'value','key2',value)
    foreach array as arr
    */
    public function update($key,$array_key,$array_new_value){
        $db = $this->redisdb($key);
        if ($db != false) {
            $this->redis->select($db);
            $array= $this->get($key);
            $array[$array_key]=$array_new_value;
            $this->replace($key, json_encode($array, JSON_UNESCAPED_UNICODE));
        }else{
            return false;
        }
    }

    /*
    replace cache
    $this->replace('isactive_'.$_SESSION['SPID'],0);
    */
    public function replace($key,$value){
        $db = $this->redisdb($key);
        if ($db != false) {
            $this->redis->select($db);
            $this->redis->set($key, $value);
        }else{
            return false;
        }
    }
	
}
?>
<?php //updated:2020-02-11 13:10:06 My- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>
<?php
/* My(Data) class 
CONTENT MANAGEMENT 
 * gets all data from maria
 * create a contructor that gets this data 
 * vars
 * $uid,$usergroup,$lang
 * update with rules for agent affiliatedemployee employee employer
 *
  add params
    $orderby
    $limit
    $where
    wherekey is the key of a selector of where
	$wherekey=explode('-',$wherekey)[1] //throw select
 *
 * */
class My extends Gaia{

//main my array created
public static function methods($f=array()){

}

function data($uid=SPID,$action='get',$f=array()){
        $this->uid=$uid;
        $this->f=$f;
        if (!empty($f)) {$this->f = $f;}
        if($action=='get'){
            $this->f = $this->get('my' . $this->uid);
            $listofchanges = array_unique($this->redis->lrange('mye' . $this->uid, 0, -1));
            //get cache if not empty read from maria

            //check exception list
            if (!empty($listofchanges)) {
                foreach ($listofchanges as $e) {
                    if($e=='user'){
                    $this->f = array_merge($this->f,$this->userdata());
                    }else{
                        if(in_array($e,$this->methods($this->f))) {
                            $this->f[$e] = $this->$e();
                        }
                }}
                //update and kill exception list
                $this->f['updatedat']=date('Y-m-d H:i:s');
                $this->del('mye' . $this->uid);
            }
        }else if($action=='set') {
            foreach ($this->methods($this->f) as $meth) {
                $this->f[$meth] = $this->$meth();
            }
            $this->f['setat']=date('Y-m-d H:i:s');
        }
        //set redis my
        if(!empty($listofchanges) || $action=='set'){
            $this->set('my' . $this->uid,$this->f);
        }
        return $this->f;
    }

    //IF AGENT EMPLOYER
function search($params=array()){	
	$q= $params['q'];
	$query="SELECT * FROM post WHERE title LIKE '%$q%' ";
	//xecho($query);
	return $this->db->fa($query);	
}

function user($params=array()){	
    if($_POST['mode']!=''){
	$sel= $this->db->f("SELECT * FROM user WHERE name='".$_POST['mode']."'");
	}else{
	$sel= $this->db->fa("SELECT * FROM user LIMIT 10 ");	
	}
	return $sel;
}
/*ONE POST POST URI

$post['mode'] is for TIED undraggable cases
NEXT CASE for any case ONE post IS SELECTED
RULE FOR ONE POST: 
$params['wherekey'] = select|id,title
$params['where']=14
means than first column after | ie id=14
*/
function post($params=array()){
if($params['wherekey']!="" && $params['where']!="" && strpos($params['wherekey'],"|")!==false){
$exp=explode('|',$params['wherekey']);
$selector=$exp[0]; //if ==select
$values=explode(',',$exp[1]);
$val=$params['where'];
$key=$values[0];
$whereQ= "post.$key=$val";
		$query="SELECT post.*,user.name as username,tax.name as taxname FROM post 
		LEFT JOIN user ON post.uid=user.id   
		LEFT JOIN tax ON tax.id=post.taxid   
		WHERE $whereQ";
		$sel=$this->db->f($query);
}elseif ($_POST['mode']!=''){
		$whereQ=  "post.uri='".$_POST['mode']."'";
		$sel=$this->db->f("SELECT post.*,user.name as username,tax.name as taxname FROM post 
		LEFT JOIN user ON post.uid=user.id   
		LEFT JOIN tax ON tax.id=post.taxid   
		WHERE $whereQ");
	}else{
		$q="SELECT post.*,user.name as username,tax.name as taxname FROM post 
		LEFT JOIN user ON post.uid=user.id   
		LEFT JOIN tax ON tax.id=post.taxid  ";
		//xecho($q);
		$sel=$this->db->fa($q);		
	}
	return $sel;
}

/*
* POST ARCHIVE ARRAY tax/[taxname]
* */
public function archieve($params=array()){
	$mode=isset($_POST['mode']) ? $_POST['mode']: '';
	$searchQ= $_POST['page']=='search' ? "AND post.title LIKE '%$mode%'":"";
	$orderQ= !$params['orderby'] ? "post.sort" : $params['orderby'];
	$limitQ= !$params['limit'] ? "4" : $params['limit'];
	$wherekey=explode('-',$params['wherekey'])[1];
	$whereQ= !$params['where'] ? "" : "AND $wherekey='".$params['where']."'";
	$query="SELECT post.* FROM post 
	LEFT JOIN tax ON post.taxid=tax.id
	WHERE post.status=2 $searchQ $whereQ GROUP BY post.id ORDER BY $orderQ LIMIT $limitQ";
	//xecho($query);
	$sel=$this->db->fa($query);
	return !$sel ? '':$sel;
}

/*
* obj 
* */
public function media($params=array()){
$orderQ= !$params['orderby'] ? "" : $params['orderby'];
$limitQ= !$params['limit'] ? "20" : $params['limit'];
$query="SELECT * from obj $orderQ LIMIT $limitQ";
//xecho($query);
$sel=$this->db->fa($query);
return !$sel ? '':$sel;
}

/*
created 
*/
function menu($params=array()){
$whereQ= !$params['where'] ? "menu.id=1" : "menu.id='".$params['where']."'";
	$query="SELECT links.* FROM menu LEFT JOIN links ON menu.id=links.menuid WHERE $whereQ ORDER BY links.sort";
	//xecho($query);
	return $this->db->fa($query);
	//return $query;
}
function postmenu($params=array()){
$orderQ= !$params['orderby'] ? "post.sort" : $params['orderby'];
$limitQ= !$params['limit'] ? "4" : $params['limit'];
$whereQ= !$params['where'] ? "post.sort" : $params['where'];
	return $this->db->fPairs("post.uri","post.title","post","LEFT JOIN tax ON post.taxid=tax.id
 WHERE post.status=2 $whereQ GROUP BY post.id $orderQ $limitQ");
}
function tax($params=array()){
$orderQ= !$params['orderby'] ? "post.sort" : $params['orderby'];
$limitQ= !$params['limit'] ? "4" : "limit ".$params['limit'];
$whereQ= !$params['where'] ? "AND post.taxid=1" : $params['where'];
	$query="SELECT post.* FROM post 
LEFT JOIN tax ON post.taxid=tax.id
WHERE post.status=2 $where GROUP BY post.id ORDER BY $orderQ $limitQ";
	return $this->db->fa($query);
	//return $query;
}
}
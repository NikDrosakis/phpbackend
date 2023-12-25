<?php //updated:2020-01-29 20:20:33 Taxonomy- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>
<?php
/*
TAXONOMY CLASS CHILD OF GAIA CLASS
TODO: insert new TAX TYPES
*/

class Taxonomy extends Gaia{
	public $types=array();
	public $type;

	public function __construct()    {

//	$this->types= $this->sys()->fetchList(array("name","parenting"),"taxgrp","WHERE status=1");


	
	}


	function page_parent(){
//insert
	$this->insert('1');

	}

	function page_non_parent(){
	//insert
	$this->insert('0');

	}

//	public function cat($table){
//		return $this->fetchAll("SELECT * FROM $table");
//	}

/* 
	public  function get_tax_cloud(){
	 global $ss, $su,$p,$TM_GLOBALS,$action, $cid,$uid, $url, $id, $lang; 
	 
	$sys=new DB();
		$result =$sys->fetchAll('SELECT * FROM tax LEFT JOIN tax_'.$lang.' ON tax.id=tax_'.$lang.'.id 
		WHERE tax.type="'.$this->type.'" GROUP BY tax_'.$lang.'.taxName '); 
		//var_dump($result);
		for ($i=0;$i<count($result);$i++){
		$numtags = count($result[$i]['taxName']);	
		$tagid = $result[$i]['id'];	
		$tagname= $sys->this_('taxName','tax_'.$lang,"id='".$tagid."'");
		$tagsize ='';
		if ($numtags > 10) {$tagsize = 26;}
		 if ($numtags < 10) {$tagsize = 18;}
		 if ($numtags < 3) {$tagsize = 13;}
		 if ($numtags ===1) {$tagsize = 9;}
		//echo '<a href="'.URL_FILE.'?cid='.$tagid.'">
		echo '<a href="'.URL_FILE.'">
		<span style="font-size:"'.$tagsize.'pt;">'.$tagname.'</span> ('.$numtaG.')</a> ';
		}
	} */

/* 	public function tag_page() {
		$sys=new DB();
		$Image= new Image;
		$TMDate= new TMDate;
		
		$result = $sys->count_("id", "FROM tax LEFT JOIN tax_{$lang} ON tax.id=tax_{$lang}.id
		WHERE id='".$cid."' GROUP BY tax_{$lang}.taxName ORDER BY num DESC",'sys','num');
		$taginfo = $result->fetch(PDO::FETCH_BOTH);$numtags = $taginfo[0];$tagname = $taginfo[1];
		$tagurl = urlencode($tagname);
		echo '<h2>',$tagname,'</h1>',$numtags;
		$select = 	$sys->fetchAll("SELECT * FROM tax 
		JOIN metadata ON metadata.id=tax.id 
		JOIN record ON tax.recID=record.recID 
		JOIN user ON record.userID=user.userID 
		WHERE tax.type='".$cid."'");
		while ($showall = $select->fetch (PDO::fetchAll)) { extract($showall);
		echo '<img class="fimg" src="'.UPLOADS.$Image->$recImage.'" alt="" width="200" align="left"/><br />';
		echo '<a href="'.URL_FILE.'?cid=',$id,'">',$taxName,'</a>
		</br>Date:',$TMDate->get_date($recDate),
		"Author:",$userName;
	}
	} */

/* 	public function get_category($cid='',$num=5, $img_width=80) {
		
		$sys=new DB();
		$Image= new Image;			
		$cat=self::tax('taxName', $cid);
		echo '<h2>',$cat,'</h2>';
		
		$showall =  $sys->fetchAll('SELECT * FROM record 
		LEFT JOIN tax ON record.recID=tax.recID 
		LEFT JOIN rec_'.$lang.' ON record.recID=rec_'.$lang.'.recID
		WHERE tax.id="'.$cid.'" ');
			
		echo '<h4><a href="'.ROOT.'post&id=',$showall['recID'],'">',$showall['recName'],'</a></h4>'; 
		//echo '<a href="'.ROOT.'post&cid='.$showall['id'].'&id=',$showall['recID'],'">';
		echo '<img src="',$Image->get_image($showall['recImage']),'" alt="',$showall['recImage'],'" width="',$img_width,'" align="left" /></a>';
		//echo '<p>Author:',$showall['userName'],'</p>';
		echo '<p>'.$sys->dic('date').':'.get_date($showall['recDate']),'</p>'; 
		echo '<br style="clear:both;"/><hr>'; 
		}
 */
/* 	public function get_category_drop_list($query){
		
		$sys=new DB();
		$result = $sys->fetchAll('SELECT * FROM record 
		JOIN tax ON record.recID=tax.recID 
		WHERE tax.id="'.$cid.'" ');
		echo '<form name="goto_cat_archive"><select ONCHANGE="location = this.options[this.selectedIndex].value;">';
		while($tag_info = $result->fetch(PDO::FETCH_BOTH)){
			$numtags = $tag_info[0];	$tagname = $tag_info[1];
			$tag_url = urlencode($tagname);
			$id = $sys->this_('id', 'tax', 'taxName', $tagname);
		//echo '<OPTION SELECTED value="index.php">'.$tagname.'('.$numtaG.')</option>'; 	
		echo '<option value="'.ROOT.'cat&cid='.$id.'">'.$tagname.'('.$numtaG.')</option>';	
		}
		echo '</select></form>';
	} */

/* 	public function set_category_insert($query1,$query2){	
		
		$sys=new DB();
		$data2 = $sys->query($query1); 	
		if($data2 -> fetchColumn() ===0){
		$taxarray = $_POST['id']; 
		foreach ($taxarray as $id) {
		$caname = $sys->taxRow('taxName', $id);
		$taxdata = query ($query2); 
		}
		}else {
		echo 'The category already exists';exit();
		//$data3 = $db -> query("INSERT INTO tax (id,taxName, type, recID) VALUES ('$id_exist','$taxName','category','$home') "); 
		}
		} */

	/*
	Categories and Subcategories Administration Editor 	
	INSERT 
	DELETE 
	Category to Subcategory
	Subcategory to Category
	TODO: i)exclude dropdown parent from same category name
	ii) if category becomes subcategory,  instead of creating third level child subcategory 
	
	*/	
	
/* 	public function edit_category(){
		$sys=new DB();
		
		if(isset($_POST['taxonom'])) {
		//CHECKED AND UNCHECKED ARRAY 
		if (isset($_POST['id'])){
		$taxadd = $_POST['id'];
		//var_dump($taxadd);
		$tax_unchecked = array_diff($this->idArray,$taxadd);
		//var_dump($tax_unchecked);
		//if unchecked
		foreach ($tax_unchecked as $taxi_unchecked) {
		//echo $taxi_unchecked<br/>;
		$sys->query("UPDATE tax SET recID='' WHERE id='".$taxi_unchecked."' AND recID='".$id."'"); 
		redirect(0);
		}
		//if checked
		foreach ($taxadd as $taxi) {
		$caname = self::tax('taxName','id',$taxi);
		//check if $caname already exists
		if (!in_array($caname,$this->taxNameWhereRecID)){
		$sys->insert("tax",array('recID','type'), array('".$id."', '".$this->type."')); 
		$sys->insert("tax",array('recID','taxName'),array('".$id."', '".$caname."')); 
		redirect(0);
		} 
		}
		} else{
		//if everything is unchecked
		$sys->query("UPDATE cat SET recID='' WHERE recID='".$id."'"); 
		redirect(0);
		}
		} 		 
		echo '('.count($this->tidWherePostID).')';
		?>
		<br/>
		<form method="post"/>
		<?
		//loop checked
		foreach ($this->tidWherePostID as $checkedid){
		echo '<input type="checkbox"  name="id[]" value="'.$checkedid.'" checked />'.self::tax('taxName',$checkedid).'<br />';	 
		}
		//find the difference in the array
		$taxNameArrayUnchecked = array_diff($this->taxNameArray,$this->taxNameWhereRecID);
		//loop unchecked
		foreach ($taxNameArrayUnchecked  as $taxNameUncheckedRow){
		 echo '<input type="checkbox"  name="id[]" value="'.$sys->this_('id','tax_{$lang}'," 'taxName'='".$taxNameUncheckedRow."'").'" />'.$taxNameUncheckedRow.'<br/>';	 
		}
		?><input name="taxonom" type="submit" value="Save Categories" /></form><?	
	} 
 */
	/*
	EDIT TAXONOMY
	*/
	
	public function parentsNameArray(){
	$parents = $this->fetchAll("SELECT * FROM tax 
		WHERE taxgrpid=? AND parent=0
		ORDER BY name DESC",array($this->type));	
	return $parents;
	}

 	public function edit(){
	$select = $this->fetchAll("SELECT * FROM tax WHERE taxgrpid=?
	ORDER BY parent,name DESC",array($this->type));
	
	if(isset($_POST['parent'])) {
		$id = $_POST['id'];
		$value = trim($_POST['parent']);
		$updateParent=$this->query("UPDATE tax SET parent=? WHERE id =? ",array($value,$id));
		if ($updateParent){redirect(URL_FILE);}
		}
	?>
	<a href="<?php echo URL_FILE;?>?action=insert_category">Insert Category</a>
	<h3>Categories(<?=count($this->parentsName)?>)- SubCategories(<?=count($select)?>)</h3>
		<?php if ($this->type ===0){ echo 'insert one '.$this->type;}
	} 

	/*
	INSERT TAXONOMY
	*/
	 public function insert($parenting=0){

		if(isset($_POST['ntaxSubmit']) && $_POST['name'] !="") {
		//check if taxName not exists 
		$taxD = $_POST['id'];
		$name = trim($_POST['name']);
		
		if($parenting!='0'){		
		$parent = $_POST['parent'];				
		$query = $this->query("INSERT INTO tax (parent,name,taxgrpid) VALUES (?,?,?)",array($parent,$name,$this->type));
		}else{
		$query = $this->query("INSERT INTO tax (name,taxgrpid) VALUES (?,?)",array($name,$this->type)); 	
		}
		
	//	if($query) {redirect(0);}		
		}
	}
/*
 * SELECT PARENT FROM CHILD ID
 * RETURN ARRAY id,name
 * */
//	public function parent_from_child($parentid){
//		$child=$this->fetch("SELECT id FROM tax WHERE parent=?",array($parentid));
//		$parent=$this->fetch("SELECT id,name FROM tax WHERE id=?",array($child['id']));
//		return $parent;
//	}
/*
 * GET PARENT FROM TAXGRID
 * PARENT TAX HAS CHILDREN
 * RETURN ARRAY OF CHILDREN
 * */
	public function has_children($postid, $taxgrpid){
		$taxids=$this->fetchList("taxid","post","WHERE id=$postid");
		$sel= $this->get("SELECT id FROM tax WHERE parent=$parentid");
		return !empty($sel) ? $sel : false;
	}
/*
 * parent_from_selectedchild
 *
 * */
	public function parents_ofselected_children($postid){
	//find
		$taxids=$this->fetchList("taxid","post","WHERE id=$postid");
		if(!empty($taxids)) {
			$imp = implode(',', $taxids);
			$parents= $this->fetchList("parent", "tax", "WHERE id IN ($imp)");
			if(!empty($parents)){
				return $parents;
			}else {
				return false;
			}
		}else{
			return false;
		}
	}
/*
*	TAXGROUPING OF TAXONOMIES
*	 RETURN ARRAY
* */
	public function tree(){
		$grp = $this->fetchList(array("id","name"),"taxgrp");
		$sel = $this->fetchAll("SELECT tax.*,
		taxgrp.name as taxgrpname,taxgrp.parenting,taxgrp.multiple
		FROM tax 
		LEFT JOIN taxgrp ON tax.taxgrpid=taxgrp.id");
		$grp=array();
		for($i=0;$i<count($sel);$i++){

		$grp[$sel[$i]['taxgrpid']][$sel[$i]['id']]=$sel[$i];

			//			$grp[$sel[$i]['taxgrpid']]=array();
//			if($sel[$i]['parent']==0) {
//			$grp[$sel[$i]['taxgrpid']][$sel[$i]['id']] = $sel[$i]['name'];
//		}else{
//			$grp[$sel[$i]['taxgrpid']][$sel[$i]['parent']][$sel[$i]['id']] = $sel[$i]['name'];
//		}
		}
		return $grp;
	}

/* 	public function selectinsert_tag_table($database='sys'){
		$sys=new DB();
		if(isset($_POST['tagging'])) {
		$id= $_POST["id"];
		$taginput = strtolower($_POST["tagname"]);
		$tagarray = explode(",",$taginput);
		for($i=0;$i<count($tagarray);$i++){
		$usetag = stripslashes(ltrim(rtrim($tagarray[$i])));
		if($usetag ==="") continue;
		$new_taxD=DBA::fetch_max('id','tax');
		
		if (!in_array($taginput,$this->taxNameWhereRecID))	{	
		$queryTag =$sys->insert("tax", array('id','type'), array(''.$new_taxD.'',''.$this->type.''));
		$queryTag2 =$sys->insert("tax_{$lang}", array('id','taxName'), array(''.$new_taxD.'',''.$usetag.''));
			if ($queryTag){redirect(0); }
			} else {
		echo("The ".$this->type." already exists");		
			}
		}
		}
		?>
		<form method="post"/><input name="id" type="hidden"/>
		<input type="text" name="tagname"  title="separate with comma" >
		<input name="tagging" type="submit" value="Insert <?=$this->type?>" /></form>
		 <?
	}
 */
}	
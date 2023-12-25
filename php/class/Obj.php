<?php //updated:2020-01-29 20:20:33 Obj- v.0.73 - Author:Nikos Drosakis - License: GPL License ?>
<?php 
/*
Object Class created with GD library 
as a static library
by Nikos Drosakis (c)2016
for Gaiasys.com

//!update with uploads
*/

class Obj extends Gaia{

	public function select($userid='',$clause=''){
	$idi=$userid !='' ? $userid : GSID;
	$selecti = $this->sys()->fetchAll("SELECT *	FROM obj WHERE uid=? $clause ",array($idi));
	if(empty($selecti)){return false;}else{	return $selecti; }
	}

	public function mode($filename,$mode='size'){
	if($mode=='size'){
	return filesize($filename);
	}else{
	list($width,$height) = getimagesize($filename);
	return (int)$$mode;
	}
	}

/* 	//updated!
	public static function get($img,$typ='profile',$buscat=0,$grp=2){
	global $GLOBAL;
	switch($typ){
	case 'profile':
		if (!empty($img) && link_exist(UPLOADS_ROOTPATH.$img)){
			return (UPLOADS.$img.'?'.time());

		} else {
		if ($grp==2){
		return IMAGES_PATH."icons_profile/".$GLOBAL['business_pimages'][$buscat];
			} else {
		return IMAGES_PATH."icons_profile/general.jpg".'?'.time();
		}
		}
	break;
	case 'bgimage':
		if (!empty($img) && link_exist(UPLOADS_ROOTPATH.$img)){
		return (UPLOADS.$img.'?'.time());
		} else {
			if ($grp==2){
		return IMAGES_PATH."background_profile/".$GLOBAL['business_pimages'][$buscat];
			} else {
		return IMAGES_PATH."icons_profile/general1.jpg".'?'.time();
			}
		}
	break;
	}
	} */

/*
 * INSERT AND UPDATE STATUS
 *
 * */
	public function insert($filename,$uid=1,$objgroup=1,$linkid=1,$table='',$multiple=0){
		$time=time();
		//change status if multiple ==0
//        if($this->sys()->get("multiple","objgroup","WHERE id=$grp")==0){
//            $view_list= implode(',',$this->sys()->fetchList("objid","objview","WHERE linkid=$linkid"));
//            $this->sys()->query("UPDATE obj SET status=1 WHERE uid=$uid AND id IN ($view_list)");
//        }
        //insert
        $ins=$this->sys()->query("INSERT INTO obj (filename,uid,objgroupid,created,modified) VALUES(?,?,?,?,?)",
            array($filename,$uid,$objgroup,$time,$time));
        $ins2=$this->sys()->query("UPDATE $table SET img=?,modified=? WHERE id=?",array($filename,$time,$linkid));
	}
	
	/*
	PROFILE IMG
	a) profile image file 
	b) default image link according to business category if company
	c) avatar link if selected if person
	*/
	//update added into memcached

	public function profile($userid=''){
		$uid=$userid !='' ? $userid : GSID;

		if($this->cache()->is('pimage_'.$uid)){
				return $this->cache()->get('pimage_'.$uid);
		}else{
			//check if img exists in db
			$img = $this->sys()->fetch("SELECT filename FROM obj			
			WHERE uid=$uid AND type=1 AND status=2");
				$null_link=IMAGES_PATH."icons_profile/general.jpg";
				$pimage= empty($img) ? $null_link
								: (link_exist(UPLOADS_ROOTPATH_ICON.'icon_'.$img['filename']) ? UPLOADS_ICON.'icon_'.$img['filename'] : $null_link);
				$this->cache()->set('pimage_'.$uid,$pimage);
		return $pimage;
		}
	}


	/* 
	RESIZE 	
	set PROPER SIZE to be acceptable for upload
	- set percent to resize according to original size
	- set size 
	*/
	static function resize($filename,$size=1){
	// Content type
	header('Content-Type: image/jpeg');
 	list($width,$height)= getimagesize($filename);
	// new sizes according to percent
	if (is_array($size)){
	$newwidth = $size[0];
	$newheight =$size[1];
	}else{
	$newwidth =  $width * $size;
	$newheight = $height * $size;
	}


	// Load
	$thumb = imagecreatetruecolor($newwidth, $newheight);
	$source = imagecreatefromjpeg($filename);

	// Resize
	imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	//imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	// Output and free memory
	imagejpeg($thumb);
	imagedestroy($thumb);
	}


//	/*
//	create img
//	- set $label
//	- set $color array(red,green,blue) - default is black(0,0,0)
//	- set $size
//	*/
	static function create($filename,$label="",$color=array(0,0,0),$size=array(109,110)){
	$my_img = imagecreate($size[0],$size[1]);
	$background = imagecolorallocate( $my_img, $color[0],$color[1],$color[2]);
	$text_colour = imagecolorallocate( $my_img, 255, 255, 0 );
	$line_colour = imagecolorallocate( $my_img, 128, 255, 0 );
	imagestring( $my_img, 3, 2, 25, $label, $text_colour );
	imagesetthickness ( $my_img, 2 );
	imageline( $my_img, 5, 45, 100, 45, $line_colour );

	/* header( "Content-type: image/png" );
	imagepng( $my_img );
	imagecolordeallocate( $line_color );
	imagecolordeallocate( $text_color );
	imagecolordeallocate( $background ); */

	imagejpeg($my_img, $filename);

	// Free up memory
	imagedestroy( $my_img );
echo $my_img;
	}


	static function thumbnail($path,$size=50){

	//if file link

	$ext=explode('.',$path);
	//$path = $_FILES['image']['name'];
	//$ext = pathinfo($path, PATHINFO_EXTENSION);
	$thumbnail_dir='/gaia/img/file_extensions/';
	$images_array=array('jpg','jpeg','png','tif','gif');
	if(in_array($ext[1],$images_array)){
	return ATTACHMENTS.$path;
	}else{
	return $thumbnail_dir= $thumbnail_dir.$ext[1].'.png';
	}
	}

	public function get_pie($data){
	//fill this array with your data
	$total=array_sum($data);
	for($i=0;$i<count($data);$i++)
	{
	$arc[$i]=$data[$i]*360/$total;
	}

	// create image
		$image = imagecreatetruecolor(550,550);
		$style=IMG_ARC_PIE;
		// allocate some colors
		$white    = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$gray     = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
		$darkgray = imagecolorallocate($image, 0x90, 0x90, 0x90);
		$navy     = imagecolorallocate($image, 0x00, 0x00, 0x80);
		$darknavy = imagecolorallocate($image, 0x00, 0x00, 0x50);
		$red      = imagecolorallocate($image, 0xFF, 0x00, 0x00);
		$darkred  = imagecolorallocate($image, 0x90, 0x00, 0x00);
		$colors=array($red,$gray,$navy,$red );
		$darkcolors=array($darkred,$darkgray,$darknavy,$darkred );
		$start=0;
		// make the 3D effect
		for ($i = 60; $i > 50; $i--)
		{
			for($j=0;$j<count($data);$j++)
			{
			imagefilledarc($image, 250, $i*5, 500, 250, $start, $start+$arc[$j],$darkcolors[$j], $style);
			$start=$start+$arc[$j];
			}

		}
		for($j=0;$j<count($data);$j++)
			{
		imagefilledarc($image, 250, 250, 500, 250, $start, $start+$arc[$j], $colors[$j], $style);
		$start=$start+$arc[$j];
			}

		// flush image
		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
	}

//	public function url2Image(){
//		if($_POST){
//		$url = $_POST['url'];
//		//$name = basename($url);
//		//list($txt, $ext) = explode(".", $name);
//
//		//$name = $name.".".$ext;
//		$upload = file_put_contents('uploads/'.$url,file_get_contents($url));
//		//check success
//		if($upload)  echo "Success: <a href='".UPLOADS.'01.jpg'."' target='_blank'>Check Uploaded</a>"; else "please check your folder permission";
//		}
//
//		<form method="post" >-->
//			Your URL: <input type="text" name="url" enctype="multipart/form-data"/>-->
//			<input type="submit" name="furl" />-->
//		</form>-->
 //}
//
//	//attachments files
	public function icon($file,$icon=false){
	$path_parts = pathinfo(UPLOADS.$file);
	$ext=$path_parts['extension'];
	$icons=array('doc'=>'doc','docx'=>'doc','rtf'=>'doc','pdf'=>'pdf','xls'=>'xls','csv'=>'xls','xlsx'=>'xls','zip'=>'zip','rar'=>'zip','gz'=>'zip','mp3'=>'audio','wav'=>'audio','mp4'=>'video','wma'=>'video','flv'=>'video','avi'=>'video','mpg'=>'video','html'=>'html','txt'=>'txt','ppt'=>'ppt','pptx'=>'ppt');

	$thumbs=array('jpg','png','gif','jpeg');

	if (in_array($ext,array_keys($icons))){
	return '/gaia/img/file_extensions/'.$icons[$ext].'.png';

	}elseif(in_array($ext,$thumbs)){
		return ($icon==true ? UPLOADS_ICON.'icon_'.$file : UPLOADS.$file);

	}else{
	return '/gaia/img/file_extensions/Me.png';
	}
	}


//IMAGE UPLOADER FUNCTIONS
#####  This function will proportionally resize image #####
	static function normal_resize_image($source, $destination, $image_type, $max_size, $image_width, $image_height, $quality){

	if($image_width <= 0 || $image_height <= 0){return '';} //return false if nothing to resize

	//do not resize if image is smaller than max size
	if($image_width <= $max_size && $image_height <= $max_size){
		if(self::save_image($source, $destination, $image_type, $quality)){
			return true;
		}
	}

	//Construct a proportional size of new image
	$image_scale	= min($max_size/$image_width, $max_size/$image_height);
	$new_width		= ceil($image_scale * $image_width);
	$new_height		= ceil($image_scale * $image_height);

	$new_canvas		= imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image

	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
		self::save_image($new_canvas, $destination, $image_type, $quality); //save resized image
	}

	return true;
	}

##### This function corps image to create exact square, no matter what its original size! ######
	static function crop_image_square($image_name,$source, $destination, $image_type, $square_size, $image_width, $image_height, $quality){
	if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize

	if( $image_width > $image_height ){
	//echo $image_width;
	//echo '<br/>';
	//echo $image_height;
	//echo '<br/>';
		$x_offset = 0;
		$y_offset = -(($image_width - $image_height) / 2);
//echo '<br/>';
		$s_size 	= $image_width - ($x_offset * 2);
	}else{
		$x_offset = 0;
		$y_offset = ($image_height - $image_width) / 2;
		$s_size = $image_height - ($y_offset * 2);
	}

	$new_canvas	= imagecreatetruecolor($square_size, $square_size); //Create a new true color image
	//Copy and resize part of an image with resampling
	if(imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)){

	//fill white if not png gif
	$image_info = pathinfo($image_name);
	$image_extension = strtolower($image_info["extension"]);

	//if (!in_array($image_extension,array('gif','png'))){
	$backgroundColor = imagecolorallocate($new_canvas, 255, 255, 255);
	imagefill($new_canvas, 0, 0, $backgroundColor);
	imagecopy($new_canvas,$source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size);
	//}
		self::save_image($new_canvas, $destination, $image_type, $quality);
	}

	return true;
	}

	static function save_image($source, $destination, $image_type, $quality){
	switch(strtolower($image_type)){//determine mime type
		case 'image/png':
			imagepng($source, $destination); return true; //save png file
			break;
		case 'image/gif':
			imagegif($source, $destination); return true; //save gif file
			break;
		case 'image/jpeg': case 'image/pjpeg':
			imagejpeg($source, $destination, $quality); return true; //save jpeg file
			break;
		default: return false;
	}
	}

}
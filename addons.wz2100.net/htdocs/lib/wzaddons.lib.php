<?php
include_once('../../wz2100.net/lib/global.lib.php');
include_once(dirname(__FILE__) . '/../data/addons.inc.php');

$errors = array();

function getuser()
{
	return $GLOBALS['loggedinuserid'];
}
function checkuser($otherid)
{
	global $loggedinuserid, $isreviewer;
	if ($isreviewer) return true;
	if (!$loggedinuserid) return false;
	$array1 = explode(':',$otherid);
	$array2 = explode(':',$loggedinuserid);
	return $array1[0] === $array2[0];
}
function checkisuser($otherid)
{
	global $loggedinuserid;
	if (!$loggedinuserid) return false;
	$array1 = explode(':',$otherid);
	$array2 = explode(':',$loggedinuserid);
	return $array1[0] === $array2[0];
}

function getaddontype($filename='')
{
	$category = 'mod';
	$type = 'map';
	$name = '';
	$players = 0;
	if (substr($filename,-3) != '.wz')
	{
		return array('','','','',$filename);
	}
	if (substr($filename,-7) == '.mod.wz')
	{
		$type = 'mod';
		$name = substr($filename,0,-7);
	}
	else if (substr($filename,-8) == '.gmod.wz')
	{
		$type = 'gmod';
		$name = substr($filename,0,-8);
	}
	else if (substr($filename,-7) == '.cam.wz')
	{
		$type = 'cam';
		$name = substr($filename,0,-7);
	}
	else if (preg_match('/^[0-9]{1,2}c\\-/',$filename))
	{
		$category = 'map';
		if (substr($filename,1,2) == 'c-')
		{
			$players = intval(substr($filename,0,1));
			$name = substr($filename,3,-3);
		}
		else
		{
			$players = intval(substr($filename,0,2));
			$name = substr($filename,4,-3);
		}
	}
	else
	{
		$type = 'mod';
		$name = substr($filename,0,-3);
		$filename = substr($filename,0,-3).'.mod.wz';
	}
	return array($category,$type,$players,$name,$filename);
}

function addons_alloc($file)
{
	global $_ADDONS, $errors;
	// alloc new file from a file form
	if ($file['error'] == UPLOAD_ERR_OK)
	{
		$tmp_name = $file["tmp_name"];
		$filename = $file["name"];
		$players = 0;
		$name = '';
		$id = '';
		$version = '';
		$author = @$GLOBALS['user']->data['username'];
		if (!$author) $author = 'Anonymous';
		$submitterid = getuser();
		list($category,$type,$players,$name,$filename) = getaddontype($filename);
		if (!$category)
		{
			$errors[] = 'File must be a Warzone map or mod. A mod must end in one of: .mod.wz, .gmod.wz, or .cam.wz';
			return false;
		}
		$name = str_replace('_',' ',$name);
		
		// extract version number
		while (true)
		{
			if (ctype_digit($name) || ctype_alpha($name) || strlen($name) <= 1) break;
			if (ctype_digit(substr($name,-1)) || substr($name,-1) == ' ' || substr($name,-1) == '-' || substr($name,-1) == '.')
			{
				$version = substr($name,-1).$version;
				$name = substr($name,0,-1);
			}
			else if (strtolower(substr($name,-5))=='alpha' && !ctype_alpha(substr($name,-6,1)))
			{
				$version = substr($name,-5).$version;
				$name = substr($name,0,-5);
			}
			else if (strtolower(substr($name,-4))=='beta' && !ctype_alpha(substr($name,-5,1)))
			{
				$version = substr($name,-4).$version;
				$name = substr($name,0,-4);
			}
			else if (strtolower(substr($name,-2))=='rc' && !ctype_alpha(substr($name,-3,1)))
			{
				$version = substr($name,-2).$version;
				$name = substr($name,0,-2);
			}
			else if (strtolower(substr($name,-1))=='v' && !ctype_alpha(substr($name,-2,1)))
			{
				$name = substr($name,0,-1);
				break;
			}
			else
			{
				break;
			}
		}
		if (substr($version,0,1) == ' ' || substr($version,0,1) == '-' || substr($version,0,1) == '.')
			$version = substr($version,1);
		
		$id = simplify($name);
		if (!$id)
		{
			$errors[] = 'Filename doesn\'t exist.';
			return false;
		}
		if (array_key_exists($id, $_ADDONS[$category]) || array_key_exists($id, $_ADDONS['unapproved'][$category]))
		{
			$errors[] = 'We already have a '.$category.' with the name "'.$id.'"; please rename yours.';
			return false;
		}
		if (ctype_lower($name{0})) $name{0} = strtoupper($name{0});
		
		$newaddon = array(
			'fullid' => "unapproved/$category/$id",
			'id' => $id,
			'name' => $name,
			'version' => $version,
			'author' => $author,
			'submitter' => $author,
			'submitterid' => $submitterid,
			'submittime' => time(),
			'desc' => '',
			'htmldesc' => '',
			'timecreated' => time(),
			'category' => $category,
			'type' => $type,
			'pic' => '',
			'morepics' => array(),
			'license' => false,
			'filename' => $filename,
			'dir' => "files/unapproved/$category/$id/",
			'players' => $players,
			'tileset' => false,
			'approved' => false,
			'unfinished' => true,
		);
		
		mkdir("files/unapproved/$category/$id/");
		if (!move_uploaded_file($tmp_name, "files/unapproved/$category/$id/$filename"))
		{
			$errors[] = 'Error uploading file.';
			return false;
		}
		chmod("files/unapproved/$category/$id/$filename", 0777);
		$_ADDONS['unapproved'][$category][$id] = $newaddon;
		persist_save('_ADDONS', dirname(__FILE__) . '/../data/addons.inc.php');
		return $newaddon;
	}
	$errors[] = 'Error uploading file.';
	return false;
}
function &addon_setapproved($fullid, $approved) // deprecated
{
	global $_ADDONS;
	if (!is_string($fullid)) $fullid = $fullid['fullid'];
	$addon =& getaddon($fullid);
	if (!$addon) return false;
	$approved = ($approved?true:false);
	$category = $addon['category'];
	$id = $addon['id'];
	$newfullid = ($approved?'':'unapproved/').$addon['category'].'/'.$addon['id'];
	if ($newfullid == $addon['fullid']) return $addon; // already done

	//$newdir = "files/$newfullid/";

	//echo '<br />'.$addon['dir'].': '.(is_dir($addon['dir'])?'DIR EXISTS':'DIR DOESN\'T EXIST');
	//echo '<br />'.$newdir.': '.(is_dir($newdir)?'DIR EXISTS':'DIR DOESN\'T EXIST');

	if (getaddon($newfullid))
	{
		if ($approved)
		{
			addon_spambin($newfullid);
		}
		else
		{
			addon_spambin($fullid);
			return getaddon($newfullid);
		}
	}

	//echo '<br />'.$addon['dir'].': '.(is_dir($addon['dir'])?'DIR EXISTS':'DIR DOESN\'T EXIST');
	//echo '<br />'.$newdir.': '.(is_dir($newdir)?'DIR EXISTS':'DIR DOESN\'T EXIST');

	$newdir = "files/$newfullid/";
	rename($addon['dir'], $newdir);
	$addon['fullid'] = $newfullid;
	$addon['dir'] = $newdir;
	$addon['approved'] = $approved;

	//echo '<br />'.$addon['dir'].': '.(is_dir($addon['dir'])?'DIR EXISTS':'DIR DOESN\'T EXIST');
	//echo '<br />'.$newdir.': '.(is_dir($newdir)?'DIR EXISTS':'DIR DOESN\'T EXIST');

	if ($approved)
	{
		unset($addon['unfinished']);
		$_ADDONS[$category][$id] = $addon;
		unset($_ADDONS['unapproved'][$category][$id]);
		return $_ADDONS[$category][$id];
	}
	else
	{
		$_ADDONS['unapproved'][$category][$id] = $addon;
		unset($_ADDONS[$category][$id]);
		return $_ADDONS['unapproved'][$category][$id];
	}
}
function &addon_setapproval($fullid, $approval = 'approved')
{
	global $_ADDONS;
	if (!is_string($fullid)) $fullid = $fullid['fullid'];
	$addon =& getaddon($fullid);
	if (!$addon) return false;
	$category = $addon['category'];
	$id = $addon['id'];

	if (!$approval) $approval = 'unapproved';
	$approved = ($approval=='approved');
	if (!in_array($approval, array('approved','unapproved','rejected'))) return false;

	$newfullid = ($approved?'':$approval.'/').$addon['category'].'/'.$addon['id'];
	if ($newfullid == $addon['fullid']) return $addon; // already done

	//$newdir = "files/$newfullid/";

	//echo '<br />'.$addon['dir'].': '.(is_dir($addon['dir'])?'DIR EXISTS':'DIR DOESN\'T EXIST');
	//echo '<br />'.$newdir.': '.(is_dir($newdir)?'DIR EXISTS':'DIR DOESN\'T EXIST');

	if (getaddon($newfullid))
	{
		if ($approved)
		{
			addon_spambin($newfullid);
		}
		else
		{
			addon_spambin($fullid);
			return getaddon($newfullid);
		}
	}

	//echo '<br />'.$addon['dir'].': '.(is_dir($addon['dir'])?'DIR EXISTS':'DIR DOESN\'T EXIST');
	//echo '<br />'.$newdir.': '.(is_dir($newdir)?'DIR EXISTS':'DIR DOESN\'T EXIST');

	$newdir = "files/$newfullid/";
	rename($addon['dir'], $newdir);
	$addon['fullid'] = $newfullid;
	$addon['dir'] = $newdir;
	$addon['approved'] = $approved;

	//echo '<br />'.$addon['dir'].': '.(is_dir($addon['dir'])?'DIR EXISTS':'DIR DOESN\'T EXIST');
	//echo '<br />'.$newdir.': '.(is_dir($newdir)?'DIR EXISTS':'DIR DOESN\'T EXIST');

	if ($approved)
	{
		unset($addon['unfinished']);
		$_ADDONS[$category][$id] = $addon;
		unset($_ADDONS['unapproved'][$category][$id]);
		return $_ADDONS[$category][$id];
	}
	else
	{
		$_ADDONS['unapproved'][$category][$id] = $addon;
		unset($_ADDONS[$category][$id]);
		return $_ADDONS['unapproved'][$category][$id];
	}
}
function &addon_duplicatetounapproved($fullid)
{
	global $_ADDONS;
	if (!is_string($fullid)) $fullid = $fullid['fullid'];
	$addon =& getaddon($fullid);
	if (!@$addon['approved']) return false;
	if (getaddon('unapproved/'.$fullid)) return getaddon('unapproved/'.$fullid);

	$category = $addon['category'];
	$id = $addon['id'];
	$newfullid = 'unapproved/'.$addon['fullid'];
	$newdir = "files/$newfullid/";

	if (!($dh = opendir($addon['dir']))) return false;
	if (!mkdir($newdir)) return false;
	while (($file = readdir($dh)) !== false)
	{
		if ($file == '.' || $file == '..') continue;
		copy($addon['dir'].$file, $newdir.$file);
	}
	closedir($dh);

	$newaddon = $addon;
	$_ADDONS['unapproved'][$category][$id] = $newaddon;
	$_ADDONS['unapproved'][$category][$id]['approved'] = false;
	$_ADDONS['unapproved'][$category][$id]['fullid'] = $newfullid;
	$_ADDONS['unapproved'][$category][$id]['dir'] = $newdir;
	return $_ADDONS['unapproved'][$category][$id];
}
function addon_spambin($fullid)
{
	global $_ADDONS;
	// this is a true delete, not a reject, so use with caution!
	if (!is_string($fullid)) $fullid = $fullid['fullid'];
	$addon =& getaddon($fullid);
	if (!($dh = opendir($addon['dir']))) return false;
	while (($file = readdir($dh)) !== false)
	{
		if ($file != '.' && $file != '..')
			unlink($addon['dir'].$file);
	}
	closedir($dh);
	rmdir($addon['dir']);
	if ($addon['approved']) unset($_ADDONS[$addon['category']][$addon['id']]);
	else if (!$addon['approved']) unset($_ADDONS['unapproved'][$addon['category']][$addon['id']]);
}
function addon_update()
{
	persist_save('_ADDONS', dirname(__FILE__) . '/../data/addons.inc.php');
}

function getyouraddons()
{
	global $_ADDONS;
	$youraddons = array();
	foreach ($_ADDONS['map'] as $addon) if (checkisuser($addon['submitterid'])) $youraddons[] = $addon['fullid'];
	foreach ($_ADDONS['mod'] as $addon) if (checkisuser($addon['submitterid'])) $youraddons[] = $addon['fullid'];
	return $youraddons;
}
function getyourunapprovedaddons($reviewer = false)
{
	global $_ADDONS;
	$youraddons = array();
	foreach ($_ADDONS['unapproved']['map'] as $addon) if (checkisuser($addon['submitterid']) || $reviewer) $youraddons[] = $addon['fullid'];
	foreach ($_ADDONS['unapproved']['mod'] as $addon) if (checkisuser($addon['submitterid']) || $reviewer) $youraddons[] = $addon['fullid'];
	return $youraddons;
}
function getaddonsforapproval()
{
	global $_ADDONS;
	$youraddons = array();
	foreach ($_ADDONS['unapproved']['map'] as $addon) if (@$addon['rating'] >= 1 && !@$addon['unfinished']) $youraddons[] = $addon['fullid'];
	foreach ($_ADDONS['unapproved']['mod'] as $addon) if (@$addon['rating'] >= 1 && !@$addon['unfinished']) $youraddons[] = $addon['fullid'];
	return $youraddons;
}
function getaddonsforrating()
{
	global $_ADDONS;
	$youraddons = array();
	foreach ($_ADDONS['unapproved']['map'] as $addon) if (!isset($addon['rating']) && !@$addon['unfinished']) $youraddons[] = $addon['fullid'];
	foreach ($_ADDONS['unapproved']['mod'] as $addon) if (!isset($addon['rating']) && !@$addon['unfinished']) $youraddons[] = $addon['fullid'];
	return $youraddons;
}
function getaddons()
{
	global $_ADDONS;
	$youraddons = array();
	foreach ($_ADDONS['map'] as $addon) $youraddons[] = $addon['fullid'];
	foreach ($_ADDONS['map'] as $addon) $youraddons[] = $addon['fullid'];
	return $youraddons;
}
function getunapprovedaddons()
{
	global $_ADDONS;
	$youraddons = array();
	foreach ($_ADDONS['unapproved']['map'] as $addon) $youraddons[] = $addon['fullid'];
	foreach ($_ADDONS['unapproved']['mod'] as $addon) $youraddons[] = $addon['fullid'];
	return $youraddons;
}
function addontype($addon)
{
	if ($addon['type'] == 'map')
		return ''.$addon['players'].'-player map';
	else if ($addon['type'] == 'mod')
		return 'Skirmish mod';
	else if ($addon['type'] == 'gmod')
		return 'Global mod';
	else if ($addon['type'] == 'cam')
		return 'Campaign mod';
}

function &getaddon($fullid)
{
	global $_ADDONS; $null = NULL;
	if (!is_string($fullid)) $fullid = $fullid['fullid'];
	$unapproved = false;
	if (substr($fullid,0,11) == 'unapproved/')
	{
		$unapproved = true;
		$fullid = substr($fullid,11);
	}
	@list($category, $id) = explode('/',$fullid);
	if (!$category || !$id) return $null;
	if (!$unapproved && @$_ADDONS[$category][$id]) return $_ADDONS[$category][$id];
	if ($unapproved && @$_ADDONS['unapproved'][$category][$id]) return $_ADDONS['unapproved'][$category][$id];
	return $null;
}
function &getaddonforediting($fullid)
{
	$null = NULL;
	$curaddon = &getaddon($fullid);
	if (!checkuser($curaddon['submitterid'])) return $null;
	if (isreviewer() || !$curaddon['approved']) return $curaddon;
	return addon_duplicatetounapproved($curaddon);
}

function rendererrors()
{
	global $errors;
	$render = '';
	foreach ($errors as $error)
	{
		$render .= '<p style="border:2px solid #FF0000;padding:2px 4px;margin:1em 0;"><strong>Error:</strong> '.$error.'</p>';
	}
	$errors = array();
	return $render;
}

// makes $name lowercase, then strips out all non-alphanumeric characters
// if an array is passed, numbers will be added until an ID that doesn't exist in the array is found
function simplify($name,$array=array())
{
	$name = strtr($name, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz");
	$in = ($name[0]=='*');
	$name = preg_replace('/[^a-z0-9]+/','',$name);
	$i = 1;
	if ($name) $id = 'internal-'.$name;
	$id = $name;
	while (in_array($id,$array)) $id = $name.(++$i);
	return $id;
}





/**
* Creates square thumbnail images; the thumbnails retain their aspect ratio and
* their background color can be set (''=transparent).
* Default is 128x128 transparent.
* Usage: mkthumbdir(string $dir,int $size,string $color)
* Example: mkthumbdir('.',128,'FFFFFF')
* @author Zarel <aesoft.org>
*/

function mkthumbdir($dir='.',$size=128,$color='')
{
  if ($color!=='' && (strlen($color)!=6 || !ctype_xdigit($color)) ||
    intval($size)<=0) /* $size or $color is not valid */
    return FALSE;
  $size=intval($size);$color=lower($color); /*sanitize $size and $color */
  
  $files = @opendir('.') or die('Cannot open current directory.');
  while (false !== ($file = @readdir($files)))
  {
    if (substr($file,0,1)!=='.') /* skip '.', '..', and any hidden file */
      mkthumb($dir.'/'.$file,$size,$color);
  }
  @closedir($files);
  return TRUE;
}

function mkthumb($file,$size=64,$color='',$output=FALSE)
{
  /** sanity checks **/
  if ($color!=='' && (strlen($color)!=6 || !ctype_xdigit($color))
  || intval($size)<=0) /* $size or $color is not valid */
    return FALSE;
  $size=intval($size); $color=lower($color); $ic=hexdec($color);
  if (is_dir($file) || preg_match('/^_thumb_[0-9]+(_[0-9a-f]{6})?_/',
  basename($file)) || (substr($file,-4)!='.gif' &&substr($file,-4)!='.png'
  && substr($file,-5)!='.jpeg' && substr($file,-4)!='.jpg'))
    return FALSE; /* skip non-images and thumbs */
  $thumbn = dirname($file).'/_thumb_'.$size.($color?'_'.lower($color):
  '').'_'.substr(basename($file),0,strrpos(basename($file),'.')).'.gif';
  if (file_exists($thumbn))
  {
    if ($output) readfile($thumbn);
    return FALSE;
  }
  
  /** import to GD **/
  if (substr($file,-4)=='.gif')
  {  if (!$srci = @imagecreatefromgif($file)) return FALSE;}
  else if (substr($file,-4)=='.png')
  {  if (!$srci = @imagecreatefrompng($file)) return FALSE;}
  else
  {  if (!$srci = @imagecreatefromjpeg($file)) return FALSE;}
  
  /** make thumbnail **/
  $srcw = imagesx($srci); $srch = imagesy($srci); /* get img dimensions */
  $desti = imagecreatetruecolor($size,$size);
  if ($color)
    imagefilledrectangle($desti, 0,0, $size,$size,
    imagecolorallocate($desti, 0xFF&($ic>>0x10),0xFF&($ic>>0x8), 0xFF&$ic));
  else /* transparent */
  {
    imagefilledrectangle($desti, 0,0, $size,$size,
    imagecolorallocate($desti, 253,252,255));
    imagecolortransparent($desti, imagecolorallocate($desti,253,252,255));
  }
  if ($srcw >$srch) /* landscape */
    imagecopyresampled($desti,$srci,0, round(($srcw-$srch)*$size/$srcw/2),
    0,0,$size,round($srch*$size/$srcw), $srcw,$srch);
  else /* portrait */
    imagecopyresampled($desti,$srci, round(($srch-$srcw)*$size/$srch/2),0,
    0,0,round($srcw*$size/$srch),$size,$srcw,$srch);
  imagedestroy($srci);

  if ($output)
  {
    header("Content-type: image/gif");
    imagegif($desti) or die('Error');
  }
  else imagegif($desti,$thumbn);
  imagedestroy($desti);
  return TRUE;
}

function mkthumb_novpad($file,$size=64,$color='',$output=FALSE)
{
  /** sanity checks **/
  if ($color!=='' && (strlen($color)!=6 || !ctype_xdigit($color))
  || intval($size)<=0) /* $size or $color is not valid */
    return FALSE;
  $size=intval($size); $color=lower($color); $ic=hexdec($color);
  if (is_dir($file) || preg_match('/^_thumbnv_[0-9]+(_[0-9a-f]{6})?_/',
  basename($file)) || (substr($file,-4)!='.gif' &&substr($file,-4)!='.png'
  && substr($file,-5)!='.jpeg' && substr($file,-4)!='.jpg'))
    return FALSE; /* skip non-images and thumbs */
  $thumbn = dirname($file).'/_thumbnv_'.$size.($color?'_'.lower($color):
  '').'_'.substr(basename($file),0,strrpos(basename($file),'.')).'.gif';
  if (file_exists($thumbn))
  {
    if ($output) readfile($thumbn);
    return FALSE;
  }
  
  /** import to GD **/
  if (substr($file,-4)=='.gif')
  {  if (!$srci = @imagecreatefromgif($file)) return FALSE;}
  else if (substr($file,-4)=='.png')
  {  if (!$srci = @imagecreatefrompng($file)) return FALSE;}
  else
  {  if (!$srci = @imagecreatefromjpeg($file)) return FALSE;}
  
  /** make thumbnail **/
  $srcw = imagesx($srci); $srch = imagesy($srci); /* get img dimensions */
  $sizex = $sizey = $size;
  if ($srcw > $srch) /* landscape */
    $sizey = round($srch*$size/$srcw);

  $desti = imagecreatetruecolor($sizex,$sizey);
  if ($color)
    imagefilledrectangle($desti, 0,0, $sizex,$sizey,
    imagecolorallocate($desti, 0xFF&($ic>>0x10),0xFF&($ic>>0x8), 0xFF&$ic));
  else /* transparent */
  {
    imagefilledrectangle($desti, 0,0, $sizex,$sizey,
    imagecolorallocate($desti, 253,252,255));
    imagecolortransparent($desti, imagecolorallocate($desti,253,252,255));
  }
  if ($srcw >$srch) /* landscape */
    imagecopyresampled($desti,$srci,0,0,
    0,0,$size,$sizey, $srcw,$srch);
  else /* portrait */
    imagecopyresampled($desti,$srci, round(($srch-$srcw)*$size/$srch/2),0,
    0,0,round($srcw*$size/$srch),$size,$srcw,$srch);
  imagedestroy($srci);

  if ($output)
  {
    header("Content-type: image/gif");
    imagegif($desti) or die('Error');
  }
  else imagegif($desti,$thumbn);
  imagedestroy($desti);
  return TRUE;
}

function lower($str)
{
  return strtr($str, "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
  "abcdefghijklmnopqrstuvwxyz");
}

/* if (@$_REQUEST['f'])
{
  if (!@$_REQUEST['s']) $_REQUEST['s']=128;
  if (!@$_REQUEST['c']) $_REQUEST['c']='';
  mkthumb($_REQUEST['f'],$_REQUEST['s'],$_REQUEST['c'],!isset($_REQUEST['savetofile']));
  if (isset($_REQUEST['savetofile'])) echo 'done';
} */

//mkthumbdir() or die('Error.');

//echo 'Done.';

$acted = false;
if (@$_POST['act'] == 'editaddon')
{
	$curaddon = &getaddon(@$_REQUEST['fullid']);
	if (!$curaddon) $errors[] = 'Addon not found';
	else if (!checkuser($curaddon['submitterid'])) $errors[] = 'Access denied.';
	else
	{
		$curaddon = &getaddonforediting($curaddon);
		if (!simplify(@$_POST['name']))
			$errors[] = 'Name must contain at least one letter or number.';
		else
			$curaddon['name'] = trim($_POST['name']);
		if (!@$_POST['license'])
			$errors[] = 'Please select a license.';
		else if (!@$_POST['certify'] && !$curaddon['license'])
			$errors[] = 'You must certify that you have the legal right to upload this '.$curaddon['category'].'.';
		else
			$curaddon['license'] = $_POST['license'];
		if (!trim(@$_POST['desc']))
			$errors[] = 'You must give a description of your '.$curaddon['category'].'.';
		if (!$curaddon['pic'])
		{
			if ($curaddon['category'] == 'map')
				$errors[] = 'You must include a preview image of your map.';
			else
				$errors[] = 'You must include a logo or screenshot for your mod.';
		}
		$curaddon['version'] = trim($_POST['version']);
		$curaddon['author'] = trim($_POST['author']);
		$curaddon['desc'] = trim($_POST['desc']);
		$curaddon['htmldesc'] = nl2br(htmlentities(trim($_POST['desc'])));
		if (!@$curaddon['author']) $curaddon['author'] = 'Anonymous';
		$curaddon['assignedcopyright'] = @$_POST['assigncopyright']?true:false;
		if (@$_POST['tileset'])
		{
			$curaddon['tileset'] = simplify($_POST['tileset']);
			if ($curaddon['tileset'] == 'none' || !$curaddon['tileset']) $curaddon['tileset'] = false;
		}
		if (@$_POST['status'] && isreviewer())
		{
			if ($_POST['status']=='spambin' && isadmin())
			{
				addon_spambin($curaddon);
				$errors = array();
			}
			else
				$curaddon = addon_setapproved($curaddon, $_POST['status']=='approved');
		}
		if (!$errors)
		{
			$curaddon['unfinished'] = false;
			$acted = $curaddon;
		}
		persist_save('_ADDONS', dirname(__FILE__) . '/../data/addons.inc.php');
	}
}



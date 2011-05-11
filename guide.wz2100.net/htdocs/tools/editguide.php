<?php

chdir('..');

include_once 'tools/wzguide.lib.php';
@include_once 'tools/guide.inc.php';

if (!$isadmin) die('Access denied');

$id = $_GET['p'];

//'<div style="max-width:800px;"><table width="100%" border="0" cellspacing="0" 
//<tr valign="top"><th class="l nb" width="35%">&nbsp;</th><th class="c" width="9%">Price</th><th class="l" width="56%" style="padding-left:20px;">Effect</th></tr>
//<tr><td class="l" valign="middle">Hardened MG Bullets</a></td><td class="c price">$18</td><td class="l"><ul style="padding-top:5px;"><li>Upgrades machinegun damage to 130%</li></ul></td></tr>'

function guide2html($str)
{
	$str = str_replace('<rtable>','<!--research table--><div style="max-width:800px;"><table width="100%" border="0" cellspacing="0">
<tr valign="top"><th class="l nb" width="35%">&nbsp;</th><th class="c" width="9%">Price</th><th class="l" width="56%" style="padding-left:20px;">Effect</th></tr>', $str);
	$str = str_replace('</rtable>','</table></div><!--research table-->', $str);
	$str = preg_replace('/\|\| ([^\|]+) \|\| ([^\|]+) \|\| ([^\|]+) \|\|/', '<tr><td class="l" valign="middle">$1</td><td class="c price">$2</td><td class="l"><ul style="padding-top:5px;">$3</ul></td></tr>', $str);
	$str = preg_replace('/<forumimg ([0-9]+)>/','<p><a href="http://forums.wz2100.net/download/file.php?id=$1&mode=view"><img src="http://forums.wz2100.net/download/file.php?id=$1&t=1" alt="" /></a></p>', $str);
	return $str;
}
function html2guide($str)
{
	$str = str_replace('<!--research table--><div style="max-width:800px;"><table width="100%" border="0" cellspacing="0">
<tr valign="top"><th class="l nb" width="35%">&nbsp;</th><th class="c" width="9%">Price</th><th class="l" width="56%" style="padding-left:20px;">Effect</th></tr>', '<rtable>', $str);
	$str = str_replace('</table></div><!--research table-->', '</rtable>', $str);
	$str = preg_replace('/<tr><td class="l" valign="middle">([^<]+)<\/td><td class="c price">([^<]+)<\/td><td class="l"><ul style="padding-top:5px;">(.+?)<\/ul><\/td><\/tr>/', '|| $1 || $2 || $3 ||', $str);
	$str = preg_replace('/<p><a href="http\:\/\/forums\.wz2100\.net\/download\/file\.php\?id=([0-9]+)&mode=view"><img src="http\:\/\/forums\.wz2100\.net\/download\/file\.php\?id=([0-9]+)&t=1" alt="" \/><\/a><\/p>/', '<forumimg $1>', $str);

	$str = preg_replace('/<h3><a name="([^"]*)">([^<]*)<\/a><\/h3>/', '<h3 id="$1">$2</h3>', $str);
	$str = preg_replace('/<h4><a name="([^"]*)">([^<]*)<\/a><\/h4>/', '<h4 id="$1">$2</h4>', $str);
	$str = preg_replace('/<span class="lkey">([^<]*)<\/span>/', '<kbd class="lkey">$1</kbd>', $str);
	$str = preg_replace('/<span class="key">([^<]*)<\/span>/', '<kbd>$1</kbd>', $str);
	return $str;
}

$_POST['text'] = stripslashes(@$_POST['text']);

if ($id == '.htaccess' && $text = $_POST['text'])
{
	$fp = fopen('.htaccess', 'w');
	fwrite($fp, $text) or fclose($fp);
	fclose($fp);
}
else if ($text = $_POST['text'])
{
	$text = guide2html(stripslashes($text));
	$_POST['titlebar'] = str_replace('>>','<span class="arrow">&raquo;</span>',stripslashes($_POST['titlebar']));
	$_POST['title'] = stripslashes($_POST['title']);
	$reply = 'Saved.';
	$success = true;
	if ($id[1] == '/')
		$msg = &$guide[$id[0]]['subdirs'][substr($id,2)];
	else
		$msg = &$guide[$id];
	if (!isset($msg) && $gen) $msg['gen'] = $gen;
	
	{
		//include 'lib/DiffEngine.php';
		//$formatter = new DiffFormatter();
		//echo '[';
		//echo $formatter->format(new Diff($msg['text'], $text));
		//die();

		$msg['text'] = $text;
	}
	
	unset($msg['cachedsb']);
	if (isset($_POST['title'])) $msg['title'] = $_POST['title'];
	if (isset($_POST['titlebar'])) $msg['titlebar'] = $_POST['titlebar'];
	persist_update('guide') || $reply = 'Error.';
	if ($step = @$msg['gen']) include 'generate.php';
	if ($msg['autogen'])
	{
		$wdata = '<'.'?php include_once \'tools/wzguide.lib.php\'; $msg = $guide[\''.$id.'\']; ?'.'>'.file_get_contents('tools/template.html');
		$wdata = str_replace('{TITLE}',$msg['title'],$wdata);
		$wdata = str_replace('<title>Home - ','<title>',$wdata);
		$wdata = str_replace('"{ROOT}"','"./"',$wdata);
		$wdata = str_replace('{ROOT}','',$wdata);
		if ($id != 'faq') $wdata = str_replace('{NAV}',' class="c"',$wdata);
		$wdata = str_replace('{LEFTNAV}',leftnav($id),$wdata);
		$wdata = str_replace('{TITLEBAR}',$msg['titlebar'],$wdata);
		$wdata = str_replace('{CONTENT}',guide($id,'php').'<'.'?php echo comments(\''.$id.'\'); ?'.'>',$wdata);
		if ($id == 'faq')
		{
		$wdata = str_replace('<li class="tab-guide cur">','<li class="tab-guide">',$wdata);
		$wdata = str_replace('<li class="tab-faq">','<li class="tab-faq cur">',$wdata);
		}
		$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
		$fp = fopen($id.'.php', 'w');
		fwrite($fp, $wdata) or fclose($fp);
		fclose($fp);
	}
}

$id = $_GET['p'];

if (isset($_GET['mb']) && !$id && $id = simplify($_POST['idname']))
{
	$_POST['titlebar'] = str_replace('>>','<span class="arrow">&raquo;</span>',stripslashes($_POST['titlebar']));
	$_POST['title'] = stripslashes($_POST['title']);
	$guide[$id] = array('autogen' => TRUE, 'title' => $_POST['title']?$_POST['title']:'-', 'titlebar' => $_POST['titlebar']?$_POST['titlebar']:'-');
	unset($guide[$id]['cachedsb']);
	persist_update('guide');
	$wdata = '<'.'?php include_once \'r.inc.php\'; $msg = $guide[\''.$id.'\']; ?'.'>'.file_get_contents('template.html');
	$wdata = str_replace('{TITLE}','<'.'?php echo $msg[\'title\']; ?'.'>',$wdata);
	$wdata = str_replace('"{ROOT}"','"./"',$wdata);
	$wdata = str_replace('{ROOT}','',$wdata);
	$wdata = str_replace('{NAV}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<'.'?php echo leftnav(\''.$id.'\'); ?'.'>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<'.'?php echo $msg[\'titlebar\']; ?'.'>',$wdata);
	$wdata = str_replace('{CONTENT}','<'.'?php echo guide(\''.$id.'\'),comments(\''.$id.'\'); ?'.'>',$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	$fp = fopen($id.'.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
}
if (isset($_GET['sb']))
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Sidebar</title>
</head>
<body>
<div style="padding:5px">
<?php
foreach ($guide as $i => $tlitem)
{
	echo '<a href="editguide.php?p='.$i.'" target="_top"'.($i==$id?' style="font-weight:bold;"':'').'>'.(@$tlitem['autogen']?'<em>'.$i.'</em>':$i).'</a><br />';
	if (@$tlitem['subdirs']) foreach ($tlitem['subdirs'] as $j => $slitem)
	{
		echo '- <a href="editguide.php?p='.$i.'/'.$j.'" target="_top"'.($i.'/'.$j==$id?' style="font-weight:bold;"':'').'>'.$j.'</a><br />';
	}
}
echo '<br /><a href="editguide.php" target="_top"'.(!$id?' style="font-weight:bold;"':'').'>New</a>';
?>
</div>
</body>
</html>
<?php
}
else if (isset($_GET['mb']) && !$id)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Edit</title>
</head>
<body>
<div><?php echo $reply; ?>&nbsp;</div>
<form action="editguide.php?mb&amp;p=<?php echo $id; ?>" method="post">
ID: <input type="text" name="idname" size="30" /><br />
Title: <input type="text" name="title" size="50" /><br />
Titlebar: <input type="text" name="titlebar" size="80" /><br />
<input type="submit" value="New" />
</form>
</body>
</html>
<?php
}
else if (isset($_GET['mb']))
{
	if ($id[1] == '/')
		$msg = &$guide[$id[0]]['subdirs'][substr($id,2)];
	else
		$msg = &$guide[$id];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Edit</title>
</head>
<body>
<div><?php echo @$reply; ?>&nbsp;</div>
<form action="editguide.php?mb&amp;p=<?php echo $id; ?>" method="post">
<?php

if ($id=='.htaccess')
{

?>
<textarea name="text" rows="30" cols="90"><?php echo htmlspecialchars(file_get_contents('.htaccess')); ?></textarea>
<?php

}
else
{

?>
<?php if (isset($msg['title'])) echo 'Title: <input type="text" name="title" value="'.htmlspecialchars($msg['title']).'" size="50" /><br />'; ?>
<?php if (isset($msg['titlebar'])) echo 'Titlebar: <input type="text" name="titlebar" value="'.htmlspecialchars(str_replace('<span class="arrow">&raquo;</span>','>>',$msg['titlebar'])).'" size="80" /><br />'; ?>
<textarea name="text" rows="30" cols="90"><?php echo htmlspecialchars(html2guide(guide($id, false, false))); ?></textarea>
<?php

}

?>
<input type="submit" value="Save" />
</form>
</body>
</html>
<?php
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Edit <?php echo $id; ?></title>
  </head>
  <frameset cols="220,*" border="0">
    <frame name="actbar" src="editguide.php?sb&amp;p=<?php echo $id; ?>" frameborder="0" marginwidth="0" marginheight="0" noresize="noresize" scrolling="auto" />
    <frame name="main" src="editguide.php?mb&amp;p=<?php echo $id; ?>" frameborder="0" marginwidth="0" marginheight="0" noresize="noresize" scrolling="auto" />
    <noframes>
      Error: Frames-less version not yet supported.
    </noframes>
  </frameset>
</html>
<?php
}
?>
<?php

include_once 'lib/wzaddons.lib.php';

$uploadtype = strval(@$_REQUEST['type']);
$fullid = strval(@$_REQUEST['fullid']);
$uploaded = false;
$curaddon = false;

if ($uploadtype != 'wz' && $uploadtype != 'pic' && $uploadtype != 'morepics') die('Unknown uploadtype.');
$curaddon = &getaddon($fullid);
if ($uploadtype != 'wz' && !$curaddon) die('Addon not found.');
if ($curaddon && !checkuser($curaddon['submitterid'])) die('Access denied');
if ($_FILES)
{
	if ($uploadtype == 'wz' && $curaddon)
	{
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK)
		{
			$filename = $_FILES['file']['name'];
			@list($category,$type,$players,$name,$filename) = getaddontype($filename);
			if ($type != $curaddon['type'] || $players != $curaddon['players'])
			{
				$errors[] = 'New version must be the same map/mod type as previous version.';
			}
			else
			{
				$curaddon = &getaddonforediting($curaddon);
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $curaddon['dir'].$filename))
				{
					$errors[] = 'Upload failed';
				}
				else
				{
					$curaddon['filename'] = $filename;
					if ($curaddon['rating'] < 1)
					{
						unset($curaddon['ratingvotes']);
						unset($curaddon['rating']);
					}
					addon_update();
					$uploaded = $curaddon;
				}
			}
		}
	}
	else if ($uploadtype == 'wz')
	{
		$uploaded = addons_alloc($_FILES['file']);
	}
	else if ($uploadtype == 'pic' || $uploadtype == 'morepics')
	{
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK)
		{
			$file = strtolower($_FILES['file']['name']);
			$filetype = '';
			if (substr($file,-4)=='.gif') $filetype = 'gif';
			if (substr($file,-4)=='.png') $filetype = 'png';
			if (substr($file,-5)=='.jpeg' || substr($file,-4)=='.jpg') $filetype = 'jpg';
			if (!$filetype)
			{
				$errors[] = 'You must upload a GIF, PNG, or JPEG image.';
			}
			else
			{
				$curaddon = &getaddonforediting($curaddon);
				$file = 'pic.'.$filetype;
				if (!move_uploaded_file($_FILES['file']['tmp_name'], 'files/temp/'.$file))
				{
					$errors[] = 'Upload failed.';
				}
				else
				{
					if (!mkthumb('files/temp/'.$file,80))
					{
						$errors[] = 'Your uploaded image was corrupt.';
						unlink("files/temp/$file");
					}
					else
					{
						$picnum = ($uploadtype == 'pic'?'':''.(count($curaddon['morepics'])+1));
						rename("files/temp/$file",$curaddon['dir'].'pic'.$picnum.'.'.$filetype) || unlink("files/temp/$file");
						rename("files/temp/_thumb_80_pic.gif",$curaddon['dir']."_thumb_80_pic".$picnum.".gif") || unlink("files/temp/_thumb_80_pic.gif");
						if ($uploadtype == 'pic')
						{
							$curaddon['pic'] = $file;
						}
						else
						{
							$curaddon['morepics'][] = 'pic'.$picnum.'.'.$filetype;
						}
						addon_update();
						$uploaded = $curaddon;
					}
				}
			}
		}
	}
}
else if (@$_POST['act'] == 'delmorepic')
{
	$picnum = intval($_REQUEST['picnum']);
	
	unlink($curaddon['dir'].'_thumb_80_pic'.($picnum+1).'.gif');
	unlink($curaddon['dir'].$curaddon['morepics'][$picnum]);
	foreach ($curaddon['morepics'] as $i => $pic)
	{
		if ($i > $picnum)
		{
			$newpic = 'pic'.($i).substr($pic,-4);
			rename($curaddon['dir'].'_thumb_80_pic'.($i+1).'.gif',$curaddon['dir'].'_thumb_80_pic'.($i).'.gif');
			rename($curaddon['dir'].$curaddon['morepics'][$i],$curaddon['dir'].$newpic);
			$curaddon['morepics'][$i] = $newpic;
		}
	}
	array_splice($curaddon['morepics'],$picnum,1);
	
	addon_update();
	
	$uploaded = $curaddon;
	$uploadtype = 'morepics';
}

?>
<!DOCTYPE html PUBLIC>
<html>
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	    <meta http-equiv="content-language" content="en" /> 
		<title>File upload form</title>
		<script type="text/javascript" language="javascript" src="http://static.wz2100.net/theme/jquery-1.4.1.min.js"></script>
		<style>
<!--
html,body
{
	margin: 0;
	padding: 0;
	overflow: hidden;
	font-family: Verdana, sans-serif;
	font-size: 12pt;
}
form
{
	display: block;
	margin: 0;
	padding: 2px;
}
input
{
	display: block;
	margin: 0;
	padding: 0;
}
img
{
	vertical-align: middle;
}
-->
		</style>
		<script>
<!--

function upload()
{
	if (!($('#file').val())) return false;
	$('#loading').css('display','block');
	$('#fileform').submit();
	return true;
}
function action()
{
	$('#loading').html('<img src="images/loading.gif" alt="" /> Loading...');
	$('#loading').css('display','block');
}

//-->
		</script>
	</head>
	<body>
<?php
if ($uploaded || $errors)
{
?>
<script>
<?php
	if ($uploaded)
	{
?>
parent.fileform_callback_<?php echo $uploadtype,($curaddon?'':'_new') ?>((<?php echo json_encode($uploaded); ?>), '<?php echo $fullid; ?>');
<?php
	}
	if ($errors)
	{
?>
parent.fileform_errors((<?php echo json_encode(rendererrors()); ?>));
<?php
	}
?>
</script>
<?php
}
?>
		<div id="loading" style="display:none;padding-bottom:100px;"><img src="/images/loading.gif" alt="" /> Uploading...</div>
		<form action="/fileform.php?type=<?php echo $uploadtype; ?>&amp;fullid=<?php echo $fullid; ?>" method="post" enctype="multipart/form-data" id="fileform">
			<input type="file" name="file" id="file" onChange="upload();" />
		</form>
<?php
if ($uploadtype == 'morepics')
{
?>
<form action="/fileform.php?type=morepics&amp;fullid=<?php echo $curaddon['fullid']; ?>" target="morepics" method="post" id="delmorepic"><input type="hidden" name="act" value="delmorepic" /><input type="hidden" name="picnum" id="picnum" value="" /></form>
<?php
}
?>
	</body>
</html>
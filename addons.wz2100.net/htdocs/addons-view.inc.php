<?php

$fullid = strval(@$_REQUEST['fullid']);
if (!$fullid) $fullid = strval(@$_REQUEST['sub']);

$addon =& getaddon($fullid);
if (!$addon) die('404 '.$fullid);

?>
<h1>
	<?php echo $addon['name']; ?>
</h1>
<div class="addon">
	<?php
	
	if ($addon['pic'])
	{
		mkthumb_novpad($addon['dir'].$addon['pic'],320);
		echo '<a href="/'.$addon['dir'].$addon['pic'].'" rel="lytebox['.$addon['fullid'].']" onclick="myLytebox.start(this, false, false); return false" class="pic"><img src="/'.$addon['dir'].'_thumbnv_320_pic.gif"  alt="" /></a>';
	}
	else echo '<span class="nopic">No picture available</span>';
	
	?>
	<?php if (checkuser($addon['submitterid'])) echo '<div style="float:right"><a href="/submit/'.$addon['fullid'].'">Edit</a></div>'; ?>
	<div class="lp"><strong><?php echo htmlentities($addon['name']); ?></strong> <?php echo htmlentities($addon['version']); ?> <em class="addontype"><?php echo addontype($addon); ?></em> <em class="byline">by <?php if ($addon['author'] == @$addon['submitter']) echo '<a href="http://forums.wz2100.net/memberlist.php?mode=viewprofile&u='.$addon['submitterid'].'">'.htmlentities($addon['author']).'</a>'; else echo htmlentities($addon['author']).' <small>(Submitter: <a href="http://forums.wz2100.net/memberlist.php?mode=viewprofile&u='.$addon['submitterid'].'">'.htmlentities(@$addon['submitter']?$addon['submitter']:'[view]').'</a>)</small>'; ?></em></div>

<?php if (@$addon['rating']) { ?>
	<div class="rating">
		<em>Rating:</em>
		<span style="float:left;width:80px;height:16px;background:transparent url(/images/star-empty.png)"><span style="display:block;width:<?php echo intval($addon['rating']*16); ?>px;height:16px;background:transparent url(/images/star.png)"></span></span>
		<strong><?php $strrating = strval(round($addon['rating'],1)); if (strlen($strrating) == 1) $strrating .= '.'; if (strlen($strrating) == 2) $strrating .= '0'; echo $strrating; ?></strong>
	</div>
<?php } ?>

	<div class="lp"><?php
	echo $addon['htmldesc'];
?></div>
	<div class="lp downloadbtn"><a href="/<?php echo htmlentities('download'.substr($addon['dir'],5).$addon['filename']); ?>"><em>Download</em> <code><?php echo htmlentities($addon['filename']); ?></code></a></div>
<div style="clear:both;"></div></div>
<?php
if ($addon['morepics'])
{
?>
<h2>Screenshots</h2>
<div class="addon">
	<ul class="gallery">
<?php
	foreach ($addon['morepics'] as $i => $morepic)
	{
?>
		<li><a href="/<?php echo $addon['dir'],$morepic; ?>" rel="lytebox[<?php echo $addon['fullid']; ?>]" onclick="myLytebox.start(this, false, false); return false"><img src="/<?php echo $addon['dir'],'_thumb_80_pic',($i+1),'.gif'; ?>" alt="" /></a></li>
<?php
	}
?>
	</ul><div style="font-size:1px;overflow:visible;height:1px;clear:both;"></div>
</div>
<?php
}
?>
<h2 id="installing">Installing</h2>
<p>
<em>See:</em> <a href="http://wz2100.net/faq#HowdoIinstallamap">How do I install a map or mod?</a>
</p>
<?php if (@$addon['ratingvotes'] || isreviewer()) { ?>
<h2 id="reviews">Reviews</h2>
<?php
	if (@$_POST['rating'])
	{
		$id = @$_POST['id'];
		$name = @$_POST['name'];
		if (!$id) $id = $user_id;
		if (!$name) $name = $user->data['username'];
		if ($id != $user_id && !isadmin())
		{
			echo '<strong>You do not have permission to edit that review; it is not yours.</strong>';
		}
		else
		{
			if (!@$addon['rating']) @$addon['rating'] = 0;
			
			$editedtext = $_POST['review'];
			$addon['ratingvotes'][''.$id]['reviewsrc'] = $editedtext;
			$editedtext = str_replace("\r",'',$editedtext);
			$editedtext = str_replace("\n\n",'</p><p>',$editedtext);
			$editedtext = str_replace("\n",'<br />',$editedtext);
			$addon['ratingvotes'][''.$id]['review'] = '<p>'.$editedtext.'</p>';
			$addon['ratingvotes'][''.$id]['rating'] = 0+$_POST['rating'];
			$addon['ratingvotes'][''.$id]['name'] = $name;
			
			$total = 0;
			foreach ($addon['ratingvotes'] as $vote) $total += $vote['rating'];
			$addon['rating'] = $total/count($addon['ratingvotes']);
			
			addon_update();
		}
	}

?>

<?php if (@$addon['ratingvotes']) foreach ($addon['ratingvotes'] as $i => $vote) { ?>
<div class="content-box">
<?php

if (isadmin()) echo '<textarea style="display:none" id="reviewsrc-',$i,'">',htmlentities(@$vote['reviewsrc']),'</textarea><button onclick="edit(',$i,',\'',$vote['name'],'\',',$vote['rating'],');return false" style="float:right">Edit</button>';
?>
	<div class="rating" style="float:left;">
		<em><a href="http://forums.wz2100.net/memberlist.php?mode=viewprofile&u=<?php echo $i ?>"><?php echo $vote['name'] ?></a> rates this <?php echo $addon['type'] ?>:</em>
		<span style="float:left;width:80px;height:16px;background:transparent url(/images/star-empty.png)"><span style="display:block;width:<?php echo intval($vote['rating']*16); ?>px;height:16px;background:transparent url(/images/star.png)"></span></span>
		<strong><?php $strrating = strval(round($vote['rating'],1)); if (strlen($strrating) == 1) $strrating .= '.'; if (strlen($strrating) == 2) $strrating .= '0'; echo $strrating; ?></strong>
	</div>
	<div style="clear:both">
		<?php if (@$vote['review']) echo $vote['review']; ?>
	</div>
</div>
<?php
}
?>
<?php if (isreviewer()) { ?>
<div id="reviewbox" class="content-box"><h2>Add/edit your review</h2>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>#reviews" method="post">
<?php if (isadmin()) { ?>
	<p>User ID: <input type="text" class="textbox" id="vote-id" name="id" value="<?php echo $user_id; ?>" size="6" /></p>
<?php } ?>
	<p><input type="text" class="textbox" size="12" id="vote-name" name="name" value="<?php echo $user->data['username']; ?>"<?php if (!isadmin()) echo ' disabled="disabled"'; ?> />'s rating: <input type="text" class="textbox" size="5" id="vote-rating" name="rating" value="<?php echo htmlentities(@$addon['ratingvotes'][''.$user_id]['rating']); ?>" /> <em>(Required; must be between 1.0 and 5.0)</em></p>
	<p><textarea class="textbox" cols="60" rows="10" id="vote-review" name="review"><?php echo htmlentities(@$addon['ratingvotes'][''.$user_id]['reviewsrc']); ?></textarea></p>
	<p><input type="submit" value="Add/Edit" /></p>
</form></div>
<?php } } ?>
<script>
<!--

$('#reviewbox').after('<div id="addreviewbox" class="content-box" style="padding-bottom:1em"><button onclick="addreview()">Add review</button></div>').hide();
$('#reviewbox p:last-child').append('<button onclick="edit(0)">Cancel</button>');

function addreview()
{
	edit(<?php echo $user_id; ?>,'<?php echo $user->data['username']; ?>','<?php echo htmlentities(@$addon['ratingvotes'][''.$user_id]['rating']); ?>');
}
function edit(id,name,rating)
{
	if (id == <?php echo $user_id; ?>)
	{
		$('#addreviewbox').hide();
	}
	else
	{
		$('#addreviewbox').show();
	}
	if (id==0)
	{
		$('#reviewbox').hide();
	}
	else
	{
		$('#reviewbox').show();
		document.getElementById('vote-id').value = id;
		document.getElementById('vote-name').value = name;
		document.getElementById('vote-rating').value = rating;
		document.getElementById('vote-review').value = document.getElementById('reviewsrc-'+id)?document.getElementById('reviewsrc-'+id).value:'';
	}
}

//-->
</script>
<h2 id="license">License</h2>
<p>
<?php

switch ($addon['license'])
{
	case 'CC-0':
		echo '<a href="http://creativecommons.org/publicdomain/zero/1.0/" target="_blank"><img src="/images/license-CC-0.png" alt="CC-0" /></a>';
		break;
	case 'CC-BY-3.0+/GPLv2+':
		echo '<a href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><img src="/images/license-CC-BY-3.0.png" alt="CC-BY-3.0" /></a>';
		break;
	case 'CC-BY-SA-3.0+/GPLv2+':
		echo '<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><img src="/images/license-CC-BY-SA-3.0.png" alt="CC-BY-SA-3.0" /></a>';
		break;
	default:
		echo $addon['license'];
}

?>
</p>
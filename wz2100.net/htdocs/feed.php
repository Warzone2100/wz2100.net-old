<?php
header('Content-type: application/atom+xml');

// Session management (from phpbb)
define('PHPBB_ROOT_PATH', '../forums.wz2100.net/htdocs/');
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = 'php';
include($phpbb_root_path.'common.php');
include($phpbb_root_path.'includes/functions_display.php');
include($phpbb_root_path.'includes/bbcode.php');

$newsforumid = 1;

$sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_poster, t.topic_first_poster_name, t.topic_replies, p.post_text, p.bbcode_uid, p.bbcode_bitfield, p.post_attachment, p.post_id
	FROM ' . TOPICS_TABLE . " t
		LEFT JOIN " . POSTS_TABLE . " p
			ON t.topic_first_post_id = p.post_id
	WHERE t.forum_id = $newsforumid
		AND t.topic_type IN (" . POST_NORMAL . ")
		AND t.topic_status IN (0,1)
	ORDER BY t.topic_id DESC";
$result = $db->sql_query_limit($sql, 20, 0);

$topics = array();
while ($row = $db->sql_fetchrow($result))
{
	$topics[] = $row;
}
$db->sql_freeresult($result);

echo '<';

$user->data = array (
  'user_id' => '913',
  'user_type' => '0',
  'group_id' => '6',
  'user_permissions' => 'zik0zjzik0zjzijoao
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
 
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
 
 
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
 
zik0zi000000
 
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000
zik0zi000000',
  'user_perm_from' => '0',
  'user_ip' => '75.73.229.208',
  'user_regdate' => '1199396112',
  'username' => 'Zarel',
  'username_clean' => 'zarel',
  'user_password' => '$H$9M6scQbfgf2eEEZ9.TFis/fsXZWN6U.',
  'user_passchg' => '0',
  'user_pass_convert' => '0',
  'user_email' => 'zarelsl@gmail.com',
  'user_email_hash' => '152864984717',
  'user_birthday' => '29- 3-1990',
  'user_lastvisit' => '1272056430',
  'user_lastmark' => '1265592264',
  'user_lastpost_time' => '1272075527',
  'user_lastpage' => '../../htdocs/index.php',
  'user_last_confirm_key' => '',
  'user_last_search' => '1271932934',
  'user_warnings' => '0',
  'user_last_warning' => '0',
  'user_login_attempts' => '0',
  'user_inactive_reason' => '0',
  'user_inactive_time' => '0',
  'user_posts' => '4221',
  'user_lang' => 'en',
  'user_timezone' => '-6.00',
  'user_dst' => '1',
  'user_dateformat' => '|F jS, Y|, g:i a',
  'user_style' => '5',
  'user_rank' => '6',
  'user_colour' => '961616',
  'user_new_privmsg' => '0',
  'user_unread_privmsg' => '0',
  'user_last_privmsg' => '1272056430',
  'user_message_rules' => '0',
  'user_full_folder' => '-3',
  'user_emailtime' => '0',
  'user_topic_show_days' => '0',
  'user_topic_sortby_type' => 't',
  'user_topic_sortby_dir' => 'd',
  'user_post_show_days' => '0',
  'user_post_sortby_type' => 't',
  'user_post_sortby_dir' => 'a',
  'user_notify' => '0',
  'user_notify_pm' => '1',
  'user_notify_type' => '2',
  'user_allow_pm' => '1',
  'user_allow_viewonline' => '1',
  'user_allow_viewemail' => '1',
  'user_allow_massemail' => '1',
  'user_options' => '231295',
  'user_avatar' => '913_1256667468.gif',
  'user_avatar_type' => '1',
  'user_avatar_width' => '64',
  'user_avatar_height' => '64',
  'user_sig' => '',
  'user_sig_bbcode_uid' => '',
  'user_sig_bbcode_bitfield' => '0',
  'user_from' => 'Minnesota, USA',
  'user_icq' => '',
  'user_aim' => 'ZarelSL',
  'user_yim' => 'ZarelSL',
  'user_msnm' => 'shadowstriker9@msn.com',
  'user_jabber' => 'zarelsl@googlesemailprovidernospam.com',
  'user_website' => 'http://guide.wz2100.net/',
  'user_occ' => 'Student',
  'user_interests' => '',
  'user_actkey' => '',
  'user_newpasswd' => '',
  'user_form_salt' => '005a22aeb75123cf',
  'user_new' => '0',
  'user_reminded' => '0',
  'user_reminded_time' => '0',
  'session_id' => 'a28e7bcad46406874ebbb9b22fa90320',
  'session_user_id' => '913',
  'session_forum_id' => '0',
  'session_last_visit' => '1272056430',
  'session_start' => '1272074100',
  'session_time' => '1272082979',
  'session_ip' => '160.94.88.150',
  'session_browser' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; en-US) AppleWebKit/533.6 (KHTML, like Gecko) Chrome/5.0.379.0 Safari/533.6',
  'session_forwarded_for' => '',
  'session_page' => '../../htdocs/5200',
  'session_viewonline' => '1',
  'session_autologin' => '1',
  'session_admin' => '1',
  'is_registered' => true,
  'is_bot' => false,
);

?>?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en-en">
<link rel="self" type="application/atom+xml" href="http://wz2100.net/feed.atom" />

<title>Warzone 2100 News</title>
<link href="http://wz2100.net/" />
<updated><?php echo gmdate("Y-m-d\TH:i:s\Z", (int)$topics[0]['topic_time']); ?></updated>

<author><name><![CDATA[Warzone 2100 Project]]></name></author>
<id>http://wz2100.net/feed.atom</id>
<?php

foreach ($topics as $row) if ($row['post_attachment'])
{
	$attach_list[] = (int) $row['post_id'];
}

if (sizeof($attach_list))
{
	$sql = 'SELECT *
		FROM ' . ATTACHMENTS_TABLE . '
		WHERE ' . $db->sql_in_set('post_msg_id', $attach_list) . '
			AND in_message = 0
		ORDER BY filetime DESC, post_msg_id ASC';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$attachments[$row['post_msg_id']][] = $row;
	}
	$db->sql_freeresult($result);
}

foreach ($topics as $row)
{
	// Parse the message and subject
	$message = censor_text($row['post_text']);

	// Second parse bbcode here
	if ($row['bbcode_bitfield']) // INTENTIONAL
	{
		$user->setup('viewtopic');
		$bbcode_bitfield = base64_decode($row['bbcode_bitfield']);
		$bbcode = new bbcode(base64_encode($bbcode_bitfield));
		$bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
	}

	$message = bbcode_nl2br($message);
	$message = smiley_text($message);

	$update_count = array();
	if (!empty($attachments[$row['post_id']]))
	{
		parse_attachments($newsforumid, $message, $attachments[$row['post_id']], $update_count);
	}
	
	$message = str_replace('"../forums.wz2100.net/htdocs/','"http://forums.wz2100.net/',$message);

	//$message = str_replace('<a href="http://forums.wz2100.net/download/file.php?id=','<a class="img" rel="lytebox[attachments]" onclick="myLytebox.start(this, false, false); return false;" href="http://forums.wz2100.net/download/file.php?id=',$message)
?>
<entry>
<author><name><![CDATA[<?php echo $row['topic_first_poster_name']; ?>]]></name></author>
<updated><?php echo gmdate("Y-m-d\TH:i:s\Z", (int)$row['topic_time']); ?></updated>
<id>http://wz2100.net/news/<?php echo $row['topic_id']; ?></id>
<link href="http://wz2100.net/news/<?php echo $row['topic_id']; ?>"/>
<title type="html"><![CDATA[<?php echo $row['topic_title']; ?>]]></title>
 
<category term="News" scheme="http://wz2100.net/news" label="News"/>
<content type="html" xml:base="http://wz2100.net/news/<?php echo $row['topic_id']; ?>"><![CDATA[
	<p class="byline">by <strong><?php echo $row['topic_first_poster_name']; ?></strong> <small>on <?php echo date('Y F j', (int)$row['topic_time']) ?></small></p>
	<?php echo $message; ?>
	<p><a href="http://forums.wz2100.net/viewtopic.php?f=1&amp;t=<?php echo $row['topic_id']; ?>">(<?php echo $row['topic_replies']; ?> comment<?php if ((int)$row['topic_replies'] != 1) echo 's'; ?>)</a></p>
]]></content>
</entry>
<?php
}
?>
</feed>
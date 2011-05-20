<?php
// Settings
include_once(dirname(__FILE__) . '/settings.inc.php');

// Zarels persist library
include_once(dirname(__FILE__) . '/persist.lib.php');

// phpBB Session handler
define('PHPBB_ROOT_PATH', dirname(__FILE__) . '/../forums.wz2100.net/htdocs/');
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = 'php';
require_once($phpbb_root_path.'common.php');
require_once($phpbb_root_path.'includes/functions_display.php');
require_once($phpbb_root_path.'includes/functions_user.php');
require_once($phpbb_root_path . 'includes/bbcode.php');

$protocol = 'http://';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
{
    $protocol = 'https://';
}

// boo magic quotes
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

// Session management (from phpbb)
$user->session_begin();
$auth->acl($user->data);

$isadmin = group_memberships($settings['administrators'], $user->data['user_id'], true);
$isreviewer = ($isadmin ? true : group_memberships($settings['reviewers'], $user->data['user_id'], true));

$username = $user->data['username'];

$loggedinuserid = $user->data['user_id'];
$user_id = $user->data['user_id'];
if (!$user->data['is_registered']) $loggedinuserid = 0;

function isadmin() { return $GLOBALS['isadmin']; }
function isreviewer() { return $GLOBALS['isadmin'] or $GLOBALS['isreviewer']; }

function print_header($active_page)
{
    global $protocol, $user;
    
    echo "\t" . '<div class="overall-header-home"><div id="overall-header">' . "\n";
    echo "\t\t" . '<div><a href="' . $protocol . 'www.wz2100.net"><span><img src="' . $protocol . 'static.wz2100.net/theme/warzone2100.png" id="overall-logo" width="217" height="100" alt="Warzone 2100" /></span></a></div>' . "\n";
    echo "\t\t\t" . '<ul>';
    echo '<li class="tab-home' . ($active_page == 'home' ? ' cur' : '') . '"><a href="' . $protocol . 'www.wz2100.net/" title="About Warzone 2100"><span>Home</span></a></li>';
    echo '<li class="tab-download' . ($active_page == 'download' ? ' cur' : '') . '"><a href="'. $protocol . 'www.wz2100.net/download" title="Free download"><span>Download</span></a></li>';
    echo '<li class="tab-addons' . ($active_page == 'addons' ? ' cur' : '') . '"><a href="' . $protocol . 'addons.wz2100.net/" title="Maps and mods"><span>Addons</span></a></li>';
    echo '<li class="tab-faq' . ($active_page == 'faq' ? ' cur' : '') . '"><a href="' . $protocol . 'guide.wz2100.net/faq" title="Frequently Asked Questions"><span>FAQ</span></a></li>';
    echo '<li class="tab-guide' . ($active_page == 'guide' ? ' cur' : '') . '"><a href="' . $protocol . 'guide.wz2100.net/" title="User manual"><span>Guide</span></a></li>';
    echo '<li class="tab-forum' . ($active_page == 'forum' ? ' cur' : '') . '"><a href="' . $protocol . 'forums.wz2100.net/" title="Community forums"><span>Forum</span></a></li>';
    echo '<li class="tab-dev' . ($active_page == 'dev' ? ' cur' : '') . '"><a href="' . $protocol . 'developer.wz2100.net/" title="Development resources"><span>Development</span></a></li>';
    echo '</ul>' . "\n";
    echo "\t\t" . '<div class="overall-header-shadow"></div>' . "\n";
    echo "\t\t" . '</div>' . "\n";
    echo "\t" . '</div>' . "\n";
    

    if ($user->data['user_unread_privmsg'])
    {
      echo '<div class="warzone-alert"><a href="' . $protocol . 'forums.wz2100.net/ucp.php?i=pm&folder=inbox">You have <strong>',$user->data['user_unread_privmsg'],'</strong> unread message',($user->data['user_unread_privmsg']==1?'':'s'),'</a></div>';
    }
}

function print_footer()
{
    global $protocol;
    
    echo "\t" . '<div id="g_foot" class="warzone-footer">' . "\n";
    echo "\t\t" . '<ul>' . "\n";
    echo "\t\t\t" . '<li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/contact">Contact</a>' . "\n";
    echo "\t\t\t" . '</li><li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/feed.atom">RSS</a>' . "\n";
    echo "\t\t\t" . '</li><li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/site-policy">Terms of Service</a>' . "\n";
    echo "\t\t\t" . '</li><li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/privacy-policy">Privacy</a>' . "\n";
    echo "\t\t\t" . '</li><li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/imprint">Imprint</a>' . "\n";
    echo "\t\t\t" . '</li><li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/credits">Credits</a>' . "\n";
    echo "\t\t\t" . '</li><li>' . "\n";
    echo "\t\t\t\t" . '<a href="' . $protocol . 'www.wz2100.net/license">License</a>' . "\n";
    echo "\t\t\t" . '</li>' . "\n";
    echo "\t\t" . '</ul>' . "\n";
    echo "\t" . '</div>' . "\n";
}

?>

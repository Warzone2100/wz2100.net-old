<?php

/********************************************************
 ** Persistence library                                **
 ** Version 1.1.1 - Released 2010 Apr 18               **
 ** By Guangcong Luo <Zarel>                           **
 ** released under public domain / CC0                 **
 ** http://creativecommons.org/licenses/zero/1.0/      **
 ********************************************************
 *
 * Meant for PHP 4.0.4 and up. I've tried to make it work in
 * PHP 3, but I've never tested it, so use it in PHP 3 at
 * your own risk.
 *
 **[ Description ]***************************************
 *
 * persist_update('variable_name') puts $variable_name into
 * variable_name.inc.php. So the next time you include
 * variable_name.inc.php, it will create $variable_name which
 * contains exactly what it contained the last time you called
 * persist_update().
 *
 * Since you'll probably want to store more than one variable, use
 * $variable_name as an array.
 *
 * Make sure PHP has permissions to edit variable_name.inc.php
 * (If all else fails, CHMOD to 777).
 *
 * 'variable_name' can be anything.
 *
 * Example code:
 * <?php
 *  include 'persist.lib.php';
 *
 *  echo 'You are visitor #'.(++$_PERSIST['hitcounter']).'.';
 *  persist_update();
 * ?>
 *
 * Example code (custom variable name):
 * <?php
 *  include 'persist.lib.php';
 *  include 'includes/storage.inc.php';
 *
 *  echo 'You are visitor #'.(++$STORAGE['hitcounter']).'.';
 *  persist_update('STORAGE', 'includes/storage.inc.php');
 * ?>
 *
 * If a script does not need to update the value of $_PERSIST, it can
 * simply include persist.inc.php instead of persist.lib.php .
 *
 * The first example is useful if you are using one persist variable
 * in one file, while the second is useful if you are using multiple
 * persist variables in multiple files.
 *
 **[ Function reference ]********************************
 *
 *  persist_update(name, path)
 *   Updates stored $_PERSIST.
 *   Both parameters are optional.
*   name - the name of the variable.
 *          Default: '_PERSIST'
 *   path - the path (RELATIVE TO persist.lib.php) to the file storing
 *          the variable.
 *          Default: '[name].inc.php'
 *                   [name] will be lowercase, and a preceding '_' will
 *                   be removed.
 *
 ********************************************************/

@include_once substr(__FILE__,0,strrpos(__FILE__,'/')+1).'persist.inc.php';

// Updates $_PERSIST
// Returns true if successful, false if unsuccessful.
function persist_update($name='', $path='')
{
  if (!$name) $name = '_PERSIST';
  if (!$path) $path = strtolower(substr($name,0,1)==='_'?substr($name,1):$name).'.inc.php';;
  if (substr($path,0,1)!='/') $path = substr(__FILE__,0,strrpos(__FILE__,'/')+1).$path;
  // Open persist.inc.php and start editing it
  if (!is_writable($path)) return false;
  $res = @fopen($path,"w");
  if (!$res) return false;
  fwrite($res,"<?php\n\$".$name." = ".persist_tophp($GLOBALS[$name]).";\n?>\n");
  fclose($res);
  return true;
}

// Returns a PHP representation of a variable. Kind of like the opposite of eval().
// e.g. persist_tophp("It's a string!") = "'It\'s a string!'"
//      persist_tophp(12) = "12";
function persist_tophp($var, $pre='')
{
  if (is_null($var)) // NULL
    return 'NULL';
  if (is_bool($var)) // Boolean
    return ($var?'TRUE':'FALSE');
  if (is_int($var) || is_float($var)) // Number
    return ''.$var;
  if (is_string($var)) // String
    return "'".php_escape($var)."'";
  if (is_array($var)) //Array
  {
    if (empty($var)) return 'array()';
    $buf = "array(\n";
    $nleft = count($var); $i = -1; reset($var);
    // Recurse (Whee!)
    while (($cur = each($var)) !== FALSE)
    {
      $buf .= $pre."\t";
      if (!is_int($cur[0]))
      {  $buf .= "'".php_escape($cur[0])."' => ";
        $i = FALSE;
      }
      else if ($i===FALSE || $cur[0] != ++$i) 
      {  $buf .= $cur[0].' => ';
        $i = ($i!==FALSE&&$cur[0]>$i?$cur[0]:FALSE);
      }
      $buf .= persist_tophp($cur[1], $pre."\t");
      if (--$nleft) $buf .= ',';
      $buf .= "\n";
    }
    return $buf.$pre.')';
  }
  return "unserialize('".php_escape(serialize($var))."')";
}

function php_escape($str)
{
  return strtr($str,array("\\" => "\\\\", "'" =>"\\'"));
}

?>
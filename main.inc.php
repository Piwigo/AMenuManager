<?php
/*
Plugin Name: Advanced Menu Manager
Version: 2.1.0
Description: Gestion avancée du menu / Advanced management of menu
Plugin URI: http://piwigo.org
Author: Piwigo team
Author URI: http://piwigo.org
*/

/*
--------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@grum.fr
    website  : http://photos.fr
    PWG user : http://forum.piwigo.org/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
--------------------------------------------------------------------------------

:: HISTORY

| release | date       |
| 2.0.0b  | 2008/07/27 | * initial release with own blocks classes
| 2.0.0   | 2008/10/23 | * first release for piwigo's blocks classes
| 2.1.0   | 2009/07/26 | * add a functionality : random image can be changed
|         |            |   every x seconds (from 0.5 to 60)
|         |            | * bug resolved : random image block is displayed only
|         |            |   if user have accessibility to more than 0 images
|         |            |   random images are choosen in the accessible images for
|         |            |   a user (permission + level)
|         |            |   (cf. post:107877 on french forum)
|         |            |   (cf. topic:14374 on french forum)
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |


:: TO DO

--------------------------------------------------------------------------------

:: NFO
  AMM_AIM : classe to manage plugin integration into plugin menu
  AMM_AIP : classe to manage plugin admin pages
  AMM_PIP : classe to manage plugin public integration

--------------------------------------------------------------------------------
*/

// pour faciliter le debug - make debug easier :o)
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', true);

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('AMM_DIR' , basename(dirname(__FILE__)));
define('AMM_PATH' , PHPWG_PLUGINS_PATH . AMM_DIR . '/');

define('AMM_VERSION' , '2.1.0'); // => ne pas oublier la version dans l'entête !!

global $prefixeTable, $page;


if(defined('IN_ADMIN'))
{
  //AMM admin part loaded and active only if in admin page
  include_once("amm_aim.class.inc.php");
  $obj = new AMM_AIM($prefixeTable, __FILE__);
  $obj->init_events();
  set_plugin_data($plugin['id'], $obj);
}
else
{
  //AMM public part loaded and active only if in public page
  include_once("amm_pip.class.inc.php");
  $obj = new AMM_PIP($prefixeTable, __FILE__);
  set_plugin_data($plugin['id'], $obj);
}


?>

<?php
/*
Plugin Name: Advanced Menu Manager
Version: 2.1.3
Description: Gestion avancée du menu / Advanced management of menu
Plugin URI: http://piwigo.org
Author: Piwigo team
Author URI: http://piwigo.org
*/

/*
--------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@grum.fr
    website  : http://photos.grum.fr
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
| 2.1.1   | 2009/07/27 | * random picture is preloaded before the first ajax call
|         |            |   assuming the display of a thumbnail even if javascript
|         |            |   is disabled on the browser
|         |            |   (cf. post:116807 on french forum)
|         |            | * give the possibility to choose between an automatic
|         |            |   and a fixed height for the block menu
|         |            |   (cf. post:116804 on french forum)
|         |            | * compatibility with Sylvia theme
|         |            |   (cf. post:116800 on french forum)
| 2.1.2   | 2009/11/16 | * adding new translations
|         |            |    - es_ES
|         |            |    - hu_HU (thx to sámli)
| 2.1.3   | 2009/11/24 | * mantis:1285
|         |            |   move the js for "random image" in the the footer
|         |            |   (having the js inside the <dl> tag was not w3c
|         |            |   compliant)
|         |            | * mantis:1132
|         |            |   Allowing order management for items in Piwigo's core blocks
|         |            | * mantis:1133
|         |            |   Allowing to group content from Piwigo's core blocks
|         |            |
|         |            |
|         |            |
|         |            |
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

define('AMM_VERSION' , '2.1.3'); // => ne pas oublier la version dans l'entête !!

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

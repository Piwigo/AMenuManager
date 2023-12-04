<?php
/*
Plugin Name: Advanced Menu Manager
Version: 3.2.16
Description: Gestion avancée du menu / Advanced management of menu
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=250
Author: Piwigo team
Author URI: http://piwigo.org
Has Settings: true
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
|         |            |
| 2.0.0   | 2008/10/23 | * first release for piwigo's blocks classes
|         |            |
| 2.1.0   | 2009/07/26 | * add a functionality : random image can be changed
|         |            |   every x seconds (from 0.5 to 60)
|         |            |
|         |            | * bug resolved : random image block is displayed only
|         |            |   if user have accessibility to more than 0 images
|         |            |   random images are choosen in the accessible images
|         |            |   for a user (permission + level)
|         |            |   (cf. post:107877 on french forum)
|         |            |   (cf. topic:14374 on french forum)
|         |            |
| 2.1.1   | 2009/07/27 | * random picture is preloaded before the first ajax
|         |            |   call assuming the display of a thumbnail even if
|         |            |   javascript is disabled on the browser
|         |            |   (cf. post:116807 on french forum)
|         |            |
|         |            | * give the possibility to choose between an automatic
|         |            |   and a fixed height for the block menu
|         |            |   (cf. post:116804 on french forum)
|         |            |
|         |            | * compatibility with Sylvia theme
|         |            |   (cf. post:116800 on french forum)
|         |            |
| 2.1.2   | 2009/11/16 | * adding new translations
|         |            |    - es_ES
|         |            |    - hu_HU (thx to sámli)
|         |            |
| 2.1.3   | 2009/11/24 | * mantis: feature 1285
|         |            |   move the js for "random image" in the the footer
|         |            |   (having the js inside the <dl> tag was not w3c
|         |            |   compliant)
|         |            |
|         |            | * mantis: feature 1132
|         |            |   Allowing order management for items in Piwigo's core
|         |            |   blocks
|         |            |
|         |            | * mantis: feature 1133
|         |            |   Allowing to group content from Piwigo's core blocks
|         |            |
|         |            | * mantis: feature 1278
|         |            |   Allowing to manage access to menu items with a right
|         |            |   management system
|         |            |
|         |            | * mantis: feature 1100
|         |            |   Random picture : compatibility with theme 'montblanc'
|         |            |
| 2.1.4   | 2009/11/29 | * mantis: feature 1299
|         |            |   Allows to manage access for the 'Admin' users
|         |            |
|         |            | * mantis: feature 1298
|         |            |   Users 'Webmaster' aren't managed
|         |            |
|         |            | * mantis: feature 1297
|         |            |   AMM don't works properly if a block 'menu' or
|         |            |   'specials' is hidden
|         |            |
| 2.1.5   | 2009/12/15 | * mantis: feature 1331
|         |            |   JS code used to manage the random picture is always
|         |            |   loaded even if there is no menubar
|         |            |
|         |            | * adding new translations
|         |            |    - zh_CN (thx mzs777)
|         |            |
|         |            | * update translations
|         |            |    - hu_HU (thx to sámli)
|         |            |
| 2.1.6   | 2009/12/19 | * mantis: feature 1336
|         |            |   Error message about an undefined var 'tabsheet' on
|         |            |   the admin panel
|         |            |
| 2.2.0   | 2010/03/28 | * updated for Piwigo 2.1 compatibility
|         |            |
|         |            | * mantis: feature 1384
|         |            |   Problem of length of title field in the custom menu
|         |            |   module
|         |            |
|         |            | * mantis: bug 1476
|         |            |   Error message on login screen
|         |            |
|         |            | * mantis: bug 1541
|         |            |   Items order is not respected in admin pages
|         |            |
| 3.0.0   | 2011/01/09 | * mantis: feature 1296
|         |            |   . add permissions for managing personal menu
|         |            |
|         |            | * mantis: feature 1477
|         |            |   . Possibility to pre-select the "random images"
|         |            |
|         |            | * mantis: bug 1680
|         |            |   . Warning if a new lang is added in Piwigo
|         |            |
|         |            | * mantis: feature 1709
|         |            |   . Change title links by sub tabs
|         |            |
|         |            | * mantis: feature 1723
|         |            |   . Display links using user access right
|         |            |
|         |            | * mantis: bug 1776
|         |            |   . Unable to set access for the administrator
|         |            |
|         |            | * mantis: bug 1910
|         |            |   . Incompatibility with Internet Explorer
|         |            |     (partially fixed : works, but lloks a little bit
|         |            |      ugly)
|         |            |
|         |            | * mantis: feature 2052
|         |            |   . Convert album to menu
|         |            |
|         |            | * mantis: feature 2128
|         |            |   . Random picture : preload a set of picture
|         |            |
|         |            | * mantis: feature 2129
|         |            |   . User & group access management is not consistent
|         |            |
|         |            | * plugin core rewrited
|         |            |
| 3.0.1   | 2011/01/31 | * mantis: feature 2157
|         |            |   . Personalised blocks : title & content are inverted
|         |            |
|         |            | * mantis: feature 2158
|         |            |   . Album to menu : error message about number of
|         |            |     pictures
|         |            |
|         |            | * mantis: feature 2159
|         |            |   . Update process : users acess is not managed on links
|         |            |
|         |            | * mantis: feature 2162
|         |            |   . Personalised blocks : when adding a new block,
|         |            |     previous title & content are not reseted
|         |            |
|         |            | * mantis: feature 2163
|         |            |   . Update process : error message on gallery side
|         |            |
|         |            | * mantis: bug 2165
|         |            |   . Database schema not completely updated
|         |            |
| 3.0.2   | 2011/02/01 | * mantis: bug 2166
|         |            |   . Error message on gallery side about
|         |            |             create_table_add_character_set()
|         |            |
| 3.0.3   | 2011/02/07 | * mantis: bug 2166
|         |            |   . Error message on gallery side about
|         |            |             create_table_add_character_set()
|         |            |
|         |            | * mantis: bug 2182
|         |            |   . links and personnal blocks : double quote are not
|         |            |     correctly managed
|         |            |
|         |            | ===> note: the release 3.0.3 was never officially
|         |            |            published
|         |            |
| 3.1.0   | 2011/04/10 | * mantis: bug 2144
|         |            |   . Compatibility with Piwigo 2.2
|         |            |
|         |            | * mantis: bug 2166 (fixed in 3.0.3)
|         |            |   . Error message on gallery side about
|         |            |             create_table_add_character_set()
|         |            |
|         |            | * mantis: bug 2182 (fixed in 3.0.3)
|         |            |   . links and personnal blocks : double quote are not
|         |            |     correctly managed
|         |            |
| 3.1.1   | 2011/04/24 | * mantis: bug 2275
|         |            |   . Install don't create tables
|         |            |
| 3.1.2   | 2011/05/15 | * add sv_SE language
|         |            |
| 3.1.3   | 2011/05/24 | * mantis bug:2311
|         |            |   . broken javascript if random pic set is empty
|         |            |
|         |            | * mantis bug:2312
|         |            |   . randomPictureJS is loaded even if menu is hidden
|         |            |
|         |            | * mantis bug:2281
|         |            |   . Custom language value is use in queries unescaped.
|         |            |
| 3.1.4   | 2011/06/29 | * mantis bug:2371
|         |            |   . User access management don't work if user is linked
|         |            |     to a group
|         |            |
|         |            | * mantis bug:2522
|         |            |   . Incompatibility with other plugin managing the menu
|         |            |     content
|         |            |
| 3.2.0   | 2012/05/27 | * mantis: bug 2642
|         |            |   . Compatibility with Piwigo 2.4
|         |            |
|         |            | * mantis bug:2371
|         |            |   . User access management don't work if user is linked
|         |            |     to a group => seems to be definitively fixed...
|         |            |
| 3.2.1   | 2012/07/15 | * mantis: bug 2695
|         |            |   . In admin page, it's not possible to manage "random pict"
|         |            |
| 3.2.2   | 2012/07/15 | * mantis: bug 2696
|         |            |   . When applying config on "random pict", admin page is blocked on waiting icon
|         |            |
| 3.2.3   | 2012/07/15 | * mantis: bug 2697
|         |            |   . hu_HU language file is not correct
|         |            |
| 3.2.4   | 2012/12/09 | * mantis: bug 2799
|         |            |   . Undefined index 'installed'
|         |            |
| 3.2.5   | 2013/03/12 | * Checked compatibility with Piwigo 2.5
|         |            | * Custom menus are compatible with Extended Description plugin syntax
|         |            | * new language pt_BR
|         |            | * mantis bug:2799 compatibility with PHP 5.4
|         |            | * random picture not computed for search engine robots
|         |            |
| 3.2.6   | 2014/01/23 | * Checked compatibility with Piwigo 2.6
|         |            |
| 3.2.7   | 2014/04/21 | * speed improvement for Random Picture in menu
|         |            |
| 3.2.8   | 2014/09/22 | * Checked compatibility with Piwigo 2.7 (thanks to modification on trigger_* by mistic)
|         |            |
| 3.2.9   | 2014/11/27 | * bug fixed, avoid returning empty value on admin.links.get for accessUsers/accessGroups
|         |            |
| 3.2.10  | 2017/05/19 | * speed improvement with the new persistent_cache (new in Piwigo 2.7)
|         |            | * compatibility PHP 7
|         |            | * compatibility MySQL 5.7
|         |            |
| 3.2.11  | 2017/06/16 | * github #1, input parameter "page" is not valid (Piwigo 2.9.1+)
|         |            |
| 3.2.12  | 2017/06/19 | * github #1 again, better way to fix it, replace "/" by "-" for sub-tab
|         |            |
| 3.2.13  | 2020/04/30 | * github #3 compatibility issue with Bootstrap themes: let themes use their own templates
|         |            |
| 3.2.14  | 2021/08/04 | * compatibility with MySQL 8 (escape word `groups` in SQL queries)
|         |            |
| 3.2.15  | 2021/11/13 | * compatibility with Piwigo 12
|         |            |
| 3.2.16  | 2023/12/04 | * checked compatibility with Piwigo 14 and new language strings

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

include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
include_once('amm_version.inc.php'); // => Don't forget to update this file !!
include_once('amm_root.class.inc.php');


global $prefixeTable, $page;

if(!defined('AJAX_CALL'))
{
  if(defined('IN_ADMIN'))
  {
    //AMM admin part loaded and active only if in admin page
    include_once("amm_aim.class.inc.php");
    $obj = new AMM_AIM($prefixeTable, __FILE__);
    $obj->initEvents();
    set_plugin_data($plugin['id'], $obj);
  }
  else
  {
    if(CommonPlugin::checkGPCRelease(AMM_GPC_NEEDED) and !mobile_theme())
    {
      AMM_root::checkPluginRelease();

      //AMM public part loaded and active only if in public page
      include_once("amm_pip.class.inc.php");
      $obj = new AMM_PIP($prefixeTable, __FILE__);
      set_plugin_data($plugin['id'], $obj);
    }
  }
}



?>

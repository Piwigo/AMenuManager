<?php

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', true);

defined('AMM_DIR') || define('AMM_DIR' , basename(dirname(__FILE__)));
defined('AMM_PATH') || define('AMM_PATH' , PHPWG_PLUGINS_PATH . AMM_DIR . '/');

include_once('amm_version.inc.php'); // => Don't forget to update this file !!
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCore.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCTables.class.inc.php');


global $gpc_installed, $gpcNeeded, $lang; //needed for plugin manager compatibility

/* -----------------------------------------------------------------------------
AMM needs the Grum Plugin Classe
----------------------------------------------------------------------------- */
$gpc_installed=false;
$gpcNeeded="3.0.0";
if(file_exists(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php'))
{
  @include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
  // need GPC release greater or equal than 3.0.0
  if(CommonPlugin::checkGPCRelease(3,0,0))
  {
    @include_once("amm_install.class.inc.php");
    $gpc_installed=true;
  }
}

function gpcMsgError(&$errors)
{
  global $gpcNeeded;
  $msg=sprintf(l10n('To install this plugin, you need to install Grum Plugin Classes %s before'), $gpcNeeded);
  if(is_array($errors))
  {
    array_push($errors, $msg);
  }
  else
  {
    $errors=Array($msg);
  }
}
// -----------------------------------------------------------------------------



load_language('plugin.lang', AMM_PATH);

function plugin_install($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable, $gpc_installed, $gpcNeeded;
  if($gpc_installed)
  {
    //$menu->register('mbAMM_links', 'Links', 0, 'AMM');
    //$menu->register('mbAMM_randompict', 'Random pictures', 0, 'AMM');
    $amm=new AMM_install($prefixeTable, __FILE__);
    $result=$amm->install();
    GPCCore::register($amm->getPluginName(), AMM_VERSION, $gpcNeeded);
  }
  else
  {
    gpcMsgError($errors);
  }
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable;

  $amm=new AMM_install($prefixeTable, __FILE__);
  $result=$amm->activate();
}

function plugin_deactivate($plugin_id)
{
}

function plugin_uninstall($plugin_id)
{
  global $prefixeTable, $gpc_installed;
  if($gpc_installed)
  {
    $amm=new AMM_install($prefixeTable, __FILE__);
    $result=$amm->uninstall();
    GPCCore::unregister($amm->getPluginName());
  }
  else
  {
    gpcMsgError($errors);
  }
}



?>

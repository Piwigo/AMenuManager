<?php

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', true);

defined('AMM_DIR') || define('AMM_DIR' , basename(dirname(__FILE__)));
defined('AMM_PATH') || define('AMM_PATH' , PHPWG_PLUGINS_PATH . AMM_DIR . '/');

include_once('amm_version.inc.php'); // => Don't forget to update this file !!


global $gpcInstalled, $lang; //needed for plugin manager compatibility

/* -----------------------------------------------------------------------------
AMM needs the Grum Plugin Classe
----------------------------------------------------------------------------- */
$gpcInstalled=false;
if(file_exists(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php'))
{
  @include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
  // need GPC release greater or equal than AMM_GPC_NEEDED
  if(CommonPlugin::checkGPCRelease(AMM_GPC_NEEDED))
  {
    include_once("amm_install.class.inc.php");
    $gpcInstalled=true;
  }
}

function gpcMsgError(&$errors)
{
  $msg=sprintf(l10n('To install this plugin, you need to install Grum Plugin Classes %s before'), AMM_GPC_NEEDED);
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
  global $prefixeTable, $gpcInstalled;
  if($gpcInstalled)
  {
    $amm=new AMM_install($prefixeTable, __FILE__);
    $result=$amm->install();
  }
  else
  {
    gpcMsgError($errors);
  }
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable, $gpcInstalled;
  if($gpcInstalled)
  {
    $amm=new AMM_install($prefixeTable, __FILE__);
    $result=$amm->activate();
  }
}

function plugin_deactivate($plugin_id)
{
  global $prefixeTable, $gpcInstalled;

  if($gpcInstalled)
  {
    $amm=new AMM_install($prefixeTable, __FILE__);
    $amm->deactivate();
  }
}

function plugin_uninstall($plugin_id)
{
  global $prefixeTable, $gpcInstalled;
  if($gpcInstalled)
  {
    $amm=new AMM_install($prefixeTable, __FILE__);
    $result=$amm->uninstall();
  }
  else
  {
    gpcMsgError($errors);
  }
}


?>

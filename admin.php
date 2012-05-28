<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

global $prefixeTable;

load_language('plugin.lang', AMM_PATH);

$main_plugin_object = get_plugin_data($plugin_id);

if(CommonPlugin::checkGPCRelease(AMM_GPC_NEEDED))
{
  AMM_root::checkPluginRelease();

  include(AMM_PATH."amm_aip.class.inc.php");
  $plugin_ai = new AMM_AIP($prefixeTable, $main_plugin_object->getFileLocation());
}
else
{
  /*
   * plugin was upgraded, but GPC was not
   * display a page to inform user to upgrade GPC
   */
  include(AMM_PATH."amm_aip_release.class.inc.php");
  $plugin_ai = new AMM_AIPRelease($prefixeTable, $main_plugin_object->getFileLocation());
}

$plugin_ai->manage();

?>

<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin     : Advanced Menu Manager
 * -----------------------------------------------------------------------------
 * Author     : Grum
 *   email    : grum@piwigo.org
 *   website  : http://photos.grum.fr
 *   PWG user : http://forum.piwigo.org/profile.php?id=3706
 *
 *   << May the Little SpaceFrog be with you ! >>
 *
 * -----------------------------------------------------------------------------
 *
 * See main.inc.php for release information
 *
 * AIPRelease class => display warning if GPC release is not up to date
 *
 * -----------------------------------------------------------------------------
 */


if(!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once('amm_root.class.inc.php');

class AMM_AIPRelease extends AMM_root
{
  public function __construct($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);
    $this->loadConfig();
    $this->initEvents();
  }

  /*
    display administration page
  */
  public function manage()
  {
    global $template;

    $template->set_filename('plugin_admin_content', dirname($this->getFileLocation())."/admin/amm_admin.tpl");

    $pluginInfo=array(
      'AMM_VERSION' => "<i>".$this->getPluginName()."</i> ".l10n('gmaps_release').AMM_VERSION,
      'PATH' => AMM_PATH
    );

    $template->assign('plugin', $pluginInfo);
    $template->assign('AMM_BODY_PAGE', '<p class="warnings">'.sprintf(l10n('g002_gpc_not_up_to_date'),AMM_GPC_NEEDED, GPC_VERSION).'</p>');
    $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
  }

} //class


?>

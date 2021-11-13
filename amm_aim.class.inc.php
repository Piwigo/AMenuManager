<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://www.grum.fr

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  AMM_AIM : classe to manage plugin integration into plugin menu

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once('amm_root.class.inc.php');

class AMM_AIM extends AMM_root
{
  function __construct($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);
  }

  /*
    initialize events call for the plugin
  */
  function initEvents()
  {
    parent::initEvents();
    add_event_handler('loc_end_page_header', array(&$this, 'adminPanel'));
  }

  public function adminPanel()
  {
    global $template;

    $template->append('footer_elements', "<script>$(document).ready(function () { $('li a[href=\"".$template->get_template_vars('U_CONFIG_MENUBAR')."\"]').attr('href', '".$this->getAdminLink()."-setmenu-position'); });</script>");
  }

} // amm_aim  class


?>

<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://www.grum.fr

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  AIP classe => manage integration in administration interface

  --------------------------------------------------------------------------- */
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'AMenuManager/amm_root.class.inc.php');
include_once(PHPWG_ROOT_PATH.'include/block.class.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCTabSheet.class.inc.php');


class AMM_AIP extends AMM_root
{
  protected $tabsheet;
  protected $blocksId=array('menu' => 'Menu', 'special' => 'Specials');


  public function __construct($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);

    $this->loadConfig();
    $this->initEvents();

    $this->tabsheet = new tabsheet();
    $this->tabsheet->add('setmenu',
                          l10n('g002_setmenu'),
                          $this->getAdminLink().'-setmenu');
    $this->tabsheet->add('links',
                          l10n('g002_addlinks'),
                          $this->getAdminLink().'-links');
    $this->tabsheet->add('randompict',
                          l10n('g002_randompict'),
                          $this->getAdminLink().'-randompict');
    $this->tabsheet->add('personnalblock',
                          l10n('g002_personnalblock'),
                          $this->getAdminLink().'-personnalblock');
    $this->tabsheet->add('album',
                          l10n('g002_album'),
                          $this->getAdminLink().'-album');
  }


  /**
   * manage plugin integration into piwigo's admin interface
   */
  public function manage()
  {
    global $template, $page;

    $template->set_filename('plugin_admin_content', dirname(__FILE__)."/admin/amm_admin.tpl");

    $this->initRequest();

    $this->tabsheet->select($_GET['tab']);
    $this->tabsheet->assign();
    $selected_tab=$this->tabsheet->get_selected();
    $template->assign($this->tabsheet->get_titlename(), "[".$selected_tab['caption']."]");

    $template_plugin["AMM_VERSION"] = "<i>".$this->getPluginName()."</i> ".l10n('g002_version').AMM_VERSION;
    $template_plugin["AMM_PAGE"] = $_GET['tab'];
    $template_plugin["PATH"] = AMM_PATH;

    $template->assign('plugin', $template_plugin);
    GPCCore::setTemplateToken();

    switch($_GET['tab'])
    {
      case 'links':
        $this->displayLinksPage($_REQUEST['t']);
        break;

      case 'randompict':
        $this->displayRandompicPage();
        break;

      case 'personnalblock':
        $this->displayPersonalisedBlockPage();
        break;

      case 'setmenu':
        $this->displayBlocksPage($_REQUEST['t']);
        break;

      case 'album':
        $this->displayAlbumPage();
        break;
    }

    $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
  }

  public function initEvents()
  {
    parent::initEvents();
    add_event_handler('blockmanager_register_blocks', array(&$this, 'registerBlocks') );
  }


  public function loadCSS()
  {
    global $template;

    parent::loadCSS();
    GPCCore::addUI('gpcCSS');
    GPCCore::addHeaderCSS('amm.css', 'plugins/'.$this->getDirectory().'/'.$this->getPluginNameFiles().".css");
    GPCCore::addHeaderCSS('amm.cssT', 'plugins/'.$this->getDirectory().'/'.$this->getPluginNameFiles().'_'.$template->get_themeconf('name').".css");
  }

  /**
   * if empty, initialize the $_REQUEST var
   *
   * if not empty, check validity for the request values
   *
   */
  private function initRequest()
  {
    //initialise $REQUEST values if not defined
    if(!array_key_exists('tab', $_GET)) $_GET['tab']='setmenu';

    $tmp=explode('-', $_GET['tab'].'-');
    $_GET['tab']=$tmp[0];
    $_REQUEST['t']=$tmp[1];

    if(!($_GET['tab']=='links' or
         $_GET['tab']=='randompict' or
         $_GET['tab']=='personnalblock' or
         $_GET['tab']=='setmenu' or
         $_GET['tab']=='album'
        )
      ) $_GET['tab']='setmenu';


    /*
     * checks for links page
     */
    if($_GET['tab']=='links')
    {
      if(!isset($_REQUEST['t'])) $_REQUEST['t']='links';

      if(!($_REQUEST['t']=='links' or
           $_REQUEST['t']=='config'
          )
        ) $_REQUEST['t']='config';
    }


    /*
     * checks for blocks menu page
     */
    if($_GET['tab']=='setmenu')
    {
      if(!isset($_REQUEST['t'])) $_REQUEST['t']='position';

      if(!($_REQUEST['t']=='position' or
           $_REQUEST['t']=='blocksContent'
          )
        ) $_REQUEST['t']='position';
    }

  } //initRequest


  /**
   * display the links management page
   */
  private function displayLinksPage($tab)
  {
    global $template, $user;

    GPCCore::addHeaderJS('jquery.ui', 'themes/default/js/ui/jquery.ui.core.js', array('jquery'));
    GPCCore::addHeaderJS('jquery.ui.widget', 'themes/default/js/ui/jquery.ui.widget.js', array('jquery.ui'));
    GPCCore::addHeaderJS('jquery.ui.mouse', 'themes/default/js/ui/jquery.ui.mouse.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.position', 'themes/default/js/ui/jquery.ui.position.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.sortable', 'themes/default/js/ui/jquery.ui.sortable.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.dialog', 'themes/default/js/ui/jquery.ui.dialog.js', array('jquery.ui.widget'));

    $template->set_filename('body_page',
                            dirname($this->getFileLocation()).'/admin/amm_links.tpl');

    $linksTabsheet = new GPCTabSheet('linksTabsheet', $this->tabsheet->get_titlename(), 'tabsheet2 gcBorder2', 'itab2');
    $linksTabsheet->select($tab);
    $linksTabsheet->add('links',
                          l10n('g002_setting_link_links'),
                          $this->getAdminLink().'-links-links');
    $linksTabsheet->add('config',
                          l10n('g002_configlinks'),
                          $this->getAdminLink().'-links-config');
    $linksTabsheet->assign();

    switch($tab)
    {
      case 'links':
        $template->assign('sheetContent', $this->displayLinksPageLinks());
        break;
      case 'config':
        $template->assign('sheetContent', $this->displayLinksPageConfig());
        break;
    }

    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
    $template->assign('pageNfo', l10n('g002_addlinks_nfo'));
  }

  /**
   * display the randompict management page
   */
  private function displayRandompicPage()
  {
    global $template, $user;

    GPCCore::addHeaderJS('jquery.ui', 'themes/default/js/ui/jquery.ui.core.js', array('jquery'));
    GPCCore::addHeaderJS('jquery.ui.widget', 'themes/default/js/ui/jquery.ui.widget.js', array('jquery.ui'));
    GPCCore::addHeaderJS('jquery.ui.mouse', 'themes/default/js/ui/jquery.ui.mouse.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.position', 'themes/default/js/ui/jquery.ui.position.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.sortable', 'themes/default/js/ui/jquery.ui.sortable.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.dialog', 'themes/default/js/ui/jquery.ui.dialog.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.slider', 'themes/default/js/ui/jquery.ui.slider.js', array('jquery.ui.widget'));
    GPCCore::addUI('inputList,inputText,inputRadio,categorySelector');
    GPCCore::addHeaderJS('amm.rpc', 'plugins/AMenuManager/js/amm_randomPictConfig.js', array('jquery', 'gpc.inputList', 'gpc.inputText', 'gpc.inputRadio', 'gpc.categorySelector'));

    $template->set_filename('body_page',
                            dirname($this->getFileLocation()).'/admin/amm_randompicconfig.tpl');

    $datas=array(
      'config' => array(
          'infosName' => $this->config['amm_randompicture_showname'],
          'infosComment' => $this->config['amm_randompicture_showcomment'],
          'freqDelay' => $this->config['amm_randompicture_periodicchange'],
          'selectMode' => $this->config['amm_randompicture_selectMode'],
          'selectCat' => json_encode($this->config['amm_randompicture_selectCat']),
          'blockHeight' => $this->config['amm_randompicture_height'],
          'blockTitles' => array()
        ),
      'selectedLang' => $user['language'],
      'fromLang' => substr($user['language'],0,2),
      'langs' => array()
    );

    $lang=get_languages();
    foreach($lang as $key => $val)
    {
      $datas['langs'][$key] = $val;
      $datas['config']['blockTitles'][$key] = isset($this->config['amm_randompicture_title'][$key])?base64_decode($this->config['amm_randompicture_title'][$key]):'';
    }

    $template->assign("datas", $datas);

    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
    $template->assign('pageNfo', l10n('g002_randompict_nfo'));
  }

  /**
   * display the personnal blocks management page
   */
  private function displayPersonalisedBlockPage()
  {
    global $template, $user;

    GPCCore::addHeaderJS('jquery.ui', 'themes/default/js/ui/jquery.ui.core.js', array('jquery'));
    GPCCore::addHeaderJS('jquery.ui.widget', 'themes/default/js/ui/jquery.ui.widget.js', array('jquery.ui'));
    GPCCore::addHeaderJS('jquery.ui.mouse', 'themes/default/js/ui/jquery.ui.mouse.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.position', 'themes/default/js/ui/jquery.ui.position.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.sortable', 'themes/default/js/ui/jquery.ui.sortable.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.dialog', 'themes/default/js/ui/jquery.ui.dialog.js', array('jquery.ui.widget'));
    GPCCore::addUI('inputList,inputText,inputRadio');
    GPCCore::addHeaderJS('amm.upbm', 'plugins/AMenuManager/js/amm_personalisedBlocks.js', array('jquery', 'gpc.inputList', 'gpc.inputText', 'gpc.inputRadio'));


    $template->set_filename('body_page',
                            dirname($this->getFileLocation()).'/admin/amm_personalised.tpl');

    $datas=array(
      'selectedLang' => $user['language'],
      'fromLang' => substr($user['language'],0,2),
      'langs' => get_languages()
    );

    $template->assign("datas", $datas);

    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
    $template->assign('pageNfo', l10n('g002_personnalblock_nfo'));
  }

  /**
   * display the core blocks menu management page
   */
  private function displayBlocksPage($tab)
  {
    global $template, $conf;

    GPCCore::addHeaderJS('jquery.ui', 'themes/default/js/ui/jquery.ui.core.js', array('jquery'));
    GPCCore::addHeaderJS('jquery.ui.widget', 'themes/default/js/ui/jquery.ui.widget.js', array('jquery.ui'));
    GPCCore::addHeaderJS('jquery.ui.mouse', 'themes/default/js/ui/jquery.ui.mouse.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.position', 'themes/default/js/ui/jquery.ui.position.js', array('jquery.ui.widget'));
    GPCCore::addHeaderJS('jquery.ui.sortable', 'themes/default/js/ui/jquery.ui.sortable.js', array('jquery.ui.widget'));
    GPCCore::addUI('inputList');
    GPCCore::addHeaderJS('amm.cbm', 'plugins/AMenuManager/js/amm_blocks.js', array('jquery', 'jquery.ui.sortable', 'gpc.inputList'));

    $template->set_filename('body_page',
                            dirname($this->getFileLocation()).'/admin/amm_coreBlocks.tpl');

    $blocksTabsheet = new GPCTabSheet('blocksTabsheet', $this->tabsheet->get_titlename(), 'tabsheet2 gcBorder2', 'itab2');
    $blocksTabsheet->add('position',
                          l10n('g002_setting_blocks_position'),
                          '', false, "cbm.displayTabContent('position');");
    $blocksTabsheet->add('config',
                          l10n('g002_setting_core_blocks_content'),
                          '', false, "cbm.displayTabContent('blocksContent');");
    $blocksTabsheet->select($tab);
    $blocksTabsheet->assign();


    $users=new GPCUsers();
    $groups=new GPCGroups();

    $registeredBlocks=$this->getRegisteredBlocks();
    foreach($registeredBlocks as $key=>$val)
    {
      $registeredBlocks[$key]['users']=json_encode($registeredBlocks[$key]['users']);
      $registeredBlocks[$key]['groups']=json_encode($registeredBlocks[$key]['groups']);
    }

    $this->sortCoreBlocksItems();

    foreach($this->config['amm_blocks_items'] as $menuId=>$menu)
    {
      $this->config['amm_blocks_items'][$menuId]['visibilityForm'] = $this->makeBlockVisibility($menu['visibility'], $menuId);
      $this->config['amm_blocks_items'][$menuId]['translation']=$this->defaultMenus[$menuId]['translation'];
      $this->defaultMenus[$menuId]['visibilityForm'] = $this->makeBlockVisibility("/", $menuId);
    }

    $datas=array(
      'tab' => $tab,
      'users' => $users->getList(),
      'groups' => $groups->getList(),
      'coreBlocks' => array(
            'blocks' => $this->blocksId,
            'defaultValues' => $this->defaultMenus,
            'items' => $this->config['amm_blocks_items']
          ),
      'menuBlocks' => $registeredBlocks
    );

    $template->assign("datas", $datas);

    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
    $template->assign('pageNfo', l10n('g002_setmenu_nfo'));
  }



  /**
   * display the album to menu management page
   */
  private function displayAlbumPage()
  {
    global $template, $user;

    GPCCore::addUI('categorySelector');
    GPCCore::addHeaderJS('amm.ac', 'plugins/AMenuManager/js/amm_albumConfig.js', array('jquery','gpc.categorySelector'));

    $template->set_filename('body_page',
                            dirname($this->getFileLocation()).'/admin/amm_album.tpl');

    $datas=array(
      'albums' => json_encode($this->config['amm_albums_to_menu'])
    );

    $template->assign("datas", $datas);

    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
    $template->assign('pageNfo', l10n('g002_album_nfo'));
  }


  /*
   *  ---------------------------------------------------------------------------
   * links functionnalities
   * ---------------------------------------------------------------------------
   */

  /**
   * display the links management page
   */
  private function displayLinksPageLinks()
  {
    global $template, $user;

    GPCCore::addUI('inputList,inputRadio,inputText,inputCheckbox');
    GPCCore::addHeaderJS('amm.ulm', 'plugins/AMenuManager/js/amm_links.js', array('jquery', 'gpc.inputList', 'gpc.inputText', 'gpc.inputRadio', 'gpc.inputCheckbox'));

    $template->set_filename('sheet_page',
                            dirname($this->getFileLocation()).'/admin/amm_linkslinks.tpl');

    $users=new GPCUsers();
    $groups=new GPCGroups();

    $datas=array(
      'access' => array('users' => $users->getList(), 'groups' => $groups->getList()),
      'iconsValues' => array(),
      'modesValues' => array(
        array(
          'value' => 0,
          'label' => l10n("g002_mode_".$this->urlsModes[0])
        ),
        array(
          'value' => 1,
          'label' => l10n("g002_mode_".$this->urlsModes[1])
        )
      )
    );

    $directory=dir(dirname($this->getFileLocation()).'/links_pictures/');
    while($file=$directory->read())
    {
      if(in_array(get_extension(strtolower($file)), array('jpg', 'jpeg','gif','png')))
      {
        $datas['iconsValues'][] = array(
          'img' => AMM_PATH."links_pictures/".$file,
          'value' => $file,
          'label' => $file
        );
      }
    }

    $template->assign("datas", $datas);

    return($template->parse('sheet_page', true));
  }

  /**
   * display the links config page
   */
  private function displayLinksPageConfig()
  {
    global $template, $user;

    GPCCore::addUI('inputList,inputRadio,inputText');
    GPCCore::addHeaderJS('amm.ulc', 'plugins/AMenuManager/js/amm_linksConfig.js', array('jquery', 'gpc.inputList', 'gpc.inputText', 'gpc.inputRadio'));

    $template->set_filename('sheet_page',
                            dirname($this->getFileLocation()).'/admin/amm_linksconfig.tpl');

    $datas=array(
      'config' => array(
          'showIcons' => $this->config['amm_links_show_icons'],
          'titles' => array()
        ),
      'selectedLang' => $user['language'],
      'fromLang' => substr($user['language'],0,2),
      'langs' => array()
    );

    $lang=get_languages();
    foreach($lang as $key => $val)
    {
      $datas['langs'][$key] = $val;
      $datas['config']['titles'][$key] = isset($this->config['amm_links_title'][$key])?base64_decode($this->config['amm_links_title'][$key]):'';
    }

    $template->assign("datas", $datas);
    return($template->parse('sheet_page', true));
  }



  /*
   * ---------------------------------------------------------------------------
   * blocks functionnalities
   * ---------------------------------------------------------------------------
   */

  /**
   * this function returns an HTML FORM to use with each menu items
   *
   * @param String $visibility : a formatted string like :
   *                              users type1(,users typeX)/(groupId0)(,groupIdX)
   * @param String $blockId    : block Id
   * @return String : html ready to use
  */
  private function makeBlockVisibility($visibility, $menuId)
  {
    $local_tpl = new Template(AMM_PATH."admin/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->getFileLocation()).'/admin/amm_coreBlocks_detail.tpl');


    $parameters=explode("/", $visibility);

    /* submenu access system is :
     *  - by default, everything is accesible
     *  - items not accessible are defined
     */
    $users=new GPCUsers();
    $users->setAlloweds(explode(',', $parameters[0]), false);
    $groups=new GPCGroups();
    $groups->setAlloweds(explode(',', $parameters[1]), false);

    $local_tpl->assign('name', $menuId);
    $local_tpl->assign('users', $users->getList());
    $local_tpl->assign('groups', $groups->getList());

    return($local_tpl->parse('body_page', true));
  }


} // AMM_AIP class

?>

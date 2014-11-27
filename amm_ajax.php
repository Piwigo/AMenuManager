<?php
/*
 * -----------------------------------------------------------------------------
 * Plugin Name: Advanced Menu Manager
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
 * manage all the ajax requests
 * -----------------------------------------------------------------------------
 */


  define('PHPWG_ROOT_PATH',dirname(dirname(dirname(__FILE__))).'/');
  if(!defined('AJAX_CALL')) define('AJAX_CALL', true);

  /*
   * set ajax module in admin mode if request is used for admin interface
   */
  if(!isset($_REQUEST['ajaxfct'])) $_REQUEST['ajaxfct']='';
  if(preg_match('/^admin\./i', $_REQUEST['ajaxfct'])) define('IN_ADMIN', true);

  // the common.inc.php file loads all the main.inc.php plugins files
  include_once(PHPWG_ROOT_PATH.'include/common.inc.php' );
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCAjax.class.inc.php');
  include_once('amm_root.class.inc.php');

  load_language('plugin.lang', AMM_PATH);


  class AMM_Ajax extends AMM_root
  {
    /**
     * constructor
     */
    public function __construct($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->loadConfig();
      $this->checkRequest();
      $this->returnAjaxContent();
    }

    /**
     * check the $_REQUEST values and set default values
     *
     */
    protected function checkRequest()
    {
      global $user;

      if(!isset($_REQUEST['errcode'])) $_REQUEST['errcode']='';
      GPCAjax::checkToken();

      // check if asked function is valid
      if(!($_REQUEST[GPC_AJAX]=='admin.links.get' or
           $_REQUEST[GPC_AJAX]=='admin.links.set' or
           $_REQUEST[GPC_AJAX]=='admin.links.list' or
           $_REQUEST[GPC_AJAX]=='admin.links.delete' or
           $_REQUEST[GPC_AJAX]=='admin.links.order' or
           $_REQUEST[GPC_AJAX]=='admin.links.setConfig' or
           $_REQUEST[GPC_AJAX]=='admin.randomPict.setConfig' or
           $_REQUEST[GPC_AJAX]=='admin.blocks.get' or
           $_REQUEST[GPC_AJAX]=='admin.blocks.set' or
           $_REQUEST[GPC_AJAX]=='admin.blocks.delete' or
           $_REQUEST[GPC_AJAX]=='admin.blocks.list' or
           $_REQUEST[GPC_AJAX]=='admin.album.setConfig' or
           $_REQUEST[GPC_AJAX]=='admin.coreBlocks.setConfig'
           )) $_REQUEST[GPC_AJAX]='';

      if(preg_match('/^admin\./i', $_REQUEST[GPC_AJAX]) and !is_admin()) $_REQUEST[GPC_AJAX]='';


      if($_REQUEST[GPC_AJAX]!='')
      {
        /*
         * no check for admin.links.list request
         */

        /*
         * check admin.links.get request
         * check admin.blocks.get request
         */
        if($_REQUEST[GPC_AJAX]=='admin.links.get' or
           $_REQUEST[GPC_AJAX]=='admin.blocks.get request')
        {
          if(!isset($_REQUEST['id'])) $_REQUEST['id']='';

          if($_REQUEST['id']=='') $_REQUEST[GPC_AJAX]='';
        }

        /*
         * check admin.links.set request
         */
        if($_REQUEST[GPC_AJAX]=='admin.links.set')
        {
          if(!isset($_REQUEST['id'])) $_REQUEST['id']='';
          if(!isset($_REQUEST['datas']['label'])) $_REQUEST['datas']['label']='';
          if(!isset($_REQUEST['datas']['url'])) $_REQUEST['datas']['url']='';
          if(!isset($_REQUEST['datas']['icon'])) $_REQUEST['datas']['icon']='';
          if(!isset($_REQUEST['datas']['mode'])) $_REQUEST['datas']['mode']='0';
          if(!isset($_REQUEST['datas']['visible'])) $_REQUEST['datas']['visible']='n';
          if(!isset($_REQUEST['datas']['accessUsers']) or $_REQUEST['datas']['accessUsers']=='') $_REQUEST['datas']['accessUsers']=array();
          if(!isset($_REQUEST['datas']['accessGroups']) or $_REQUEST['datas']['accessGroups']=='') $_REQUEST['datas']['accessGroups']=array();

          if($_REQUEST['datas']['label']=='' or
             $_REQUEST['datas']['url']=='' or
             $_REQUEST['datas']['icon']=='' or
             !($_REQUEST['datas']['mode']=='0' or $_REQUEST['datas']['mode']=='1') or
             !($_REQUEST['datas']['visible']=='y' or $_REQUEST['datas']['visible']=='n')
            ) $_REQUEST[GPC_AJAX]='';
        }

        /*
         * check admin.links.delete request
         * check admin.blocks.delete request
         *
         */
        if($_REQUEST[GPC_AJAX]=='admin.links.delete' or
           $_REQUEST[GPC_AJAX]=='admin.blocks.delete')
        {
          if(!isset($_REQUEST['id'])) $_REQUEST['id']='';

          if($_REQUEST['id']=='') $_REQUEST[GPC_AJAX]='';
        }

        /*
         * check admin.links.order request
         */
        if($_REQUEST[GPC_AJAX]=='admin.links.order')
        {
          if(!isset($_REQUEST['datas']['links']) or $_REQUEST['datas']['links']=='') $_REQUEST['datas']['links']=array();

          if(count($_REQUEST['datas']['links'])<=1) $_REQUEST[GPC_AJAX]='';
        }


        /*
         * check admin.links.setConfig request
         */
        if($_REQUEST[GPC_AJAX]=='admin.links.setConfig')
        {
          if(!isset($_REQUEST['datas']['showIcons'])) $_REQUEST['datas']['showIcons']='';
          if(!isset($_REQUEST['datas']['title']) or $_REQUEST['datas']['title']=='') $_REQUEST['datas']['title']=array();

          if($_REQUEST['datas']['showIcons']=='' or
             count($_REQUEST['datas']['title'])==0
            ) $_REQUEST[GPC_AJAX]='';
        }



        /*
         * check admin.randomPict.setConfig request
         */
        if($_REQUEST[GPC_AJAX]=='admin.randomPict.setConfig')
        {
          if(!isset($_REQUEST['datas']['blockHeight'])) $_REQUEST['datas']['blockHeight']='';
          if(!isset($_REQUEST['datas']['blockTitles']) or $_REQUEST['datas']['blockTitles']=='') $_REQUEST['datas']['blockTitles']=array();
          if(!isset($_REQUEST['datas']['infosName'])) $_REQUEST['datas']['infosName']='';
          if(!isset($_REQUEST['datas']['infosComment'])) $_REQUEST['datas']['infosComment']='';
          if(!isset($_REQUEST['datas']['freqDelay'])) $_REQUEST['datas']['freqDelay']='';
          if(!isset($_REQUEST['datas']['selectMode'])) $_REQUEST['datas']['selectMode']='';
          if(!isset($_REQUEST['datas']['selectCat']) or $_REQUEST['datas']['selectCat']=='') $_REQUEST['datas']['selectCat']=array();

          if(!is_numeric($_REQUEST['datas']['blockHeight']) or
             count($_REQUEST['datas']['blockTitles'])==0 or
             !($_REQUEST['datas']['infosName']=='n' or
               $_REQUEST['datas']['infosName']=='o' or
               $_REQUEST['datas']['infosName']=='u') or
             !($_REQUEST['datas']['infosComment']=='n' or
               $_REQUEST['datas']['infosComment']=='o' or
               $_REQUEST['datas']['infosComment']=='u') or
             !is_numeric($_REQUEST['datas']['freqDelay']) or
             !($_REQUEST['datas']['selectMode']=='a' or
               $_REQUEST['datas']['selectMode']=='f' or
               $_REQUEST['datas']['selectMode']=='c') or
             ($_REQUEST['datas']['selectMode']=='c' and
              count($_REQUEST['datas']['selectCat'])==0)
            ) $_REQUEST[GPC_AJAX]='';
        }


        /*
         * check admin.blocks.set request
         */
        if($_REQUEST[GPC_AJAX]=='admin.blocks.set')
        {
          if(!isset($_REQUEST['id'])) $_REQUEST['id']='';
          if(!isset($_REQUEST['datas']['nfo'])) $_REQUEST['datas']['nfo']='';
          if(!isset($_REQUEST['datas']['visible'])) $_REQUEST['datas']['visible']='';
          if(!isset($_REQUEST['datas']['langs']) or $_REQUEST['datas']['langs']=='') $_REQUEST['datas']['langs']=array();

          if($_REQUEST['datas']['nfo']=='' or
             !($_REQUEST['datas']['visible']=='y' or $_REQUEST['datas']['visible']=='n') or
             count($_REQUEST['datas']['langs'])==0
            ) $_REQUEST[GPC_AJAX]='';
        }




        /*
         * check admin.coreBlocks.setConfig request
         */
        if($_REQUEST[GPC_AJAX]=='admin.coreBlocks.setConfig')
        {
          if(!isset($_REQUEST['datas']['menuItems']) or $_REQUEST['datas']['menuItems']=='') $_REQUEST['datas']['menuItems']=array();
          if(!isset($_REQUEST['datas']['blocks']) or $_REQUEST['datas']['blocks']=='') $_REQUEST['datas']['blocks']=array();

          if(count($_REQUEST['datas']['menuItems'])!=count($this->defaultMenus)
            ) $_REQUEST[GPC_AJAX]='';
        }


        /*
         * check admin.album.setConfig request
         */
        if($_REQUEST[GPC_AJAX]=='admin.album.setConfig')
        {
          if(!isset($_REQUEST['datas']['selectCat']) or $_REQUEST['datas']['selectCat']=='') $_REQUEST['datas']['selectCat']=array();
        }

      }

    } //checkRequest


    /**
     * return ajax content
     */
    protected function returnAjaxContent()
    {
      $result="KO!".l10n('g002_error_invalid_ajax_call');
      switch($_REQUEST[GPC_AJAX])
      {
        case 'admin.links.get':
          $result=$this->ajax_amm_admin_linksGet($_REQUEST['id']);
          break;
        case 'admin.links.set':
          $result=$this->ajax_amm_admin_linksSet($_REQUEST['id'],$_REQUEST['datas']['label'],$_REQUEST['datas']['url'],$_REQUEST['datas']['mode'],$_REQUEST['datas']['icon'],$_REQUEST['datas']['visible'],$_REQUEST['datas']['accessUsers'],$_REQUEST['datas']['accessGroups']);
          break;
        case 'admin.links.list':
          $result=$this->ajax_amm_admin_linksList();
          break;
        case 'admin.links.order':
          $result=$this->ajax_amm_admin_linksOrder($_REQUEST['datas']['links']);
          break;
        case 'admin.links.delete':
          $result=$this->ajax_amm_admin_linksDelete($_REQUEST['id']);
          break;
        case 'admin.links.setConfig':
          $result=$this->ajax_amm_admin_linksSetConfig($_REQUEST['datas']);
          break;

        case 'admin.randomPict.setConfig':
          $result=$this->ajax_amm_admin_randomPictSetConfig($_REQUEST['datas']);
          break;

        case 'admin.blocks.get':
          $result=$this->ajax_amm_admin_blocksGet($_REQUEST['id']);
          break;
        case 'admin.blocks.set':
          $result=$this->ajax_amm_admin_blocksSet($_REQUEST['id'],$_REQUEST['datas']['visible'],$_REQUEST['datas']['nfo'],$_REQUEST['datas']['langs']);
          break;
        case 'admin.blocks.list':
          $result=$this->ajax_amm_admin_blocksList();
          break;
        case 'admin.blocks.delete':
          $result=$this->ajax_amm_admin_blocksDelete($_REQUEST['id']);
          break;

        case 'admin.coreBlocks.setConfig':
          $result=$this->ajax_amm_admin_coreBlocksSetConfig($_REQUEST['datas']['menuItems'], $_REQUEST['datas']['blocks']);
          break;

        case 'admin.album.setConfig':
          $result=$this->ajax_amm_admin_albumSetConfig($_REQUEST['datas']);
          break;
      }
      GPCAjax::returnResult($result);
    }


    /*
     * -------------------------------------------------------------------------
     *
     * ADMIN FUNCTIONS
     *
     * -------------------------------------------------------------------------
     */


    /*
     * -------------------------------------------------------------------------
     * Links
     * -------------------------------------------------------------------------
     */

    /**
     * return a html formatted list of urls
     */
    private function ajax_amm_admin_linksList()
    {
      global $template, $user;
      $local_tpl = new Template(AMM_PATH."admin/", "");
      $local_tpl->set_filename('body_page',
                    dirname($this->getFileLocation()).'/admin/amm_linkslinks_detail.tpl');



      $datas['links']=array();

      $links=$this->getLinks();
      foreach($links as $link)
      {
        $datas['links'][]=array(
          'id' => $link['id'],
          'label' => $link['label'],
          'url' => $link['url'],
          'mode' => l10n("g002_mode_".$this->urlsModes[$link['mode']]),
          'icon' => "plugins/".AMM_DIR."/links_pictures/".$link['icon'],
          'visible' => l10n('g002_yesno_'.$link['visible'])
        );
      }
      $local_tpl->assign('themeconf', $template->get_template_vars('themeconf'));
      $local_tpl->assign('datas', $datas);
      $local_tpl->assign('plugin', array('PATH' => AMM_PATH));

      return($local_tpl->parse('body_page', true));
    }


    /**
     * update links order
     *
     * @param Array $links
     * @return String : OK or KO
     */
    private function ajax_amm_admin_linksOrder($links)
    {
      return($this->setLinksOrder($links)?'OK':'KO');
    }


    /**
     * delete a link
     *
     * @param Integer $id : link id
     * @return String : OK or KO
     */
    private function ajax_amm_admin_linksDelete($id)
    {
      return($this->deleteLink($id)?'OK':'KO');
    }

    /**
     * return link content as a json string
     *
     * @param Integer $id : link id
     * @return String : json string
     */
    private function ajax_amm_admin_linksGet($id)
    {
      $link=$this->getLink($id);
      
      if (empty($link['accessUsers']))
      {
        $link['accessUsers'] = array();
      }
      else
      {
        $link['accessUsers']=explode(',', $link['accessUsers']);
      }

      if (empty($link['accessGroups']))
      {
        $link['accessGroups'] = array();
      }
      else
      {
        $link['accessGroups']=explode(',', $link['accessGroups']);
      }
      
      return(json_encode($link));
    }

    /**
     * set link values
     * if id is empty, create a new link
     *
     * @param String $id : link id
     * @param String $label : link label
     * @param String $url : link url
     * @param String $mode : link mode (open a new window or not)
     * @param String $icon : displayed icon
     * @param String $visible : link visibility
     * @return String : $id if OK, otherwise -1
     */
    private function ajax_amm_admin_linksSet($id, $label, $url, $mode, $icon, $visible, $accessUsers, $accessGroups)
    {
      return($this->setLink($id, $label, $url, $mode, $icon, $visible, implode(',', $accessUsers), implode(',', $accessGroups)));
    }

    /**
     * set the links config
     *
     * $config is an array with keys :
     *  String 'showIcons' : values 'y' or 'n'
     *  Array  'titles'    : each array occurs is an array('id' => '', 'value' => '')
     *                            id = lang id ('fr_FR', 'en_UK', ...)
     *
     * @param Array $config
     * @return String : OK or KO
     */
    private function ajax_amm_admin_linksSetConfig($config)
    {
      $this->config['amm_links_show_icons']=$config['showIcons'];

      $this->config['amm_links_title']=array();
      foreach($config['title'] as $title)
      {
        $this->config['amm_links_title'][$title['id']]=base64_encode($title['value']);
      }

      $this->saveConfig();

      return('OK!'.l10n('g002_config_saved'));
    }


    /*
     * -------------------------------------------------------------------------
     * random picture
     * -------------------------------------------------------------------------
     */


    /**
     * set the random picture config
     *
     * $config is an array with keys :
     *  Integer 'blockHeight' : if value=0, the browser assume an automatic size
     *  Array  'blockTitles'  : each array occurs is an array('id' => '', 'value' => '')
     *                            id = lang id ('fr_FR', 'en_UK', ...)
     *  String 'infosName'      : allow to display picture's name ;
     *                              can take 'n' (no), 'o' (over), or 'u' (under)
     *  String 'infosComment'   : allow to display picture's comment ;
     *                              can take 'n' (no), 'o' (over), or 'u' (under)
     *  Integer 'freqDelay'     : allow to choose change frequency (delay is in
     *                            milliseconds)
     *                            if value=0, there is no change
     *  String 'selectMode'     : allows to choose picture to be randomly selected
     *                              can take 'a' (all), 'f' (webmaster's favorites), 'c' (from categories)
     *  Array  'selectCat'      : list of selected categories (choosing a cat implies to choose all sub cat)
     *
     * @param Array $config
     * @return String : OK or KO
     */
    private function ajax_amm_admin_randomPictSetConfig($config)
    {
      $this->config['amm_randompicture_selectMode']=$config['selectMode'];
      $this->config['amm_randompicture_selectCat']=$config['selectCat'];
      $this->config['amm_randompicture_showname']=$config['infosName'];
      $this->config['amm_randompicture_showcomment']=$config['infosComment'];
      $this->config['amm_randompicture_periodicchange']=$config['freqDelay'];
      $this->config['amm_randompicture_height']=($config['blockHeight']==99)?0:$config['blockHeight'];
      $this->config['amm_randompicture_title']=array();
      foreach($config['blockTitles'] as $title)
      {
        $this->config['amm_randompicture_title'][$title['id']]=base64_encode($title['value']);
      }

      $this->saveConfig();
      return('OK!'.l10n('g002_config_saved'));
    }



    /*
     * -------------------------------------------------------------------------
     * personalised blocks
     * -------------------------------------------------------------------------
     */

    /**
     * return a html formatted list of blocks
     */
    private function ajax_amm_admin_blocksList()
    {
      global $template, $user;

      $local_tpl = new Template(AMM_PATH."admin/", "");
      $local_tpl->set_filename('body_page',
                    dirname($this->getFileLocation()).'/admin/amm_personalised_detail.tpl');



      $datas=array(
        'blocks'=>array()
      );

      $blocks=$this->getPersonalisedBlocks(false, '', true);
      foreach($blocks as $block)
      {
        $datas['blocks'][]=array(
          'id' => $block['id'],
          'nfo' => $block['nfo'],
          'title' => $block['title'],
          'visible' => l10n('g002_yesno_'.$block['visible'])
        );
      }

      $local_tpl->assign('themeconf', $template->get_template_vars('themeconf'));
      $local_tpl->assign('datas', $datas);
      $local_tpl->assign('plugin', array('PATH' => AMM_PATH));

      return($local_tpl->parse('body_page', true));
    }

    /**
     * delete a block
     *
     * @param Integer $id : block id
     * @return String : OK or KO
     */
    private function ajax_amm_admin_blocksDelete($id)
    {
      return($this->deletePersonalisedBlock($id)?'OK':'KO');
    }

    /**
     * return block content as a json string
     *
     * @param Integer $id : block id
     * @return String : json string
     */
    private function ajax_amm_admin_blocksGet($id)
    {
      return(json_encode($this->getPersonalisedBlock($id)));
    }

    /**
     * set block values
     * if id is empty, create a new block
     *
     * @param String $id      : block id
     * @param String $visible : block visibility ('y' or 'n')
     * @param String $nfo     : block description
     * @param Array  $langs   : block langs, each record is an array
     *                                  array('title' => '', 'content' => '')
     * @return String : $id if OK, otherwise -1
     */
    private function ajax_amm_admin_blocksSet($id, $visible, $nfo, $lang)
    {
      return($this->setPersonalisedBlock($id, $visible, $nfo, $lang));
    }

    /*
     * -------------------------------------------------------------------------
     * core blocks
     * -------------------------------------------------------------------------
     */

    /**
     * set the menu config
     *  - core blocks content
     *  - blocks order&visibility
     *
     * @param Array $subMenus : for core blocks, sub menu items
     *                          array(
     *                            'subMenuId' => array(
     *                                          'visibility' => (String),
     *                                          'order' => (Integer),
     *                                          'container' => (String),
     *                                        )
     *                          )
     * @param Array $menus    : menu blocks order&visibility
     *                          array(
     *                            'block1' => array(
     *                                'id'    => (String),
     *                                'order' => (Integer),
     *                                'users' => array(),
     *                                'groups' => array()
     *                            )
     *                          )
     * @return String : OK or KO
     */
    private function ajax_amm_admin_coreBlocksSetConfig($subMenu, $menus)
    {
      foreach($subMenu as $key=>$val)
      {
        if(!isset($subMenu[$key]['visibilityUser']) or $subMenu[$key]['visibilityUser']=='') $subMenu[$key]['visibilityUser']=array();
        if(!isset($subMenu[$key]['visibilityGroup']) or $subMenu[$key]['visibilityGroup']=='') $subMenu[$key]['visibilityGroup']=array();

        $subMenu[$key]['visibility']=implode(',', $subMenu[$key]['visibilityUser']).'/'.implode(',', $subMenu[$key]['visibilityGroup']);
        unset($subMenu[$key]['visibilityUser']);
        unset($subMenu[$key]['visibilityGroup']);
      }
      $this->config['amm_blocks_items']=$subMenu;
      $this->saveConfig();


      foreach($menus as $key=>$val)
      {
        if(!isset($menus[$key]['users']) or $menus[$key]['users']=='') $menus[$key]['users']=array();
        if(!isset($menus[$key]['groups']) or $menus[$key]['groups']=='') $menus[$key]['groups']=array();
      }
      $this->setRegisteredBlocks($menus);

      return('OK!'.l10n('g002_config_saved'));
    }



    /*
     * -------------------------------------------------------------------------
     * album to menu
     * -------------------------------------------------------------------------
     */


    /**
     * set the album to menu config
     *
     * $config is an array with keys :
     *  Array  'selectCat'      : list of selected categories
     *
     * @param Array $config
     * @return String : OK or KO
     */
    private function ajax_amm_admin_albumSetConfig($config)
    {
      $this->config['amm_albums_to_menu']=$config['selectCat'];

      $this->saveConfig();
      return('OK!'.l10n('g002_config_saved'));
    }


  } //class


  $returned=new AMM_Ajax($prefixeTable, __FILE__);
?>


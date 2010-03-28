<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  PIP classe => manage integration in public interface

  --------------------------------------------------------------------------- */
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'AMenuManager/amm_root.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/ajax.class.inc.php');

class AMM_PIP extends AMM_root
{
  protected $ajax;
  protected $displayRandomImageBlock=true;

  function AMM_PIP($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);
    $this->ajax = new Ajax();
    $this->css = new css(dirname($this->filelocation).'/'.$this->plugin_name_files."2.css");

    $this->load_config();
    $this->init_events();
  }


  /* ---------------------------------------------------------------------------
  Public classe functions
  --------------------------------------------------------------------------- */


  /*
    initialize events call for the plugin
  */
  public function init_events()
  {
    //TODELETE: add_event_handler('loc_begin_menubar', array(&$this, 'modify_menu') );
    parent::init_events();
    add_event_handler('loading_lang', array(&$this, 'load_lang'));
    add_event_handler('blockmanager_apply', array(&$this, 'blockmanager_apply') );
    add_event_handler('loc_end_page_header', array(&$this->css, 'apply_CSS'));
    add_event_handler('loc_end_page_tail', array(&$this, 'applyJS'));
  }

  /*
    load language file
  */
  public function load_lang()
  {
    global $lang;

    //load_language('plugin.lang', AMM_PATH);

    // ajax is managed here ; this permit to use user&language properties inside
    // ajax content
    $this->return_ajax_content();
  }

  public function blockmanager_apply( $menu_ref_arr )
  {
    global $user, $page;
    $menu = & $menu_ref_arr[0];


    /*
      Add a new random picture section
    */
    if ( ( ($block = $menu->get_block( 'mbAMM_randompict' ) ) != null ) && ($user['nb_total_images'] > 0) )
    {
      $block->set_title(  base64_decode($this->my_config['amm_randompicture_title'][$user['language']]) );
      $block->data = array(
        "delay" => $this->my_config['amm_randompicture_periodicchange'],
        "blockHeight" => $this->my_config['amm_randompicture_height'],
        "firstPicture" => $this->ajax_amm_get_random_picture()
      );
      $block->template = dirname(__FILE__).'/menu_templates/menubar_randompic.tpl';
    }
    else
    {
      $this->displayRandomImageBlock=false;
    }

    /*
      Add a new section (links)
    */
    if ( ($block = $menu->get_block( 'mbAMM_links' ) ) != null )
    {
      $urls=$this->get_urls(true);
      if ( count($urls)>0 )
      {
        if($this->my_config['amm_links_show_icons']=='y')
        {
          for($i=0;$i<count($urls);$i++)
          {
            $urls[$i]['icon']=get_root_url().'plugins/'.AMM_DIR."/links_pictures/".$urls[$i]['icon'];
          }
        }

        $block->set_title( base64_decode($this->my_config['amm_links_title'][$user['language']]) );
        $block->template = dirname(__FILE__).'/menu_templates/menubar_links.tpl';

        $block->data = array(
          'LINKS' => $urls,
          'icons' => $this->my_config['amm_links_show_icons']
        );
      }
    }

    /*
      Add personnal blocks random picture section
    */
    $sections=$this->get_sections(true);

    if(count($sections))
    {
      $id_done=array();
      foreach($sections as $key => $val)
      {
        if(!isset($id_done[$val['id']]))
        {
          if ( ($block = $menu->get_block( 'mbAMM_personalised'.$val['id'] ) ) != null )
          {
            $block->set_title( $val['title'] );
            $block->template = dirname(__FILE__).'/menu_templates/menubar_personalised.tpl';
            $block->data = stripslashes($val['content']);
          }
          $id_done[$val['id']]="";
        }
      }
    }


    /* manage items from special & menu sections
     *  reordering items
     *  grouping items
     *  managing rights to access
    */
    $blocks=Array();

    if($menu->is_hidden('mbMenu'))
    {
      // if block is hidden, make a fake to manage AMM features
      // the fake block isn't displayed
      $blocks['menu']=new DisplayBlock('amm_mbMenu');
      $blocks['menu']->data=Array();
    }
    else
    {
      $blocks['menu']=$menu->get_block('mbMenu');
    }

    if($menu->is_hidden('mbSpecials'))
    {
      // if block is hidden, make a fake to manage AMM features
      // the fake block isn't displayed
      $blocks['special']=new DisplayBlock('amm_mbSpecial');
      $blocks['special']->data=Array();
    }
    else
    {
      $blocks['special']=$menu->get_block('mbSpecials');
    }

    $menuItems=array_merge($blocks['menu']->data, $blocks['special']->data);
    $this->sortSectionsItems();

    $blocks['menu']->data=Array();
    $blocks['special']->data=Array();

    $users=new users("");
    $groups=new groups("");
    $user_groups=$this->get_user_groups($user['id']);

    foreach($this->my_config['amm_sections_items'] as $key => $val)
    {
      if(isset($menuItems[$key]))
      {
        $access=explode("/",$val['visibility']);
        $users->set_alloweds(str_replace(",", "/", $access[0]));
        $groups->set_alloweds(str_replace(",", "/", $access[1]));

        /* test if user status is allowed to access the menu item
         * if access is managed by group, the user have to be associated with an allowed group to access the menu item
        */
        if($users->is_allowed($user['status']) && (
            ($access[1]=='') ||
            (($access[1]!='') && $groups->are_allowed($user_groups)))
        )
        {
          $blocks[$val['container']]->data[$key]=$menuItems[$key];
        }
      }
    }
    if(count($blocks['menu']->data)==0) $menu->hide_block('mbMenu');
    if(count($blocks['special']->data)==0) $menu->hide_block('mbSpecials');
  }


  protected function get_user_groups($user_id)
  {
    $returned=array();
    $sql="SELECT group_id FROM ".USER_GROUP_TABLE."
          WHERE user_id = ".$user_id." ";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        array_push($returned, $row['group_id']);
      }
    }
    return($returned);
  }

  /*
    return ajax content
  */
  protected function return_ajax_content()
  {
    global $ajax, $template;

    if(isset($_REQUEST['ajaxfct']))
    {
      if($_REQUEST['ajaxfct']=='randompic')
      {
        $result="<p class='errors'>".l10n('g002_error_invalid_ajax_call')."</p>";
        switch($_REQUEST['ajaxfct'])
        {
          case 'randompic':
            $result=$this->ajax_amm_get_random_picture();
            break;
        }
        $this->ajax->return_result($result);
      }
    }
  }


  // return the html content for the random picture block
  private function ajax_amm_get_random_picture()
  {
    global $user;

    $local_tpl = new Template(AMM_PATH."menu_templates/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->filelocation).'/menu_templates/menubar_randompic_inner.tpl');

      $sql="SELECT i.id as image_id, i.file as image_file, i.comment, i.path, i.tn_ext, c.id as catid, c.name, c.permalink, RAND() as rndvalue, i.name as imgname
            FROM ".CATEGORIES_TABLE." c, ".IMAGES_TABLE." i, ".IMAGE_CATEGORY_TABLE." ic
            WHERE c.id = ic.category_id
              AND ic.image_id = i.id
              AND i.level <= ".$user['level']." ";
      if($user['forbidden_categories']!="")
      {
        $sql.=" AND c.id NOT IN (".$user['forbidden_categories'].") ";
      }

      $sql.=" ORDER BY rndvalue
            LIMIT 0,1";


      $result = pwg_query($sql);
      if($result and $nfo = pwg_db_fetch_assoc($result))
      {
        $nfo['section']='category';
        $nfo['category']=array(
          'id' => $nfo['catid'],
          'name' => $nfo['name'],
          'permalink' => $nfo['permalink']
        );

        $template_datas = array(
          'LINK' => make_picture_url($nfo),
          'IMG' => get_thumbnail_url($nfo),
          'IMGNAME' => $nfo['imgname'],
          'IMGCOMMENT' => $nfo['comment'],
          'SHOWNAME' => $this->my_config['amm_randompicture_showname'],
          'SHOWCOMMENT' => $this->my_config['amm_randompicture_showcomment']
        );
      }
      else
      {
        $template_datas = array();
      }

    $local_tpl->assign('datas', $template_datas);
    $local_tpl->assign('plugin', array('PATH' => AMM_PATH));

    return($local_tpl->parse('body_page', true));
  }


  public function applyJS()
  {
    global $user, $template, $page;

    if(!array_key_exists('body_id', $page))
    {
      /*
       * it seems the error message reported on mantis:1476 is displayed because
       * the 'body_id' doesn't exist in the $page
       *
       * not abble to reproduce the error, but initializing the key to an empty
       * value if it doesn't exist may be a sufficient solution
       */
      $page['body_id']="";
    }

    if($this->displayRandomImageBlock && $page['body_id'] == 'theCategoryPage')
    {
      $local_tpl = new Template(AMM_PATH."admin/", "");
      $local_tpl->set_filename('body_page', dirname($this->filelocation).'/menu_templates/menubar_randompic.js.tpl');

      $data = array(
        "delay" => $this->my_config['amm_randompicture_periodicchange'],
        "blockHeight" => $this->my_config['amm_randompicture_height'],
        "firstPicture" => $this->ajax_amm_get_random_picture()
      );

      $local_tpl->assign('data', $data);

      $template->append('footer_elements', $local_tpl->parse('body_page', true));
    }

  }

} // AMM_PIP class


?>

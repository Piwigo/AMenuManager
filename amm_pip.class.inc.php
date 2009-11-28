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


    /*
      hide items from special & menu sections
    */
    $blocks=Array();
    $blocks['menu']=$menu->get_block('mbMenu');
    $blocks['special']=$menu->get_block('mbSpecials');

    $menuItems=array_merge($blocks['menu']->data, $blocks['special']->data);
    $this->sortSectionsItems();

    $blocks['menu']->data=Array();
    $blocks['special']->data=Array();

    foreach($this->my_config['amm_sections_items'] as $key => $val)
    {
      if(isset($menuItems[$key]))
      {
        $blocks[$val['container']]->data[$key]=$menuItems[$key];
      }
    }
    if(count($blocks['menu']->data)==0) $menu->hide_block('mbMenu');
    if(count($blocks['special']->data)==0) $menu->hide_block('mbSpecials');
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
      if($result and $nfo = mysql_fetch_array($result))
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
    global $user, $template;

    if($this->displayRandomImageBlock)
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

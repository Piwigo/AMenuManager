<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@grum.fr
    website  : http://photos.grum.fr
    PWG user : http://forum.piwigo.org/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  AMM_root : root class for plugin

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/common_plugin.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/css.class.inc.php');


class AMM_root extends common_plugin
{
  protected $css;   //the css object
  protected $defaultMenus = array(
    'favorites' => array('container' => 'special', 'visibility' => '', 'order' => 0, 'translation' => 'favorite_cat'),
    'most_visited' => array('container' => 'special', 'visibility' => '', 'order' => 1, 'translation' => 'most_visited_cat'),
    'best_rated' => array('container' => 'special', 'visibility' => '', 'order' => 2, 'translation' => 'best_rated_cat'),
    'random' => array('container' => 'special', 'visibility' => '', 'order' => 3, 'translation' => 'random_cat'),
    'recent_pics' => array('container' => 'special', 'visibility' => '', 'order' => 4, 'translation' => 'recent_pics_cat'),
    'recent_cats' => array('container' => 'special', 'visibility' => '', 'order' => 5, 'translation' => 'recent_cats_cat'),
    'calendar' => array('container' => 'special', 'visibility' => '', 'order' => 6, 'translation' => 'calendar'),
    'qsearch' => array('container' => 'menu', 'visibility' => '', 'order' => 0, 'translation' => 'qsearch'),
    'tags' => array('container' => 'menu', 'visibility' => '', 'order' => 1, 'translation' => 'Tags'),
    'search' => array('container' => 'menu', 'visibility' => '', 'order' => 2, 'translation' => 'Search'),
    'comments' => array('container' => 'menu', 'visibility' => '', 'order' => 3, 'translation' => 'comments'),
    'about' => array('container' => 'menu', 'visibility' => '', 'order' => 4, 'translation' => 'About'),
    'rss' => array('container' => 'menu', 'visibility' => '', 'order' => 5, 'translation' => 'Notification')
  );

  function AMM_root($prefixeTable, $filelocation)
  {
    $this->plugin_name="Advanced Menu Manager";
    $this->plugin_name_files="amm";
    parent::__construct($prefixeTable, $filelocation);

    $list=array('urls', 'personalised');
    $this->set_tables_list($list);
  }

  /* ---------------------------------------------------------------------------
  common AIP & PIP functions
  --------------------------------------------------------------------------- */

  /* this function initialize var $my_config with default values */
  public function init_config()
  {
    $this->my_config=array(
      'amm_links_show_icons' => 'y',
      'amm_links_title' => array(),
      'amm_randompicture_showname' => 'n',     //n:no, o:over, u:under
      'amm_randompicture_showcomment' => 'n',   //n:no, o:over, u:under
      'amm_randompicture_periodicchange' => 0,   //0: no periodic change ; periodic change in milliseconds
      'amm_randompicture_height' => 0,           //0: automatic, otherwise it's the fixed height in pixels
      'amm_randompicture_title' => array(),
      'amm_sections_items' => $this->defaultMenus
    );

    $languages=get_languages();
    foreach($languages as $key => $val)
    {
      if($key=='fr_FR')
      {
        $this->my_config['amm_links_title'][$key]=base64_encode('Liens');
        $this->my_config['amm_randompicture_title'][$key]=base64_encode('Une image au hasard');
      }
      else
      {
        $this->my_config['amm_links_title'][$key]=base64_encode('Links');
        $this->my_config['amm_randompicture_title'][$key]=base64_encode('A random picture');
      }
    }
  }

  public function load_config()
  {
    parent::load_config();
  }

  public function init_events()
  {
    add_event_handler('blockmanager_register_blocks', array(&$this, 'register_blocks') );
  }

  public function register_blocks( $menu_ref_arr )
  {
    $menu = & $menu_ref_arr[0];
    if ($menu->get_id() != 'menubar')
      return;
    $menu->register_block( new RegisteredBlock( 'mbAMM_randompict', 'Random pictures', 'AMM'));
    $menu->register_block( new RegisteredBlock( 'mbAMM_links', 'Links', 'AMM'));

    $sections=$this->get_sections(true);
    if(count($sections))
    {
      $id_done=array();
      foreach($sections as $key => $val)
      {
        if(!isset($id_done[$val['id']]))
        {
          $menu->register_block( new RegisteredBlock( 'mbAMM_personalised'.$val['id'], $val['title'], 'AMM'));
          $id_done[$val['id']]="";
        }
      }
    }
  }

  // return an array of urls (each url is an array)
  protected function get_urls($only_visible=false)
  {
    $returned=array();
    $sql="SELECT * FROM ".$this->tables['urls'];
    if($only_visible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $sql.=" ORDER BY position";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_array($result))
      {
        $row['label']=stripslashes($row['label']);
        $returned[]=$row;
      }
    }
    return($returned);
  }

  //return number of url
  protected function get_count_url($only_visible=false)
  {
    $returned=0;
    $sql="SELECT count(id) FROM ".$this->tables['urls'];
    if($only_visible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $result=pwg_query($sql);
    if($result)
    {
      $tmp=mysql_fetch_row($result);
      $returned=$tmp[0];
    }
    return($returned);
  }

  // return an array of sections (each section is an array)
  protected function get_sections($only_visible=false, $lang="", $only_with_content=true)
  {
    global $user;

    if($lang=="")
    {
      $lang=$user['language'];
    }

    $returned=array();
    $sql="SELECT * FROM ".$this->tables['personalised']."
WHERE (lang = '*' OR lang = '".$lang."') ";
    if($only_visible)
    {
      $sql.=" AND visible = 'y' ";
    }
    if($only_with_content)
    {
      $sql.=" AND content != '' ";
    }
    $sql.=" ORDER BY id, lang DESC ";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_array($result))
      {
        $returned[]=$row;
      }
    }
    return($returned);
  }


  protected function sortSectionsItemsCompare($a, $b)
  {
    if($a['container']==$b['container'])
    {
      if($a['order']==$b['order']) return(0);
      return(($a['order']<$b['order'])?-1:1);
    }
    else return(($a['container']<$b['container'])?-1:1);
  }

  protected function sortSectionsItems()
  {
    uasort($this->my_config['amm_sections_items'], array($this, "sortSectionsItemsCompare"));
  }

} // amm_root  class



?>

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

include_once(PHPWG_ROOT_PATH.'include/block.class.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCUsersGroups.class.inc.php');


class AMM_root extends CommonPlugin
{
  protected $defaultMenus = array(
    /* about visibility & accessibility system :
     *  - by default, everything is visible (users & groups)
     *  - items not visibles are listed (in release < 3.3.4, this rule wa applied to groups only)
     *
     * on the user interface, checked items are visibles => not checked items are stored
     */
    'favorites' => array('container' => 'special', 'visibility' => '/', 'order' => 0, 'translation' => 'My favorites'),
    'most_visited' => array('container' => 'special', 'visibility' => '/', 'order' => 1, 'translation' => 'Most visited'),
    'best_rated' => array('container' => 'special', 'visibility' => '/', 'order' => 2, 'translation' => 'Best rated'),
    'random' => array('container' => 'special', 'visibility' => '/', 'order' => 3, 'translation' => 'Random pictures'),
    'recent_pics' => array('container' => 'special', 'visibility' => '/', 'order' => 4, 'translation' => 'Recent pictures'),
    'recent_cats' => array('container' => 'special', 'visibility' => '/', 'order' => 5, 'translation' => 'Recent categories'),
    'calendar' => array('container' => 'special', 'visibility' => '/', 'order' => 6, 'translation' => 'Calendar'),
    'qsearch' => array('container' => 'menu', 'visibility' => '/', 'order' => 0, 'translation' => 'Quick search'),
    'tags' => array('container' => 'menu', 'visibility' => '/', 'order' => 1, 'translation' => 'Tags'),
    'search' => array('container' => 'menu', 'visibility' => '/', 'order' => 2, 'translation' => 'Search'),
    'comments' => array('container' => 'menu', 'visibility' => '/', 'order' => 3, 'translation' => 'Comments'),
    'about' => array('container' => 'menu', 'visibility' => '/', 'order' => 4, 'translation' => 'About'),
    'rss' => array('container' => 'menu', 'visibility' => '/', 'order' => 5, 'translation' => 'Notification')
  );
  protected $urlsModes=array(0 => 'new_window', 1 => 'current_window');



  public function __construct($prefixeTable, $filelocation)
  {
    $this->setPluginName("Advanced Menu Manager");
    $this->setPluginNameFiles("amm");
    parent::__construct($prefixeTable, $filelocation);

    $list=array('urls', 'personalised', 'personalised_langs', 'blocks');
    $this->setTablesList($list);
  }

  public function __destruct()
  {
    unset($this->defaultMenus);
    parent::__destruct();
  }

  /*
   * ---------------------------------------------------------------------------
   * common AIP & PIP functions
   * ---------------------------------------------------------------------------
   */

  /**
   * this function initialize config var with default values
   */
  public function initConfig()
  {
    $this->config=array(
      'amm_links_show_icons' => 'y',
      'amm_links_title' => array(),
      'amm_randompicture_preload' => 25,     //number preloaded random pictures
      'amm_randompicture_showname' => 'n',     //n:no, o:over, u:under
      'amm_randompicture_showcomment' => 'n',   //n:no, o:over, u:under
      'amm_randompicture_periodicchange' => 0,   //0: no periodic change ; periodic change in milliseconds
      'amm_randompicture_height' => 0,           //0: automatic, otherwise it's the fixed height in pixels
      'amm_randompicture_title' => array(),
      'amm_randompicture_selectMode' => 'a',     // a:all, f:webmaster's favorites, c:categories
      'amm_randompicture_selectCat' => array(),  // categories id list
      'amm_blocks_items' => $this->defaultMenus,
      'amm_albums_to_menu' => array(),
      'amm_old_blk_menubar' => '',                // keep a copy of piwigo's menubar config
      'newInstall' => 'n'
    );

    $languages=get_languages();
    foreach($languages as $key => $val)
    {
      if($key=='fr_FR')
      {
        $this->config['amm_links_title'][$key]=base64_encode('Liens');
        $this->config['amm_randompicture_title'][$key]=base64_encode('Une image au hasard');
      }
      else
      {
        $this->config['amm_links_title'][$key]=base64_encode('Links');
        $this->config['amm_randompicture_title'][$key]=base64_encode('A random picture');
      }
    }
  }

  public function registerBlocks( $menu_ref_arr )
  {
    $menu = & $menu_ref_arr[0];
    if ($menu->get_id() != 'menubar') return;

    $menu->register_block( new RegisteredBlock( 'mbAMM_randompict', 'Random pictures', 'AMM'));
    $menu->register_block( new RegisteredBlock( 'mbAMM_links', 'Links', 'AMM'));

    $blocks=$this->getPersonalisedBlocks();
    if(count($blocks))
    {
      $idDone=array();
      foreach($blocks as $key => $val)
      {
        if(!isset($idDone[$val['id']]))
        {
          $menu->register_block( new RegisteredBlock( 'mbAMM_personalised'.$val['id'], $val['title'], 'AMM'));
          $idDone[$val['id']]="";
        }
      }
    }

    $this->loadConfig();

    if(count($this->config['amm_albums_to_menu'])>0)
    {
      $sql="SELECT id, name
            FROM ".CATEGORIES_TABLE."
            WHERE id IN(".implode(',', $this->config['amm_albums_to_menu']).");";

      $result=pwg_query($sql);
      if($result)
      {
        while($row=pwg_db_fetch_assoc($result))
        {
          $row['name']=trigger_change('render_category_name', $row['name'], 'amm_album_to_menu');

          $menu->register_block( new RegisteredBlock( 'mbAMM_album'.$row['id'], $row['name'].' ('.l10n('g002_album2menu').') ', 'AMM'));
        }
      }
    }
  }


  /*
   *  ---------------------------------------------------------------------------
   *
   * Links functions
   *
   * ---------------------------------------------------------------------------
   */

  /**
   * return an array of links (each url is an array)
   *
   * @param Bool onlyVisible : if true, only visible links are returned
   * @return Array
   */
  protected function getLinks($onlyVisible=false)
  {
    $returned=array();
    $sql="SELECT id, label, url, mode, icon, position, visible, accessUsers, accessGroups
          FROM ".$this->tables['urls'];
    if($onlyVisible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $sql.=" ORDER BY position ASC, id ASC";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $returned[]=$row;
      }
    }
    return($returned);
  }

  /**
   * return values for a given link
   *
   * @param String $id : link id
   * @return Array
   */
  protected function getLink($id)
  {
    $returned=array();
    $sql="SELECT id, label, url, mode, icon, position, visible, accessUsers, accessGroups
          FROM ".$this->tables['urls']."
          WHERE id = '$id';";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $returned=$row;
      }
    }
    return($returned);
  }

  /**
   * set values for a link
   * if link id is empty : create a new link
   * if not, update link
   *
   * @param String $id : link id
   * @return Integer : -1 if fails
   *                   otherwise link id
   */
  protected function setLink($id, $label, $url, $mode, $icon, $visible, $accessUsers, $accessGroups)
  {
    if($id=='')
    {
      $sql="INSERT INTO ".$this->tables['urls']." (label,url,mode,icon,position,visible,accessUsers,accessGroups) VALUES
            (
             '".$label."',
             '".$url."',
             '$mode',
             '$icon',
             0,
             '$visible',
             '$accessUsers',
             '$accessGroups'
            );";
    }
    else
    {
      $sql="UPDATE ".$this->tables['urls']."
            SET label='".$label."',
                url='".$url."',
                mode='$mode',
                icon='$icon',
                visible='$visible',
                accessUsers='".$accessUsers."',
                accessGroups='".$accessGroups."'
            WHERE id='$id';";
    }
    $result=pwg_query($sql);
    if($result)
    {
      if($id=='')
      {
        return(pwg_db_insert_id());
      }
      else
      {
        return($id);
      }
    }
    return(-1);
  }

  /**
   * delete a given link
   *
   * @param String $id : link id
   * @return Bool : true if deleted, otherwise false
   */
  protected function deleteLink($id)
  {
    $sql="DELETE FROM ".$this->tables['urls']."
          WHERE id = '$id';";
    $result=pwg_query($sql);
    if($result) return(true);
    return(false);
  }



  /**
   * return number of links
   *
   * @param Bool onlyVisible : if true, only visible links are counted
   * @return Array
   */
  protected function getLinksCount($onlyVisible=false)
  {
    $returned=0;
    $sql="SELECT count(id) FROM ".$this->tables['urls'];
    if($onlyVisible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $result=pwg_query($sql);
    if($result)
    {
      $tmp=pwg_db_fetch_row($result);
      $returned=$tmp[0];
    }
    return($returned);
  }

  /**
   * set order from given links
   *
   * @param Array $links : array
   *                        each item is an array ('id' => '', 'order' =>'')
   * @return Bool :
   */
  protected function setLinksOrder($links)
  {
    $returned=true;

    foreach($links as $link)
    {
      $sql="UPDATE ".$this->tables['urls']."
            SET position='".$link['order']."'
            WHERE id='".$link['id']."';";
      $result=pwg_query($sql);
      if(!$result) $returned=false;
    }
    return($returned);
  }


  /*
   *  ---------------------------------------------------------------------------
   *
   * Personalised Blocks functions
   *
   * ---------------------------------------------------------------------------
   */


  /**
   * return an array of personalised blocks (each block is an array)
   *
   * @param Bool onlyVisible : if true, only visibles blocks are returned
   * @return Array
   */
  protected function getPersonalisedBlocks($onlyVisible=false, $lang='', $emptyContent=false)
  {
    global $user;

    if($lang=="") $lang=$user['language'];

    $returned=array();
    $sql="SELECT pt.id, pt.visible, pt.nfo, ptl.lang, ptl.title, ptl.content
          FROM ".$this->tables['personalised']." pt
            LEFT JOIN ".$this->tables['personalised_langs']." ptl
            ON pt.id=ptl.id
          WHERE (ptl.lang = '*' OR ptl.lang = '".pwg_db_real_escape_string($lang)."') ";

    if($onlyVisible) $sql.=" AND pt.visible = 'y' ";
    if($emptyContent==false) $sql.=" AND ptl.content != '' ";

    $sql.=" ORDER BY pt.id, ptl.lang ASC ";

    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $row['content'] = trigger_change('get_extended_desc', $row['content']);
        $returned[]=$row;
      }
    }
    return($returned);
  }

  /**
   * return values for a given personalised block
   *
   * @param String $id : link id
   * @return Array
   */
  protected function getPersonalisedBlock($id)
  {
    $returned=array(
      'visible' => false,
      'nfo' => '',
      'langs' => array()
    );

    $sql="SELECT visible, nfo
          FROM ".$this->tables['personalised']."
          WHERE id='$id';";

    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $returned['visible']=$row['visible'];
        $returned['nfo']=$row['nfo'];
      }

      $sql="SELECT lang, title, content
            FROM ".$this->tables['personalised_langs']."
            WHERE id='$id'
            ORDER BY lang ASC;";

      $result=pwg_query($sql);
      if($result)
      {
        while($row=pwg_db_fetch_assoc($result))
        {
          $returned['langs'][$row['lang']]=$row;
        }
      }
    }
    return($returned);
  }

  /**
   * set values for a personalised block
   * if block id is empty : create a new block
   * if not, update block
   *
   * @param String $id : block id
   * @return Integer : -1 if fails
   *                   otherwise block id
   */
  protected function setPersonalisedBlock($id, $visible, $nfo, $langs)
  {
    $ok=false;

    if($id=='')
    {
      $sql="INSERT INTO ".$this->tables['personalised']." (visible,nfo) VALUES
            (
             '$visible',
             '".$nfo."'
            );";
      $result=pwg_query($sql);
      if($result) $ok=true;
      $id=pwg_db_insert_id();
    }
    else
    {
      $sql="UPDATE ".$this->tables['personalised']."
            SET visible='$visible',
                nfo='".pwg_db_real_escape_string(stripslashes($nfo))."'
            WHERE id='$id';";
      $result=pwg_query($sql);
      if($result)
      {
        $sql="DELETE FROM ".$this->tables['personalised_langs']."
              WHERE id='$id';";
        $result=pwg_query($sql);

        if($result) $ok=true;
      }
    }

    if($ok)
    {
      $values=array();
      foreach($langs as $key => $lang)
      {
        $values[]="('$id',
                    '".substr($lang['lang'],0,5)."',
                    '".pwg_db_real_escape_string(stripslashes($lang['title']))."',
                    '".pwg_db_real_escape_string(stripslashes($lang['content']))."')";
      }
      $sql="INSERT INTO ".$this->tables['personalised_langs']." VALUES ".implode(',', $values);
      $result=pwg_query($sql);

      if($result) $ok=true;
    }

    if($ok) return($id);
    return(-1);
  }

  /**
   * delete a given personalised block
   *
   * @param String $id : block id
   * @return Bool : true if deleted, otherwise false
   */
  protected function deletePersonalisedBlock($id)
  {
    $sql="DELETE FROM ".$this->tables['personalised']."
          WHERE id = '$id';";
    $result=pwg_query($sql);
    if($result)
    {
      $sql="DELETE FROM ".$this->tables['personalised_langs']."
            WHERE id = '$id';";
      $result=pwg_query($sql);

      if($result) return(true);
    }

    return(false);
  }



  /**
   * return an array of all registered blocks
   * each array item is an array :
   *  String  'id'      => ''
   *  Integer 'order'   => 0
   *  Array   'users'   => array()
   *  Array   'groups'  => array()
   *  String  'name'    => ''
   *  String  'owner'   => ''
   *
   * @param Bool $userFiltered : true if returned blocks are filtered to current
   *                             user
   * @return Array
   */
  protected function getRegisteredBlocks($userFiltered=false)
  {
    global $conf, $user;

    $returned=array();
    $order=0;
    $users=new GPCUsers();
    $groups=new GPCGroups();

    $menu = new BlockManager('menubar');
    $menu->load_registered_blocks();
    $registeredBlocks = $menu->get_registered_blocks();

    $nbExistingGroups=0;
    $sql="SELECT COUNT(id) AS nbGroup
          FROM `".GROUPS_TABLE."`;";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $nbExistingGroups=$row['nbGroup'];
      }
    }


    $userGroups=array();
    $sql="SELECT group_id
          FROM ".USER_GROUP_TABLE."
          WHERE user_id = '".$user['id']."';";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $userGroups[$row['group_id']]='';
      }
    }

    $sql="SELECT id, `order`, users, `groups`
          FROM ".$this->tables['blocks']."
          ORDER BY `order`;";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $row['users']=(trim($row['users'])=='')?array():explode(',', $row['users']);
        $row['groups']=(trim($row['groups'])=='')?array():explode(',', $row['groups']);

        if(isset($registeredBlocks[$row['id']]))
        {
          $ok=true;
          if($userFiltered)
          {
            // filter access enabled

            $users->setAlloweds($row['users'], false);
            if($users->isAllowed($user['status']))
            {
              // user is authorized, check group
              if(count($userGroups))
              {
                // users is attached to one group or more, check access for each group
                // at least, one group must have right access
                $nbOk=count($userGroups);

                $groups->setAlloweds($row['groups'], false);
                foreach($row['groups'] as $val)
                {
                  if(isset($userGroups[$val]) and !$groups->isAllowed($val)) $nbOk--;
                }

                // no group is authorized to access the menu
                if($nbOk==0) $ok=false;
              }
              elseif($nbExistingGroups>0 and count($row['groups'])>0)
              {
                // if user is not attached to any group and if at least there 1
                // existing group, no authorization to access to the menu
                $ok=false;
              }
            }
            else
            {
              // user not authorized
              $ok=false;
            }
          }

          if($ok)
          {
            $returned[$row['id']]=array(
              'id'    => $row['id'],
              'order' => $row['order'],
              'users' => $row['users'],
              'groups'=> $row['groups'],
              'name'  => $registeredBlocks[$row['id']]->get_name(),
              'owner' => $registeredBlocks[$row['id']]->get_owner()
            );
            $order=$row['order'];
          }
          unset($registeredBlocks[$row['id']]);
        }
      }
    }

    /*
     * add new blocks, unknown from plugin
     * by default, users & groups are visibles
     */
    foreach($registeredBlocks as $key=>$val)
    {
      $order+=10;

      $returned[$key]=array(
        'id'    => $key,
        'order' => $order,
        'users' => array(),
        'groups'=> array(),
        'name'  => $val->get_name(),
        'owner' => $val->get_owner()
      );
    }

    return($returned);
  }


  /**
   * set order for registered blocks
   *
   * note : Piwigo's order is maintened
   *
   * @param Array $block : array of block ; each items is an array
   *                        String  'id'    => ''
   *                        Integer 'order' => ''
   *                        Array   'users'   => array()
   *                        Array   'groups'  => array()
   * @return Bool : true, false is something is wrong
   */
  protected function setRegisteredBlocks($blocks)
  {
    $returned=true;

    $sql="DELETE FROM ".$this->tables['blocks'];
    pwg_query($sql);

    foreach($blocks as $block)
    {
      $sql="INSERT INTO ".$this->tables['blocks']." VALUES (
            '".$block['id']."',
            '".$block['order']."',
            '".implode(',', $block['users'])."',
            '".implode(',', $block['groups'])."'
            );";
      $result=pwg_query($sql);
      if(!$result) $returned=false;
    }

    return($returned);
  }


  static public function checkPluginRelease()
  {
    global $template, $prefixeTable, $conf;

    $config=array();
    GPCCore::loadConfig('amm', $config);

    if($config['installed']!=AMM_VERSION2)
    {
      /* the plugin was updated without being deactivated
       * deactivate + activate the plugin to process the database upgrade
       */
      include(AMM_PATH."amm_install.class.inc.php");
      $amm=new AMM_Install($prefixeTable, dirname(__FILE__));
      $amm->deactivate();
      $amm->activate();
      if(is_object($template)) $template->delete_compiled_templates();
    }
  }





  protected function sortCoreBlocksItemsCompare($a, $b)
  {
    if($a['container']==$b['container'])
    {
      if($a['order']==$b['order']) return(0);
      return(($a['order']<$b['order'])?-1:1);
    }
    else return(($a['container']<$b['container'])?-1:1);
  }

  protected function sortCoreBlocksItems()
  {
    uasort($this->config['amm_blocks_items'], array($this, "sortCoreBlocksItemsCompare"));
  }

} // amm_root  class



?>

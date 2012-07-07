<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://www.grum.fr

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  AMM_Install : classe to manage plugin install

  --------------------------------------------------------------------------- */
  include_once('amm_version.inc.php');
  include_once('amm_root.class.inc.php');
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCTables.class.inc.php');


  class AMM_install extends AMM_root
  {
    private $tablef;

    public function __construct($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->tablef= new GPCTables($this->tables);
    }

    public function __destruct()
    {
      unset($this->tablesManager);
      unset($this->tablef);
      parent::__destruct();
    }


    /**
     * function for installation process
     *
     * @return Bool : true if install process is ok, otherwise false
     */
    public function install()
    {
      $this->initConfig();
      $this->loadConfig();
      $this->config['installed']=AMM_VERSION2;
      $this->config['newInstall']='y';
      $this->saveConfig();

      $tables_def=array(
"CREATE TABLE  `".$this->tables['urls']."` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(50) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `mode` int(11) NOT NULL default '0',
  `icon` varchar(50) NOT NULL default '',
  `position` int(11) NOT NULL default '0',
  `visible` char(1) NOT NULL default 'y',
  `accessUsers` varchar(1024) NOT NULL,
  `accessGroups` varchar(1024) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `order_key` (`position`)
) DEFAULT CHARACTER SET ".DB_CHARSET." COLLATE utf8_general_ci",

"CREATE TABLE  `".$this->tables['personalised']."` (
  `id` int(11) NOT NULL auto_increment,
  `visible` char(1) NOT NULL default 'y',
  `nfo` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) DEFAULT CHARACTER SET ".DB_CHARSET." COLLATE utf8_general_ci",

"CREATE TABLE `".$this->tables['personalised_langs']."` (
  `id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `lang` CHAR(5)  NOT NULL default '*',
  `title` VARCHAR(255)  NOT NULL default '',
  `content` TEXT  NOT NULL,
  PRIMARY KEY (`id`, `lang`)
) DEFAULT CHARACTER SET ".DB_CHARSET." COLLATE utf8_general_ci",

"CREATE TABLE `".$this->tables['blocks']."` (
  `id` VARCHAR(40)  NOT NULL,
  `order` INTEGER UNSIGNED NOT NULL,
  `users` VARCHAR(1024)  NOT NULL,
  `groups` VARCHAR(1024)  NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `byOrder`(`order`)
) DEFAULT CHARACTER SET ".DB_CHARSET." COLLATE utf8_general_ci"

);
      //$table_def array
      //$tables_def = create_table_add_character_set($tables_def);
      $result=$this->tablef->create($tables_def);
      unset($tables_def);

      GPCCore::register($this->getPluginName(), AMM_VERSION, AMM_GPC_NEEDED);

      return($result);
    }


    /**
     * function for uninstall process
     */
    public function uninstall()
    {
      $this->deleteConfig();
      $this->tablef->drop();
      GPCCore::unregister($this->getPluginName());
    }

    public function activate()
    {
      global $template;

      $this->initConfig();
      $this->loadConfig();

      $this->udpateTablesDef();

      $this->config['newInstall']='n';
      $this->config['installed']=AMM_VERSION2; //update the installed release number
      $this->saveConfig();

      GPCCore::register($this->getPluginName(), AMM_VERSION, AMM_GPC_NEEDED);
    }

    public function deactivate()
    {
      $this->initConfig();
      $this->loadConfig();
      $this->restoreMenuConfig();
    }


    /**
     * update tables & config between releases
     *
     */
    protected function udpateTablesDef()
    {
      global $conf;

      /* AMM release earlier than the 2.1.3 uses two parameters to manage the display
       * of the menu items ("amm_sections_modspecials" and "amm_sections_modmenu")
       *
       * These two parameters are replaced by a single parameter "amm_blocks_items"
       *
       * This function aim to import the old conf into the new conf property
      */
      if(isset($this->config['amm_sections_modspecials']))
      {
        foreach($this->config['amm_sections_modspecials'] as $key=>$val)
        {
          $this->config['amm_blocks_items'][$key]['visibility']=($val=="y")?"guest,generic,normal,admin/":"admin/";
        }
        unset($this->config['amm_sections_modspecials']);
      }

      if(isset($this->config['amm_sections_modmenu']))
      {
        foreach($this->config['amm_sections_modmenu'] as $key=>$val)
        {
          $this->config['amm_blocks_items'][$key]['visibility']=($val=="y")?"guest,generic,normal,admin/":"admin/";
        }
        unset($this->config['amm_sections_modmenu']);
      }

      if(!array_key_exists('installed', $this->config))
      {
        /*
         * if key does not exist, probably try to update a plugin older than the
         * 2.2.0 release
         */
        $this->config['installed']="02.01.06";
      }

      switch($this->config['installed'])
      {
        case '02.01.06':
          $this->config['newInstall']='n';
          $this->updateFrom_020106();
        case '02.02.00':
        case '02.02.01':
        case '02.02.02':
        case '02.02.03':
          $this->config['newInstall']='n';
          $this->updateFrom_020200();
        case '03.00.00':
          $this->config['newInstall']='n';
          $this->updateFrom_030000();
        default:
          /*
           * default is applied for fresh install
           */

          if($this->config['installed']<='02.02.03' or
             $this->config['newInstall']=='y')
          {
            /*
             * if    new install
             *    or plugin updated from a release <= 2.2.3
             *    or plugin
             *
             * update AMM menu from piwigo's menu
             */
            $this->backupMenuConfig(true);
          }
          else
          {
            /*
             * plugin actived without being installed or updated, only backup
             * the piwigo's menu
             */
            $this->backupMenuConfig(false);
          }
          break;
      }

    }

    /**
     * update the database from the release 2.1.6
     *
     * - update config for menu translation
     * - update fields length for table 'personalised'
     */
    private function updateFrom_020106()
    {
      $sql="ALTER TABLE `".$this->tables['personalised']."`
            MODIFY COLUMN `id` INTEGER  NOT NULL AUTO_INCREMENT,
            MODIFY COLUMN `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY COLUMN `nfo` VARCHAR(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
      pwg_query($sql);

      foreach($this->config['amm_blocks_items'] as $key => $val)
      {
        $this->config['amm_blocks_items'][$key]['translation'] = $this->defaultMenus[$key]['translation'];
      }
    }



    /**
     * update the database from the release 2.2.0
     *
     * - create 'personalised_lang' table ; filled from the 'personalised' table
     *   values
     * - modify 'personalised' table structure (remove lang attributes)
     * - modify 'urls' table structure (add users&group access)
     * - update 'urls' table values (default values for users access)
     * - update config (parameter 'amm_sections_items' is renamed into 'amm_blocks_items')
     */
    private function updateFrom_020200()
    {
      global $user;

      $tables_def=array(
"CREATE TABLE `".$this->tables['personalised_langs']."` (
  `id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `lang` CHAR(5)  NOT NULL default '*',
  `title` VARCHAR(255)  NOT NULL default '',
  `content` TEXT  NOT NULL,
  PRIMARY KEY (`id`, `lang`)
)  DEFAULT CHARACTER SET ".DB_CHARSET." COLLATE utf8_general_ci",

"CREATE TABLE `".$this->tables['blocks']."` (
  `id` VARCHAR(40)  NOT NULL,
  `order` INTEGER UNSIGNED NOT NULL,
  `users` VARCHAR(1024)  NOT NULL,
  `groups` VARCHAR(1024)  NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `byOrder`(`order`)
)  DEFAULT CHARACTER SET ".DB_CHARSET." COLLATE utf8_general_ci"
      );

      $this->tablef->setTables(array(
        $this->tables['personalised_langs'],
        $this->tables['blocks'])
      );
      $result=$this->tablef->create($tables_def);

      $sql="INSERT INTO `".$this->tables['personalised_langs']."`
              SELECT pap.id, pap.lang, pap.title, pap.content
              FROM `".$this->tables['personalised']."` pap
              WHERE pap.title!='' OR pap.content!='';";
      pwg_query($sql);


      if(!isset($user['language']) or $user['language']=='')
      {
        $sql="SELECT language FROM ".USER_INFOS_TABLE." WHERE user_id='1';";
        $result=pwg_query($sql);
        if($result)
        {
          while($row=pwg_db_fetch_assoc($result))
          {
            $user['language']=$row['language'];
          }
        }
      }
      $sql="DELETE FROM `".$this->tables['personalised']."` WHERE lang!='".$user['language']."';";
      pwg_query($sql);

      $sql="ALTER TABLE `".$this->tables['personalised']."` DROP COLUMN `lang`,
             DROP COLUMN `title`,
             DROP COLUMN `content`;";
      pwg_query($sql);

      $sql="ALTER TABLE `".$this->tables['urls']."`
            ADD COLUMN `accessUsers` VARCHAR(1024)  NOT NULL AFTER `visible`,
            ADD COLUMN `accessGroups` VARCHAR(1024)  NOT NULL AFTER `accessUsers`;";
      pwg_query($sql);

      if(isset($this->config['amm_sections_items']))
      {
        $this->config['amm_blocks_items']=$this->config['amm_sections_items'];
        unset($this->config['amm_sections_items']);
      }


      $usersList=array('guest', 'generic', 'normal', 'webmaster', 'admin');
      foreach($this->config['amm_blocks_items'] as $key => $item)
      {
        $tmp0=explode('/', $item['visibility']);
        $this->config['amm_blocks_items'][$key]['visibility']=implode(',', array_diff($usersList, explode(',', $tmp0[0]))).'/'.$tmp0[1];
      }
    }



    /**
     * update the database from the release 3.0.0
     *
     * - add auto increment on personnalised_lang table
     */
    private function updateFrom_030000()
    {
      global $user;

      $sql="ALTER TABLE `".$this->tables['personalised']."` MODIFY COLUMN `id` INTEGER  NOT NULL AUTO_INCREMENT;";
      pwg_query($sql);
    }




    /**
     * report hidden menu from piwigo's config to AMM config
     */
    private function backupMenuConfig($updateMenu=false)
    {
      global $conf;

      $this->config['amm_old_blk_menubar']=$conf['blk_menubar'];
      pwg_query("UPDATE ".CONFIG_TABLE." SET value = '' WHERE param='blk_menubar';");

      if($updateMenu and $conf['blk_menubar']!='')
      {
        $tmp=unserialize($conf['blk_menubar']);
        foreach($tmp as $key => $val)
        {
          pwg_query("REPLACE INTO ".$this->tables['blocks']." VALUES ('$key', '".abs($val)."', '".($val<0?'guest,generic,normal,webmaster,admin':'')."', '');");
        }
      }
    }

    /**
     * restore piwigo's menu
     */
    private function restoreMenuConfig()
    {
      if($this->config['amm_old_blk_menubar']!='')
        pwg_query("UPDATE ".CONFIG_TABLE." SET value = '".pwg_db_real_escape_string($this->config['amm_old_blk_menubar'])."' WHERE param='blk_menubar';");
    }



  } //class

?>

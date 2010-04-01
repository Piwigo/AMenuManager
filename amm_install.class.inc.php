<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.fr
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  MyPolls_Install : classe to manage plugin install

  --------------------------------------------------------------------------- */
  include_once('amm_version.inc.php');
  include_once('amm_root.class.inc.php');
  include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCTables.class.inc.php');


  class AMM_install extends AMM_root
  {
    private $tablesManager;
    private $exportfile;

    public function AMM_install($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->tablesManager= new GPCTables($this->tables);
      $this->exportfile=dirname($this->getFileLocation()).'/'.$this->getPluginNameFiles().'.sql';
    }

    /*
        function for installation process
        return true if install process is ok, otherwise false
    */
    public function install()
    {
      $this->initConfig();
      $this->loadConfig();
      $this->config['installed']=AMM_VERSION2;
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
  PRIMARY KEY  (`id`),
  KEY `order_key` (`position`)
)",

"CREATE TABLE  `".$this->tables['personalised']."` (
  `id` int(11) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `visible` char(1) NOT NULL default 'y',
  `nfo` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`lang`)
)"
);
      //$table_def array
      $tables_def = create_table_add_character_set($tables_def);
      $result=$this->tablesManager->create($tables_def);
      return($result);
    }


    /*
        function for uninstall process
    */
    public function uninstall()
    {
      $this->tablesManager->export($this->exportfile);
      $this->deleteConfig();
      $this->tablesManager->drop();
    }

    public function activate()
    {
      global $template;

      $this->initConfig();
      $this->loadConfig();

      $this->udpateTablesDef();

      $this->config['installed']=AMM_VERSION2; //update the installed release number
      $this->saveConfig();
    }

    public function deactivate()
    {
    }


    /**
     * update tables & config between releases
     *
     */
    protected function udpateTablesDef()
    {
      /* AMM release earlier than the 2.1.3 uses two parameters to manage the display
       * of the menu items ("amm_sections_modspecials" and "amm_sections_modmenu")
       *
       * These two parameters are replaced by a single parameter "amm_sections_items"
       *
       * This function aim to import the old conf into the new conf property
      */
      if(isset($this->config['amm_sections_modspecials']))
      {
        foreach($this->config['amm_sections_modspecials'] as $key=>$val)
        {
          $this->config['amm_sections_items'][$key]['visibility']=($val=="y")?"guest,generic,normal,admin/":"admin/";
        }
        unset($this->config['amm_sections_modspecials']);
      }

      if(isset($this->config['amm_sections_modmenu']))
      {
        foreach($this->config['amm_sections_modmenu'] as $key=>$val)
        {
          $this->config['amm_sections_items'][$key]['visibility']=($val=="y")?"guest,generic,normal,admin/":"admin/";
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

      if($this->config['installed']<="02.01.06")
      {
        /*
         * 2.2.0 updates
         *
         * - update fields length for table 'personalised'
         * - update config for menu translation
         */
        $sql="ALTER TABLE `".$this->tables['personalised']."`
              MODIFY COLUMN `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
              MODIFY COLUMN `nfo` VARCHAR(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
        pwg_query($sql);

        foreach($this->config['amm_sections_items'] as $key => $val)
        {
          $this->config['amm_sections_items'][$key]['translation'] = $this->defaultMenus[$key]['translation'];
        }
      }
    }

  } //class

?>

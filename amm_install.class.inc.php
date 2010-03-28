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
  include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/tables.class.inc.php');


  class AMM_install extends AMM_root
  {
    private $tablef;
    private $exportfile;

    public function AMM_install($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->tablef= new manage_tables($this->tables);
      $this->exportfile=dirname($this->filelocation).'/'.$this->plugin_name_files.'.sql';
    }

    /*
        function for installation process
        return true if install process is ok, otherwise false
    */
    public function install()
    {
      $this->init_config();
      $this->load_config();
      $this->my_config['installed']=AMM_VERSION2;
      $this->save_config();

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
      $result=$this->tablef->create_tables($tables_def);
      return($result);
    }


    /*
        function for uninstall process
    */
    public function uninstall()
    {
      $this->tablef->export($this->exportfile);
      $this->delete_config();
      $this->tablef->drop_tables();
    }

    public function activate()
    {
      global $template;

      $this->init_config();
      $this->load_config();

      $this->udpateTablesDef();

      $this->my_config['installed']=AMM_VERSION2; //update the installed release number
      $this->save_config();
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
      if(isset($this->my_config['amm_sections_modspecials']))
      {
        foreach($this->my_config['amm_sections_modspecials'] as $key=>$val)
        {
          $this->my_config['amm_sections_items'][$key]['visibility']=($val=="y")?"guest,generic,normal,admin/":"admin/";
        }
        unset($this->my_config['amm_sections_modspecials']);
      }

      if(isset($this->my_config['amm_sections_modmenu']))
      {
        foreach($this->my_config['amm_sections_modmenu'] as $key=>$val)
        {
          $this->my_config['amm_sections_items'][$key]['visibility']=($val=="y")?"guest,generic,normal,admin/":"admin/";
        }
        unset($this->my_config['amm_sections_modmenu']);
      }

      if(!array_key_exists('installed', $this->my_config))
      {
        /*
         * if key does not exist, probably try to update a plugin older than the
         * 2.2.0 release
         */
        $this->my_config['installed']="02.01.06";
      }

      if($this->my_config['installed']<="02.01.06")
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

        foreach($this->my_config['amm_sections_items'] as $key => $val)
        {
          $this->my_config['amm_sections_items'][$key]['translation'] = $this->defaultMenus[$key]['translation'];
        }
      }
    }

  } //class

?>

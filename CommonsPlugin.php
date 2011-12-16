<?php


if(class_exists('Omeka_Plugin_Abstract')) {
    
    class CommonsPlugin extends Omeka_Plugin_Abstract
    {
        protected $_hooks = array(
            'admin_append_to_items_show_secondary',
            'admin_theme_header',
            'admin_append_to_collections_form',
            'after_save_form_collection',
            'after_save_form_item',
            'config',
            'config_form',
            'install',
            'uninstall'
        );
        
        protected $_filters = array(
            'admin_items_form_tabs',
            'admin_navigation_main'
        );
        
        protected $_options = array();
        
        public function hookAdminAppendToItemsShowSecondary($item)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $db = get_db();
            $commonsRecordTable = $db->getTable('CommonsRecord');
    
            $commonsCollection = $commonsRecordTable->itemPartOfCommonsCollection($item);
            if($commonsCollection) {
                $collection = $db->getTable('Collection')->find($commonsCollection->record_id);
                $link = "<p id='commons-item-collection'>This item is part of the Omeka Commons via collection {$collection->name}</p>";
                $license = $commonsCollection->license;
            } else {
                $findParams = array('record_type'=>'Item', 'record_id'=>$item->id);
                $commonsRecords = $commonsRecordTable->findBy($findParams);
                if(empty($commonsRecords)) {
                    $link = "<p id='commons-item-add'>Make this item part of the Omeka Commons</p>";
                } else {
                    $commonsRecord = $commonsRecords[0];
                    $link = "<p id='commons-item-status'>Already part of the Omeka Commons</p>";
                    $license = $commonsRecord->license;
                }
            }
    
            $html = "<div class='info-panel'>";
            $html .= "<h2>Omeka Commons</h2>";
            $html .= $link;
            $html .= "</div>";
            echo $html;
        }
        
        public function hookAdminThemeHeader()
        {
            queue_js('commons');
            queue_css('commons');
        }
        
        public function hookAdminAppendToCollectionsForm($collection)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
            echo $this->commonsForm($record, 'Collection');
        }
        
        public function hookAfterSaveFormCollection($collection)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
            if(isset($_POST['in_commons']) && $_POST['in_commons'] == 'on' ) {
                if(!$record) {
                    $record = new CommonsRecord();
                    $record->record_id = $collection->id;
                    $record->record_type = 'Collection';
                }
                $record->license = $_POST['commons_license'];
                $record->save();
                //true on a collection record sends along the item data, too.
                $record->export(true);
            } else {
                if($record) {
                    $record->delete();
                }
            }
        }
    
        public function filterAdminNavigationMain($tabs)
        {
            $tabs['Omeka Commons'] = uri('commons');
            return $tabs;
        }
        
        public function filterAdminItemsFormTabs($tabs, $item)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
            $tabs['Omeka Commons'] = $this->commonsForm($record, 'Item');
            return $tabs;
        }
        
        public function hookAfterSaveFormItem($item, $post)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
            if(!$item->public) {
                if($record) {
                    $record->delete();
                }
                throw new Omeka_Validator_Exception("Only public Items can be part of the Omeka Commons");
            }
    
            if(isset($_POST['in_commons']) && $_POST['in_commons'] == 'on' ) {
                if(!$record) {
                    $record = new CommonsRecord();
                    $record->record_id = $item->id;
                    $record->record_type = 'Item';
                }
                $record->license = $_POST['commons_license'];
                $record->save();
                $record->export();
            } else {
                if($record) {
                    $record->delete();
                }
            }
        }
        
        public function hookConfig()
        {
            $this->setOptions($_POST);
        }
        
        public function hookConfigForm()
        {
            
            include 'config_form.php';
        }
        
        public function hookInstall()
        {
            $db = get_db();
            $sql = "
                CREATE TABLE IF NOT EXISTS `$db->CommonsRecord` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `commons_id` int(10) unsigned DEFAULT NULL,
                  `record_id` int(10) unsigned NOT NULL,
                  `record_type` tinytext NOT NULL,
                  `license` enum('cc-0','by','by-nc','by-nd','by-nc-nd','by-nc-sa','by-sa') DEFAULT NULL,
                  `last_export` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `status` tinytext COLLATE utf8_unicode_ci,
                  PRIMARY KEY (`id`),
                  KEY `record_id` (`record_id`),
                  KEY `license` (`license`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
            ";
            
            $db->exec($sql);
    
        }
        
        public function hookUninstall()
        {
            
            
            
        }
        
        private function setOptions($post)
        {
            unset($post['install_plugin']);
            foreach($post as $name=>$value) {
                set_option($name, $value);
            }
        }
        
        private function commonsForm($record, $record_type)
        {
            $html = "<fieldset>";
            $html .= "<label for='in_commons'>Make part of Omeka Commons?</label><input type='checkbox' name='in_commons'";
    
            if($record) {
                $html .= " checked='checked' />";
            } else {
                $html .= " />";
            }
    
            $html .= "</fieldset>";
            return $html;
        }
    }
        
} else {
    
    class CommonsPlugin
    {
        protected $_hooks = array(
            'admin_append_to_items_show_secondary',
            'admin_theme_header',
            'admin_append_to_collections_form',
            'after_save_form_collection',
            'after_save_form_item',
            'config',
            'config_form',
            'install',
            'uninstall'
        );
        
        protected $_filters = array(
            'admin_items_form_tabs',
            'admin_navigation_main'
        );

        public function __construct()
    {
        $this->_db = Omeka_Context::getInstance()->getDb();
    }
    
    /**
     * Set up the plugin to hook into Omeka.
     *
     * Adds the plugin's hooks and filters. Plugin writers must call this method
     * after instantiating their plugin class.
     */
    public function setUp()
    {
        $this->_addHooks();
        $this->_addFilters();
    }
    
    /**
     * Set options with default values.
     *
     * Plugin authors may want to use this convenience method in their install
     * hook callback.
     */
    protected function _installOptions()
    {
        $options = $this->_options;
        if (!is_array($options)) {
            return;
        }
        foreach ($options as $name => $value) {
            // Don't set options without default values.
            if (!is_string($name)) {
                continue;
            }
            set_option($name, $value);
        }
    }
    
    /**
     * Delete all options.
     *
     * Plugin authors may want to use this convenience method in their uninstall
     * hook callback.
     */
    protected function _uninstallOptions()
    {
        $options = self::$_options;
        if (!is_array($options)) {
            return;
        }
        foreach ($options as $name => $value) {
            delete_option($name);
        }
    }
    
    /**
     * Validate and add hooks.
     */
    private function _addHooks()
    {
        $hookNames = $this->_hooks;
        if (!is_array($hookNames)) {
            return;
        }
        foreach ($hookNames as $hookName) {
            $functionName = 'hook' . Inflector::camelize($hookName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Hook callback "' . $functionName . '" does not exist.');
            }
            add_plugin_hook($hookName, array($this, $functionName));
        }
    }
    
    /**
     * Validate and add filters.
     */
    private function _addFilters()
    {
        $filterNames = $this->_filters;
        if (!is_array($filterNames)) {
            return;
        }
        foreach ($filterNames as $filterName) {
            $functionName = 'filter' . Inflector::camelize($filterName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Filter callback "' . $functionName . '" does not exist.');
            }
            add_filter($filterName, array($this, $functionName));
        }
    }
        
        
        
        public function hookAdminAppendToItemsShowSecondary($item)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $db = get_db();
            $commonsRecordTable = $db->getTable('CommonsRecord');
    
            $commonsCollection = $commonsRecordTable->itemPartOfCommonsCollection($item);
            if($commonsCollection) {
                $collection = $db->getTable('Collection')->find($commonsCollection->record_id);
                $link = "<p id='commons-item-collection'>This item is part of the Omeka Commons via collection {$collection->name}</p>";
                $license = $commonsCollection->license;
            } else {
                $findParams = array('record_type'=>'Item', 'record_id'=>$item->id);
                $commonsRecords = $commonsRecordTable->findBy($findParams);
                if(empty($commonsRecords)) {
                    $link = "<p id='commons-item-add'>Make this item part of the Omeka Commons</p>";
                } else {
                    $commonsRecord = $commonsRecords[0];
                    $link = "<p id='commons-item-status'>Already part of the Omeka Commons</p>";
                    $license = $commonsRecord->license;
                }
            }
    
            $html = "<div class='info-panel'>";
            $html .= "<h2>Omeka Commons</h2>";
            $html .= $link;
            $html .= cc_license_link($license);
            $html .= "</div>";
            echo $html;
        }
        
        public function hookAdminThemeHeader()
        {
            queue_js('commons');
            queue_css('commons');
        }
        
        public function hookAdminAppendToCollectionsForm($collection)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
            echo $this->commonsForm($record, 'Collection');
        }
        
        public function hookAfterSaveFormCollection($collection)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
            if(isset($_POST['in_commons']) && $_POST['in_commons'] == 'on' ) {
                if(!$record) {
                    $record = new CommonsRecord();
                    $record->record_id = $collection->id;
                    $record->record_type = 'Collection';
                }
                $record->license = $_POST['commons_license'];
                $record->save();
                //true on a collection record sends along the item data, too.
                $record->export(true);
            } else {
                if($record) {
                    $record->delete();
                }
            }
        }
    
        public function filterAdminNavigationMain($tabs)
        {
            $tabs['Omeka Commons'] = uri('commons');
            return $tabs;
        }
        
        public function filterAdminItemsFormTabs($tabs, $item)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
            $tabs['Omeka Commons'] = $this->commonsForm($record, 'Item');
            return $tabs;
        }
        
        public function hookAfterSaveFormItem($item, $post)
        {
            if(!get_option('commons_key')) {
                return;
            }
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
            if(!$item->public) {
                if($record) {
                    $record->delete();
                }
                throw new Omeka_Validator_Exception("Only public Items can be part of the Omeka Commons");
            }
    
            if(isset($_POST['in_commons']) && $_POST['in_commons'] == 'on' ) {
                if(!$record) {
                    $record = new CommonsRecord();
                    $record->record_id = $item->id;
                    $record->record_type = 'Item';
                }
                $record->license = $_POST['commons_license'];
                $record->save();
                $record->export();
            } else {
                if($record) {
                    $record->delete();
                }
            }
        }
        
        public function hookConfig()
        {
            $this->setOptions($_POST);
        }
        
        public function hookConfigForm()
        {
            
            include 'config_form.php';
        }
        
        public function hookInstall()
        {
            $db = get_db();
            $sql = "
                CREATE TABLE IF NOT EXISTS `$db->CommonsRecord` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `commons_id` int(10) unsigned DEFAULT NULL,
                  `record_id` int(10) unsigned NOT NULL,
                  `record_type` tinytext NOT NULL,
                  `last_export` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `status` tinytext COLLATE utf8_unicode_ci,
                  PRIMARY KEY (`id`),
                  KEY `record_id` (`record_id`),
                  KEY `license` (`license`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
            ";
            
            $db->exec($sql);
    
        }
        
        public function hookUninstall()
        {
            
            
            
        }
        
        private function setOptions($post)
        {
            unset($post['install_plugin']);
            foreach($post as $name=>$value) {
                set_option($name, $value);
            }
        }
        
        private function commonsForm($record, $record_type)
        {
            $html = "<fieldset>";
            $html .= "<label for='in_commons'>Make part of Omeka Commons?</label><input type='checkbox' name='in_commons'";
    
            if($record) {
                $html .= " checked='checked' />";
            } else {
                $html .= " />";
            }
    
            $html .= "</fieldset>";
            return $html;
        }
    }
}

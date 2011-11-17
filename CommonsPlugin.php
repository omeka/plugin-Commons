<?php

if(class_exists('Omeka_Plugin_Abstract')) {
    _log('abstract exists');
    class CommonsPlugin extends Omeka_Plugin_Abstract
    {
        protected $_hooks = array(
            'admin_append_to_items_show_secondary',
            'admin_theme_header'
        
        );
        
        public function hookAdminAppendToItemsShowSecondary()
        {
            
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $plugin = $request->getModuleName();
            $action = $request->getActionName();
            $params = $request->getParams();
            $findParams = array('record_type'=>'Item', 'record_id'=>$params['id']);
            $commonsRecords = get_db()->getTable('CommonsRecord')->findBy($findParams);
            if(empty($commonsRecords)) {
                $link = "<p id='commons-item-add'>Make this item part of the Omeka Commons</p>";
            } else {
                $link = "<p id='commons-item-status'>Already part of the Omeka Commons</p>";
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
        }
        
        public function install()
        {
            
            
        }
        
        public function uninstall()
        {
            
            
            
        }
        
        
        
    }
    
    
    
    
} else {
    
    class CommonsPlugin
    {
        
        
        
        
        protected $_hooks = array(
            'admin_append_to_items_show_secondary'
        
        );
        
        public function adminAppendToItemsShowSecondary()
        {
            _log('in hook');
            $html = "<div class='info-panel'>";
            $html .= "<h2>Omeka Commons</h2>";
            $html .= "<p>Make this item part of the Omeka Commons</p>";
            $html .= "</div>";
            return $html;
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
            $functionName = Inflector::variablize($hookName);
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
            $functionName = Inflector::variablize($filterName);
            if (!is_callable(array($this, $functionName))) {
                throw new Omeka_Plugin_Exception('Filter callback "' . $functionName . '" does not exist.');
            }
            add_filter($filterName, array($this, $functionName));
        }
    }
                
    }
    
    
    
    
    
    
    
    
    
    
    
    
}

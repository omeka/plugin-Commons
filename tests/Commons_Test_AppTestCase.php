<?php

require_once PLUGIN_DIR . '/Commons/libraries/Commons/Exporter.php';
require_once PLUGIN_DIR . '/Commons/libraries/Commons/Exporter/Collection.php';
require_once PLUGIN_DIR . '/Commons/libraries/Commons/Exporter/Item.php';
require_once PLUGIN_DIR . '/Commons/libraries/Commons/Exporter/Exhibit.php';
class Commons_Test_AppTestCase extends Omeka_Test_AppTestCase
{

    public function setUp()
    {
        parent::setUp();
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->setUp('Commons');
        $pluginHelper->setUp('ExhibitBuilder');
        $user = $this->_getDefaultUser();
        $this->_authenticateUser($user);
        $this->_setUpData();
        $this->_authenticateUser($this->_getDefaultUser());
    }

    public function tearDown()
    {
        parent::tearDown();
    }
    
    public function _setUpData()
    {
        set_option('commons_key', '123');
    }
    
    
}
<?php

if(!class_exists('Omeka_Plugin_Abstract')) {
    require_once 'Omeka_Plugin_Abstract.php';
}

require_once(HELPERS);

if(!function_exists('plugin_is_active')){

    function plugin_is_active($name, $version = null, $compOperator = '>=')
    {
        $plugin = get_db()->getTable('Plugin')->findByDirectoryName($name);
        if (!$plugin) {
            return false;
        }
        if (!$plugin->isActive()) {
            return false;
        }
        if ($version) {
            return version_compare($plugin->getDbVersion(), $version, $compOperator);
        } else {
            return true;
        }
    }
}

if(!function_exists('queue_js')) {

    function queue_js($file, $dir = 'javascripts')
    {
        if (is_array($file)) {
            foreach($file as $singleFile) {
                queue_js($singleFile, $dir);
            }
            return;
        }
        __v()->headScript()->appendFile(src($file, $dir, 'js'));
    }
}

if(!function_exists('queue_css')) {
    function queue_css($file, $media = 'all', $conditional = false, $dir = 'css')
    {
        if (is_array($file)) {
            foreach($file as $singleFile) {
                queue_css($singleFile, $media, $conditional, $dir);
            }
            return;
        }
        __v()->headLink()->appendStylesheet(css($file, $dir), $media, $conditional);
    }

}

define('COMMONS_PLUGIN_DIR', PLUGIN_DIR . '/Commons');
define('COMMONS_BASE_URL', 'http://localhost/commons');

// /*
set_option('commons_key', 'afaae64c6c9cc0ac86c4e5389495835cda643cbd');
  
define('COMMONS_API_URL', 'http://localhost/Omeka/commons-api/import');
//define('COMMONS_API_APPLY_URL', 'http://localhost/commons/commons-api/site/apply');
// */


 /*
set_option('commons_key', '4b0e952797dde61b868213d376d7775d740566f0');

define('COMMONS_API_URL', 'http://test.omeka.org/omeka-commons/commons-api/import');
define('COMMONS_API_APPLY_URL', 'http://test.omeka.org/omeka-commons/commons-api/site/apply');
// */

require_once COMMONS_PLUGIN_DIR . '/CommonsPlugin.php';
require_once COMMONS_PLUGIN_DIR . '/helpers/functions.php';
require_once HELPERS;
$commons = new CommonsPlugin();
$commons->setUp();
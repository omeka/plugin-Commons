<?php

define('COMMONS_API_URL', 'http://localhost/commons/commons-api/import');
define('COMMONS_API_APPLY_URL', 'http://localhost/commons/commons-api/site/apply');
define('COMMONS_PLUGIN_DIR', PLUGIN_DIR . '/Commons');
//define('COMMONS_API_URL', 'http://dev.omeka.org/omeka-commons/commons-api/import');
//define('COMMONS_API_APPLY_URL', 'http://dev.omeka.org/omeka-commons/commons-api/site/apply');

require_once COMMONS_PLUGIN_DIR . '/CommonsPlugin.php';
require_once COMMONS_PLUGIN_DIR . '/helpers/functions.php';
require_once HELPERS;
$commons = new CommonsPlugin();
$commons->setUp();
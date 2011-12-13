<?php

define('COMMONS_API_URL', 'http://localhost/commons/commons-api/import');
define('COMMONS_API_APPLY_URL', 'http://localhost/commons/commons-api/installation/apply');

//define('COMMONS_API_URL', 'http://dev.omeka.org/omeka-commons/commons-api/import');
//define('COMMONS_API_APPLY_URL', 'http://dev.omeka.org/omeka-commons/commons-api/installation/apply');
require_once PLUGIN_DIR . '/Commons/helpers/commons.php';
require_once PLUGIN_DIR . '/Commons/CommonsPlugin.php';
require_once HELPERS;
$commons = new CommonsPlugin();
$commons->setUp();
<?php

define('COMMONS_API_URL', 'http://localhost/commons/commons-api/import');
require_once PLUGIN_DIR . '/Commons/CommonsPlugin.php';
require_once HELPERS;
$commons = new CommonsPlugin();
$commons->setUp();
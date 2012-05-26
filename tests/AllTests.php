<?php

require_once (PLUGIN_DIR . '/Commons/tests/Commons_Test_AppTestCase.php');

class Commons_AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new Commons_AllTests('Commons Tests');
        $testCollector = new PHPUnit_Runner_IncludePathTestCollector(
          array(dirname(__FILE__) . '/unit')
        );
        $suite->addTestFiles($testCollector->collectTests());
        return $suite;
    }
}
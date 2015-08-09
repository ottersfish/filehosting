<?php

use Laracasts\TestDummy\Factory;

class LogsAdminControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testIndex()
    {
        #dependency injection?
    }

}
?>
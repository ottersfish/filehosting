<?php

use Laracasts\TestDummy\Factory;

class AdminControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testIndex()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockFolder->shouldReceive('getFolderList')
                ->once()
                ->andReturn(Factory::create('Folder'));

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn(Factory::create('admin_user'));
        Auth::shouldReceive('check')
            ->times(5)
            ->andReturn(true);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->call('GET', route('admin.index'));

        $this->assertTrue($response->isOk());
        $this->assertViewHas('folders');
    }
}
?>
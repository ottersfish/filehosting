<?php

use Laracasts\TestDummy\Factory;

class FoldersAdminControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testIndex()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockFolder->shouldReceive('getAllFolderListAdmin')
                ->once()
                ->andReturn(Factory::times(3)->create('Folder'));

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('admin_user'));
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->call('GET', route('admin.folders.index'));

        $this->assertTrue($response->isOk());
    }

}
?>
<?php

use Laracasts\TestDummy\Factory;

class FilesAdminControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }
    public function testIndex()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockFolder = Mockery::mock('FolderDao');
        $mockKey->shouldReceive('getFilesAdmin')
                ->once()
                ->andReturn(Factory::times(3)->create('Key'));
        $mockFolder->shouldReceive('getFolderListAdmin')
                ->once()
                ->andReturn(Factory::times(3)->create('Folder'));

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('admin_user'));
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('FolderDao', $mockFolder   );

        $response = $this->call('GET', route('admin.files.index'));

        $this->assertTrue($response->isOk());
    }

    public function testShow()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('UserDao');
        $mockKey->shouldReceive('getFiles')
                ->once()
                ->andReturn(Factory::times(3)->create('myFile'));

        $mockUser->shouldReceive('getUserById')
                ->once()
                ->andReturn(Factory::create('User'));

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('admin_user'));
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('UserDao', $mockUser);

        $response = $this->call('GET', route('admin.files.show'));

        $this->assertTrue($response->isOk());
    }

}
?>
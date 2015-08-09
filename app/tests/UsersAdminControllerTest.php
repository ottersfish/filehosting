<?php

use Laracasts\TestDummy\Factory;

class UsersAdminControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testIndex()
    {
        $mockUser = Mockery::mock('UserDao');
        $mockUser->shouldReceive('getUsers')
                ->once()
                ->andReturn(Factory::times(3)->create('User'));

        $this->app->instance('UserDao', $mockUser);

        $response = $this->call('GET', route('admin.users.index'));

        $this->assertTrue($response->isOk());
        $this->assertViewHas('users');
    }

    public function testEditAdmin()
    {
        $response = $this->call('GET', route('admin.users.delete', ['id' => 1]));

        // $this->assertTrue($response->isOk());
        $this->assertResponseStatus(403);
    }

    public function testEditNotExists()
    {
        $mockUser = Mockery::mock('UserDao');
        $mockUser->shouldReceive('userExists')
                ->once()
                ->andReturn(false);

        $this->app->instance('UserDao', $mockUser);

        $response = $this->call('GET', route('admin.users.delete', ['users' => 3]));

        $this->assertResponseStatus(404);
    }

    // public function testEditNotOk()
    // {
    //     # filesize error
    //     $mockUser = Mockery::mock('UserDao');
    //     $mockKey = Mockery::mock('KeyDao');
    //     $mockFolder = Mockery::mock('FolderDao');
    //     $user = Factory::create('User');
    //     $mockUser->shouldReceive('userExists')
    //             ->once()
    //             ->andReturn(true);
    //     $mockUser->shouldReceive('getUserById')
    //             ->once()
    //             ->andReturn($user);

    //     $mockKey->shouldReceive('getFiles')
    //             ->once()
    //             ->andReturn(Factory::times(3)->create('Key', ['id_user' => $user->id]));

    //     $mockFolder->shouldReceive('getFolderByOwnership')
    //             ->once()
    //             ->andReturn(Factory::times(3)->create('Folder', ['owner' => $user->id]));

    //     Auth::shouldReceive('user')
    //         ->once()
    //         ->andReturn(Factory::create('admin_user'));
    //     Auth::shouldReceive('check')
    //         ->times(3)
    //         ->andReturn(true);

    //     $this->app->instance('UserDao', $mockUser);
    //     $this->app->instance('KeyDao', $mockKey);
    //     $this->app->instance('FolderDao', $mockFolder);

    //     $response = $this->call('GET', route('admin.users.delete', ['users' => 3]));

    //     $this->assertTrue($response->isOk());
    // }

    public function testDestroy()
    {
        $mockUser = Mockery::mock('UserDao');
        $mockKey = Mockery::mock('KeyDao');
        $mockFolder = Mockery::mock('FolderDao');
        $mockFile = Mockery::mock('myFileDao');
        $user = Factory::create('User');
        $mockFile->shouldReceive('deleteFilesByOwnership')
                ->once();
        $mockUser->shouldReceive('deleteUserById')
                ->once()
                ->andReturn($user->username);
        $mockKey->shouldReceive('deleteFilesByOwnership')
                ->once();
        $mockFolder->shouldReceive('deleteFoldersByOwnership')
                ->once();

        $this->app->instance('UserDao', $mockUser);
        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('FolderDao', $mockFolder);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->action('DELETE', 'UsersAdminController@destroy');

        $this->assertRedirectedToRoute('admin.users.index');
        $this->assertSessionHas('message');
    }

}
?>
<?php

use Laracasts\TestDummy\Factory;

class FolderControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    // public function testStore()
    // {

    // }

    public function testShowFails($value='')
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockKey = Mockery::mock('KeyDao');

        $mockFolder->shouldReceive('getKeyByName')
                ->once()
                ->andReturn(null);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('User'));

        $this->app->instance('FolderDao', $mockFolder);
        $this->app->instance('KeyDao', $mockKey);

        $response = $this->call('GET', 'folders');

        $this->assertRedirectedToRoute('folders.show');
        $this->assertSessionHas('folderError');

    }

    // public function testShowOk()
    // {
    //     # filesize failed
    //     $mockFolder = Mockery::mock('FolderDao');
    //     $mockKey = Mockery::mock('KeyDao');

    //     $mockKey->shouldReceive('getFilesByFolderandOwner')
    //             ->once()
    //             ->andReturn(Factory::times(4)->create('Key'));

    //     $mockFolder->shouldReceive('getKeyByName')
    //             ->once()
    //             ->andReturn('test');
    //     $mockFolder->shouldReceive('getFolderByParent')
    //             ->once()
    //             ->andReturn(Factory::times(3)->create('Folder'));
    //     $mockFolder->shouldReceive('getFolderList')
    //             ->once()
    //             ->andReturn(Factory::times(3)->create('Folder'));

    //     Auth::shouldReceive('user')
    //         ->twice()
    //         ->andReturn(Factory::create('User'));

    //     $this->app->instance('FolderDao', $mockFolder);
    //     $this->app->instance('KeyDao', $mockKey);

    //     $response = $this->call('GET', 'folders');

    //     $this->assertTrue($response->isOk());
    //     $this->assertSessionHas('folder_name');
    //     $this->assertSessionHas('parents');
    //     $this->assertSessionHas('folders');
    //     $this->assertSessionHas('files');

    // }

    public function testEditNotFound()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(false);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->call('GET', route('folders.edit'));

        $this->assertTrue($response->isOk());
    }

    public function testEditForbidden()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('User');
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);

        $mockUser->shouldReceive('canEditFolder')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->call('GET', route('folders.edit'));

        $this->assertRedirectedToRoute('home');
        $this->assertSessionHasErrors();
    }

    public function testEditOk()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('User');
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockFolder->shouldReceive('getFolderByKey')
                ->once()
                ->andReturn(Factory::create('Folder'));

        // $mockUser->username = 'test';
        $mockUser->shouldReceive('getAttribute')
                ->once()
                ->with('username')
                ->andReturn('blah');
        $mockUser->shouldReceive('canEditFolder')
                ->once()
                ->andReturn(true);

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($mockUser);
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->call('GET', route('folders.edit'));

        $this->assertTrue($response->isOk());
        $this->assertTrue(!! $response->original->folder);
        $this->assertTrue(!! $response->original->method, 'put');

    }

    // public function testUpdate($value='')
    // {
    //     # put method???
    // }

    // public function testDestroy($value='')
    // {
    //     # delete method
    // }

 }
 ?>
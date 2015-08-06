<?php

use Laracasts\TestDummy\Factory;

class FilesControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    // public function testIndex()
    // {
    //     $mock = Mockery::mock('KeyDao');
    //     $mock->shouldReceive('getFiles')
    //          ->once()
    //          ->andReturn(Factory::times(3)->create('Key'));
    //     Auth::shouldReceive('user')
    //         ->twice()
    //         ->andReturn(Factory::create('User'));
    //     Auth::shouldReceive('check')
    //         ->times(3)
    //         ->andReturn(true);
    //     // Helpers::shouldReceive('getFileSize')
    //     //     ->times(3)
    //     //     ->andReturn('19 KB');
    //     $mockHelpers = Mockery::mock('Helpers');
    //     $mockHelpers->shouldReceive('formatFileSize')
    //                 ->times(3)
    //                 ->andReturn('test');

    //     $this->app->instance('KeyDao', $mock);

    //     $response = $this->call('GET', 'files');

    //     $this->assertTrue($response->isOk());
    //     $this->assertTrue(!! $response->original->files);
    // }

    public function testCreateLoggedIn()
    {
        $mock = Mockery::mock('FolderDao');
        $mock->shouldReceive('getFolderList')
             ->once()
             ->andReturn(Factory::times(3)->create('Folder'));
        Auth::shouldReceive('user')
            ->times(3)
            ->andReturn(Factory::create('User'));
        Auth::shouldReceive('check')
            ->times(7)
            ->andReturn(true);

        $this->app->instance('FolderDao', $mock);

        $response = $this->call('GET', 'files/upload');

        $this->assertTrue($response->isOk());
        $this->assertTrue(!! $response->original->folders);
    }

    public function testCreateLoggedInAsAdmin()
    {
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('super_admin_user'));
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        $response = $this->call('GET', 'files/upload');

        $this->assertRedirectedTo('admin');
    }

    public function testCreateNotLoggedIn()
    {
        Auth::shouldReceive('check')
            ->times(7)
            ->andReturn(false);

        $response = $this->call('GET', 'files/upload');

        $this->assertTrue($response->isOk());
    }

}
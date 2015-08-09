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

    // public function testStore()
    // {
    //     # need to create dummy file object
    //     # how to call post method?
    // }

    public function testShowFileNotFound()
    {
        $mock = Mockery::mock('KeyDao');
        $mock->shouldReceive('fileExists')
             ->once()
             ->andReturn(false);

        $this->app->instance('KeyDao', $mock);

        $response = $this->call('GET', 'files/f23rffa');

        $this->assertRedirectedToRoute('files.create');
        $this->assertSessionHasErrors();
    }

    public function testShowFile()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(true);
        $key = Factory::build('Key');
        $mockKey->shouldReceive('getByKey')
                ->once()
                ->andReturn($key);
        $mockFile = Mockery::mock('myFileDao');
        $mockFile->shouldReceive('getFileInfo')
                ->once()
                ->andReturn(Factory::build('myFile'), ['key' => $key->key]);

        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(false);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->call('GET', 'files/f23rffa');

        $this->assertTrue($response->isOk());
        $this->assertViewHas('file');
    }

    public function testEditNotFound()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(false);

        $this->app->instance('KeyDao', $mockKey);
        
        $response = $this->call('GET', 'files/fasfa/edit');

        $this->assertTrue($response->isOk());
    }

    public function testEditForbidden()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(true);

        $mockUser = Mockery::mock('UserDao');
        $mockUser->shouldReceive('canEdit')
                 ->once()
                 ->andReturn(false);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('KeyDao', $mockKey);

        $response = $this->call('GET', '/files/fasfa/edit');

        $this->assertRedirectedToRoute('files.create');
        $this->assertSessionHasErrors();
    }

    public function testEditOk()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('UserDao');
        $mockFile = Mockery::mock('myFileDao');
        $mockFolder = Mockery::mock('FolderDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(true);
        $key = Factory::build('Key');
        $mockKey->shouldReceive('getByKey')
                ->once()
                ->andReturn($key);
        $mockKey->shouldReceive('getFolderKeyByKey')
                ->once()
                ->andReturn($key);
        $mockUser->shouldReceive('canEdit')
                 ->once()
                 ->andReturn(true);
        $mockUser->shouldReceive('getAttribute')
                 ->once()
                 ->andReturn(1);
        $mockFile->shouldReceive('getFileInfo')
                ->once()
                ->andReturn(Factory::build('myFile'), ['key' => $key->key]);
        $mockFolder->shouldReceive('getFolderList')
                   ->once()
                   ->andReturn(Factory::times(3)->create('Folder'));
        $mockFolder->shouldReceive('getFolderName')
                   ->once()
                   ->andReturn('blahblah');

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($mockUser);
        Auth::shouldReceive('check')
            ->atLeast()->once()
            ->andReturn(false);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);
        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->call('GET', '/files/fasfa/edit');

        $this->assertTrue($response->isOk());
        $this->assertViewHas('file');
    }

    // public function testUpdate()
    // {
    //     Session::start();

    //     Route::enableFilters();
    //     $mockFile = Mockery::mock('myFileDao');
    //     $mockFile->shouldReceive('renameFile')
    //              ->once();
    //     $user = Factory::create('User');
    //     $this->be($user);

    //     $this->app->instance('myFile', $mockFile);

    //     $response = $this->call('PUT', 'files', ['_token' => csrf_token()]);

    //     $this->assertRedirectedToRoute('files.edit');
    //     $this->assertSessionHas('message', 'File was successfully edited.');
    // }

    public function testSuccess()
    {
        $response = $this->call('GET', route('files.success'));

        $this->assertTrue($response->isOk());
        $this->assertTrue(!! $response->original->key);
    }

    /**
     * @expectedException Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     */
    public function testDownloadFails()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockFile = Mockery::mock('myFileDao');
        $key = Factory::create('Key');
        $mockKey->shouldReceive('getByKey')
                ->once()
                ->andReturn($key);

        $file = new StdClass;
        $file->path = 'blah';
        $file->fileName = 'blah';
        $mockFile->shouldReceive('getFilePath')
                ->once()
                ->andReturn($file);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->call('GET', route('files.download'));

        //expected exception
    }

    // public function testDownloadOk()
    // {
        // $mockKey = Mockery::mock('KeyDao');
        // $mockFile = Mockery::mock('myFileDao');
        // $key = Factory::create('Key');
        // $mockKey->shouldReceive('getByKey')
        //         ->once()
        //         ->andReturn($key);

        //need to create dummy file object
        // $file = new StdClass;
        // $file->path = 'blah';
        // $file->fileName = 'blah';
        // $mockFile->shouldReceive('getFilePath')
        //         ->once()
        //         ->andReturn($file);

        // $this->app->instance('KeyDao', $mockKey);
        // $this->app->instance('myFileDao', $mockFile);

        // $response = $this->call('GET', route('files.download'));

        //expected exception
    // }

    // public function testMoveFolder()
    // {
    //     put method handling(?)
    // }

}
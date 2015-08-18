<?php

use Laracasts\TestDummy\Factory;

class FilesControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testIndex()
    {
        $mock = Mockery::mock('KeyDao');
        $mock->shouldReceive('getFiles')
             ->once()
             ->andReturn(Factory::times(3)->create('Key'));
        Auth::shouldReceive('user')
            ->times(5)
            ->andReturn(Factory::create('User'));
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('KeyDao', $mock);

        $response = $this->call('GET', 'files');

        $this->assertTrue($response->isOk());
        $this->assertTrue(!! $response->original->files);
    }

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

    public function testStoreFolderNotFound()
    {
        $mockFolder = Mockery::mock('FolderDao');

        $mockFolder->shouldReceive('getFolderKeyByOwnerandName')
                ->once()
                ->andReturn();
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(false);
        $faker = new Faker\Factory;
        $input = array(
                'folder' => $faker->create()->word
        );

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FilesController@store', null, $input, [], ['HTTP_REFERER' => route('files.index')]);

        $this->assertRedirectedToRoute('files.index');
        $this->assertSessionHasErrors();
    }

    public function testStoreFolderForbidden()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(false);
        $faker = new Faker\Factory;
        $input = array(
                'folder' => $faker->create()->word
        );

        Auth::shouldReceive('check')
            ->twice()
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FilesController@store', null, $input, [], ['HTTP_REFERER' => route('files.index')]);

        $this->assertRedirectedToRoute('files.index');
        $this->assertHasOldInput();
        $this->assertSessionHasErrors();
    }

    public function testStoreValidationFails()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('UserDao');
        $mockValidation = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockKey->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidation);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(true);
        $mockValidation->shouldReceive('fails')
                ->once()
                ->andReturn(true);
        $faker = new Faker\Factory;
        $input = array(
                'folder' => $faker->create()->word
        );

        Auth::shouldReceive('check')
            ->twice()
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FilesController@store', null, $input, [], ['HTTP_REFERER' => route('files.index')]);

        $this->assertRedirectedToRoute('files.index');
        $this->assertHasOldInput();
        $this->assertSessionHasErrors();
    }

    public function testStoreOk()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('UserDao');
        $mockFile = Mockery::mock('myFileDao');
        $mockValidation = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockKey->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidation);
        $mockKey->shouldReceive('saveKey')
                ->once()
                ->andReturn(true);
        $mockFile->shouldReceive('saveFile')
                ->once()
                ->andReturn(true);
        $mockFile->shouldReceive('moveFile')
                ->once();
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(true);
        $mockValidation->shouldReceive('fails')
                ->once()
                ->andReturn(false);
        $faker = new Faker\Factory;
        $input = array(
                'folder' => $faker->create()->word
        );

        Auth::shouldReceive('check')
            ->twice()
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('myFileDao', $mockFile);
        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FilesController@store', null, $input, []);

        $this->assertRedirectedTo('files/success');
    }

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

    public function testUpdate()
    {
        $mockFile = Mockery::mock('myFileDao');
        $mockFile->shouldReceive('renameFile')
                 ->once();
        $user = Factory::create('User');
        $this->be($user);

        $this->app->instance('myFileDao', $mockFile);

        $response = $this->action('PUT', 'FilesController@update');

        $this->assertRedirectedToRoute('files.edit');
        $this->assertSessionHas('message', 'File was successfully edited.');
    }

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
    }

    public function testDownloadOk()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockFile = Mockery::mock('myFileDao');
        $key = Factory::create('Key');
        $mockKey->shouldReceive('getByKey')
                ->once()
                ->andReturn($key);

        $faker = new Faker\Factory;
        $file = [
            'test' => false,
            'originalName' => $faker->create()->word,
            'extension' => $faker->create()->fileExtension,
            'mimeType' => $faker->create()->mimeType,
            'size' => $faker->create()->randomNumber,
            'path' => $faker->create()->file($sourceDir = storage_path(), $targetDir = storage_path('/files')),
            'error' => 0
        ];

        $fileDum = new StdClass;
        $fileDum->path = $file['path'];
        $fileDum->fileName = $file['originalName'];
        $mockFile->shouldReceive('getFilePath')
                ->once()
                ->andReturn($fileDum);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->call('GET', route('files.download'));
        $this->assertTrue($response->isOk());

        unlink($file['path']);
    }

    public function testMoveFolderNotExists()
    {
        $mockFolder = Mockery::mock('FolderDao');
        
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(false);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('PUT', 'FilesController@moveFolder', [], [], [], ['HTTP_REFERER' => route('files.edit')]);

        $this->assertSessionHasErrors();
    }

    public function testMoveFolderForbidden()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');
        
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
                ->once()
                ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('PUT', 'FilesController@moveFolder', [], [], [], ['HTTP_REFERER' => route('files.edit')]);        

        $this->assertRedirectedToRoute('files.edit');
        $this->assertSessionHasErrors();
    }

    public function testMoveFolderOk()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');
        $mockKey = Mockery::mock('KeyDao');
        
        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(true);
        $mockKey->shouldReceive('moveFile')
                ->once();

        Auth::shouldReceive('user')
                ->once()
                ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);
        $this->app->instance('KeyDao', $mockKey);

        $response = $this->action('PUT', 'FilesController@moveFolder', [], [], [], ['HTTP_REFERER' => route('files.edit')]);

        $this->assertRedirectedToRoute('files.edit');
        $this->assertSessionHas('message');
    }

}
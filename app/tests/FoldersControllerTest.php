<?php

use Laracasts\TestDummy\Factory;

class FolderControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testStoreParentNotExists()
    {
        $mockFolder = Mockery::mock('FolderDao');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('User'));

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FoldersController@store', [], [], [], ['HTTP_REFERER' => route('folders.show')]);

        $this->assertRedirectedToRoute('folders.show');
        $this->assertSessionHasErrors();
    }

    public function testStoreParentForbidden()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('getAttribute')
                ->once()
                ->with('id')
                ->andReturn(1);
        $mockUser->shouldReceive('getAttribute')
                ->once()
                ->with('username')
                ->andReturn('test');
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
            ->times(3)
            ->andReturn($mockUser);
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FoldersController@store', [], [], [], ['HTTP_REFERER' => route('folders.show')]);

        $this->assertTrue($response->isForbidden());

    }

    public function testStoreValidationFailed()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');
        $mockValidationRes = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockFolder->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidationRes);
        $mockUser->shouldReceive('getAttribute')
                ->once()
                ->with('id')
                ->andReturn(1);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(true);
        $mockValidationRes->shouldReceive('fails')
                ->once()
                ->andReturn(true);

        Auth::shouldReceive('user')
            ->times(2)
            ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FoldersController@store', [], [], [], ['HTTP_REFERER' => route('folders.show')]);

        $this->assertRedirectedToRoute('folders.show');
        $this->assertHasOldInput();
        $this->assertSessionHasErrors();

    }

    public function testStoreValidationSaveError()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');
        $mockValidationRes = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockFolder->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidationRes);
        $mockFolder->shouldReceive('createFolder')
                ->once()
                ->andReturn(false);
        $mockUser->shouldReceive('getAttribute')
                ->once()
                ->with('id')
                ->andReturn(1);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(true);
        $mockValidationRes->shouldReceive('fails')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
            ->times(2)
            ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FoldersController@store', [], [], [], ['HTTP_REFERER' => route('folders.show')]);

        $this->assertRedirectedToRoute('folders.show');
        $this->assertSessionHasErrors();

    }

    public function testStoreValidationOk()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockUser = Mockery::mock('UserDao');
        $mockValidationRes = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('exists')
                ->once()
                ->andReturn(true);
        $mockFolder->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidationRes);
        $mockFolder->shouldReceive('createFolder')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('getAttribute')
                ->once()
                ->with('id')
                ->andReturn(1);
        $mockUser->shouldReceive('ownsFolder')
                ->once()
                ->andReturn(true);
        $mockValidationRes->shouldReceive('fails')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
            ->times(2)
            ->andReturn($mockUser);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'FoldersController@store', [], [], [], ['HTTP_REFERER' => route('folders.show')]);

        $this->assertRedirectedToRoute('folders.show');
        $this->assertSessionHas('folderMessage');

    }

    public function testShowFails()
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

    public function testShowOk()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockKey = Mockery::mock('KeyDao');

        $mockKey->shouldReceive('getFilesByFolderandOwner')
                ->once()
                ->andReturn(Factory::times(4)->create('Key'));

        $mockFolder->shouldReceive('getKeyByName')
                ->once()
                ->andReturn('test');
        $mockFolder->shouldReceive('getFolderByParent')
                ->once()
                ->andReturn(Factory::times(3)->create('Folder'));
        $mockFolder->shouldReceive('getFolderList')
                ->once()
                ->andReturn(Factory::times(3)->create('Folder'));

        Auth::shouldReceive('user')
            ->atLeast(0)
            ->andReturn(Factory::create('User'));
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('FolderDao', $mockFolder);
        $this->app->instance('KeyDao', $mockKey);

        $response = $this->call('GET', 'folders');

        $this->assertTrue($response->isOk());
        $this->assertViewHas('folder_name');
        $this->assertViewHas('parents');
        $this->assertViewHas('folders');
        $this->assertViewHas('files');

    }

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

    public function testUpdateValidationFails()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockValidationRes = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('getFolderByKey')
                ->once()
                ->andReturn(Factory::create('Folder'));
        $mockFolder->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidationRes);
        $mockValidationRes->shouldReceive('fails')
                ->once()
                ->andReturn(true);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('PUT', 'FoldersController@update', [], [], [], ['HTTP_REFERER' => route('folders.edit')]);

        $this->assertRedirectedToRoute('folders.edit');
        $this->assertHasOldInput();
        $this->assertSessionHasErrors();
    }

    public function testUpdateRenameFails()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockValidationRes = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('getFolderByKey')
                ->once()
                ->andReturn(Factory::create('Folder'));
        $mockFolder->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidationRes);
        $mockFolder->shouldReceive('renameFolder')
                ->once()
                ->andReturn(false);
        $mockValidationRes->shouldReceive('fails')
                ->once()
                ->andReturn(false);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('PUT', 'FoldersController@update', [], [], [], ['HTTP_REFERER' => route('folders.edit')]);

        $this->assertRedirectedToRoute('folders.edit');
        $this->assertSessionHasErrors();
    }

    public function testUpdateRenameOk()
    {
        $mockFolder = Mockery::mock('FolderDao');
        $mockValidationRes = Mockery::mock('StdClass');

        $mockFolder->shouldReceive('getFolderByKey')
                ->once()
                ->andReturn(Factory::create('Folder'));
        $mockFolder->shouldReceive('validate')
                ->once()
                ->andReturn($mockValidationRes);
        $mockFolder->shouldReceive('renameFolder')
                ->once()
                ->andReturn(true);
        $mockValidationRes->shouldReceive('fails')
                ->once()
                ->andReturn(false);

        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('PUT', 'FoldersController@update', [], [], [], ['HTTP_REFERER' => route('folders.edit')]);

        $this->assertRedirectedToRoute('folders.edit');
        $this->assertSessionHas('message');
    }

    public function testDestroy()
    {
        $mockFile = Mockery::mock('myFileDao');
        $mockKey = Mockery::mock('KeyDao');
        $mockFolder = Mockery::mock('FolderDao');

        $mockFile->shouldReceive('deleteFilesinFolder')
                ->once();
        $mockKey->shouldReceive('deleteKeysinFolder')
                ->once();
        $mockFolder->shouldReceive('deleteFolder')
                ->once();

        $this->app->instance('myFileDao', $mockFile);
        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('DELETE', 'FoldersController@destroy');

        $this->assertRedirectedToRoute('folders.show');
        $this->assertSessionHas('messages');
    }

 }
 ?>
<?php

use Laracasts\TestDummy\Factory;

class RevisionsControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testShow()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockFile = Mockery::mock('myFileDao');

        $key = Factory::create('Key');
        $mockKey->shouldReceive('getByKey')
                ->once()
                ->andReturn($key->key);

        $mockFile->shouldReceive('getFileInfo')
                ->once()
                ->andReturn(Factory::create('myFile', ['key' => $key->key]));
        $mockFile->shouldReceive('getRevisionHistory')
                ->once()
                ->andReturn(Factory::times(3)->create('myFile', ['key' => $key->key]));

        Auth::shouldReceive('check')
            ->times(4)
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('User'));

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->call('GET', route('revisions.show'));
        
        $this->assertTrue($response->isOk());

    }

    // public function testUpdate()
    // {
    //     # post method
    // }

    public function testSetActiveNotFound()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(Factory::create('User'));

        $this->app->instance('KeyDao', $mockKey);

        $response = $this->call('GET', route('revisions.set-active'));

        $this->assertTrue($response->isOk());

    }

    public function testSetActiveForbidden()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('User');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('canEdit')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('KeyDao', $mockKey);

        $response = $this->call('GET', route('revisions.set-active'));

        $this->assertRedirectedToRoute('home');
        $this->assertSessionHasErrors();
        
    }

    public function testSetActiveIDNotValid()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('User');
        $mockFile = Mockery::mock('myFileDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('canEdit')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('getAttribute')
                ->with('username')
                ->once()
                ->andReturn('test');
        $mockFile->shouldReceive('isValidId')
                ->once()
                ->andReturn(false);

        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($mockUser);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->call('GET', route('revisions.set-active'));

        $this->assertTrue($response->isOk());
        
    }

    public function testSetActiveOk()
    {
        $mockKey = Mockery::mock('KeyDao');
        $mockUser = Mockery::mock('User');
        $mockFile = Mockery::mock('myFileDao');
        $mockKey->shouldReceive('fileExists')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('canEdit')
                ->once()
                ->andReturn(true);
        $mockFile->shouldReceive('isValidId')
                ->once()
                ->andReturn(true);
        $mockFile->shouldReceive('setActive')
                ->once();

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($mockUser);

        $this->app->instance('KeyDao', $mockKey);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->action('GET', 'RevisionsController@setActive', [], [], [], ['HTTP_REFERER' => route('revisions.show')]);

        $this->assertRedirectedToRoute('revisions.show');
        
    }
}

?>
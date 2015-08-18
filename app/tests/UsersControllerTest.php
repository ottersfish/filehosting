<?php

use Laracasts\TestDummy\Factory;

class UsersControllerTest extends TestCase {

    public function __construct(){
        Factory::$factoriesPath = 'app/tests/factories';
    }

    public function testCreate()
    {
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(false);

        $response = $this->call('GET', route('users.register'));

        $this->assertTrue($response->isOk());
    }

    public function testStore()
    {

        $mockUser = Mockery::mock('UserDao');
        $mockFolder = Mockery::mock('FolderDao');
        $mockValidator = Mockery::mock('StdClass');

        $key = Factory::create('Key');
        $user = Factory::create('User');
        $mockUser->shouldReceive('validateRegister')
                ->once()
                ->andReturn($mockValidator);
        $mockUser->shouldReceive('checkAvailbility')
                ->once()
                ->andReturn(array());
        $mockUser->shouldReceive('saveUser')
                ->once()
                ->andReturn(true);
        $mockUser->shouldReceive('getUserByUsernameEmail')
                ->once()
                ->andReturn($user);
        $mockFolder->shouldReceive('createRootFolder')
                ->once()
                ->andReturn(true);
        $mockValidator->shouldReceive('fails')
                ->once()
                ->andReturn(false);

        $this->app->instance('UserDao', $mockUser);
        $this->app->instance('FolderDao', $mockFolder);

        $response = $this->action('POST', 'UsersController@store', null, $user->toArray(), []);

        $this->assertRedirectedToRoute('users.login');
        $this->assertSessionHas('message');

    }

    public function testEdit()
    {
        $mockUser = Mockery::mock('UserDao');
        $mockUser->shouldReceive('getProfile')
                ->once()
                ->andReturn(Factory::create('User'));

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn(Factory::create('User'));
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('UserDao', $mockUser);

        $response = $this->call('GET', route('users.edit'));

        $this->assertTrue($response->isOk());
        $this->assertViewHas('user');
        $this->assertViewHas('method', 'put');
    }

    public function testUpdate()
    {

        $mockUser = Mockery::mock('UserDao');
        $mockFile = Mockery::mock('myFileDao');
        $mockValidator = Mockery::mock('StdClass');

        $key = Factory::create('Key');
        $mockUser->shouldReceive('validateEditProf')
                ->once()
                ->andReturn($mockValidator);
        $mockUser->shouldReceive('editProfile')
                ->once()
                ->andReturn(true);
        $mockValidator->shouldReceive('fails')
                ->once()
                ->andReturn(false);
        $user = Factory::create('User');
        $credentials = array('name' => $user->username,
                             'password' => $user->password,
                             'con_password' => $user->password);

        Auth::shouldReceive('user')
                ->once()
                ->andReturn($user);

        $this->app->instance('UserDao', $mockUser);
        $this->app->instance('myFileDao', $mockFile);

        $response = $this->action('PUT', 'UsersController@update', null, $credentials, [], ['HTTP_REFERER' => route('users.update')]);

        $this->assertRedirectedToRoute('users.edit');
        $this->assertSessionHas('messages');
    }

    public function testLogin()
    {
        $response = $this->call('GET', route('users.login'));

        $this->assertTrue($response->isOk());
    }

    public function testDoLoginNoPass()
    {
        $credentials = array('username' => 'admin',
                             'password' => '');

        $response = $this->action('POST', 'UsersController@doLogin', null, $credentials, [], ['HTTP_REFERER' => route('users.do-login')]);

        $this->assertRedirectedToRoute('users.do-login');
        $this->assertSessionHasErrors(['password']);
    }

    public function testDoLoginOk()
    {
        $credentials = array('username' => 'admin',
                             'password' => 'admin');

        $response = $this->action('POST', 'UsersController@doLogin', null, $credentials, [], ['HTTP_REFERER' => route('users.do-login')]);

        $this->assertRedirectedToRoute('users.do-login');
    }

    public function testLogout()
    {
        Auth::shouldReceive('logout')
            ->once();

        $response = $this->call('GET', route('users.logout'));

        $this->assertRedirectedToRoute('home');
    }

}
?>
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

    // public function testStore()
    // {
    //  # post method
    // }

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

    // public function testUpdate($value='')
    // {
    //     # code...
    // }

    public function testLogin()
    {
        $response = $this->call('GET', route('users.login'));

        $this->assertTrue($response->isOk());
    }

    // public function testDoLogin()
    // {
    //     # code...
    // }

    public function testLogout()
    {
        Auth::shouldReceive('logout')
            ->once();

        $response = $this->call('GET', route('users.logout'));

        $this->assertRedirectedToRoute('home');
    }

}
?>
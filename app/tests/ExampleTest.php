<?php

class ExampleTest extends TestCase {
    /**
     * A basic functional test example.
     *
     * @return void
     */

    public function setUp(){
        parent::setUp();

        Session::start();

        Route::enableFilters();
    }

    public function testRootFolder()
    {
        $crawler = $this->call('GET', '/');
        // $this->get('/');

        $this->assertRedirectedTo('/home');
    }

    public function testHomeGetIndex(){
        $crawler = $this->client->request('GET', 'home');

        $this->assertTrue($this->client->getResponse()->isOk());
    }

    public function testLoginSuccess(){
        $credentials = array('username' => 'admin',
                             'password' => 'admin');

        $response = $this->action('POST', 'LoginController@postIndex', null, $credentials);

        $this->assertRedirectedTo('/');

        // $this->assertTrue($this->client->getRepsonse()->isOk());
    }

    public function testLoginFailed(){
        $credentials = array('username' => 'admin',
                             'password' => '');

        $response = $this->action('POST', 'LoginController@postIndex', null, $credentials, [], ['HTTP_REFERER' => url('login')]);

        $this->assertRedirectedTo('login');

        $this->assertSessionHasErrors(['password']);
    }

}

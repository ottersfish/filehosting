<?php

use Laracasts\TestDummy\Factory;

class FilesControllerTest extends TestCase {
    public function testIndex()
    {
        $mock = Mockery::mock('KeyDao');
        $mock->shouldReceive('getFiles')
             ->once()
             ->andReturn(Mockery::any());

        $dum = new StdClass;
        $dum->id = 1;
        $dum->username = 'test';

        Auth::shouldReceive('user')
            ->twice()
            ->andReturn($dum);
        Auth::shouldReceive('check')
            ->times(3)
            ->andReturn(true);

        $this->app->instance('KeyDao', $mock);

        $response = $this->call('GET', 'files');

        $this->assertTrue($response->isOk());
        $this->assertTrue(!! $response->original->files);
    }

}
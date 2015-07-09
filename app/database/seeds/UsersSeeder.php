<?php
class UsersSeeder extends Seeder {
	public function run(){
		User::create(array(
			'username' => 'admin',
			'password' => Hash::make('admin'),
			'name'=>'admin',
			'email'=>'admin@example.com',
			'is_admin'=>true)
		);
		User::create(array(
			'username' => 'guest',
			'password' => '',
			'name' => 'guest',
			'email' => 'guest@example.com'
			)
		);
	}

}
?>
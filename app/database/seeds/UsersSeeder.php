<?php
class UsersSeeder extends Seeder {
	public function run(){
		User::create(array(
			'username'=>'admin',
			'password'=>Hash::make('admin'),
			'name'=>'admin',
			'email'=>'admin@example.com',
			'is_admin'=>true));
	}
}
?>
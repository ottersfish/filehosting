<?php

	$factory('User', [
		'email' => $faker->email,
		'username' => $faker->userName,
		'password' => $faker->password,
		'name' => $faker->name,
		'is_admin' => FALSE
	]);

	$factory('User', 'admin_user', [
		'email' => $faker->email,
		'username' => $faker->userName,
		'password' => $faker->password,
		'name' => $faker->name,
		'is_admin' => TRUE
	]);

	$factory('User', 'super_admin_user', [
		'id' => 1,
		'email' => 'admin@example.com',
		'username' => 'admin',
		'password' => $faker->password,
		'name' => 'admin',
		'is_admin' => TRUE
	]);

	$factory('User', 'guest_user', [
		'id' => 2,
		'email' => 'guest@example.com',
		'username' => 'guest',
		'password' => '',
		'name' => 'guest',
		'is_admin' => FALSE
	]);

	$factory('Key', [
		// 'path' => $faker->regexify('^(.+)/([^/]+)$'),
		'path' => $faker->word,
		'key' => $faker->word,
		'folder_key' => 'factory:Folder',
		'id_user' => 'factory:User'
	]);

	$factory('Folder', [
		'key' => $faker->word,
		'parent' => $faker->word,
		'folder_name' => $faker->regexify('^(.+)/([^/]+)$'),
		'owner' => 'factory:User'
	]);

?>
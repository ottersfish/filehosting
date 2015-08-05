<?php

	$factory('User', [
		// 'id' => $faker->randomNumber,
		'email' => $faker->email,
		'username' => $faker->userName,
		'password' => $faker->password,
		'name' => $faker->name
		// 'is_admin' => FALSE
	]);

	$factory('User', 'admin_user', [
		'id' => $faker->randomNumber,
		'email' => $faker->email,
		'username' => $faker->userName,
		'password' => $faker->password,
		'name' => $faker->name,
		'is_admin' => TRUE
	]);

	$factory('Key', [
		'id' => 1
	]);

?>
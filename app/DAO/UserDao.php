<?php
/**
* 
*/
class UserDao extends User
{
	public function validateRegister($regData){
		$messages=array(
			'alphanum' => 'Only characters and number are allowed for :attribute.'
		);

		$rules=array(
			'email_address' => 'required|email',
			'username' => 'required|alphanum',
			'password' => 'required',
			'con_password' => 'required|same:password',
			'name' => 'required'
		);

		return Validator::make($regData, $rules, $messages);
	}

	public function checkAvailbility($username, $email){
		$errors = array();
		$users = $this->getUserByUsername($username);
		$emails = $this->getUserByEmail($email);
		if(!is_null($users))array_push($errors, 'Username has already taken');
		if(!is_null($emails))array_push($errors, 'Email has already been registered');
		return $errors;
	}

	public function saveUser($regData){
		$user = $this->create($regData);
		$user->email = $regData['email_address'];
		$user->password = Hash::make($user->password);
		return $user->save();
	}

	public function validateLogin($credentials){
		$messages = array(
			'alphanum' => 'Only characters and number are allowed for :attribute.',
		);

		$rules=array(
			'username' => 'required|alphanum',
			'password' => 'required'
		);
		return Validator::make($credentials, $rules, $messages);
	}

	public function getUsers(){
		return $this->all();
	}

	public function getUserById($id){
		return $this->find($id);
	}

	public function getUserByUsername($username){
		return $this->where('username', $username)->pluck('id');
	}

	public function getUserByEmail($email_address){
		return $this->where('email', $email_address)->pluck('id');
	}

	public function deleteUserById($id){
		$this->where('id', '=', $id)->delete();
	}
}
?>
<?php

class LoginController extends BaseController {
	public function getIndex(){
		return View::make('login.index');
	}

	public function postIndex(){
		$credentials=Input::only('username','password');

		$messages = array(
			'alphanum' => 'Only characters and number are allowed for :attribute.',
		);

		$rules=array(
			'username' => 'required|alphanum',
			'password' => 'required'
		);

		$validation_result=Validator::make($credentials,$rules,$messages);
		if($validation_result->passes()){
			if(Auth::attempt($credentials)){
				return Redirect::intended('/');
			}
			else{
				return Redirect::back()
					->withInput()
					->withErrors("Invalid Credentials");
			}
		}
		else{
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}

	public function getLogout(){
		Auth::logout();

		return Redirect::to('/');
	}


	public function getRegister(){
		return View::make('login.register');
	}

	public function postRegister(){
		$input=Input::all();
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

		$validation_result=Validator::make($input,$rules,$messages);
		if($validation_result->passes()){
			$users = DB::table('users')
						->where('username', $input['username'])
						->pluck('id');
			$emails = DB::table('users')
						->where('email', $input['email_address'])
						->pluck('id');
			if(is_null($users) && is_null($emails)){
				$create = User::create($input);
				$create->email=$input['email_address'];
				$create->password=Hash::make($create->password);
				if($create->save()){
					return Redirect::intended('login')
						->with('message','test');
				}
				else{
					return Redirect::back()
						->withErrors('Error occured, please try again.');
				}

			}
			else{
				$errors = array();
				if(!is_null($users))array_push($errors, 'Username has already taken');
				if(!is_null($emails))array_push($errors, 'Email has already been registered');
				return Redirect::back()
					->withInput()
					->withErrors($errors);
			}
		}else{
			//var_dump($validation_result);return;
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}
}
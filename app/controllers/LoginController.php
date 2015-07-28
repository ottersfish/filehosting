<?php

class LoginController extends BaseController {

	protected $userDao, $folderDao;
	
	public function __construct(UserDao $userDao, FolderDao $folderDao){
		$this->userDao = $userDao;
		$this->folderDao = $folderDao;
	}

	public function getLogout(){
		Auth::logout();

		return Redirect::to('/');
	}

	public function getIndex(){
		return View::make('login.index');
	}


	public function getRegister(){
		return View::make('login.register');
	}

	public function postIndex(){
		$credentials = Input::only('username','password');
		$validation_result = $this->userDao->validateLogin($credentials);

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

	public function postRegister(){
		$regData = Input::all();
		$validation_result = $this->userDao->validateRegister($regData);
		if($validation_result->passes()){
			$errors = $this->userDao->checkAvailbility($regData['username'], $regData['email']);
			if(empty($errors)){
				if($this->userDao->saveUser($regData)){
					$user = $this->userDao->getUserByUsernameEmail($regData['username'], $regData['email']);
					if($this->folderDao->createRootFolder($user->id)){
						return Redirect::intended('login')
							->with('message','test');
					}
					else{
						return Redirect::back()
							->withErrors('Error occured, please try again.');
					}
				}
				else{
					return Redirect::back()
						->withErrors('Error occured, please try again.');
				}
			}
			else{
				return Redirect::back()
					->withInput()
					->withErrors($errors);
			}
		}
		else{
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}
}
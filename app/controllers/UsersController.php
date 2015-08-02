<?php

class UsersController extends \BaseController {

    protected $userDao, $folderDao;

    public function __call($method, $args){
        return Response::view('notfound')->header('statusCode', 404);
    }
    
    public function __construct(UserDao $userDao, FolderDao $folderDao){
        $this->userDao = $userDao;
        $this->folderDao = $folderDao;
    }


    /**
     * Show the form for user register.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('users.register');
    }


    /**
     * Create a user.
     *
     * @return Response
     */
    public function store()
    {
        $regData = Input::all();
        $validation_result = $this->userDao->validateRegister($regData);
        if($validation_result->passes()){
            $errors = $this->userDao->checkAvailbility($regData['username'], $regData['email']);
            if(empty($errors)){
                if($this->userDao->saveUser($regData)){
                    $user = $this->userDao->getUserByUsernameEmail($regData['username'], $regData['email']);
                    if($this->folderDao->createRootFolder($user->id)){
                        return Redirect::route('users.login')
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


    /**
     * Show the form for editing user profile.
     *
     * @return Response
     */
    public function edit()
    {
        return View::make('users.profile')
            ->with('user', $this->userDao->getProfile(Auth::user()->id))
            ->with('method', 'put');
    }


    /**
     * Update user's name and password.
     *\
     * @return Response
     */
    public function update()
    {
        $updData = Input::only('name', 'password', 'con_password');
        $validation_result = $this->userDao->validateEditProf($updData);
        if(!$validation_result->fails()){
            if($this->userDao->editProfile(Auth::user()->id)){
                return Redirect::back()
                    ->with('messages', 'Successfully update profile.');
            }
            else{
                return Redirect::back()
                    ->with('errors', 'Couldn\'t update profile, please try again.');
            }
        }
        else{
            return Redirect::back()
                ->withInput()
                ->withErrors($validation_result);
        }
    }


    /**
     * Show login form.
     *
     * @return Response
     */
    public function login()
    {
        return View::make('users.login');
    }


    /**
     * Auth the provided credentials.
     *
     * @return Response
     */
    public function doLogin()
    {
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


    /**
     * Logging out the current logged in user.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::route('home');
    }


}

<?php

class AdminController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	protected $fileDao, $userDao;
	public function __construct(FileDao $fileDao, UserDao $userDao){
		$this->fileDao = $fileDao;
		$this->userDao = $userDao;
	}

	public function index()
	{
		if(Auth::user()->is_admin){
			return Response::view('home.index');
		}
		else{
			return Response::view('unauthorized');
		}
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		if(Auth::user()->is_admin){
			if($id=='files'){
				return View::make('admin.files')
					->with('files', $this->fileDao->getFilesAdmin());
			}
			else if($id=='users'){
				return View::make('admin.users')
					->with('users', $this->userDao->getUsers());	
			}
			else{
				return Response::view('notfound');
			}
		}
		else{
			return Response::view('unauthorized');
		}
	}

	public function files($id){
		$files = $this->fileDao->getFilesByOwnership($id);
		$id_user = $this->userDao->getUserById($id);
		$files->username = $id_user->username;
		return View::make('admin.files')
				->with('files', $files);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		echo $id;
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */

	public function destroy($id)
	{
		$this->fileDao->deleteFilesAndFolder($id);
		$this->fileDao->deleteFilesByOwnership($id);
		$this->userDao->deleteUserById($id);
		return Redirect::to('admin/users')
				->with('message','User and it\'s files succesfully deleted');
	}

	public function delete($id)
	{
		if($id!=1){
			if($this->userDao->userExists($id)){
				return View::make('admin.deleteUser')
					->with('user', $this->userDao->getUserById($id))
					->with('files', $this->fileDao->getFilesByOwnership($id));
			}
			else{
				return Response::view('notfound');
			}
		}
		else{
			return Response::view('unauthorized');
		}
	}
}


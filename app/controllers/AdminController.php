<?php

class AdminController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	protected $myFileDao, $keyDao, $userDao, $folderDao;
	public function __construct(myFileDao $myFileDao, KeyDao $keyDao, UserDao $userDao, FolderDao $folderDao){
		$this->myFileDao = $myFileDao;
		$this->keyDao = $keyDao;
		$this->userDao = $userDao;
		$this->folderDao = $folderDao;
	}

	public function index()
	{
		if(Auth::user()->is_admin){
			return View::make('home.index')
				->with('folders', $this->folderDao->getFolderList(Auth::user()->id));	
		}
		else{
			return Response::view('unauthorized', 403);
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
				$files = $this->keyDao->getFilesAdmin(true, 5);
				return View::make('admin.files')
					->with('files', $files)
					->with('folders', $this->folderDao->getFolderListAdmin());
			}
			else if($id=='users'){
				return View::make('admin.users')
					->with('users', $this->userDao->getUsers());	
			}
			else if($id=='logs'){
				return View::make('admin.logs')
					->with('logs', LogDao::getLogs());
			}
			else if($id=='folders'){
				return View::make('admin.folders')
					->with('folders', $this->folderDao->getAllFolderListAdmin());
			}
			else{
				return Response::view('notfound', 404);
			}
		}
		else{
			return Response::view('unauthorized');
		}
	}

	public function files($id){
		$files = $this->keyDao->getFiles($id);
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
		$this->myFileDao->deleteFilesByOwnership($id);
		$this->keyDao->deleteFilesByOwnership($id);
		$this->folderDao->deleteFoldersByOwnership($id);
		$username = $this->userDao->deleteUserById($id);
		return Redirect::to('admin/users')
				->with('message','User '.$username.' and it\'s files succesfully deleted');
	}

	public function delete($id)
	{
		if($id!=1){
			if($this->userDao->userExists($id)){
				return View::make('admin.deleteUser')
					->with('user', $this->userDao->getUserById($id))
					->with('files', $this->keyDao->getFiles($id))
					->with('folders', $this->folderDao->getFolderByOwnership($id));
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


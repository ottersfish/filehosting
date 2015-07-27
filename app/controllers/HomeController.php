<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	protected $userDao, $keyDao, $myFileDao, $folderDao;

	public function __construct(KeyDao $keyDao, myFileDao $myFileDao, UserDao $userDao, FolderDao $folderDao){
		$this->keyDao = $keyDao;
		$this->myFileDao = $myFileDao;
		$this->userDao = $userDao;
		$this->folderDao = $folderDao;
	}

	public function initiate(){
		if(!file_exists(storage_path('/files')))mkdir(storage_path('/files'));
		$this->folderDao->createRootFolder(1);
		$this->folderDao->createRootFolder(2);
	}

	public function getIndex(){
		if(Auth::check() && Auth::user()->is_admin){
			return Redirect::to('admin');
		}
		else{ 
			if(Auth::check())
				return View::make('home.index')
					->with('folders', $this->folderDao->getFolderList(Auth::user()->id));
			else{
				return View::make('home.index');
			}
		}
	}

	private function moveFile($file, $fileKey, $fileHist){
		$main_dir = storage_path('files/');
		$target_dir = $main_dir.$fileKey->id_user.'/'.$file['folder'].'/'.$fileKey->key;
		if(!file_exists($target_dir))mkdir($target_dir);
		$file['file']->move($target_dir, $fileHist->origFilename.'.'.$fileHist->extension);
	}

	public function postIndex(){
		if(Auth::check()){
			$folder = Input::get('folder');
		}
		else{
			$folder = $this->folderDao->getFolderKeyByOwnerandName(2, '/');
		}
		$fileData = array(
			'file' 		=> Input::file('userFile'),
			'folder' 	=> $folder
		);
		if($this->folderDao->exists($fileData['folder'])){
			if(!Auth::check() or Auth::user()->ownsFolder($fileData['folder'])){
				$validation_result = $this->keyDao->validate($fileData);
				if(!$validation_result->fails()){
					$fileKey = new Key;
					if($this->keyDao->saveKey($fileData, $fileKey)){
						$fileHist = new myFile;
						$fileHist->key = $fileKey->key;
						if($this->myFileDao->saveFile($fileData, $fileHist)){
							$this->moveFile($fileData, $fileKey, $fileHist);
							return Redirect::to('home/success/'.$fileKey->key);
						}
					}
					else{
						return Redirect::to('home')
							->with('errors','Failed to upload the file, please try again!');
					}
				}
				else{
					return Redirect::back()
						->withInput()
						->withErrors($validation_result);
				}
			}
			else{
				return Redirect::back()
					->withInput()
					->withErrors('It\'s not your folder.');
			}
		}
		else{
			return Redirect::back()
				->withErrors('Folder doesn\'t exists!');
		}
	}

	public function getFiles(){
		return View::make('home.files')
				->with('files', $this->keyDao->getFiles(Auth::user()->id));
	}

	public function getDownload($key){
		return View::make('home.download')
			->with('file', $this->myFileDao
				->getFileInfo($this->keyDao->getByKey($key))
			);
	}

	public function doDownload($key){
		$file = $this->myFileDao->getFilePath(
			$this->keyDao->getByKey($key)
		);
		return Response::download($file->path, $file->fileName);
	}

	public function getSuccess($key){
		return View::make('home.success')->with('key', $key);
	}

	public function edit($key, $method){
		if($this->keyDao->fileExists($key)){
			if(Auth::user()->canEdit($key)){
				$fileInfo = $this->myFileDao->getFileInfo($this->keyDao->getByKey($key));
				if($method == 'delete'){
					return View::make('home.edit')
						->with('file', $fileInfo)
						->with('method', 'delete');
				}
				else if($method == 'edit'){
					$fileInfo->fileName = basename($fileInfo->fileName, '.'.$fileInfo->extension);
					$folders = $this->folderDao->getFolderList(Auth::user()->id);
					return View::make('home.edit')
						->with('file', $fileInfo)
						->with('folders', $folders)
						->with('method', 'put')
						->with('cur_folder', $this->folderDao->getFolderName($this->keyDao->getFolderKeyByKey($key)));;
				}
				else if($method == 'revision'){
					$revHistory = $this->myFileDao->getRevisionHistory($key);
					return View::make('home.revisions')
						->with('file', $fileInfo)
						->with('revHistory', $revHistory);
				}
				else{
					return Response::view('notfound');
				}
			}
			else{
				return Redirect::to('home')->withErrors('You aren\'t authorized to see this page.');
			}
		}
		else{
			return Response::view('notfound');
		}
	}

	public function putEditFolder($key){
		if($this->folderDao->exists(Input::get('folder')) and Auth::user()->ownsFolder(Input::get('folder'))){
			$this->keyDao->moveFile($key);
			return Redirect::to('home/edit/'.$key.'/edit')->with('message', 'File moved successfully.');
		}
		else{
			return Redirect::back()->withErrors('The folder you requested does not exists.');
		}
	}

	public function putEdit($key){
		$this->myFileDao->renameFile($key);
		return Redirect::to('home/edit/'.$key.'/edit')->with('message', 'File was successfully edited.');
	}

	public function deleteEdit($key){
		$this->myFileDao->deleteFile($key);
		$fileName = $this->keyDao->deleteKey($key);
		if(Auth::user()->is_admin){
			$prefix='admin/';
		}
		else{
			$prefix='home/';
		}
		return Redirect::to($prefix.'files')
				->with('message', $key.' successfully deleted.');
	}

	public function doRevision($key){
		$file = array(
			'file' 		=> Input::file('userFile'),
			'folder'	=> $this->keyDao->getByKey($key)->folder_key
		);
		$validation_result = $this->keyDao->validate($file);
		if(!$validation_result->fails()){
			$fileKey = $this->keyDao->getByKey($key);
			$fileHist = new myFile;
			$fileHist->key = $key;
			if($this->myFileDao->reviseFile($file, $fileHist)){
				$this->moveFile($file, $fileKey, $fileHist);
				return Redirect::to('home/edit/'.$fileKey->key.'/revision');
			}
			else{
				return Redirect::to('home/edit/'.$fileKey->key.'/revision')
					->with('errors','Failed to upload the file, please try again!');
			}
		}
		else{
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}

	public function setActive($key, $id){
		if($this->keyDao->fileExists($key)){
			if(Auth::user()->canEdit($key)){
				if($this->myFileDao->isValidId($key, $id)){
					$this->myFileDao->setActive($key, $id);
					return Redirect::back()
							->with('message', 'Successfully change active file.');
				}
				else{
					return Response::view('notfound');
				}
			}
			else{
				return Redirect::to('home')->withErrors('You aren\'t authorized to do this action.');
			}
		}
		else{
			return Response::view('notfound');
		}
	}

	public function getProfile(){
		return View::make('home.profile')
			->with('user', $this->userDao->getProfile(Auth::user()->id))
			->with('method', 'put');
	}

	public function putProfile(){
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

	public function postAddFolder(){
		$folderData = array(
			'parent' 		=> Input::get('parent'),
			'folder_name' 	=> Input::get('folder_name'),
			'owner' 		=> Auth::user()->id
		);

		$messages = array(
			'alphanum' => ':attribute must contain only number and characters'
		);
		$rules = array(
			'folder_name' 	=> 'alphanum'
		);
		$foldername_validation_result = Validator::make(Input::all(), $rules, $messages);	
		
		if($foldername_validation_result->fails()){
			return Redirect::back()->withInput()->withErrors($foldername_validation_result);
		}

		if($this->folderDao->exists($folderData['parent'])){
			if(Auth::user()->ownsFolder($folderData['parent'])){	
				$folderData['new_folder_name'] = $this->folderDao->getFolderName($folderData['parent']).$folderData['folder_name'].'/';
				$validation_result = $this->folderDao->validate($folderData);
				if(!$validation_result->fails()){
					$folderData['folder_name'] = $folderData['new_folder_name'];
					unset($folderData['new_folder_name']);
					if($this->folderDao->createFolder($folderData)){
						return Redirect::to('home/files')
							->with('folderMessage', 'Successfully created a folder.');
					}
					else{
						return Redirect::back()
							->withErrors('An error occured please try again.');
					}
				}
				else{
					return Redirect::back()
						->withInput()
						->withErrors($validation_result);
				}
			}
			else{
				return Redirect::to('unauthorized');
			}
		}
		else{
			return Redirect::back()
				->withErrors('Folder doesn\'t exists!');
		}
	}

	public function getFolders($folder_name = NULL){
		if($folder_name){
			$folder_name = '/'.$folder_name.'/';
		}
		else{
			$folder_name = '/';
		}
		$folder_key = $this->folderDao->getKeyByName($folder_name, Auth::user()->id);
		return View::make('home.folders')
			->with('folder_name', $folder_name)
			->with('parents', $this->folderDao->getFolderList(Auth::user()->id))
			->with('folders', $this->folderDao->getFolderByParent($folder_key))
			->with('files', $this->keyDao->getFilesByFolderandOwner($folder_key, Auth::user()->id));
	}

	public function getEditFolder($key, $method){
		if($this->folderDao->exists($key)){
			if(Auth::user()->canEditFolder($key)){
				$folder = $this->folderDao->getFolderByKey($key);
				if($method == 'delete'){
					return View::make('home.editFolder')
						->with('folder', $folder)
						->with('method', 'delete');
				}
				else if($method == 'edit'){
					$folder = $this->folderDao->getFolderByKey($key);
					return View::make('home.editFolder')
						->with('folder', $folder)
						->with('method', 'put');
				}
				else{
					return Response::view('notfound');
				}
			}
			else{
				return Redirect::to('home')->withErrors('You aren\'t authorized to see this page.');
			}
		}
		else{
			return Response::view('notfound');
		}
	}

	public function putRenameFolder($key){
		$new_name = Input::get('folder_name');
		$messages = array(
			'alphanum' => ':attribute must contain only number and characters'
		);
		$rules = array(
			'folder_name' 	=> 'alphanum'
		);
		$foldername_validation_result = Validator::make(Input::all(), $rules, $messages);

		if($foldername_validation_result->fails()){
			return Redirect::back()->withInput()->withErrors($foldername_validation_result);
		}
		$folderData['folder_name'] = $this->folderDao->getFolderName($this->folderDao->getFolderByKey($key)->parent).$new_name.'/';
		$validation_result = $this->folderDao->validate($folderData);
		if(!$validation_result->fails()){
			if($this->folderDao->renameFolder($key, $new_name)){
				return Redirect::back()
				->with('message', 'Successfully rename folder');
			}
			else{
				return Redirect::back()
				->withErrors('An error occured please try again.');
			}
		}
		else{
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}

	public function deleteEditFolder($key){
		$this->myFileDao->deleteFilesinFolder($key);
		$this->keyDao->deleteKeysinFolder($key);
		$this->folderDao->deleteFolder($key);
		return Redirect::to('home/folders')
			->with('messages', 'Folder deleted succesfully.');
	}
}

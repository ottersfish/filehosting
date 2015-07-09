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

	protected $keyDao, $myFileDao;

	public function __construct(KeyDao $keyDao, myFileDao $myFileDao){
		$this->keyDao = $keyDao;
		$this->myFileDao = $myFileDao;
	}

	public function getIndex(){
		if(Auth::check() && Auth::user()->is_admin){
			return Redirect::to('admin');
		}
		else{ 
			return View::make('home.index');
		}
	}

	private function moveFile($file, $fileKey, $fileHist){
		$main_dir = storage_path('files/');
		if(!file_exists($main_dir.$fileKey->id_user)){
			mkdir($main_dir.$fileKey->id_user);
		}
		$target_dir = $main_dir.$fileKey->id_user.'/'.$fileKey->key;
		if(!file_exists($target_dir))mkdir($target_dir);
		$file['file']->move($target_dir, $fileHist->origFilename.'.'.$fileHist->extension);
	}

	public function postIndex(){
		$file = array('file' => Input::file('userFile'));
		$validation_result = $this->keyDao->validate($file);
		if(!$validation_result->fails()){
			$fileKey = new Key;
			if($this->keyDao->saveKey($file, $fileKey)){
				$fileHist = new myFile;
				$fileHist->key = $fileKey->key;
				if($this->myFileDao->saveFile($file, $fileHist)){
					$this->moveFile($file, $fileKey, $fileHist);
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
					return View::make('home.edit')
						->with('file', $fileInfo)
						->with('method', 'put');
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

	public function putEdit($key){
		$this->myFileDao->renameFile($key);
		return Redirect::to('home/edit/'.$key.'/edit')->with('message', 'File was successfully edited');
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
		$file = array('file' => Input::file('userFile'));
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
}
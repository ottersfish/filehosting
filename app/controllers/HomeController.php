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

	protected $fileDao;

	public function __construct(FileDao $fileDao){
		$this->fileDao = $fileDao;
	}

	public function getIndex(){
		if(Auth::check() && Auth::user()->is_admin){
			return Redirect::to('admin');
		}
		else{ 
			return View::make('home.index');
		}
	}

	private function moveFile($file, $item){
		$main_dir = public_path('files/');
		if(!file_exists($main_dir.$item->id_user)){
			mkdir($main_dir.$item->id_user);
		}
		$target_dir = $main_dir.$item->id_user.'/'.$item->key;
		mkdir($target_dir);
		$fileName = $file['file']->getClientOriginalName();
		$file['file']->move($target_dir, $fileName);
	}

	public function postIndex(){
		$file = array('file' => Input::file('userFile'));
		$validation_result = $this->fileDao->validate($file);
		if(!$validation_result->fails()){
			$item = new UserFile;
			if($this->fileDao->saveFile($item)){
				$this->moveFile($file, $item);
				return Redirect::to('home/success/'.$item->key);
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
		return View::make('home.files')->with('files', $this->fileDao->getUserFiles(Auth::user()->id));
	}

	public function getDownload($key){
		return View::make('home.download')->with('file', $this->fileDao->getFileInfo($key));
	}

	public function doDownload($key){
		return Response::download($this->fileDao->getFilePath($key));
	}

	public function getSuccess($key){
		return View::make('home.success')->with('key', $key);
	}

	public function edit($key, $method){
		if($this->fileDao->fileExists($key)){
			if(Auth::user()->canEdit($key)){
				$fileInfo = $this->fileDao->getFileInfo($key);
				$fileInfo->fileName = basename($fileInfo->fileName, '.'.$fileInfo->extension);
				if($method == 'delete'){
					return View::make('home.edit')
						->with('file', $fileInfo)
						->with('method', 'delete');
				}
				else if($method == 'edit'){
					return View::make('home.edit')
						->with('file', $fileInfo)
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

	public function putEdit($key){
		$this->fileDao->renameFile($key);
		return Redirect::to('home/edit/'.$key.'/edit')->with('message', 'File was successfully edited');
	}

	public function deleteEdit($key){
		$fileName = $this->fileDao->deleteFile($key);
		if(Auth::user()->is_admin){
			$prefix='admin/';
		}
		else{
			$prefix='home/';
		}
		return Redirect::to($prefix.'files')
				->with('message', $fileName.' successfully deleted.');
	}
}
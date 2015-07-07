<?php

use Carbon\Carbon;
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
		if(!file_exists($target_dir))mkdir($target_dir);
		$fileName = Carbon::now()->format('Y_d_m_h_i_s_');
		$fileName .= $file['file']->getClientOriginalName();
		$file['file']->move($target_dir, $fileName);
	}

	public function postIndex(){
		$file = array('file' => Input::file('userFile'));
		$validation_result = $this->fileDao->validate($file);
		if(!$validation_result->fails()){
			$item = new UserFile;
			if($this->fileDao->saveFile($file, $item)){
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
		$file = $this->fileDao->getFilePath($key);
		return Response::download($file->path, $file->fileName);
	}

	public function getSuccess($key){
		return View::make('home.success')->with('key', $key);
	}

	public function edit($key, $method){
		if($this->fileDao->fileExists($key)){
			if(Auth::user()->canEdit($key)){
				$fileInfo = $this->fileDao->getFileInfo($key);
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
					$revHistory = $this->fileDao->getRevisionHistory($key);
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
				->with('message', $key.' successfully deleted.');
	}

	public function doRevision($key){
		$file = array('file' => Input::file('userFile'));
		$validation_result = $this->fileDao->validate($file);
		if(!$validation_result->fails()){
			$item = new UserFile;
			$item->key = $key;
			if($this->fileDao->reviseFile($file, $item)){
				$this->moveFile($file, $item);
				return Redirect::to('home/edit/'.$item->key.'/revision');
			}
			else{
				return Redirect::to('home/edit/'.$item->key.'/revision')
					->with('errors','Failed to upload the file, please try again!');
			}
		}
		else{
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}
}
<?php

//namespace App\Controllers;
//use App\Models\File;
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

	public function getIndex(){
		if(Auth::check() && Auth::user()->is_admin){
			return Redirect::to('admin');
		}
		else{ 
			return View::make('home.index');
		}
	}

	public function postIndex(){
		$file=array('file' => Input::file('userFile'));
		if(Auth::check()){
			$max_file_size=10000;
		}
		else{
			$max_file_size=1000;
		}
		$rules=array(
			'file' => 'required|max:'.$max_file_size
		);
		$validation_result = Validator::make($file, $rules);
		if(!$validation_result->fails()){
			$item = new UserFile;
			$item->path='/';
			$item->key=UserFile::genKey();
			if(Auth::check()){
				$item->id_user=Auth::user()->id;
			}
			else{
				$item->id_user=2;
			}
			// echo $item->id_user;
			// goto x;
			if($item->save()){
				$main_dir=public_path('files/');
				if(!file_exists($main_dir.$item->id_user)){
					mkdir($main_dir.$item->id_user);
				}
				$target_dir=$main_dir.$item->id_user.'/'.$item->key;
				mkdir($target_dir);
				$filename = $file['file']->getClientOriginalName();
				//$filename = str_replace(' ', '_', $filename);
				$file['file']->move($target_dir,$filename);
				return Redirect::to('home/success/'.$item->key);
			}
			else{
				return Redirect::to('home')
					->with('errors','Failed to upload the file, please try again!');
			}
			// x:
		}
		else{
			// var_dump($validation_result);
			return Redirect::back()
				->withInput()
				->withErrors($validation_result);
		}
	}

	public function getFiles(){
		$files = UserFile::where('id_user',Auth::user()->id)->get();
		return View::make('home.files')->with('files',$files);
	}

	public function getDownload($key){
		$ret = UserFile::getInfo($key);
		return View::make('home.download')->with('file',$ret);
	}

	public function doDownload($key){
		$file = UserFile::where('key',$key)->get();
		$file=$file[0];
		$id=$file->id_user;
		$targetdir=public_path('files/'.$id.'/'.$file->key);
		$lists=scandir($targetdir,1);
		$filename=$lists[0];
		return Response::download($targetdir.'/'.$filename);
	}

	public function getSuccess($key){
		return View::make('home.success')->with('key',$key);
	}

	public function edit($key, $method){
		if(Auth::user()->canEdit($key)){
			$fileinfo = UserFile::getInfo($key);
			$fileinfo->filename=basename($fileinfo->filename,'.'.$fileinfo->extension);
			if($method == 'delete'){
				return View::make('home.edit')
					->with('file', $fileinfo)
					->with('method', 'delete');
			}
			else if($method == 'edit'){
				return View::make('home.edit')
					->with('file', $fileinfo)
					->with('method', 'put');
			}
			else{
				return Response::view('notfound');
			}
		}
		else{
			//return;
			return Redirect::to('home')->withErrors('You don\'t have that authorization.');
		}
	}

	public function putEdit($key){
		//actually editting file
		$file = UserFile::where('key',$key)->get()->first();
		$targetdir=public_path('files/'.$file->id_user.'/'.$key);
		$lists=scandir($targetdir,1);
		$filename=$lists[0];
		$fileinfo=pathinfo($targetdir.'/'.$filename);
		//var_dump($fileinfo);return;
		$ext=$fileinfo['extension'];
		$targetname=Input::get('filename');
		rename($targetdir.'/'.$filename,$targetdir.'/'.$targetname.'.'.$ext);
		return Redirect::to('home/edit/'.$key.'/edit')->with('message','File was successfully edited');
	}

	public function deleteEdit($key){
		$file = UserFile::where('key',$key)->get()->first();
		$targetdir=public_path('files/'.$file->id_user.'/'.$key);
		$lists=scandir($targetdir,1);
		$filename=$lists[0];
		unlink($targetdir.'/'.$filename);
		rmdir($targetdir);
		$file->delete();
		if(Auth::user()->is_admin){
			$prefix='admin/';
		}
		else{
			$prefix='home/';
		}
		return Redirect::to($prefix.'files')
				->with('message',$filename.' successfully deleted.');
	}
}
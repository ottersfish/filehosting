<?php

class AdminController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
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
		echo 'test';
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
				$files = DB::table('files')
							->join('users','users.id','=','files.id_user')
							->select('users.username','files.id_user','files.path','files.key')
							->get();
				return View::make('admin.files')->with('files',$files);
			}
			else if($id=='users'){
				$users = User::all();
				return View::make('admin.users')->with('users',$users);	
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
		$files = UserFile::where('id_user','=',$id)->get();
		$id_user = User::find($id);
		$files->username=$id_user->username;
		return View::make('admin.files')->with('files',$files);
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
		$dir = public_path('files/'.$id);
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it,
					 RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
		    if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir);
		//unlink(public_path('files/2'));
		UserFile::where('id_user','=',$id)->delete();
		User::where('id','=',$id)->delete();
		return Redirect::to('admin/users')->with('message','User and it\'s files succesfully deleted');
	}

	public function delete($id)
	{
		if($id!=1){
			$user = User::find($id);
			$files = UserFile::where('id_user',$id)->get();
			return View::make('admin.deleteUser')->with('user',$user)->with('files',$files);
		}
		else{
			return Response::view('unauthorized');
		}
	}
}


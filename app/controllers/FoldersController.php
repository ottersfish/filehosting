<?php

class FoldersController extends \BaseController {

	protected $myFileDao, $folderDao, $keyDao;

    public function __call($method, $args){
        return Response::view('notfound')->header('statusCode', 404);
    }

	public function __construct(myFileDao $myFileDao, FolderDao $folderDao, KeyDao $keyDao)
	{
		$this->myFileDao = $myFileDao;
		$this->folderDao = $folderDao;
		$this->keyDao = $keyDao;
	}


	/**
	 * Store a newly created folder in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $folderData = array(
            'parent'         => Input::get('parent'),
            'folder_name'     => Input::get('folder_name'),
            'owner'         => Auth::user()->id
        );

        if($this->folderDao->exists($folderData['parent'])){
            if(Auth::user()->ownsFolder($folderData['parent'])){
                $validation_result = $this->folderDao->validate($folderData);
                if(!$validation_result->fails()){
                    if($this->folderDao->createFolder($folderData)){
                        return Redirect::route('folders.show')
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
                return Response::view('unauthorized');
            }
        }
        else{
            return Redirect::back()
                ->withErrors('Parent folder doesn\'t exists!');
        }
	}


	/**
	 * Display listing of files in particular folder.
	 *
	 * @param  string  $folder_name
	 * @return Response
	 */
	public function show($folder_name = null)
	{
        if($folder_name){
            $folder_name = '/'.$folder_name.'/';
        }
        else{
            $folder_name = '/';
        }
        $folder_key = $this->folderDao->getKeyByName($folder_name, Auth::user()->id);
        if($folder_key === NULL){
            return Redirect::route('folders.show')
                    ->with('folderError','Folder'.$folder_name.' not found');
        }
        else{
            return View::make('folders.show')
                ->with('folder_name', $folder_name)
                ->with('parents', $this->folderDao->getFolderList(Auth::user()->id))
                ->with('folders', $this->folderDao->getFolderByParent($folder_key))
                ->with('files', $this->keyDao->getFilesByFolderandOwner($folder_key, Auth::user()->id));
        }
	}


	/**
	 * Show the form for editing folder or move that folder to another folder.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($key)
	{
        if($this->folderDao->exists($key)){
            if(Auth::user()->canEditFolder($key)){
                $folder = $this->folderDao->getFolderByKey($key);
                return View::make('folders.edit')
                    ->with('folder', $folder)
                    ->with('method', 'put');
            }
            else{
                return Redirect::route('home')->withErrors('You aren\'t authorized to see this page.');
            }
        }
        else{
            return Response::view('notfound');
        }
	}


    /**
     * Update folder name.
     *
     * @param  string  $key
     * @return Response
     */
    public function update($key){
        $new_name = Input::get('folder_name');
        $folderData['folder_name'] = $new_name;
        $folderData['parent'] = $this->folderDao->getFolderByKey($key)->parent;
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


	/**
	 * Delete the specified folder including all it's files.
	 *
	 * @param  string  $key
	 * @return Response
	 */
	public function destroy($key)
	{
        $this->myFileDao->deleteFilesinFolder($key);
        $this->keyDao->deleteKeysinFolder($key);
        $this->folderDao->deleteFolder($key);
        return Redirect::route('folders.show')
            ->with('messages', 'Folder deleted succesfully.');
	}


}

<?php

class FilesController extends \BaseController {

    const FOLDER_NOT_EXISTS_ERROR = 'Folder doesn\'t exists!';
    const FOLDER_NOT_OWNED_ERROR = 'It\'s not your folder.';
    const FILE_UPLOAD_ERROR = 'Failed to upload the file, please try again!';
    const FILE_NOT_EXISTS_ERROR = 'File doesn\'t exists!';
    const FORBIDDEN_ERROR = 'You aren\'t authorized to see this page.';

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

    private function checkFolder($folder){
        if($this->folderDao->exists($folder)){
            if(!Auth::check() or Auth::user()->ownsFolder($folder)){
                return 'ok';
            }
            else{
                return self::FOLDER_NOT_OWNED_ERROR;
            }
        }
        else{
            return self::FOLDER_NOT_EXISTS_ERROR;
        }
    }

    protected function save($fileData){
        $fileKey = new Key;
        $key = $this->keyDao->saveKey($fileData, $fileKey);
        if($key !== NULL){
            $fileHist = new myFile;
            $fileHist->key = $fileKey->key;
            if($this->myFileDao->saveFile($fileData, $fileHist)){
                $this->myFileDao->moveFile($fileData, $fileKey, $fileHist);
                return $key;
            }
            else{
                return null;
            }
        }
        else{
            return null;
        }
    }

    /**
     * Display listing of user files.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('files.index')
                ->with('files', $this->keyDao->getFiles(Auth::user()->id));
    }


    /**
     * Display a form to upload a file.
     *
     * @return Response
     */
    public function create()
    {
        if(Auth::check() && Auth::user()->is_admin){
            return Redirect::to('admin');
        }
        else{ 
            if(Auth::check()){
                return View::make('files.create')
                    ->with('folders', $this->folderDao->getFolderList(Auth::user()->id));
            }
            else{
                return View::make('files.create');
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if(Auth::check()){
            $folder = Input::get('folder');
        }
        else{
            $folder = $this->folderDao->getFolderKeyByOwnerandName(2, '/');
        }
        $fileData = array(
            'file'         => Input::file('userFile'),
            'folder'     => $folder
        );
        $checkFolderRes = $this->checkFolder($fileData['folder']);
        if($checkFolderRes == 'ok'){
            $validation_result = $this->keyDao->validate($fileData);
            if(!$validation_result->fails()){
                $key = $this->save($fileData);
                if($key !== NULL){
                    return Redirect::route('files.success', array('id' => $key));
                }
                else{
                    return Redirect::to('files.create')
                        ->with('errors', self::FILE_UPLOAD_ERROR);
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
                ->withErrors($checkFolderRes);
        }
    }


    /**
     * Display the specified file.
     *
     * @param  string  $key
     * @return Response
     */
    public function show($key)
    {
        if($this->keyDao->fileExists($key)){
            return View::make('files.download')
                ->with('file', $this->myFileDao
                    ->getFileInfo($this->keyDao->getByKey($key))
                );
        }
        else{
            return Redirect::route('files.create')
                    ->withErrors(self::FILE_NOT_EXISTS_ERROR);
        }
    }


    /**
     * Show the form for editing file name or moving file to another folder.
     *
     * @param  string  $key
     * @return Response
     */
    public function edit($key)
    {
        if($this->keyDao->fileExists($key)){
            if(Auth::user()->canEdit($key)){
                $fileInfo = $this->myFileDao->getFileInfo($this->keyDao->getByKey($key));
                $fileInfo->fileName = basename($fileInfo->fileName, '.'.$fileInfo->extension);
                $folders = $this->folderDao->getFolderList(Auth::user()->id);
                return View::make('files.edit')
                    ->with('file', $fileInfo)
                    ->with('folders', $folders)
                    ->with('method', 'put')
                    ->with('cur_folder', $this->folderDao->getFolderName($this->keyDao->getFolderKeyByKey($key)));;
            }
            else{
                return Redirect::route('files.create')->withErrors(self::FORBIDDEN_ERROR);
            }
        }
        else{
            return Response::view('notfound');
        }
    }


    /**
     * Update file name.
     *
     * @param  string  $key
     * @return Response
     */
    public function update($key)
    {
        $this->myFileDao->renameFile($key);
        return Redirect::route('files.edit', array('key' => $key))->with('message', 'File was successfully edited.');
    }


    /**
     * Delete files including it's revisions.
     *
     * @param  string  $key
     * @return Response
     */
    public function destroy($key)
    {
        if($this->keyDao->fileExists($key)){
            $filename = $this->myFileDao->deleteFile($key);
            $this->keyDao->deleteKey($key);
            return Redirect::back()
                    ->with('message', $filename.' successfully deleted.');
        }
        else{
            return Redirect::back()
                    ->withErrors(self::FILE_NOT_EXISTS_ERROR);
        }
    }


    /**
     * Show upload success message.
     *
     * @param  string  $key
     * @return Response
     */
    public function success($key)
    {
        return View::make('files.success')
            ->with('key', $key);
    }


    /**
     * Return the requested file to user.
     *
     * @param  string  $key
     * @return Response
     */
    public function download($key)
    {
        $file = $this->myFileDao->getFilePath(
            $this->keyDao->getByKey($key)
        );
        return Response::download($file->path, $file->fileName);
    }


    /**
     * Move file to the desired folder.
     *
     * @param  string  $key
     * @return Response
     */
    public function moveFolder($key)
    {
        if($this->folderDao->exists(Input::get('folder')) and Auth::user()->ownsFolder(Input::get('folder'))){
            $this->keyDao->moveFile($key);
            return Redirect::route('files.edit', array('key' => $key))->with('message', 'File moved successfully.');
        }
        else{
            return Redirect::back()->withErrors(self::FOLDER_NOT_EXISTS_ERROR);
        }
    }

}

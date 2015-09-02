<?php

class RevisionsController extends \BaseController {

    const FILE_UPLOAD_ERROR = 'Failed to upload the file, please try again!';
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


    /**
     * Display the specified revision of a file.
     *
     * @param  string  $key
     * @return Response
     */
    public function show($key)
    {
        $fileInfo = $this->myFileDao->getFileInfo($this->keyDao->getByKey($key));
        $revHistory = $this->myFileDao->getRevisionHistory($key);
        return View::make('revisions.show')
            ->with('file', $fileInfo)
            ->with('revHistory', $revHistory);
    }


    /**
     * Store newly uploaded file and set active for the newest version.
     *
     * @param  string  $key
     * @return Response
     */
    public function update($key)
    {
        $file = array(
            'file'         => Input::file('userFile'),
            'folder'       => $this->keyDao->getByKey($key)->folder_key
        );
        $validation_result = $this->keyDao->validate($file);
        if(!$validation_result->fails()){
            $fileKey = $this->keyDao->getByKey($key);
            $fileHist = new myFile;
            $fileHist->key = $key;
            if($this->myFileDao->reviseFile($file, $fileHist)){
                $this->myFileDao->moveFile($file, $fileKey, $fileHist);
                return Redirect::route('revisions.show', array('key' => $fileKey->key));
            }
            else{
                return Redirect::route('revisions.show', array('key' => $fileKey->key))
                    ->with('errors', self::FILE_UPLOAD_ERROR);
            }
        }
        else{
            return Redirect::back()
                ->withInput()
                ->withErrors($validation_result);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function setActive($key = NULL, $id = 0)
    {
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
                return Redirect::route('home')->withErrors(self::FORBIDDEN_ERROR);
            }
        }
        else{
            return Response::view('notfound');
        }
    }



}

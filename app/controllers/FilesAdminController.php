<?php

class FilesAdminController extends \BaseController {

    protected $userDao, $folderDao, $keyDao;

    public function __call($method, $args){
        return Response::view('notfound')->header('statusCode', 404);
    }

    public function __construct(UserDao $userDao, FolderDao $folderDao, KeyDao $keyDao)
    {
        $this->userDao = $userDao;
        $this->folderDao = $folderDao;
        $this->keyDao = $keyDao;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $files = $this->keyDao->getFilesAdmin(true, 5);
        return View::make('admin.files')
            ->with('files', $files)
            ->with('folders', $this->folderDao->getFolderListAdmin());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function show($id)
    {
        $files = $this->keyDao->getFiles($id);
        $id_user = $this->userDao->getUserById($id);
        $files->username = $id_user->username;
        return View::make('admin.files')
                ->with('files', $files);
    }


}

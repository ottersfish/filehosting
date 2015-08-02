<?php

class AdminController extends BaseController {

    protected $folderDao;

    public function __call($method, $args){
        return Response::view('notfound')->header('statusCode', 404);
    }

    public function __construct(FolderDao $folderDao){
        $this->folderDao = $folderDao;
    }

    /**
     * Display a form to upload (admin treated as basic user).
     *
     * @return Response
     */
    public function index()
    {
        return View::make('admin.index')
            ->with('folders', $this->folderDao->getFolderList(Auth::user()->id));
    }
}


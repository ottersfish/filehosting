<?php

class FoldersAdminController extends \BaseController {

    protected $folderDao;

    public function __call($method, $args){
        return Response::view('notfound')->header('statusCode', 404);
    }

    public function __construct(FolderDao $folderDao)
    {
        $this->folderDao = $folderDao;
    }

	/**
	 * Display a listing of all folders in the site.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('admin.folders')
            ->with('folders', $this->folderDao->getAllFolderListAdmin());
	}


}

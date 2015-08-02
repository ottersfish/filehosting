<?php

class UsersAdminController extends \BaseController {

    protected $myFileDao, $keyDao, $userDao, $folderDao;

    public function __call($method, $args){
        return Response::view('notfound')->header('statusCode', 404);
    }   

    public function __construct(myFileDao $myFileDao, KeyDao $keyDao, UserDao $userDao, FolderDao $folderDao){
        $this->myFileDao = $myFileDao;
        $this->keyDao = $keyDao;
        $this->userDao = $userDao;
        $this->folderDao = $folderDao;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('admin.users')
            ->with('users', $this->userDao->getUsers());
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if($id!=1){
            if($this->userDao->userExists($id)){
                return View::make('admin.deleteUser')
                    ->with('user', $this->userDao->getUserById($id))
                    ->with('files', $this->keyDao->getFiles($id))
                    ->with('folders', $this->folderDao->getFolderByOwnership($id));
            }
            else{
                return Response::view('notfound');
            }
        }
        else{
            return Response::view('unauthorized');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->myFileDao->deleteFilesByOwnership($id);
        $this->keyDao->deleteFilesByOwnership($id);
        $this->folderDao->deleteFoldersByOwnership($id);
        $username = $this->userDao->deleteUserById($id);
        return Redirect::route('admin.users.index')
                ->with('message','User '.$username.' and it\'s files succesfully deleted');
    }


}

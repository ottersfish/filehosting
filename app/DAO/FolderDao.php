<?php
class FolderDao extends Folder{

    const RANDOM_CHARS = "0123456789abcdefghijklmnopqrstuvwxyz";

    private function checkKey($key){
        return is_null($this->whereKey($key)->first());
    }

    private function deleteChildFolder($parent){
        $folders = $this->where('parent', $parent)->get();
        $old_values = '';
        $first = 1;
        foreach($folders as $folder){
            $target_dir = $folder->owner.'/'.$folder->key;
            $this->deleteFolderPhysically($target_dir);
            $folder->delete();
            if(!$first){
                $old_values .= ', ';
            }
            $old_values .= $folder->key;
            $first = 0;
        }
        LogDao::logDelete($this->table, $old_values);
    }

    private function deleteFolderPhysically($path){
        $dir = storage_path('files/'.$path);
        if(file_exists($dir)){
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                }
                else{
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        }
    }

    private function renameChildFolder($key, $old_name, $new_name){
        $folders = $this->where('parent', $key)->get();
        foreach($folders as $folder){
            $ptn = '/' . preg_quote($old_name,'/') . '/';
            $old_name = $folder->folder_name;
            $folder->folder_name = preg_replace($ptn, $new_name, $folder->folder_name, 1);
            $folder->save();
            LogDao::logEdit($this->table, 'folder_name', $old_name, $folder->folder_name);
        }
    }

    private function genKey(){
        $length=15;
        while(1){
            $characters = self::RANDOM_CHARS;
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            if($this->checkKey($randomString)){
                return $randomString;
            }
        }
    }

    public function createFolder($folderData){
        $main_dir = storage_path('files/');
        $folderData['key'] = $this->genKey();
        $target_dir = $main_dir.$folderData['owner'].'/'.$folderData['key'];
        mkdir($target_dir);
        LogDao::logCreate($this->table, 'key, parent, folde_name, owner', 
            $folderData['key'].', '.$folderData['parent'].', '.$folderData['folder_name'].', '.$folderData['owner']);
        return $this->create($folderData);
    }

    public function createRootFolder($id){
        $main_dir = storage_path('files/');
        if(!file_exists($main_dir.$id))mkdir($main_dir.$id);
        $folderData = array(
            'key'            => $this->genKey(),
            'parent'        => '.',
            'folder_name'    => '/',
            'owner'            => $id
        );
        $target_dir = $main_dir.$id.'/'.$folderData['key'];
        mkdir($target_dir);
        LogDao::logCreate($this->table, 'key, parent, folde_name, owner', 
            $folderData['key'].', '.$folderData['parent'].', '.$folderData['folder_name'].', '.$folderData['owner']);
        return $this->create($folderData);
    }

    public function deleteFolder($key){
        $query = $this->where('key', $key);
        $row = $query->get()->first();
        $old_values = $row->key;
        LogDao::logDelete($this->table, $old_values);
        $target_dir = $row->owner.'/'.$key;
        $this->deleteChildFolder($key);
        $this->deleteFolderPhysically($target_dir);
        return $query->delete();
    }

    public function deleteFoldersByOwnership($owner){
        $query = $this->where('owner', $owner);
        $rows = $query->get();
        $old_values = '';
        $first = 1;
        foreach($rows as $row){
            if(!$first){
                $old_values .= ", ";
            }
            $old_values .= $row->key;
            $first = 0;
        }
        LogDao::logDelete($this->table, $old_values);
        $target_dir = $owner;
        $this->deleteFolderPhysically($target_dir);
        return $query->delete();
    }

    public function exists($key){
        return $this->where('key', $key)->get()->count();
    }

    public function validate(&$folderData){
        $messages = array(
            'alphanum' => ':attribute must contain only number and characters'
        );
        $rules = array(
            'folder_name'     => 'required|alphanum'
        );
        $validation_result = Validator::make(Input::all(), $rules, $messages);    
        
        if($validation_result->fails()){
            return $validation_result;
        }
        $folderData['folder_name'] = $this->getFolderName($folderData['parent']).$folderData['folder_name'].'/';
        
        $messages = array(
            'unique' => 'Your requested :attribute exists.'
        );

        $rules = array(
            'folder_name' => 'unique:folders,folder_name,NULL,id,owner,'.Auth::user()->id
        );

        $validation_result = Validator::make($folderData, $rules, $messages);
        if($validation_result->fails()){
            return $validation_result;
        }

        $rules = array(
            'folder_name'     => 'unique:folders,folder_name,NULL,id,owner,'.Auth::user()->id
        );

        return Validator::make($folderData, $rules, $messages);
    }

    public function getAllFolderListAdmin(){
        return $this->leftJoin('folders as fold', 'fold.key', '=', 'folders.parent')
                ->join('users', 'users.id', '=', 'folders.owner')
                ->orderBy('folders.owner')
                ->orderBy('folders.id')
                ->select((array(
                    'folders.key',
                    'folders.folder_name',
                    'fold.folder_name as parent',
                    'users.username'
                )))
                ->paginate(10);
    }

    public function getFolderByKey($key){
        return $this->where('key', $key)->get()->first();
    }

    public function getFolderByParent($parent){
        return $this->where('parent', $parent)->get();
    }

    public function getFolderKeyByOwnerandName($owner, $folder_name){
        return $this->where('owner', $owner)->where('folder_name', $folder_name)->pluck('key');
    }

    public function getFolderByOwnership($id){
        return $this->leftJoin('folders as fold', 'folders.parent', '=', 'fold.key')
                ->where('folders.owner', $id)
                ->select(array(
                    'folders.folder_name',
                    'fold.folder_name as parent'
                ))
                ->orderBy('folders.id')
                ->get();
    }

    public function getFolderList($id){
        return $this->where('owner', $id)->orderBy('folder_name')->lists('folder_name','key');
    }

    public function getFolderListAdmin(){
        return $this->all();
    }

    public function getFolderName($key){
        return $this->where('key', $key)->pluck('folder_name');
    }

    public function getKeyByName($folder_name, $id){
        return $this->where('folder_name', $folder_name)->where('owner', $id)->pluck('key');
    }

    public function renameFolder($key, $new_name){
        $folder = $this->where('key', $key)->get()->first();
        $parent_name = $this->getFolderName($folder->parent);
        $old_name = $folder->folder_name;
        $folder->folder_name = $parent_name.$new_name.'/';
        $this->renameChildFolder($key, $old_name, $folder->folder_name);
        LogDao::logEdit($this->table, 'folder_name', $old_name, $folder->folder_name);
        return $folder->save();
    }
}
?>
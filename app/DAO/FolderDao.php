<?php
class FolderDao extends Folder{

	private function checkKey($key){
		return is_null($this->whereKey($key)->first());
	}

	private function genKey(){
		$length=15;
		while(1){
			$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
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

	public function createRootFolder($id){		
		$main_dir = storage_path('files/');
		mkdir($main_dir.$id);
		$folderData = array(
			'key'			=> $this->genKey(),
			'parent'		=> '.',
			'folder_name'	=> '/',
			'owner'			=> $id
		);
		$target_dir = $main_dir.$id.'/'.$folderData['key'];
		mkdir($target_dir);
		LogDao::logCreate($this->table, 'key, parent, folde_name, owner', 
			$folderData['key'].', '.$folderData['parent'].', '.$folderData['folder_name'].', '.$folderData['owner']);
		return $this->create($folderData);
	}

	public function validate($folderData){
		$messages = array(
			'unique' => 'Your requested :attribute exists.'
		);

		$rules = array(
			'folder_name' 	=> 'required|unique:folders,folder_name,NULL,id,owner,'.Auth::user()->id
		);

		return Validator::make($folderData, $rules, $messages);
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

	public function getFolderList($id){
		return $this->where('owner', $id)->orderBy('folder_name')->lists('folder_name','key');
	}

	public function getFolderByKey($key){
		return $this->where('key', $key)->get()->first();
	}

	public function getFolderName($key){
		return $this->where('key', $key)->pluck('folder_name');
	}

	public function getFolderKeyByOwnerandName($owner, $folder_name){
		return $this->where('owner', $owner)->where('folder_name', $folder_name)->pluck('key');
	}

	public function getFolderByParent($parent){
		return $this->where('parent', $parent)->get();
	}

	public function getKeyByName($folder_name, $id){
		return $this->where('folder_name', $folder_name)->where('owner', $id)->pluck('key');
	}

	public function exists($key){
		return $this->where('key', $key)->get()->count();
	}

	private function renameChildFolder($key, $old_name, $new_name){
		$folders = $this->where('parent', $key)->get();
		foreach($folders as $folder){
			$ptn = '/' . preg_quote($old_name,'/') . '/';
			$folder->folder_name = preg_replace($ptn, $new_name, $folder->folder_name, 1);
			$folder->save();
		}
	}

	public function deleteFolderPhysically($path){
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

	private function deleteChildFolder($parent){
		$folders = $this->where('parent', $parent)->get();
		foreach($folders as $folder){
			$target_dir = $folder->owner.'/'.$folder->key;
			$this->deleteFolderPhysically($target_dir);
			$folder->delete();
		}
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
}
?>
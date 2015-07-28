<?php
class KeyDao extends Key{

	private function checkKey($key){
		return is_null($this->whereKey($key)->first());
	}

	private function genKey(){
		$length=6;
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

	public function deleteFilesAndFolder($path){
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

	public function deleteFilesByOwnership($id){
		$query = $this->where('id_user','=',$id);
		$rows = $query->get();
		if(!$rows->isEmpty()){
			$first = 1;
			$old_values = '';
			foreach($rows as $row){
				if(!$first){
					$old_values .= ', ';
				}
				$old_values .= $row->key;
				$first = 0;
			}
			LogDao::logDelete($this->table, $old_values);
			$query->delete();
		}
	}

	public function deleteGuestFiles(){
		$id = 2;
		$query = $this->where('id_user', $id)
					->whereRaw('created_at < date_sub(now(), interval 1 day)');
		$rows = $query->get();
		if(!$rows->isEmpty()){
			$old_values = '';
			$first = 1;
			foreach ($rows as $row){
				if(!$first){
					$old_values = ', ';
				}
				$old_values .= $row->key;
				$first = 0;
				$targetDir = $row->id_user.'/'.$row->folder_key.'/'.$row->key;
				$this->deleteFilesAndFolder($targetDir);
			}
			$query->delete();
			LogDao::logDelete($this->table, $old_values);
		}
	}

	public function deleteKey($key){
		$file = $this->where('key', $key)->get()->first();
		$targetDir = $file->id_user.'/'.$file->folder_key.'/'.$key;
		$this->deleteFilesAndFolder($targetDir);
		LogDao::logDelete($this->table, $key);
		$file->delete();
	}

	public function deleteKeysinFolder($folder_key){
		$query = $this->where('folder_key', $folder_key);
		$old_values = '';
		$rows = $query->get();
		$first = 1;
		foreach($rows as $row){
			if(!$first){
				$old_values .= ', ';
			}
			$old_values .= $row->key;
			$first = 0;
		}
		LogDao::logDelete($this->table, $old_values);
		return $query->delete();
	}

	public function fileExists($key){
		return $this->where('key', $key)->get()->count();
	}

	public function getFiles($owner){
		return $this->join('files', 'keys.key', '=', 'files.key')
					->join('folders', 'keys.folder_key', '=', 'folders.key')
					->where('is_active', true)
					->where('id_user', $owner)
					->select(array('folders.folder_name', 
									'keys.folder_key', 
									'keys.key', 
									'extension', 
									'keys.id_user',
									'files.origFilename', 
									'files.filename'))
					->get();
	}

	public function getFilesAdmin($paginate, $num=0){
		if($paginate){
			return $this->join('files', 'keys.key', '=', 'files.key')
					->join('users', 'users.id', '=', 'keys.id_user')
					->join('folders', 'keys.folder_key', '=', 'folders.key')
					->where('is_active', true)
					->select(array('folders.folder_name', 
									'keys.folder_key', 
									'keys.key', 
									'extension', 
									'keys.id_user',
									'files.origFilename', 
									'files.filename',
									'users.username'))
					->paginate($num);
		}
		else{
			return $this->join('files', 'keys.key', '=', 'files.key')
					->join('users', 'users.id', '=', 'keys.id_user')
					->where('is_active', true)
					->select('*')
					->get();
		}
	}

	public function getFilesByFolderandOwner($folder_key, $owner){
		return $this->join('files', 'keys.key', '=', 'files.key')
					->where('is_active', true)
					->where('id_user', $owner)
					->where('folder_key', $folder_key)
					->select(array('keys.folder_key', 'keys.key', 'files.origFilename', 'extension', 'files.filename'))
					->get();
	}

	public function getFolderKeyByKey($key){
		return $this->where('key', $key)->pluck('folder_key');
	}

	public function getKeys($id = 0){
		if($id){
			return $this->where('id_user',$id)->get();
		}
		else{
			return $this->all();
		}
	}

	public function getByKey($key){
		return $this->where('key', $key)->get()->first();
	}

	public function moveFile($key){
		$query = $this->where('key', $key);
		$file = $query->get()->first();
		$old_path = storage_path('files/'.$file->id_user.'/'.$file->folder_key.'/'.$key);
		$new_path = storage_path('files/'.$file->id_user.'/'.Input::get('folder').'/'.$key);
		rename($old_path, $new_path);
		$query->update(array('folder_key' => Input::get('folder')));
	}

	public function saveKey($file, Key $item){
		$item->path = '/';
		$item->folder_key = $file['folder'];
		$item->key = $this->genKey();
		if(Auth::check()){
			$item->id_user=Auth::user()->id;
		}
		else{
			$item->id_user=2;
		}
		LogDao::logCreate($this->table, 'folder_key, key, id_user', $file['folder'].', '.$item->key.', '.$item->id_user);
		return $item->save();
	}

	public function validate($file){
		if(Auth::check()){
			$max_file_size=10000;
		}
		else{
			$max_file_size=1000;
		}
		$rules=array(
			'file' => 'required|max:'.$max_file_size
		);
		return Validator::make($file, $rules);
	}
}
?>
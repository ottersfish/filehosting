<?php
class FileDao extends UserFile{
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

	public function saveFile($file, UserFile $item){
		$item->path = '/';
		$item->key = $this->genKey();
		$item->extension = $file['file']->getClientOriginalExtension();
		$item->filename = basename($file['file']->getClientOriginalName(), '.'.$item->extension);
		if(Auth::check()){
			$item->id_user=Auth::user()->id;
		}
		else{
			$item->id_user=2;
		}
		return $item->save();
	}

	public function getUserFiles($id = 0){
		if($id){
			return $this->where('id_user',$id)->get();
		}
		else{
			return $this->all();
		}
	}

	public function getFilePath($key){
		$ret = new stdClass();
		$file = $this->where('key', $key)->get()->first();
		$id = $file->id_user;
		$targetDir = public_path('files/'.$id.'/'.$file->key);
		$lists = scandir($targetDir,1);
		$fileName = $lists[0];
		$ret->path = $targetDir.'/'.$fileName;
		$ret->fileName = $file->filename.'.'.$file->extension;
		return $ret;
	}

	public function getFileInfo($key){
		$file = $this->where('key', $key)->get()->first();
		$id = $file->id_user;
		$targetDir = public_path('files/'.$id.'/'.$file->key);
		$lists = scandir($targetDir,1);
		$fileName = $lists[0];
		$fileSize = fileSize($targetDir.'/'.$fileName);

		$bytes = $fileSize;
		$precision = 2;
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1);
		$bytes /= (1 << (10 * $pow)); 
		$fileSize=round($bytes, $precision) . ' ' . $units[$pow];
		$ret = new stdClass();
		$ret->path = $file->path;
		$ret->key = $file->key;
		$ret->fileName = $file->filename.'.'.$file->extension;
		$ret->fileSize = $fileSize;
		$ret->id_user = $file->id_user;
		$ret->extension = $file->extension;
		return $ret;
	}

	public function renameFile($key){
		$this->where('key', $key)
			->update(array('filename' => Input::get('fileName')));
	}

	public function deleteFile($key){
		$file = $this->where('key', $key)->get()->first();
		$targetDir = $file->id_user.'/'.$key;
		$this->deleteFilesAndFolder($targetDir);
		$file->delete();
	}

	public function deleteFilesAndFolder($path){
		$dir = public_path('files/'.$path);
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

	public function fileExists($key){
		return $this->where('key', $key)->get()->count();
	}

	public function getFilesAdmin(){
		return $this->join('users','users.id','=','files.id_user')
				->select('*')
				->get();
	}

	public function getFilesByOwnership($id){
		return $this->where('id_user', '=' ,$id)->get();
	}

	public function deleteFilesByOwnership($id){
		$this->where('id_user','=',$id)->delete();
	}

	public function getRevisionHistory($key){
		$file = $this->where('key', $key)->get()->first();
		$id = $file->id_user;
		$targetDir = public_path('files/'.$id.'/'.$file->key);
		$lists = scandir($targetDir);
		$ret = array();
		foreach ($lists as $list){
			if($list == '.' or $list == '..')continue;
			$file = new stdClass();
			$file->uploadedFileName = substr($list, 20);
			$file->timestamp = basename($list, '_'.$file->uploadedFileName);
			array_push($ret, $file);
		}
		return $ret;
	}

	public function reviseFile($file, UserFile $item){
		$id = $this->where('key', $item->key)->get()->first();
		$item->extension = $file['file']->getClientOriginalExtension();
		$item->filename = basename($file['file']->getClientOriginalName(), '.'.$item->extension);
		$item->id_user = $id->id_user;
		try{
			$this->where('key', $item->key)
				->update(array(
					'filename' => $item->filename,
					'extension' => $item->extension
				));
		}
		catch (\Exception $e){
			return false;
		}
		return true;
	}
}
?>
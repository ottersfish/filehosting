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

	public function saveFile(UserFile $item){
		$item->path='/';
		$item->key=$this->genKey();
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
		$file = $this->where('key', $key)->get()->first();
		$id = $file->id_user;
		$targetDir = public_path('files/'.$id.'/'.$file->key);
		$lists = scandir($targetDir,1);
		$fileName = $lists[0];
		return $targetDir.'/'.$fileName;
	}

	public function getFileInfo($key){
		$file = $this->where('key', $key)->get()->first();
		$id=$file->id_user;
		$targetDir=public_path('files/'.$id.'/'.$file->key);
		$lists=scandir($targetDir,1);
		$fileName=$lists[0];
		$fileSize=fileSize($targetDir.'/'.$fileName);
		$fileInfo=pathinfo($targetDir.'/'.$fileName);

		$bytes=$fileSize;
		$precision=2;
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1);
		$bytes /= (1 << (10 * $pow)); 
		$fileSize=round($bytes, $precision) . ' ' . $units[$pow];
		$ret = new stdClass();
		$ret->path=$file->path;
		$ret->key=$file->key;
		$ret->fileName=$fileName;
		$ret->fileSize=$fileSize;
		$ret->id_user=$file->id_user;
		$ret->extension=$fileInfo['extension'];
		return $ret;
	}

	public function renameFile($key){
		$file = $this->where('key', $key)->get()->first();
		$targetDir = public_path('files/'.$file->id_user.'/'.$key);
		$lists = scandir($targetDir, 1);
		$fileName = $lists[0];
		$fileInfo = pathinfo($targetDir.'/'.$fileName);
		$ext = $fileInfo['extension'];
		$targetName = Input::get('fileName');
		rename($targetDir.'/'.$fileName, $targetDir.'/'.$targetName.'.'.$ext);
	}

	public function deleteFile($key){
		$file = $this->where('key', $key)->get()->first();
		$targetDir = public_path('files/'.$file->id_user.'/'.$key);
		$lists = scandir($targetDir,1);
		$fileName = $lists[0];
		unlink($targetDir.'/'.$fileName);
		rmdir($targetDir);
		$file->delete();
		return $fileName;
	}

	public function fileExists($key){
		return $this->where('key', $key)->get()->count();
	}

	public function getFilesAdmin(){
		return $this->join('users','users.id','=','files.id_user')
				->select('users.username','files.id_user','files.path','files.key')
				->get();
	}

	public function getFilesByOwnership($id){
		return $this->where('id_user', '=' ,$id)->get();
	}

	public function deleteFilesByOwnership($id){
		$this->where('id_user','=',$id)->delete();
	}
}
?>
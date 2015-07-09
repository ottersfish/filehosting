<?php
class KeyDao extends Key{

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

	public function saveKey($file, Key $item){
		$item->path = '/';
		$item->key = $this->genKey();
		if(Auth::check()){
			$item->id_user=Auth::user()->id;
		}
		else{
			$item->id_user=2;
		}
		return $item->save();
	}

	public function getKeys($id = 0){
		if($id){
			return $this->where('id_user',$id)->get();
		}
		else{
			return $this->all();
		}
	}

	public function deleteKey($key){
		$file = $this->where('key', $key)->get()->first();
		$targetDir = $file->id_user.'/'.$key;
		$this->deleteFilesAndFolder($targetDir);
		$file->delete();
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

	public function fileExists($key){
		return $this->where('key', $key)->get()->count();
	}

	public function getFiles($owner){
		return $this->join('files', 'keys.key', '=', 'files.key')
					->where('is_active', true)
					->where('id_user', $owner)
					->select('*')
					->get();
	}

	public function getFilesAdmin($paginate, $num=0){
		if($paginate){
			return $this->join('files', 'keys.key', '=', 'files.key')
					->join('users', 'users.id', '=', 'keys.id_user')
					->where('is_active', true)
					->select('*')
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

	public function deleteFilesByOwnership($id){
		$this->where('id_user','=',$id)->delete();
	}

	public function getByKey($key){
		return $this->where('key', $key)->get()->first();
	}
}
?>
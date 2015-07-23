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
		return $this->create($folderData);
	}

	public function validate($folderData){
		$messages = array(
			'unique' => 'Your requested :attribute exists.'
		);

		$rules = array(
			'folder_name' 	=> 'required|unique:folders,folder_name'
		);

		return Validator::make($folderData, $rules, $messages);
	}

	public function createFolder($folderData){
		$main_dir = storage_path('files/');
		$folderData['key'] = $this->genKey();
		$target_dir = $main_dir.$folderData['owner'].'/'.$folderData['key'];
		mkdir($target_dir);
		return $this->create($folderData);
	}

	public function getFolderLists($id){
		return $this->where('owner', $id)->orderBy('folder_name')->lists('folder_name','key');
	}

	public function getFolderName($key){
		return $this->where('key', $key)->pluck('folder_name');
	}

	public function getFolderKeyByOwnerandName($owner, $folder_name){
		return $this->where('owner', $owner)->where('folder_name', $folder_name)->pluck('key');
	}

	public function exists($key){
		return $this->where('key', $key)->get()->count();
	}
}
?>
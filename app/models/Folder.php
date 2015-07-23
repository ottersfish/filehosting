<?php

class Folder extends Eloquent {
	
	protected $table = 'folders';
	protected $fillable = array('key', 'parent', 'folder_name', 'owner');

	public function File(){
		return $this->hasMany('File');
	}

}
?>
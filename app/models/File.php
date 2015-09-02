<?php

class myFile extends Eloquent {
	
	protected $table = 'files';
	protected $fillable = array('filename');
	protected $primaryKey = 'key';

	public function Key(){
		return $this->belongsTo('Key');
	}

}
?>
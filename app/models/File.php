<?php

class myFile extends Eloquent {
	
	protected $table = 'files';
	protected $fillable = array('filename');

	public function Key(){
		return $this->belongsTo('Key');
	}

}
?>
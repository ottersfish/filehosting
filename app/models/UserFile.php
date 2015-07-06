<?php

class userFile extends Eloquent {
	
	protected $table = 'files';
	protected $fillable=array('path','key','id_user');

	public function user(){
		return $this->belongsTo('Users');
	}

}
?>
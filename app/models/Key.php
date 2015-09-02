<?php

class Key extends Eloquent {
	
	protected $table = 'keys';
	protected $fillable=array('path','key','id_user');

	public function User(){
		return $this->belongsTo('User');
	}

	public function Files(){
		return $this->hasMany('myFile', 'foreign_key');
	}

}
?>
<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public function getRememberToken(){
		return null;
	}

	public function setRememberToken($value){
	}

	public function getRememberTokenName(){
		return null;
	}

	public function getAuthIdentifier() {
		return $this->id;
	}

	public function getAuthPassword() {
		return $this->password;
	}


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
	protected $fillable = array('email','username','password','name');
	protected $guarded = array('id','is_admin');
	
	public function file(){
		return $this->hasMany('File');
	}

	public function owns($key){
		$file = Key::where('key', $key)->get()->first();
		return $file->id_user == Auth::user()->id;
	}

	public function canEdit($key){
		return $this->is_admin or $this->owns($key);
	}

	public function ownsFolder($key){
		$folder = Folder::where('key', $key)->get()->first();
		return $folder->owner == Auth::user()->id;
	}

	public function canEditFolder($key){
		return $this->is_admin or $this->ownsFolder($key);
	}
}

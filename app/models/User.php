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
		if($file->id_user == Auth::user()->id){
			return true;
		}
		else{
			return false;
		}
	}

	public function canEdit($key){
		if($this->is_admin || $this->owns($key)){
			return true;
		}
		else{
			return false;
		}
	}

	public function ownsFolder($key){
		$folder = Folder::where('key', $key)->get()->first();
		if($folder->owner == Auth::user()->id){
			return true;
		}
		else{
			return false;
		}
	}

	public function canEditFolder($key){
		if($this->is_admin || $this->ownsFolder($key)){
			return true;
		}
		else{
			return false;
		}
	}
}

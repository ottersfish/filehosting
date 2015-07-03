<?php

class userFile extends Eloquent {
	protected $table = 'files';
	protected $fillable=array('path','key','id_user');
	public function user(){
		return $this->belongsTo('Users');
	}

	private function checkKey($key){
		$cek = UserFile::whereKey($key);
		$count=0;
		foreach($cek as $tmp){
			$count++;
		}
		return $count==0;
	}

	public function scopeGenKey(){
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

	public static function getInfo($key){
		$file = UserFile::where('key',$key)->get();
		$file=$file[0];
		$id=$file->id_user;
		$targetdir=public_path('files/'.$id.'/'.$file->key);
		$lists=scandir($targetdir,1);
		$filename=$lists[0];
		$filesize=filesize($targetdir.'/'.$filename);
		$fileinfo=pathinfo($targetdir.'/'.$filename);

		$bytes=$filesize;
		$precision=2;
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1);
		$bytes /= (1 << (10 * $pow)); 
		$filesize=round($bytes, $precision) . ' ' . $units[$pow];
		$ret = new stdClass();
		$ret->path=$file->path;
		$ret->key=$file->key;
		$ret->filename=$filename;
		$ret->filesize=$filesize;
		$ret->id_user=$file->id_user;
		$ret->extension=$fileinfo['extension'];
		return $ret;
	}
}
?>
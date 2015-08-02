<?php
class Helpers{
	/**
	 * Create a string of well-formated file size
	 * @param  int     file size in Bytes
	 * @return string
	 */
	public static function formatFileSize($size){
		$bytes = $size;
		$precision = 2;
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1);
		$bytes /= (1 << (10 * $pow)); 
		return round($bytes, $precision) . ' ' . $units[$pow];
	}

    public static function moveFile($file, $fileKey, $fileHist){
        $main_dir = storage_path('files/');
        $target_dir = $main_dir.$fileKey->id_user.'/'.$file['folder'].'/'.$fileKey->key;
        if(!file_exists($target_dir))mkdir($target_dir);
        $file['file']->move($target_dir, $fileHist->origFilename.'.'.$fileHist->extension);
    }

}

?>
<?php
class Logging extends Logs{

	public static function logCreate($table, $column_affected, $action){
		$logData['table'] = $table;
		$logData['column_affected'] = $column_affected;
		$logData['action'] = $action;
		if(Auth::check())$logData['user'] = Auth::user()->id;
		else $logData['user'] = 2;
		return self::create($logData);
	}
}
?>
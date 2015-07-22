<?php
class LogDao{
	public static function logCreate($table_affected, $column_affected, $new_value){
		$logInstance = new Logs;
		$logInstance->table_affected = $table_affected;
		$logInstance->column_affected = $column_affected;
		$logInstance->action = 'create';
		$logInstance->new_value = $new_value;
		if(Auth::check())$logInstance->user = Auth::user()->id;
		else $logInstance->user = 2;
		return $logInstance->save();
	}

	public static function logDelete($table_affected, $old_value){
		$logInstance = new Logs;
		$logInstance->table_affected = $table_affected;
		$logInstance->column_affected = 'all';
		$logInstance->action = 'delete';
		$logInstance->old_value = $old_value;
		if(Auth::check())$logInstance->user = Auth::user()->id;
		else $logInstance->user = 2;
		return $logInstance->save();
	}

	public static function logEdit($table_affected, $column_affected, $old_value, $new_value){
		$logInstance = new Logs;
		$logInstance->table_affected = $table_affected;
		$logInstance->column_affected = $column_affected;
		$logInstance->action = 'edit';
		$logInstance->old_value = $old_value;
		$logInstance->new_value = $new_value;
		if(Auth::check())$logInstance->user = Auth::user()->id;
		else $logInstance->user = 2;
		return $logInstance->save();
	}

	public static function getLogs(){
		$logInstance = new Logs;
		return $logInstance->paginate(10);
	}
}
?>
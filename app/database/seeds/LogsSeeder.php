<?php
class LogsSeeder extends Seeder {
	public function run(){
		DB::disableQueryLog();
		$string = 'aaaaabbbbccccddddeeeeffffgggghhhhhhiiiiiiijjjjjjjkkkkkkkk';
		$datas = array();
		for($i=0;$i<10000;$i++){
			$data = array(
				'table_affected' => 'test',
				'column_affected' => $string=str_shuffle($string),
				'action' => $string=str_shuffle($string),
				'old_value' => $string=str_shuffle($string),
				'new_value' => $string=str_shuffle($string),
				'user' => $string=str_shuffle($string),
			);
			array_push($datas, $data);
		}
		Logs::insert($datas);
	}

}
?>
<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class deleteGuestFilesCommand extends Command {

	protected $keyDao, $fileDao;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:deleteGuestFiles';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete guest files which has been uploaded more than 1 day.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(KeyDao $keyDao, myFileDao $fileDao)
	{
		parent::__construct();
		$this->myFileDao = $fileDao;
		$this->keyDao = $keyDao;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$this->myFileDao->deleteGuestFiles();
		$this->keyDao->deleteGuestFiles();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}

<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class initiateCommand extends Command {

    protected $folderDao;

    /**
     * The console command name.
     *
     * @var string
     */

    protected $name = 'server:initiate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize project requirements.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FolderDao $folderDao)
    {
        parent::__construct();
        $this->folderDao = $folderDao;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //
        if(!file_exists(storage_path('/files')))mkdir(storage_path('/files'));
        $this->folderDao->createRootFolder(1);
        $this->folderDao->createRootFolder(2);
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

<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

class Cron extends Base
{
    protected $tasks = [
        'files' => 'Backupz\\Backup\\Files',
        'database' => 'Backupz\\Backup\\Database'
    ];

    protected function getEnabledTasks()
    {
        $config = $this->getConfig();
        return $config['cron']['tasks'];
    }

    private function getTask($name)
    {
        $app = $this->getContainer();
        $task = new $this->tasks[$name]($app);

        return $task;
    }

    private function runTask($name)
    {
        $task = $this->getTask($name);
        $task->runForAll();
    }

    public function run()
    {
        $tasks = $this->getEnabledTasks();
        foreach ($tasks as $task) {
            $this->runTask($task);
        }
    }
}

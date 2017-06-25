<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz;

class Cron extends Base
{
    /**
     * Return avalible tasks with class
     * @var array
     */
    protected $tasks = [
        'files' => 'Backupz\\Backup\\Files',
        'database' => 'Backupz\\Backup\\Database'
    ];

    /**
     * Get the cron tasks configured in config.yml
     * @return array
     */
    protected function getEnabledTasks()
    {
        $config = $this->getConfig();
        return $config['cron']['tasks'];
    }

    /**
     * Load a new task
     * @param  string $name Name of the task to load
     * @return \Backupz\Backup\BackupInterface
     */
    private function getTask($name)
    {
        $app = $this->getContainer();
        $task = new $this->tasks[$name]($app);

        return $task;
    }

    /**
     * Run an indervidual task
     * @param string $name Name of the task running
     */
    private function runTask($name)
    {
        $task = $this->getTask($name);
        $task->runForAll();
    }

    /**
     * Run all of the enabled cron tasks
     */
    public function run()
    {
        $tasks = $this->getEnabledTasks();
        foreach ($tasks as $task) {
            $this->runTask($task);
        }
    }
}

<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

interface BackupInterface
{
    public function runForAll();
    public function run($name);
    public function save();
}

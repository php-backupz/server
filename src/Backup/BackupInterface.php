<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

interface BackupInterface
{
    public function listAll();
    public function save();
    public function run($name);
    public function runForAll();
}

<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

interface StorageInterface
{
    /**
     * Configure the adapter
     */
    public function configure();
}

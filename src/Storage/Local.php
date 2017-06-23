<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

use League\Flysystem\Adapter\Local as LocalAdapter;

class Local extends StorageBase implements StorageInterface
{
    public function configure()
    {
        $config = $this->getConfig();
        $adapter = new LocalAdapter($config['root']);

        return $adapter;
    }
}

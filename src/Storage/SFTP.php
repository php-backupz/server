<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

use League\Flysystem\Sftp\SftpAdapter;

class SFTP extends StorageBase implements StorageInterface
{
    public function configure()
    {
        $config = $this->getConfig();
        $adapter = new SftpAdapter($config);

        return $adapter;
    }
}

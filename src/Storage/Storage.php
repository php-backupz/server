<?php

namespace Backupz\Storage;

use Backupz\Base;
use League\Flysystem\Filesystem;

class Storage extends Base
{
    /**
     * Flysystem adapter
     */
    protected $atapter;

    /**
     * @var League\Flysystem\Filesystem
     */
    protected $filesystem;

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

    public function initilize()
    {
        $this->configureAdapter();
        $this->connect();
    }

    private function configureAdapter()
    {
        //TODO Make this more reliable / refactor
        $app = $this->getContainer();
        $config = $app['config']['config'];
        $storage = $config['storage'];

        switch ($storage['type']) {
            case 'sftp':
                $adapter = new SFTP($app, $storage);
                break;
        }

        $this->setAdapter($adapter->getAdapter());
    }

    private function connect()
    {
        $filesystem = new Filesystem($this->getAdapter());
        $this->setFilesystem($filesystem);
    }

    public function listAllFiles()
    {
        $filesystem = $this->getFilesystem();
        $list = $filesystem->listContents();

        var_dump($list);
    }
}
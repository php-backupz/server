<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

use Backupz\Base;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Storage extends Base
{
    /**
     * Flysystem adapter
     */
    protected $adapter;

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return \League\Flysystem\AdapterInterface
     */
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
        $config = $this->getConfig();
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

    public function getLocal()
    {
        $app = $this->getContainer();
        $adapter = new Local($app['varPath']);
        $filesystem = new Filesystem($adapter);

        return $filesystem;
    }

    public function moveToRemote($localPath, $remotePath)
    {
        $local = $this->getLocal();
        $remote = $this->getFilesystem();

        $contents = $local->readAndDelete($localPath);
        $remote->put($remotePath, $contents);
    }

    /**
     * Remove all of the temporary files created
     * @return boolval The amount of files removed
     */
    public function clearCache()
    {
        $local = $this->getLocal();
        $deleted = 0;

        foreach ($local->listContents() as $object) {
            // Don't delete the .gitkeep file
            if ($object['basename'] === '.gitkeep') {
                continue;
            }

            $local->delete($object['path']);
            $deleted++;
        }

        return (int) $deleted;
    }
}

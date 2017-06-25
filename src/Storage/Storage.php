<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Storage;

use Backupz\Base;
use Backupz\Storage\Local as LocalStorage;
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

    /**
     * Set filesystem
     * @param \League\Flysystem\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Get filesystem
     * @return \League\Flysystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    public function initilize()
    {
        $this->configureAdapter();
        $this->connect();
    }

    /**
     * Setup the adapter using the configured remote
     */
    private function configureAdapter()
    {
        //TODO Make this more reliable / refactor
        $app = $this->getContainer();
        $config = $this->getConfig();
        $storage = $config['storage'];

        switch ($storage['type']) {
            case 'local':
                $adapter = new LocalStorage($app, $storage);
                break;
            case 'sftp':
                $adapter = new SFTP($app, $storage);
                break;
        }

        $this->setAdapter($adapter->getAdapter());
    }

    /**
     * Connect to the filesystem
     */
    private function connect()
    {
        $filesystem = new Filesystem($this->getAdapter());
        $this->setFilesystem($filesystem);
    }

    /**
     * List all of the files at the remote filesystem
     * @return array
     */
    public function listAllFiles()
    {
        $filesystem = $this->getFilesystem();
        $list = $filesystem->listContents();

        return $list;
    }

    /**
     * Get local filesystem for storing data dumps
     * @return \League\Flysystem\Filesystem
     */
    public function getLocal()
    {
        $app = $this->getContainer();
        $adapter = new Local($app['varPath']);
        $filesystem = new Filesystem($adapter);

        return $filesystem;
    }

    /**
     * Move a local file to a remote filesystem using steams
     * @param string $localPath Reletive path to the the local adapter
     * @param string $remotePath Reletive path to the the remote adapter
     */
    public function moveToRemote($localPath, $remotePath)
    {
        $local = $this->getLocal();
        $remote = $this->getFilesystem();

        $stream = $local->readStream($localPath);
        $remote->writeStream($remotePath, $stream);
        fclose($stream);
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

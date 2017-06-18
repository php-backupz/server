<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

use Backupz\Base;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class Files extends Base
{
    /**
     * An array of paths that should be excluded from the backup
     * @var array
     */
    protected $excluded = [];

    /**
     * Filename of the backup
     * @var string
     */
    protected $filename;

    /**
     * [$path description]
     * @var string
     */
    protected $path;

    protected $name;

    public function getExcluded()
    {
        return $this->excluded;
    }

    /**
     * @param array $excluded
     */
    public function setExcluded($excluded)
    {
        $this->excluded = $excluded;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Check if a path should be excluded
     * @param  string  $path The path to be checked
     * @return boolean
     */
    private function isExcluded($path)
    {
        foreach ($this->getExcluded() as $exclude) {
            if (strpos($path, $exclude) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a flysystem for the remote server
     * @return \League\Flysystem\Filesystem
     */
    public function getRemoteAdapter()
    {
        $config = $this->getConfig();
        $adapter = new SftpAdapter($config['websites']);

        return new Filesystem($adapter);
    }

    /**
     * Return a flysystem for the local filesystem
     * @return \League\Flysystem\Filesystem
     */
    public function getZipAdapter()
    {
        $adapter = new ZipArchiveAdapter($this->getFilename());

        return new Filesystem($adapter);
    }

    /**
     * Get the name of the local file
     * @return string
     */
    public function getTmpFilename()
    {
        $filename = $this->getFilename();

        return str_replace($this->app['varPath'] . '/', '', $filename);
    }

    /**
     * Run a backup for each of the top level directory
     */
    public function runForAll()
    {
        $remote = $this->getRemoteAdapter();
        $tld = $remote->listContents();

        foreach ($tld as $directory) {
            if ($directory['type'] !== 'dir') {
                continue;
            }

            $this->run($directory['path']);
        }

    }

    /**
     * Run the backup
     * @return boolean Did the backup run successfully?
     */
    public function run($name = '')
    {
        $filename = $this->app['varPath'] . '/' . time() . '.zip';
        $this->setFilename($filename);

        if ($name === '') {
            $name = $this->getPath();
        }
        $this->name = $name;

        $remote = $this->getRemoteAdapter();
        $zip = $this->getZipAdapter();
        $contents = $remote->listContents($name . '/public/files', true);

        // If there are no files to be backed up
        if ($contents === []) {
            return true;
        }

        foreach ($contents as $info) {
            // Only add files to the zip
            if ($info['type'] === 'dir') {
                continue;
            }

            // Check that this file shouldn't be excluded
            if ($this->isExcluded($info['path'])) {
                continue;
            }

            // Check for access to the remote file and then add it to the zip file
            $file = $remote->read($info['path']);
            if ($file) {
                $zip->write($info['path'], $file);
            }
        }

        // Close and save the zip file
        $zip = null;

        // Upload the zip file to the remote server
        $save = $this->save();

        return $save;
    }

    /**
     * Upload the backup file to the remote server
     * @return boolean
     */
    public function save()
    {
        $app = $this->getContainer();
        $filename = $this->getTmpFilename();

        $app['storage']->moveToRemote($filename, 'files/' . $this->name . '/' . $filename);

        return true;
    }
}

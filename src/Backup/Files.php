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
    protected $excluded = [];

    protected $filename;

    protected $path;

    public function getExcluded()
    {
        return $this->excluded;
    }

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

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function initilize()
    {
        $filename = '/tmp/' . time() . '.zip';
        $this->setFilename($filename);

        $config = $this->getConfig();
        $path = $config['websites']['path'];
        $this->setPath($path);
    }

    private function isExcluded($path)
    {
        foreach ($this->getExcluded() as $exclude) {
            if (strpos($path, $exclude) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getRemoteAdapter()
    {
        $config = $this->getConfig();
        $adapter = new SftpAdapter($config['websites']);

        return new Filesystem($adapter);
    }

    public function getZipAdapter()
    {
        $adapter = new ZipArchiveAdapter($this->getFilename());

        return new Filesystem($adapter);
    }

    public function run()
    {
        $path = $this->getPath();
        $remote = $this->getRemoteAdapter();
        $zip = $this->getZipAdapter();

        $contents = $remote->listContents($path . '/public/files', true);

        foreach ($contents as $info) {
            if ($info['type'] === 'dir') {
                continue;
            }

            if ($this->isExcluded($info['path'])) {
                continue;
            }

            $zip->write($info['path'], $remote->read($info['path']));
        }

        // Close and save the zip file
        $zip->getAdapter()->getArchive()->close();

        // Upload the zip file to the remote server
        $this->save();
    }

    public function getTmpFilename()
    {
        $filename = $this->getFilename();

        return str_replace('/tmp/', '', $filename);
    }

    public function save()
    {
        $app = $this->getContainer();
        $filename = $this->getTmpFilename();
        $path = $this->getPath();

        $app['storage']->moveToRemote($filename, 'files/' . $path . '/' . $filename);
    }
}

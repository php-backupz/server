<?php
/**
* @author Chris Hilsdon <chris@koolserve.uk>
*/
namespace Backupz\Backup;

use Backupz\Base;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\Console\Helper\ProgressBar;

class Files extends Base implements BackupInterface
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
     * The name of the direcorty to backup
     * @var string
     */
    protected $name;

    /**
     * Get filename
     */
    public function getExcluded()
    {
        return $this->excluded;
    }

    /**
     * Set excluded
     * @param array An array of paths to be excluded from the backup
     */
    public function setExcluded($excluded)
    {
        $this->excluded = $excluded;
    }

    /**
     * Get filename
     * @return string
     */
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
        $app = $this->getContainer();
        $remote = $this->getRemoteAdapter();
        $tld = $remote->listContents();

        foreach ($tld as $directory) {
            if ($directory['type'] !== 'dir') {
                continue;
            }

            $path = $directory['path'];

            if ($app['console']) {
                $app['console']->output->writeln('Starting backup of ' . $path);
            }

            $this->run($directory['path']);

            if ($app['console']) {
                $app['console']->output->writeln('<info>Finished!</info>');
            }
        }
    }

    /**
     * Run the backup
     * @return boolean Did the backup run successfully?
     */
    public function run($name)
    {
        $app = $this->getContainer();
        $filename = $app['varPath'] . '/' . time() . '.zip';
        $this->setFilename($filename);
        $this->name = $name;

        $remote = $this->getRemoteAdapter();
        $zip = $this->getZipAdapter();
        $contents = $remote->listContents($this->name . '/public/files', true);

        // If there are no files to be backed up
        if ($contents === []) {
            return true;
        }

        $files = [];
        foreach ($contents as $key => $info) {
            $path = $info['path'];

            // Only add files to the zip
            if ($info['type'] === 'dir') {
                continue;
            }

            // Check that this file shouldn't be excluded
            if ($this->isExcluded($path)) {
                continue;
            }

            // Add it to the list of files to be saved
            $files[] = [
                'path' => $path,
                'size' => $this->getReadableFilesize($info['size']),
            ];
        }

        $progress = false;

        if ($app['console']) {
            $progress = new ProgressBar($app['console']->output, count($files));
            $progress->setFormatDefinition('custom', "%message% \n%current%/%max% [%bar%]");
            $progress->setFormat('custom');
        }

        foreach ($files as $index => $file) {
            $path = $file['path'];

            if ($progress !== false) {
                $progress->setMessage('['.$file['size'].']: ' . $path);
                $progress->advance();
            }

            // Check for access to the remote file and then add it to the zip file
            $fileContents = $remote->read($path);
            if ($fileContents !== false) {
                $zip->write($path, $fileContents);
            }
        }

        if ($progress !== false) {
            $progress->finish();
        }
        $app['log']->output('');

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
        $remotePath = 'files/' . $this->name . '/' . $filename;

        if ($app['console']) {
            $app['log']->output('Uploading to storage');
            $app['log']->output('filename: ' . $remotePath);
        }

        $app['storage']->moveToRemote($filename, $remotePath);

        if ($app['console']) {
            $app['log']->output('Finished upload');
        }

        return true;
    }
}

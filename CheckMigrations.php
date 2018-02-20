<?php
/**
 * Created by PhpStorm.
 * User: dezer
 * Date: 16.02.18
 * Time: 16:21
 */

class CheckMigrations
{
    protected $remoteUrl;
    protected $migrationsDir = '/migrations/';
    protected $thisItem;

    protected $localItems;
    protected $localDiffItems;
    protected $localLastFile;
    protected $pathMigrationsFolder;
    protected $requestJsonNumber;

    public function __construct($remoteUrl = '', $pathMigrationsFolder = './')
    {
        $this->remoteUrl = $remoteUrl;
        $this->pathMigrationsFolder = $pathMigrationsFolder;
        $requestJsonNumber = (isset($_REQUEST['json']) ? $_REQUEST['json'] : 0);
        $this->requestJsonNumber = empty($requestJsonNumber) ? 0 : $requestJsonNumber;
        $this->localItems = [];
        $this->localDiffItems = [];
    }

    public function getRemoteDiff()
    {
        $getDiff = file_get_contents($this->remoteUrl . $this->migrationsDir . '/localCheck.php?json=' . $this->getLocalLastFile());
        $diff = json_decode($getDiff);
        $this->loadFiles($diff);
    }

    public function getLocalLastFile()
    {
        if (empty($this->localLastFile)) {
            $this->loadLastFile();
        }
        return $this->localLastFile;
    }

    public function loadLastFile()
    {
        if (empty($this->localLastFile)) {
            $localItems = $this->getLocalItems();
            sort($localItems);
            $this->localLastFile = array_pop($localItems);
        }
        $this->thisItem = $this->localLastFile;
        return $this;
    }

    public function getLocalItems()
    {
        if (empty($this->localItems)) {
            $this->loadLocalFiles();
        }
        return $this->localItems;
    }

    public function loadLocalFiles()
    {
        if ($dirHandle = opendir($this->pathMigrationsFolder)) {
            while (($file = readdir($dirHandle)) !== false) {
                $path = pathinfo($file);
                if ($path['extension'] == 'json') {
                    $this->localItems[] = $path['filename'];
                }
            }
            if (!empty($items)) {
                $this->localItems = sort($items);
            }
        }
        $this->thisItem = $this->localItems;
        return $this;
    }

    protected function loadFiles($files)
    {
        foreach ($files as $file) {
            $data = file_get_contents($this->remoteUrl . $this->migrationsDir . $file);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . $this->migrationsDir . $file, $data);
        }
    }

    public function getLocalDiffItems()
    {
        if (empty($this->localDiffItems)) {
            $this->loadDiffFiles();
        }
        return $this->localDiffItems;
    }

    public function loadDiffFiles()
    {
        if (empty($this->localDiffItems)) {
            $localFiles = $this->getLocalItems();
            foreach ($localFiles as $fileName) {
                if ($fileName > $this->requestJsonNumber) {
                    $this->localDiffItems[] = $fileName . '.json';
                }
            }
        }
        $this->thisItem = $this->localDiffItems;
        return $this;
    }

    public function showJson()
    {
        sort($this->thisItem);
        echo json_encode($this->thisItem);
    }
}
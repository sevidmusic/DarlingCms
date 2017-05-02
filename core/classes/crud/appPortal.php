<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/1/17
 * Time: 6:32 PM
 */

namespace DarlingCms\classes\crud;


class appPortal extends \DarlingCms\abstractions\crud\Acrud
{
    private $runningApps;

    public function __construct(\DarlingCms\classes\startup\singleAppStartup $singleAppStartup)
    {
        $this->runningApps = $singleAppStartup->getApp()->getComponentAttributeValue('customAttributes')['runningApps'];
    }

    /**
     * @inheritdoc
     */
    protected function query(string $storageId, string $mode, $data = null)
    {
        switch ($mode) {
            case 'save':
                if (isset($this->runningApps[$storageId]) === true) {
                    $app = $this->runningApps[$storageId];
                    return $app->setCustomAttribute('appOutput', $data);
                }
                break;
            case 'load':
                if (isset($this->runningApps[$storageId]) === true) {
                    $app = $this->runningApps[$storageId];
                    return $app->getComponentAttributeValue('customAttributes')['appOutput'];
                }
                break;
            case 'delete':
                $this->query($storageId, 'save', '');
                if ($this->query($storageId, 'load') === '') {
                    return true;
                }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function pack($data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function unpack($packedData)
    {
        return $packedData;
    }

}

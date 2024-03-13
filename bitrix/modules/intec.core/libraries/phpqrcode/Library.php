<?php
namespace intec\core\libraries\phpqrcode;

/**
 * Class Library
 * @inheritdoc
 * @package intec\core\libraries\phpmorphy
 */
class Library extends \intec\core\base\Library
{
    /**
     * @inheritdoc
     */
    protected static $_instance;

    /**
     * @inheritdoc
     */
    public function load()
    {
        if ($this->getIsLoaded())
            return true;

        include(__DIR__.'/distribution/common.php');

        return $this->getIsLoaded();
    }

    /**
     * @inheritdoc
     */
    public function getIsLoaded()
    {
        return class_exists('QRcode');
    }
}
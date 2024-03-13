<?php

namespace intec\core\bitrix\iblock;

use intec\core\bitrix\iblock\helpers\SectionsHelper;
use intec\core\collections\Arrays;

/**
 * Class Sections
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
class Sections extends Arrays
{
    /**
     * @return $this
     */
    public function handleFiles()
    {
        SectionsHelper::handleFiles($this->items);

        return $this;
    }
}

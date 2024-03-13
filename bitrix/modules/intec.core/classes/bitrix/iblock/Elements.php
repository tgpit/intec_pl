<?php

namespace intec\core\bitrix\iblock;

use intec\core\bitrix\FilesQuery;
use intec\core\bitrix\iblock\helpers\ElementsHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

/**
 * Class Elements
 * @package intec\core\bitrix\iblock
 * @deprecated
 */
class Elements extends Arrays
{
    /**
     * @return static
     */
    public function handle()
    {
        $this->handleFiles(self::HANDLE_FILES_MODE_ALL);

        return $this;
    }

    /**
     * Режим обработки файлов: Только внутренние.
     */
    const HANDLE_FILES_MODE_INTERNAL = 0b1;
    /**
     * Режим обработки файлов: Только свойства.
     */
    const HANDLE_FILES_MODE_PROPERTIES = 0b10;
    /**
     * Режим обработки файлов: Все.
     */
    const HANDLE_FILES_MODE_ALL = self::HANDLE_FILES_MODE_INTERNAL | self::HANDLE_FILES_MODE_PROPERTIES;

    /**
     * @param int $mode
     * @return static
     */
    public function handleFiles($mode = self::HANDLE_FILES_MODE_INTERNAL)
    {
        ElementsHelper::handleFiles($this->items, $mode);

        return $this;
    }
}

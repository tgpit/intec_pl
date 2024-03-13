<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

class IntecSeoFilterLoaderComponent extends CBitrixComponent
{
    /**
     * Содержит загруженные фильтры.
     * @var array
     */
    protected static $filter = null;

    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        return ArrayHelper::merge([
            'FILTER_RESULT' => null,
            '~FILTER_RESULT' => null
        ], $arParams);
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        if (!empty($this->arParams['~FILTER_RESULT'])) {
            if (Type::isArray($this->arParams['~FILTER_RESULT']) && !empty($this->arParams['~FILTER_RESULT']['ITEMS']))
                self::$filter = $this->arParams['~FILTER_RESULT'];
        }

        $this->setFrameMode(true);

        return self::$filter;
    }
}
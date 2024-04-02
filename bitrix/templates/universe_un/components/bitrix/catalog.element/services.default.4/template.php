<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

Loc::loadMessages(__FILE__);

$arSvg = [
    'ADDITIONAL' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/additional.icon.1.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/additional.icon.2.svg')
    ]
];

?>
<div id="<?= $sTemplateId ?>" class="ns-bitrix c-catalog-element c-catalog-element-services-default-4">
    <?php include(__DIR__.'/parts/blocks.php') ?>
</div>
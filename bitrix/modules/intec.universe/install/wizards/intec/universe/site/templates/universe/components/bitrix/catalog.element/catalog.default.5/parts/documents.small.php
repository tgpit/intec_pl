<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arFields
 */

if (empty($arVisual['DOCUMENTS']['NAME']))
    $arVisual['DOCUMENTS']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DOCUMENTS_NAME_DEFAULT');

$fGetFileNameWOExtension = function($fileName) {
    $iPositionExt = strrpos($fileName, ".");

    if ($iPositionExt)
        return substr($fileName, 0 , $iPositionExt);

    return $fileName;
};

?>
<div class="catalog-element-documents catalog-element-additional-block">
    <div class="catalog-element-additional-block-name-small">
        <?= $arVisual['DOCUMENTS']['NAME'] ?>
    </div>
    <div class="catalog-element-additional-block-content">
        <div class="intec-grid intec-grid-wrap catalog-element-documents-item-container">
            <?php foreach ($arFields['DOCUMENTS']['VALUES'] as $arDocument) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-documents-item',
                        'intec-grid-item' => [
                            '1',
                            '1024-3',
                            '768-2',
                            '500-1'
                        ]
                    ]
                ]) ?>
                    <?= Html::beginTag('a', [
                        'class' => 'catalog-element-documents-item-content',
                        'href' => $arDocument['SRC'],
                        'target' => '_blank'
                    ]) ?>
                        <div class="catalog-element-documents-item-name">
                            <?= $fGetFileNameWOExtension($arDocument['ORIGINAL_NAME']) ?>
                        </div>
                        <div class="catalog-element-documents-item-size">
                            <?= CFile::FormatSize($arDocument['FILE_SIZE']) ?>
                        </div>
                        <div class="catalog-element-documents-item-extension">
                            <?= FileHelper::getFileExtension($arDocument['FILE_NAME']) ?>
                        </div>
                    <?= Html::endTag('a') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php unset($arDocument) ?>
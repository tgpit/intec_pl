<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arFields
 */

$fGetFileNameWOExtension = function($fileName) {
    $iPositionExt = strrpos($fileName, ".");

    if ($iPositionExt)
        return substr($fileName, 0 , $iPositionExt);

    return $fileName;
};

?>
<div class="catalog-element-documents-item-container">
    <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch">
        <?php foreach ($arFields['DOCUMENTS']['VALUES'] as $arDocument) { ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-element-documents-item' => true,
                    'intec-grid-item' => [
                        $arVisual['DOCUMENTS']['COLUMNS'] => true,
                        '1200-3' => $arVisual['DOCUMENTS']['COLUMNS'] >= 4,
                        '768-2' => true,
                        '500-1' => true
                    ]
                ], true)
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
<?php unset($arDocument) ?>
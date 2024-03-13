<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = &$arResult['VISUAL'];
$arSvg = [
    'RATING' => FileHelper::getFileData(__DIR__.'/svg/rating.svg'),
    'GALLERY' => [
        'PLAY' => FileHelper::getFileData(__DIR__.'/svg/gallery.play.svg')
    ],
    'FILES' => [
        'DOC' => FileHelper::getFileData(__DIR__.'/svg/files.doc.svg'),
        'PDF' => FileHelper::getFileData(__DIR__.'/svg/files.pdf.svg'),
        'COMMON' => FileHelper::getFileData(__DIR__.'/svg/files.common.svg')
    ]
];

$renderItemDefault = include(__DIR__.'/parts/item.default.php');
$renderItemPlayer = include(__DIR__.'/parts/item.player.php');
$renderItemAnswer = include(__DIR__.'/parts/item.answer.php');

?>
<div class="ns-bitrix c-news-list c-news-list-reviews-list-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['SHOW']['TOP']) { ?>
                <div class="news-list-navigation">
                    <?= $arResult['NAV_STRING'] ?>
                </div>
            <?php } ?>
            <div class="news-list-items">
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                ?>
                    <div class="news-list-item" id="<?= $sAreaId ?>">
                        <?php if ($arItem['DATA']['VIDEO']['VIEW'])
                            $renderItemPlayer($arItem);
                        else
                            $renderItemDefault($arItem);

                        if ($arItem['DATA']['ANSWER']['SHOW'])
                            $renderItemAnswer($arItem);
                        ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($arVisual['NAVIGATION']['SHOW']['BOTTOM']) { ?>
                <div class="news-list-navigation">
                    <?= $arResult['NAV_STRING'] ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
<?php unset($arVisual, $renderItemDefault, $renderItemPlayer, $renderItemAnswer) ?>
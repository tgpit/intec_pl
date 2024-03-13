<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

?>
<?php if (isset($arResult['ITEM'])) {

    $arItem = &$arResult['ITEM'];
    $bOffers = !empty($arItem['OFFERS']);

    if (!empty($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']))
        $sName = $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'];
    else
        $sName = $arItem['NAME'];

?>
    <?= Html::beginTag('div', [
        'id' => $sTemplateId,
        'class' => Html::cssClassFromArray([
            'ns-bitrix' => true,
            'c-catalog-item' => true,
            'c-catalog-item-template-4' => true,
        ], true)
    ]) ?>
        <div class="catalog-item-body" data-wide="<?= $arVisual['WIDE'] ? 'true' : 'false' ?>">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'intec-grid' => [
                        '' => true,
                        '500-wrap' => true,
                        'wrap' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] >= 4,
                        'nowrap' => $arVisual['WIDE'] || (!$arVisual['WIDE'] && $arVisual['COLUMNS'] < 4),
                        'a-v-start' => true,
                        'i-6' => true
                    ]
                ], true),
            ]) ?>
                <div class="intec-grid-item-auto intec-grid-item-500-1">
                    <?php include(__DIR__.'/parts/image.php'); ?>
                </div>
                <div class="intec-grid-item">
                    <div class="catalog-item-block-container catalog-item-name-container">
                        <?= Html::tag('a', $sName, [
                            'class' => [
                                'catalog-item-name',
                                'intec-cl-text-hover'
                            ],
                            'href' => $arItem['DETAIL_PAGE_URL']
                        ]) ?>
                    </div>
                    <?php include(__DIR__.'/parts/price.php') ?>
                </div>
                <div class="intec-grid-item-auto">
                    <?php if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' || $arItem['LABEL']) { ?>
                        <div class="catalog-item-sticker">
                            <?php if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($sPercent)) { ?>
                                <div class="catalog-item-sticker-item catalog-item-sticker-percent">
                                    <span>
                                        <?= '-'.$sPercent.'%' ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <?php if ($arItem['LABEL']) { ?>
                                <div class="catalog-item-sticker-item catalog-item-sticker-label">
                                    <?php if (!empty($arItem['LABEL_ARRAY_VALUE'])) { ?>
                                        <?php foreach ($arItem['LABEL_ARRAY_VALUE'] as $value) { ?>
                                            <div class="catalog-item-sticker-label-item">
                                <span>
                                    <?= $value ?>
                                </span>
                                            </div>
                                        <?php } ?>
                                        <?php unset($value) ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php include(__DIR__.'/parts/buttons.php') ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
        <?php include(__DIR__.'/parts/script.php') ?>
	<?= Html::endTag('div') ?>
<?php } ?>
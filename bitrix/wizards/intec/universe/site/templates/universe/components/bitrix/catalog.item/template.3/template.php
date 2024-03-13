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

include(__DIR__.'/parts/image.php');

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
            'c-catalog-item-template-3' => true,
        ], true)
    ]) ?>
        <div class="catalog-item-body">
            <div class="intec-grid intec-grid-wrap intec-grid-a-v-start">
                <div class="intec-grid-item-auto intec-grid-item-500-1">
                    <?php $vImage($arItem) ?>
                </div>
                <div class="intec-grid-item">
                    <div class="catalog-item-content">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-16">
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
                                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                    <div class="catalog-item-block-container catalog-item-vote-container">
                                        <?php include(__DIR__ . '/parts/vote.php') ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="intec-grid-item-auto intec-grid-item-500-1">
                                <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                    <?php include(__DIR__ . '/parts/quantity.php') ?>
                                <?php } ?>
                                <?php if ($arItem['ARTICLE']['SHOW']) { ?>
                                    <?php include(__DIR__.'/parts/article.php'); ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if (!empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                            <div class="catalog-item-properties">
                                <?php $iCounter = 0 ?>
                                <?php foreach ($arItem['DISPLAY_PROPERTIES'] as $arProp) {
                                    $iCounter++;

                                    if ($iCounter > 3)
                                        break;
                                ?>
                                    <div class="catalog-item-property">
                                        <span class="catalog-item-property-title"><?= $arProp['NAME'] ?></span>
                                        <span class="catalog-item-property-text"><?= $arProp['VALUE'] ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                            <div class="catalog-item-description" data-role="description" data-expanded="false">
                                <div class="catalog-item-description-title">
                                    <span data-role="description.more">
                                        <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_3_TEMPLATE_DESCRIPTION_TITLE') ?>
                                        <?/*<i class="far fa-chevron-down"></i>*/?>
                                    </span>
                                </div>
                                <div class="catalog-item-description-text" data-role="description.text">
                                    <?= $arItem['PREVIEW_TEXT'] ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="catalog-item-delimiter"></div>
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-12 intec-ui-p-t-10">
                            <div class="intec-grid-item">
                                <?php include(__DIR__.'/parts/price.php') ?>
                            </div>
                            <div class="intec-grid-item-auto">
                                <?php include(__DIR__.'/parts/buttons.php') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include(__DIR__.'/parts/script.php') ?>
	<?= Html::endTag('div') ?>
<?php } ?>
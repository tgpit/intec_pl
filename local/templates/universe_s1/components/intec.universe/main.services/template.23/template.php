<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$arForm = $arResult['FORM'];

$arForm['BUTTON'] = Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT');

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

$iCounter = 0;
?>
<div class="widget c-services c-services-template-23" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="widget-items-wrap scrollbar-inner" data-role="scrollbar"">
                    <div class="widget-items">
                        <div class="widget-property-title intec-cl-background">
                            <div class="widget-item-property widget-item-number">
                                <?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_NUMBER_TITLE') ?>
                            </div>
                            <div class="widget-item-property widget-item-name intec-cl-background">
                                <?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_NAME_TITLE') ?>
                            </div>
                            <?php if ($arVisual['MEASURE']['SHOW']) { ?>
                                <div class="widget-item-property">
                                    <?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_MEASURE_TITLE') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['PRICE']['SHOW']) { ?>
                                <div class="widget-item-property">
                                    <?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_PRICE_TITLE') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arForm['USE']) { ?>
                                <div class="widget-item-property">
                                    <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                        <input name="items" type="checkbox" data-role="checkbox.all"">
                                        <span class="intec-ui-part-selector"></span>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $arData = $arItem['DATA'];
                            $arForm['PARAMETERS']['fields'][$arForm['FIELD']] = $arItem['NAME'];

                            $iCounter++;
                        ?>
                            <div class="widget-item" id="<?= $sAreaId ?>" data-role="item">
                                <div class="widget-item-property widget-item-number">
                                    <?= $iCounter ?>
                                </div>
                                <div class="widget-item-property widget-item-name">
                                    <?= Html::tag($sTag, $arItem['NAME'], [
                                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'class' => Html::cssClassFromArray([
                                            'widget-item-name' => true,
                                            'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                        ], true),
                                        'data-role' => 'item.name'
                                    ]) ?>
                                </div>
                                <?php if ($arVisual['MEASURE']['SHOW']) { ?>
                                    <div class="widget-item-property widget-item-measure">
                                        <?= !empty($arData['MEASURE']) ? $arData['MEASURE'] : '-' ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['PRICE']['SHOW']) { ?>
                                    <div class="widget-item-property widget-item-price-wrap">
                                        <?php if (!empty($arData['PRICE']['VALUE'])) { ?>
                                            <div class="widget-item-price">
                                                <?= $arData['PRICE']['VALUE'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arForm['USE']) { ?>
                                    <div class="widget-item-property widget-item-checkbox">
                                        <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                            <input name="item_<?= $arItem['ID'] ?>" type="checkbox" data-role="checkbox.item">
                                            <span class="intec-ui-part-selector"></span>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <?php if ($arForm['USE']) { ?>
                    <div class="widget-item-button-wrap">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-4 intec-grid-a-h-end">
                            <div class="intec-grid-item-auto intec-grid-item-600-1">
                                <?= Html::tag('div', Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_BUTTON_CLEAR'), [
                                    'class' => [
                                        'widget-item-button',
                                        'widget-item-button-clear',
                                        'intec-cl-background-hover',
                                        'intec-cl-border-hover',
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'mod-round-2',
                                            'size-5',
                                            'mod-transparent'
                                        ]
                                    ],
                                    'data-role' => 'button.clear'
                                ]) ?>
                            </div>
                            <div class="intec-grid-item-auto intec-grid-item-600-1">
                                <?= Html::tag('div', Html::stripTags($arForm['BUTTON']), [
                                    'class' => [
                                        'widget-item-button',
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'mod-round-2',
                                            'size-5',
                                            'scheme-current'
                                        ]
                                    ],
                                    'data-role' => 'button.order'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
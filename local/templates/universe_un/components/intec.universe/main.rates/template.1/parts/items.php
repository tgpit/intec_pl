<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<?php $vItems = function ($items) use (&$arResult, &$arVisual, &$sTemplateId) { ?>
    <?php if (!empty($items)) {

        $iCounter = 0;

        if (empty($arVisual['BUTTON']['TEXT']))
            $arVisual['BUTTON']['TEXT'] = Loc::getMessage('C_MAIN_RATES_TEMPLATE_3_TEMPLATE_ORDER_BUTTON_DEFAULT')

    ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'widget-items' => true,
                'intec-grid' => [
                    '' => !$arVisual['SLIDER']['USE'],
                    'wrap' => !$arVisual['SLIDER']['USE'],
                    'a-v-stretch' => !$arVisual['SLIDER']['USE']
                ],
                'owl-carousel' => $arVisual['SLIDER']['USE']
            ], true),
            'data' => [
                'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false'
            ]
        ]) ?>
            <?php foreach ($items as $arItem) {

                $sId = $sTemplateId.'_'.$arItem['ID'];
                $sAreaId = $this->GetEditAreaId($sId);
                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                $iCounter++;

                $arData = $arItem['DATA'];

                $bButtonShow = false;

                if ($arVisual['BUTTON']['SHOW'])
                    $bButtonShow = $arVisual['BUTTON']['MODE'] === 'detail' && !empty($arData['DETAIL']) ||
                        $arVisual['BUTTON']['MODE'] === 'order' && $arResult['FORM']['SHOW'];

            ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-item' => true,
                        'intec-grid-item' => [
                            $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                            '1200-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 4,
                            '900-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 3,
                            '650-1' => !$arVisual['SLIDER']['USE']
                        ]
                    ], true),
                    'data' => [
                        'button' => $bButtonShow ? 'true' : null
                    ]
                ]) ?>
                    <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                        <div class="widget-item-effect"></div>
                        <div class="widget-item-content">
                            <?php if (
                                $arVisual['COUNTER']['SHOW'] ||
                                $arVisual['DISCOUNT']['SHOW'] && !empty($arData['DISCOUNT']['VALUE'])
                            ) { ?>
                                <div class="widget-item-decoration intec-grid intec-grid-a-v-center">
                                    <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                        <div class="intec-grid-item">
                                            <div class="widget-item-counter">
                                                <?php if (!empty($arVisual['COUNTER']['TEXT'])) { ?>
                                                    <?= $arVisual['COUNTER']['TEXT'].' '.$iCounter ?>
                                                <?php } else { ?>
                                                    <?= $iCounter ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['DISCOUNT']['SHOW'] && !empty($arData['DISCOUNT']['VALUE'])) { ?>
                                        <div class="intec-grid-item widget-item-sticker-wrap">
                                            <div class="widget-item-sticker intec-cl-background">
                                                <?php if ($arData['DISCOUNT']['TYPE'] !== 'value') { ?>
                                                    <?= '- '.$arData['DISCOUNT']['VALUE'].' %' ?>
                                                <?php } else { ?>
                                                    <?= '- '.$arData['DISCOUNT']['VALUE'].' '.$arData['PRICE']['CURRENCY'] ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div class="widget-item-name">
                                <?= $arItem['NAME'] ?>
                            </div>
                            <?php if ($arVisual['PRICE']['SHOW'] && !empty($arData['PRICE']['NEW'])) { ?>
                                <div class="widget-item-price">
                                    <span class="widget-item-price-value intec-cl-text">
                                        <?= number_format($arData['PRICE']['NEW'],
                                            $arData['PRICE']['NEW'] - Type::toInteger($arData['PRICE']['NEW']) > 0 ? 2 : 0,
                                            '.',
                                            '&nbsp'
                                        ) ?>
                                    </span>
                                    <?php if (!empty($arData['PRICE']['CURRENCY'])) { ?>
                                        <span class="widget-item-price-currency">
                                            <?= $arData['PRICE']['CURRENCY'] ?>
                                        </span>
                                    <?php } ?>
                                </div>
                                <?php if (!empty($arData['PRICE']['OLD'])) { ?>
                                    <div class="widget-item-discount">
                                        <div class="widget-item-discount-value">
                                            <?= number_format($arData['PRICE']['OLD'],
                                                $arData['PRICE']['OLD'] - Type::toInteger($arData['PRICE']['OLD']) > 0 ? 2 : 0,
                                                '.',
                                                ' '
                                            ) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($arVisual['PREVIEW']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                                <div class="widget-item-description">
                                    <?= $arItem['PREVIEW_TEXT'] ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['PROPERTIES']['SHOW'] && $arData['PROPERTIES']['SHOW']) { ?>
                                <div class="widget-item-properties">
                                    <?php foreach ($arItem['DISPLAY_PROPERTIES'] as $arProperty) {

                                        if (empty($arProperty['DISPLAY_VALUE']))
                                            continue;

                                        if (Type::isArray($arProperty['DISPLAY_VALUE']))
                                            $arProperty['DISPLAY_VALUE'] = implode(', ', $arProperty['DISPLAY_VALUE']);

                                    ?>
                                        <div class="widget-item-property">
                                            <?= $arProperty['NAME'].': '.$arProperty['DISPLAY_VALUE'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($bButtonShow) { ?>
                                <?php if ($arVisual['BUTTON']['MODE'] === 'detail') { ?>
                                    <?= Html::tag('a', $arVisual['BUTTON']['TEXT'], [
                                        'href' => $arData['DETAIL'],
                                        'class' => [
                                            'widget-item-button',
                                            'intec-cl-background' => [
                                                '',
                                                'light-hover'
                                            ]
                                        ],
                                        'target' => $arVisual['DETAIL']['BLANK'] ? '_blank' : null
                                    ]) ?>
                                <?php } else if ($arVisual['BUTTON']['MODE'] === 'order') { ?>
                                    <?= Html::tag('div', $arVisual['BUTTON']['TEXT'], [
                                        'class' => [
                                            'widget-item-button',
                                            'intec-cl-background' => [
                                                '',
                                                'light-hover'
                                            ]
                                        ],
                                        'data' => [
                                            'role' => 'rate.button',
                                            'value' => $arItem['NAME']
                                        ]
                                    ]) ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>
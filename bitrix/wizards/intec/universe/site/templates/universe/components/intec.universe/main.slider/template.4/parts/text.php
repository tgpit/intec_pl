<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arForm
 * @var array $arItem
 * @var array $arVisual
 */

?>
<?php return function (&$arData, $bHeaderH1 = false, $arForm = []) use (&$arVisual, &$sTemplateId, &$vTextButton) { ?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'widget-item-text' => true,
            'intec-grid-item' => [
                '' => !$arData['TEXT']['HALF'],
                'auto' => $arData['TEXT']['HALF'],
                'shrink-1' => true,
                '768-1' => $arData['TEXT']['HALF'],
                'a-center' => true
            ]
        ], true),
        'data' => [
            'align' => $arData['TEXT']['ALIGN']
        ]
    ]) ?>
        <?php if ($arVisual['HEADER']['OVER']['SHOW'] && !empty($arData['OVER'])) { ?>
            <?= Html::tag('div', $arData['OVER'], [
                'class' => 'widget-item-header-over'
            ]) ?>
        <?php } ?>
        <?php if ($arVisual['HEADER']['SHOW'] && !empty($arData['HEADER'])) { ?>
            <?= Html::tag($bHeaderH1 ? 'h1' : 'div', $arData['HEADER'], [
                'class' => 'widget-item-header'
            ]) ?>
        <?php } ?>
        <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arData['DESCRIPTION'])) { ?>
            <?= Html::tag('div', $arData['DESCRIPTION'], [
                'class' => 'widget-item-description'
            ]) ?>
        <?php } ?>
        <?php if ($arData['BUTTON']['SHOW'] || $arForm['SHOW']) { ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-item-buttons' => true,
                    'intec-grid' => [
                        '' => true,
                        'wrap' => true,
                        'a-h-768-center' => true,
                        'i-8' => true,
                        'a-h-center' => $arData['TEXT']['ALIGN'] === 'center',
                        'a-h-end' => $arData['TEXT']['ALIGN'] === 'right'
                    ]
                ], true)
            ]) ?>
                <?php if ($arData['BUTTON']['SHOW']) {
                    if (empty($arData['BUTTON']['TEXT']))
                        $arData['BUTTON']['TEXT'] = Loc::getMessage('C_MAIN_SLIDER_TEMPLATE_4_BUTTON_TEXT_DEFAULT');
                ?>
                    <div class="intec-grid-item-auto">
                        <?= Html::tag('a', $arData['BUTTON']['TEXT'], [
                            'class' => [
                                'widget-item-button',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ],
                            'href' => $arData['LINK']['VALUE'],
                            'target' => $arData['LINK']['BLANK']
                        ]) ?>
                    </div>
                <?php } ?>

                <?php if ($arForm['SHOW']) { ?>
                    <div class="intec-grid-item-auto">
                        <?= Html::tag('div', $arForm['BUTTON'], [
                            'class' => [
                                'widget-item-button',
                                'intec-cl-background' => [
                                    '',
                                    'light-hover'
                                ]
                            ],
                            'data' => [
                                'role' => 'form',
                                'name' => $arData['NAME']
                            ]
                        ]) ?>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
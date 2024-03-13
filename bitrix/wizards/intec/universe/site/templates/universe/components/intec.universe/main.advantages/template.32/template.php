<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];
$iCounter = 0;

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'widget c-advantages c-advantages-template-32',
    'data' => [
        'theme' => $arVisual['THEME'],
        'number-align' => $arVisual['NUMBER']['ALIGN'],
        'preview-align' => $arVisual['PREVIEW']['ALIGN'],
        'button-align' => $arVisual['BUTTON']['ALIGN'],
        'background-use' => $arVisual['BACKGROUND']['SHOW'] ? 'true' : 'false'
    ],
    'style' => $arVisual['BACKGROUND']['SHOW'] ? 'background: '.$arVisual['BACKGROUND']['COLOR'] : null
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-v-center',
                        'i-h-25',
                        'i-v-10'
                    ]
                ]
            ]) ?>
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <div class="intec-grid-item-3 intec-grid-item-1000-1">
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
                            <?php if ($arVisual['BUTTON']['SHOW']) { ?>
                                <div class="widget-button-wrap">
                                    <?= Html::tag('a', Html::stripTags($arVisual['BUTTON']['TEXT']), [
                                        'href' => $arVisual['BUTTON']['LINK'],
                                        'class' => [
                                            'widget-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'mod-round-2',
                                                'size-2',
                                                'scheme-current'
                                            ]
                                        ]
                                    ]) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="intec-grid-item">
                    <div class="widget-content">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-16 intec-a-v-stretch">
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $arData = $arItem['DATA'];
                                ?>
                                <?= Html::beginTag('div', [
                                    'id' => $sAreaId,
                                    'class' => [
                                        Html::cssClassFromArray([
                                            'widget-item' => true,
                                            'intec-grid-item' => [
                                                '2' => true,
                                                '500-1' => true,
                                                '768-2' => true
                                            ],
                                        ], true)
                                    ]
                                ]) ?>
                                    <div class="widget-item-wrapper">
                                        <div class="widget-item-wrapper-2">
                                            <?php if (!empty($arData['NUMBER']) && $arVisual['NUMBER']['SHOW']) { ?>
                                                <?= Html::tag('div', $arData['NUMBER'], [
                                                    'class' => [
                                                        Html::cssClassFromArray([
                                                            'widget-item-number' => true,
                                                            'intec-cl-text' => $arVisual['THEME'] == 'light',
                                                        ], true)
                                                    ]
                                                ]) ?>
                                            <?php } ?>
                                            <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                                <div class="widget-item-description">
                                                    <?= $arItem['PREVIEW_TEXT'] ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
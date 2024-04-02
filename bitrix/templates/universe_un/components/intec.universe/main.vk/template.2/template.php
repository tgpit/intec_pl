<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\platform\main\Component;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$sItemTag = $arVisual['ITEM']['URL']['USE'] ? 'a' : 'span';

$svg = [
    'VK' => FileHelper::getFileData(__DIR__ . '/svg/vk.svg')
];

?>
<div class="widget c-vk c-vk-template-2 fragment" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
        <?php if ($arVisual['HEADER']['SHOW']) { ?>
            <div class="fragment-header">
                <div class="fragment-title intec-grid intec-grid-a-v-baseline intec-grid-a-v-768-center">
                    <?php if ($arVisual['HEADER']['TITLE']['SHOW']) { ?>
                        <div class="fragment-title-text intec-grid-item">
                            <?= $arVisual['HEADER']['TITLE']['VALUE'] ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['URL']['LIST']['SHOW']) { ?>
                        <?php

                            if (empty($arVisual['URL']['LIST']['TEXT']))
                                $arVisual['URL']['LIST']['TEXT'] = Loc::getMessage('IC_VK_TEMPLATE_2_TEMPLATE_URL_LIST_TEXT_DEFAULT');

                        ?>
                        <div class="fragment-all intec-grid-item-auto intec-grid-item-shrink-none">
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'intec-grid',
                                    'flex--a-v-center',
                                    'intec-cl-svg-path-stroke-hover'
                                ],
                                'href' => $arVisual['URL']['LIST']['VALUE'],
                                'target' => $arVisual['URL']['LIST']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <span class="fragment-all-desktop intec-cl-text-hover flex-item">
                                    <?= $arVisual['URL']['LIST']['TEXT'] ?>
                                </span>
                                <span class="fragment-all-mobile">
                                    <?= FileHelper::getFileData(__DIR__ . '/svg/list.arrow.svg') ?>
                                </span>
                            <?= Html::endTag('a') ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if ($arVisual['HEADER']['DESCRIPTION']['SHOW']) { ?>
                    <div class="fragment-description">
                        <?= $arVisual['HEADER']['DESCRIPTION']['VALUE'] ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="fragment-content">
            <?= Html::beginTag('div', [
                'class' => [
                    'fragment-items',
                    'flex',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-v-stretch',
                        'i-16',
                        '768-nowrap'
                    ]
                ]
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $item) { ?>
                    <?php

                        $picture = ArrayHelper::getFirstValue($item['PICTURES']);

                        if (!$picture)
                            $picture = [
                                'ORIGINAL' => SITE_TEMPLATE_PATH . '/images/picture.missing.png',
                                'THUMB' => SITE_TEMPLATE_PATH . '/images/picture.missing.png'
                            ];

                    ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'fragment-item' => true,
                            'intec-grid-item' => [
                                $arVisual['ITEMS']['COLUMNS'] => true,
                                '1200-4' => $arVisual['ITEMS']['COLUMNS'] > 4,
                                '1024-3' => $arVisual['ITEMS']['COLUMNS'] > 3,
                                '768' => [
                                    'auto' => true,
                                    'shrink-none' => true
                                ]
                            ]
                        ], true)
                    ]) ?>
                        <div class="fragment-item-content intec-grid intec-grid-o-vertical border-sb border-transparent-hover">
                            <?= Html::beginTag($sItemTag, [
                                'class' => Html::cssClassFromArray([
                                    'fragment-item-picture' => true,
                                    'ui' => [
                                        'img-cover' => true,
                                        'ratio' => [
                                            '1x1' => $arVisual['ITEM']['PICTURE']['RATIO'] == '1x1',
                                            '4x3' => $arVisual['ITEM']['PICTURE']['RATIO'] == '4x3',
                                            '16x9' => $arVisual['ITEM']['PICTURE']['RATIO'] == '16x9',
                                            '21x9' => $arVisual['ITEM']['PICTURE']['RATIO'] == '21x9'
                                        ]
                                    ]
                                ], true),
                                'href' => $arVisual['ITEM']['URL']['USE'] ? $item['URL'] : null,
                                'target' => $arVisual['ITEM']['URL']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $picture['THUMB'], [
                                    'decoding' => 'async',
                                    'loading' => $arVisual['LAZYLOAD']['USE'] ? null : 'lazy',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $picture['THUMB'] : null
                                    ]
                                ]) ?>
                            <?= Html::endTag($sItemTag) ?>
                            <?php if (!empty($item['TEXT'])) { ?>
                                <?= Html::tag('div', $item['TEXT'], [
                                    'class' => [
                                        'fragment-item-text',
                                        'flex-item',
                                        'flex-item--grow-1',
                                        'ui-text-height-4',
                                        'ind-m' => [
                                            'h-24',
                                            't-24'
                                        ]
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?= Html::beginTag($sItemTag, [
                                'class' => Html::cssClassFromArray([
                                    'fragment-item-title' => true,
                                    'intec-grid-item-auto' => true,
                                    'intec-grid-item-shrink-none' => true,
                                    'intec-grid' => true,
                                    'intec-grid-a-v-center' => true,
                                    'intec-cl' => [
                                        'svg-path-fill-hover' => $arVisual['ITEM']['URL']['USE'],
                                        'text-hover' => $arVisual['ITEM']['URL']['USE'],
                                        'svg-path-stroke-hover' => $arVisual['ITEM']['URL']['USE']
                                    ]
                                ], true),
                                'href' => $arVisual['ITEM']['URL']['USE'] ? $item['URL'] : null,
                                'target' => $arVisual['ITEM']['URL']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <span class="fragment-item-title-icon intec-grid-item-auto">
                                    <?= $svg['VK'] ?>
                                </span>
                            <span class="fragment-item-title-text intec-grid-item ind-p-l-11">
                                    <span class="fragment-item-title-text ui-text-height-4">
                                        <?= $item['DATE'] ?>
                                    </span>
                                </span>
                                <span class="fragment-item-title-svg-container intec-grid-item-auto intec-grid-item-shrink-none">
                                        <span class="intec-ui-picture">
                                            <?= FileHelper::getFileData(__DIR__ . '/svg/element.arrow.svg') ?>
                                        </span>
                                </span>
                            <?= Html::endTag($sItemTag) ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
</div>


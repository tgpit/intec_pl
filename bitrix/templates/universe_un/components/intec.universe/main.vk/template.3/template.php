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

<div class="widget c-vk c-vk-template-3 fragment" id="<?= $sTemplateId ?>">
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
                                $arVisual['URL']['LIST']['TEXT'] = Loc::getMessage('IC_VK_TEMPLATE_3_TEMPLATE_URL_LIST_TEXT_DEFAULT');

                            ?>
                            <div class="fragment-all intec-grid-item-auto intec-grid-item-shrink-none">
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'intec-grid',
                                        'intec-grid-a-v-center',
                                        'intec-cl-svg-path-stroke-hover'
                                    ],
                                    'href' => $arVisual['URL']['LIST']['VALUE'],
                                    'target' => $arVisual['URL']['LIST']['BLANK'] ? '_blank' : null
                                ]) ?>
                                <span class="fragment-all-desktop intec-cl-text-hover intec-grid-item-auto">
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
                            'intec-grid-item-auto' => true,
                            'intec-grid-item' => [
                                $arVisual['ITEMS']['COLUMNS'] => true,
                                '1024-1' => $arVisual['ITEMS']['COLUMNS'] > 1,
                                '768' => [
                                    'auto' => true,
                                    'shrink-none' => true
                                ]
                            ]
                        ], true)
                    ]) ?>
                    <div class="fragment-item-content intec-grid ">
                        <?= Html::beginTag($sItemTag, [
                            'class' => [
                                'fragment-item-picture',
                                'intec-grid-item-auto',
                                'intec-grid-item' => [
                                    '2',
                                    '768-1',
                                    'shrink-none'
                                ]
                            ],
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
                        <div class="fragment-item-info intec-grid-item-auto intec-grid-item-2 intec-grid-item-768-1 intec-grid-item-shrink-none intec-grid intec-grid-o-vertical">
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
                                            <span class="fragment-item-title-text">
                                                <?= $item['DATE'] ?>
                                            </span>
                                        </span>
                            <span class="fragment-item-title-svg-container intec-grid-item-auto intec-grid-item-shrink-none">
                                                <span class="intec-ui-picture">
                                                    <?= FileHelper::getFileData(__DIR__ . '/svg/element.arrow.svg') ?>
                                                </span>
                                            </span>
                            </span>
                            <?= Html::endTag($sItemTag) ?>
                            <?php if (!empty($item['TEXT'])) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'fragment-item-description',
                                        'intec-grid-item'
                                    ]
                                ]) ?>
                                <?= Html::tag('div', $item['TEXT']) ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>
<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\Core;

/**
  * @var array $arResult
  * @var array $arSort
  * @var array $arVisual
*/

$arSort['PROPERTY'] = Core::$app->request->get('sort');

$arVisual = $arResult['VISUAL'];
?>
<?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
    <?php if ($arSort['PROPERTY'] == $arSection['CODE'] || $arSort['PROPERTY'] == $arSection['ID'] || empty($arSort['PROPERTY'])) { ?>
        <div class="news-menu-item-opener intec-grid-item-1" data-status="close" data-role="mobile.menu.opener">
            <span>
                <?= $arSection['NAME'] ?>
            </span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <?php break; ?>
    <?php } ?>
<?php } ?>
<div class="news-menu-wrapper" data-role='mobile.menu.items.list' data-position="<?= $arVisual['MENU']['POSITION'] ?>">
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'intec-grid' => [
                '' => true,
                'wrap' => $arVisual['MENU']['POSITION'] == 'left',
                '1024-nowrap' => $arVisual['MENU']['POSITION'] == 'left',
                'item-1' => $arVisual['MENU']['POSITION'] == 'top',
                '768-wrap' => true
            ],
            'news-menu' => true,
            'news-menu-left' => $arVisual['MENU']['POSITION'] == 'left',
            'news-menu-top' => $arVisual['MENU']['POSITION'] == 'top',
            'scrollbar-inner' => true
        ], true),
        'data-role' => 'scrollbar'
    ]) ?>
        <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
            <?php
                $bActive = !empty($arSort['PROPERTY']) && ($arSort['PROPERTY'] == $arSection['CODE'] || $arSort['PROPERTY'] == $arSection['ID']);
                $sLink = null;

                if (!$bActive)
                    $sLink = !empty($arSection['CODE']) ? '?sort='.$arSection['CODE'] : '?sort='.$arSection['ID'];
            ?>
            <?= Html::beginTag($bActive ? 'div' : 'a', [
                'href' => $sLink,
                'data' => [
                    'active' => $bActive ? 'true' : 'false',
                    'role' => 'menu.item.link'
                ],
                'class' => Html::cssClassFromArray([
                    'intec-grid' => [
                        'item-1' => $arVisual['MENU']['POSITION'] == 'left',
                        'item-1024-auto' => $arVisual['MENU']['POSITION'] == 'left',
                        'item-auto' => $arVisual['MENU']['POSITION'] == 'top',
                        'item-768-1' => true,
                    ],
                    'news-menu-item-link' => true,
                ], true)
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'news-menu-item' => true,
                        'intec' => [
                            'grid' => [
                                '' => true,
                                'a-v-center' => true
                            ],
                            'cl' => [
                                'background' => $bActive
                            ]
                        ]
                    ], true),
                    'data' => [
                        'active' => $bActive ? 'true' : 'false',
                        'role' => 'menu.item'
                    ]
                ]) ?>
                    <div class="intec-grid-item-auto news-menu-item-name">
                        <?= $arSection['NAME'] ?>
                    </div>
                    <?= Html::tag('div', $arSection['ELEMENT_CNT'], [
                        'class' => Html::cssClassFromArray([
                            'news-menu-item-quantity' => true,
                            'intec' => [
                                'grid-item-auto' => true,
                                'cl-background-light' => $bActive
                            ],
                        ], true),
                        'data' => [
                            'role' => 'menu.item.quantity'
                        ]
                    ]) ?>
                <?= Html::endTag('div') ?>
            <?= Html::endTag($bActive ? 'div' : 'a') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
</div>
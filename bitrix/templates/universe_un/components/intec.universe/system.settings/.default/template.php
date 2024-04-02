<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\net\Url;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));

Loc::loadMessages(__FILE__);

/**
 * @var Closure[] $arViews
 */
include(__DIR__.'/parts/views.php');

$sUrl = new Url(Core::$app->request->getUrl());
$sUrl->getQuery()->removeAt($arResult['VARIABLES']['VARIANT']);
$sUrl = $sUrl->build();

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-intec-universe',
        'c-system-settings',
        'c-system-settings-default'
    ],
    'data' => [
        'changed' => 'false',
        'expanded' => 'false'
    ]
]) ?>
    <!--noindex-->
    <div class="system-settings-overlay" data-role="overlay"></div>
    <div class="system-settings-blind" data-role="blind">
        <div class="system-settings-close" data-role="close">
            <i class="fal fa-times"></i>
        </div>
        <div class="system-settings-panel" data-role="panel">
            <div class="system-settings-panel-items" data-role="panel.items">
                <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'system-settings-panel-item',
                        'data' => [
                            'role' => 'panel.item',
                            'code' => $arSection['code']
                        ]
                    ]) ?>
                        <?php if (!empty($arSection['icon'])) { ?>
                            <div class="system-settings-panel-item-icon">
                                <?= $arSection['icon'] ?>
                            </div>
                        <?php } ?>
                        <div class="system-settings-panel-item-tip">
                            <div class="system-settings-panel-item-tip-triangle"></div>
                            <div class="system-settings-panel-item-tip-content">
                                <div class="system-settings-panel-item-tip-name">
                                    <?= $arSection['name'] ?>
                                </div>
                                <?php if (!empty($arSection['description'])) { ?>
                                    <div class="system-settings-panel-item-tip-description">
                                        <?= $arSection['description'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        </div>
        <div class="system-settings-content" data-role="content">
            <div class="system-settings-menu">
                <div class="system-settings-menu-wrapper scrollbar-inner scroll-mod-hiding" data-role="scrollbar">
                    <div class="system-settings-menu-wrapper-2">
                        <div class="system-settings-tabs" data-role="tabs">
                            <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'system-settings-tab',
                                    'data' => [
                                        'role' => 'tab',
                                        'code' => $arSection['code']
                                    ]
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'system-settings-tab-content',
                                            'intec-grid' => [
                                                '',
                                                'nowrap',
                                                'a-h-start',
                                                'a-v-center',
                                                'i-h-6'
                                            ]
                                        ]
                                    ]) ?>
                                        <?php if (!empty($arSection['icon'])) { ?>
                                            <div class="system-settings-tab-icon intec-grid-item-auto">
                                                <?= $arSection['icon'] ?>
                                            </div>
                                        <?php } ?>
                                            <div class="system-settings-tab-container intec-grid-container">
                                                <div class="system-settings-tab-version intec-grid-version">
                                                    <?php
                                                    include(__DIR__.'/../../../../../../../include/header/solution.php');
                                                    include(__DIR__.'/parts/version/version.php');
                                                    ?>
                                                </div>
                                                        <div class="system-settings-tab-name intec-grid-item">
                                                            <?= $arSection['name'] ?>
                                                        </div>
                                            </div>

                                    <?= Html::endTag('div') ?>
                                    <?php if (!empty($arSection['categories'])) { ?>
                                        <div class="system-settings-tab-categories" data-role="tab.categories">
                                            <?php foreach ($arSection['categories'] as $arCategory) { ?>
                                                <?= Html::beginTag('div', [
                                                    'class' => 'system-settings-tab-category',
                                                    'data' => [
                                                        'role' => 'tab.category',
                                                        'code' => $arSection['code'].'.'.$arCategory['code']
                                                    ]
                                                ]) ?>
                                                    <?= $arCategory['name'] ?>
                                                <?= Html::endTag('div') ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                        <div class="system-settings-banners">
                            <?php foreach ($arResult['BANNERS'] as $arBanner) { ?>
                                <?php if (
                                    empty($arBanner['content']) ||
                                    empty($arBanner['content']['type']) ||
                                    empty($arBanner['content']['value'])
                                ) continue ?>
                                <div class="system-settings-banner">
                                    <?php
                                        if ($arBanner['content']['type'] === 'file') {
                                            include($arBanner['content']['value']);
                                        } else {
                                            echo $arBanner['content']['value'];
                                        }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="system-settings-containers-wrap">
                <div class="system-settings-containers-wrap-2">
                    <div class="system-settings-containers" data-role="containers">
                        <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                        <?php
                            $arCategories = [null];

                            if (!empty($arSection['categories']))
                                $arCategories = $arSection['categories'];
                        ?>
                            <?php if ($arSection['form']) { ?>
                                <?= Html::beginForm(null, 'post', [
                                    'class' => 'system-settings-containers-group',
                                    'data' => [
                                        'role' => 'containers.group',
                                        'code' => $arSection['code']
                                    ]
                                ]) ?>
                                    <input type="hidden" name="section" value="<?= $arResult['SECTION'] ?>" />
                            <?php } else { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'system-settings-containers-group',
                                    'data' => [
                                        'role' => 'containers.group',
                                        'code' => $arSection['code']
                                    ]
                                ]) ?>
                            <?php } ?>
                                <?php
                                    if (
                                        !empty($arSection['content']) &&
                                        !empty($arSection['content']['start']) &&
                                        !empty($arSection['content']['start']['type']) &&
                                        !empty($arSection['content']['start']['value'])
                                    ) {
                                        if ($arSection['content']['start']['type'] === 'file') {
                                            include($arSection['content']['start']['value']);
                                        } else {
                                            echo $arSection['content']['start']['value'];
                                        }
                                    }
                                ?>
                                <?php foreach ($arCategories as $arCategory) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'system-settings-container',
                                        'data' => [
                                            'role' => 'container',
                                            'code' => $arSection['code'].(!empty($arCategory) ? '.'.$arCategory['code'] : null)
                                        ]
                                    ]) ?>
                                        <div class="system-settings-container-wrapper scrollbar-inner scroll-mod-hiding" data-role="scrollbar">
                                            <div class="system-settings-container-wrapper-2">
                                                <?php if ($arSection['code'] === 'variants') { ?>
                                                    <div class="system-settings-container-title">
                                                        <?= Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_VARIANTS_TITLE') ?>
                                                    </div>
                                                    <div class="system-settings-container-content">
                                                        <div class="system-settings-variants intec-grid intec-grid-wrap intec-grid-i-h-2 intec-grid-i-v-15 intec-grid-a-h-start intec-grid-a-v-start" data-role="variants">
                                                            <?php foreach ($arResult['VARIANTS'] as $arVariant) { ?>
                                                            <?php
                                                                $sPicture = $arVariant['picture'];

                                                                if (empty($sPicture))
                                                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                                            ?>
                                                                <?= Html::beginTag('a', [
                                                                    'href' => Html::encode($arVariant['link']),
                                                                    'class' => [
                                                                        'system-settings-variant',
                                                                        'intec-grid-item-2'
                                                                    ],
                                                                    'data' => [
                                                                        'role' => 'variant',
                                                                        'code' => $arVariant['code'],
                                                                        'name' => $arVariant['name']
                                                                    ]
                                                                ]) ?>
                                                                    <div class="system-settings-variant-wrapper">
                                                                        <div class="system-settings-variant-preview">
                                                                            <?= Html::tag('div', null, [
                                                                                'class' => 'system-settings-variant-preview-picture',
                                                                                'data' => [
                                                                                    'lazyload-use' => $arResult['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                                                    'original' => $arResult['LAZYLOAD']['USE'] ? $sPicture : null
                                                                                ],
                                                                                'style' => [
                                                                                    'background-image' => !$arResult['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                                                                ]
                                                                            ]) ?>
                                                                            <?= Html::tag('div', Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_VARIANTS_VARIANT_SELECT'), [
                                                                                'class' => [
                                                                                    'system-settings-variant-preview-button',
                                                                                    'intec-ui' => [
                                                                                        '',
                                                                                        'control-button',
                                                                                        'mod-round-2'
                                                                                    ]
                                                                                ]
                                                                            ]) ?>
                                                                        </div>
                                                                        <div class="system-settings-variant-name">
                                                                            <?= Html::encode($arVariant['name']) ?>
                                                                        </div>
                                                                    </div>
                                                                <?= Html::endTag('a') ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } else if ($arSection['code'] === 'properties') { ?>
                                                    <?php if ($arCategory['code'] !== 'templates') { ?>
                                                        <div class="system-settings-properties intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-h-10 intec-grid-i-v-20" data-role="properties">
                                                            <?php foreach ($arCategory['properties'] as $sPropertyKey => $arProperty) { ?>
                                                            <?php
                                                                $sName = ArrayHelper::getValue($arProperty, 'name');
                                                                $sType = ArrayHelper::getValue($arProperty, 'type');
                                                                $sView = ArrayHelper::getValue($arProperty, 'view');

                                                                if (empty($sType))
                                                                    continue;

                                                                if (empty($sView))
                                                                    $sView = $sType;

                                                                $fView = ArrayHelper::getValue($arViews, $sView);

                                                                if (empty($fView))
                                                                    continue;

                                                                $arProperty = ArrayHelper::merge([
                                                                    'title' => true,
                                                                    'grid' => [
                                                                        'size' => 1
                                                                    ]
                                                                ], $arProperty);

                                                            ?>
                                                                <?= Html::beginTag('div', [
                                                                    'class' => [
                                                                        'system-settings-property',
                                                                        'intec-grid-item'.(!empty($arProperty['grid']['size']) ? '-'.$arProperty['grid']['size'] : null)
                                                                    ],
                                                                    'data' => [
                                                                        'role' => 'property',
                                                                        'code' => $sPropertyKey,
                                                                        'type' => $sType,
                                                                        'view' => $sView
                                                                    ]
                                                                ]) ?>
                                                                <?php if ($arProperty['title'] === true) { ?>
                                                                    <div class="system-settings-property-name">
                                                                        <?= $sName ?>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="system-settings-property-content">
                                                                    <?php $fView($sPropertyKey, $arProperty) ?>
                                                                </div>
                                                                <?= Html::endTag('div') ?>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <?php include(__DIR__.'/parts/templates.php') ?>
                                                    <?php } ?>
                                                <?php } else {
                                                    if (!empty($arCategory)) {
                                                        if (
                                                            !empty($arCategory['content']) &&
                                                            !empty($arCategory['content']['type']) &&
                                                            !empty($arCategory['content']['value'])
                                                        ) {
                                                            if ($arCategory['content']['type'] === 'file') {
                                                                include($arCategory['content']['value']);
                                                            } else {
                                                                echo $arCategory['content']['value'];
                                                            }
                                                        }
                                                    } else {
                                                        if (
                                                            !empty($arSection['content']) &&
                                                            !empty($arSection['content']['page']) &&
                                                            !empty($arSection['content']['page']['type']) &&
                                                            !empty($arSection['content']['page']['value'])
                                                        ) {
                                                            if ($arSection['content']['page']['type'] === 'file') {
                                                                include($arSection['content']['page']['value']);
                                                            } else {
                                                                echo $arSection['content']['page']['value'];
                                                            }
                                                        }
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                                <?php if ($arSection['code'] === 'properties') { ?>
                                    <div class="system-settings-buttons system-settings-buttons-right">
                                        <div class="system-settings-button-wrap">
                                            <?= Html::beginTag('button', [
                                                'class' => [
                                                    'system-settings-button',
                                                    'intec-ui' => [
                                                        '',
                                                        'control-button',
                                                        'mod-round-2'
                                                    ]
                                                ],
                                                'type' => 'submit',
                                                'name' => $arResult['VARIABLES']['ACTION'],
                                                'value' => 'apply',
                                                'data' => [
                                                    'role' => 'button',
                                                    'action' => 'apply',
                                                    'state' => 'none'
                                                ]
                                            ]) ?>
                                                <span class="system-settings-part-effect system-settings-part-effect-bounce">
                                                    <span class="system-settings-part-effect-wrapper">
                                                        <i></i><i></i><i></i>
                                                    </span>
                                                </span>
                                                <div class="system-settings-part-content intec-ui-part-icon">
                                                    <i class="fal fa-check"></i>
                                                </div>
                                                <div class="system-settings-part-content intec-ui-part-content">
                                                    <?= Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_BUTTONS_APPLY') ?>
                                                </div>
                                            <?= Html::endTag('button') ?>
                                        </div>
                                    </div>
                                    <div class="system-settings-buttons system-settings-buttons-bottom intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-a-h-end intec-grid-i-h-8" data-role="buttons">
                                        <div class="system-settings-button-wrap intec-grid-item-auto">
                                            <?= Html::beginTag('button', [
                                                'class' => [
                                                    'system-settings-button',
                                                    'intec-ui' => [
                                                        '',
                                                        'control-button',
                                                        'mod-round-2'
                                                    ]
                                                ],
                                                'type' => 'submit',
                                                'name' => $arResult['VARIABLES']['ACTION'],
                                                'value' => 'reset',
                                                'data' => [
                                                    'role' => 'button',
                                                    'action' => 'reset'
                                                ]
                                            ]) ?>
                                                <div class="intec-ui-part-icon">
                                                    <i class="far fa-sync"></i>
                                                </div>
                                                <div class="intec-ui-part-content">
                                                    <?= Loc::getMessage('C_SYSTEM_SETTINGS_DEFAULT_BUTTONS_RESET') ?>
                                                </div>
                                            <?= Html::endTag('button') ?>
                                        </div>
                                    </div>
                                <?php } else {
                                    if (
                                        !empty($arSection['content']) &&
                                        !empty($arSection['content']['end']) &&
                                        !empty($arSection['content']['end']['type']) &&
                                        !empty($arSection['content']['end']['value'])
                                    ) {
                                        if ($arSection['content']['end']['type'] === 'file') {
                                            include($arSection['content']['end']['value']);
                                        } else {
                                            echo $arSection['content']['end']['value'];
                                        }
                                    }
                                } ?>
                            <?php if ($arSection['form']) { ?>
                                <?= Html::endForm() ?>
                            <?php } else { ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
    <!--/noindex-->
<?= Html::endTag('div') ?>

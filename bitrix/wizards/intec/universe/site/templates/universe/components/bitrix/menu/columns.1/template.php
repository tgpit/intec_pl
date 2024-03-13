<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult))
    return;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-menu',
        'c-menu-columns-1'
    ]
]) ?>
    <?= Html::beginTag('div', [
        'class' => [
            'menu-columns',
            'intec-grid' => [
                '',
                'wrap',
                'a-h-start',
                'a-v-start',
                'i-h-10',
                'i-v-20',
            ]
        ],
        'data-role' => 'menu'
    ]) ?>
        <?php foreach ($arResult as $arItem) { ?>
        <?php
            $bActive = $arItem['ACTIVE'];
            $bSelected = $arItem['SELECTED'];

            $sUrl = $bActive ? null : $arItem['LINK'];
            $sTag = $bActive ? 'span' : 'a';
        ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'menu-column',
                    'intec-grid-item',
                    'intec-grid-item-550-1'
                ],
                'data' => [
                    'active' => $bActive ? 'true' : 'false',
                    'selected' => $bSelected ? 'true' : 'false',
                    'expand' => 'false',
                    'role' => 'menu.item'
                ]
            ]) ?>
                <div class="menu-column-wrapper">
                    <div class="menu-column-header intec-cl-text" data-role="menu.item.header">
                        <?= Html::tag($sTag, $arItem['TEXT'], [
                            'class' => 'menu-column-header-link',
                            'href' => $sUrl
                        ]) ?>
                        <?php if (!empty($arItem['ITEMS'])) { ?>
                            <span class="menu-column-header-icon" data-role="menu.item.icon">
                                <i class="far fa-angle-down"></i>
                            </span>
                        <?php } ?>
                    </div>
                    <?php if (!empty($arItem['ITEMS'])) { ?>
                        <div class="menu-column-items-wrapper" data-role="submenu">
                            <div class="menu-column-items">
                                <?php foreach ($arItem['ITEMS'] as $arChild) { ?>
                                <?php
                                    $bChildActive = $arChild['ACTIVE'];
                                    $bChildSelected = $arChild['SELECTED'];

                                    $sChildUrl = $bChildActive ? null : $arChild['LINK'];
                                    $sChildTag = $bChildActive ? 'span' : 'a';
                                ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'menu-column-item',
                                        'data' => [
                                            'role' => 'submenu.item',
                                            'active' => $bChildActive ? 'true' : 'false',
                                            'selected' => $bChildSelected ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                        <?= Html::tag($sChildTag, $arChild['TEXT'], [
                                            'class' => Html::cssClassFromArray([
                                                'menu-column-item-link' => true,
                                                'intec-cl-text' => $bChildSelected,
                                                'intec-cl-text-hover' => true
                                            ], true),
                                            'href' => $sChildUrl
                                        ]) ?>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
             <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>

    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var root = data.nodes;
            var selectors = {
                'menu': '[data-role="menu"]',
                'menuItem': '[data-role="menu.item"]',
                'menuItemHeader': '[data-role="menu.item.header"]',
                'menuItemIcon': '[data-role="menu.item.icon"]',
                'submenu': '[data-role="submenu"]',
                'submenuItem': '[data-role="submenu.item"]'
            };

            var classes = {
                'adapted': 'menu-adapted',
                'active': 'menu-column-header-active'
            };
            
            var menu = $(selectors.menu, root);
            var submenu = $(selectors.submenu, menu);
            var submenuItems = $(selectors.submenuItem, menu);
            var menuItemIcon = menu.find(selectors.menuItemIcon);

            menuItemIcon.on('click', function () {
                if ($(window).width() < 550) {
                    var self = $(this).parents(selectors.menuItem);
                    var expand = self.attr('data-expand');
                    var submenu = self.find(selectors.submenu);

                    if (expand === 'true') {
                        submenu.hide(300);
                        $(this).removeClass(classes.active);
                        self.attr('data-expand', 'false');
                    } else {
                        submenu.show(300);
                        $(this).addClass(classes.active);
                        self.attr('data-expand', 'true');
                    }
                }
            });

            function menuUpdate () {
                if ($(window).width() < 550) {
                    root.addClass(classes.adapted);
                    submenu.hide();
                    menuItemIcon.removeClass(classes.active);
                    submenuItems.each(function () {
                        if ($(this)[0].dataset.selected === 'true') {
                            var parentMenuItem = $(this).parents(selectors.menuItem);
                            parentMenuItem.attr('data-expand', 'true');
                            parentMenuItem.find(selectors.submenu).show();
                            parentMenuItem.find(selectors.menuItemIcon).addClass(classes.active);
                        }
                    });
                } else {
                    root.removeClass(classes.adapted);
                    submenu.show();
                }
            }

            $(window).on('resize', function () {
                menuUpdate();
            });

            menuUpdate();
        }, {
            'name': '[Component] bitrix:menu (columns.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>

<?= Html::endTag('div') ?>
<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

/** @var InnerTemplate[] $arTemplates */
$arTemplates = $arResult['TEMPLATES'];
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arData = [
    'id' => $sTemplateId,
    'folder' => $templateFolder
];
$sSiteUrl = Core::$app->request->getHostInfo().SITE_DIR;

$arVisual = $arResult['VISUAL'];

$this->setFrameMode(true);

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => Html::cssClassFromArray([
        'vcard' => true,
        'widget' => [
            '' => true,
            'transparent' => $arVisual['TRANSPARENCY']
        ],
        'c-header' => [
            '' => true,
            'template-1' => true
        ]
    ], true),
    'data' => [
        'transparent' => $arVisual['TRANSPARENCY'] ? 'true' : 'false'
    ]
]) ?>
    <div class="widget-content">
        <div style="display: none;">
            <span class="url">
                <span class="value-title" title="<?= $sSiteUrl ?>"></span>
            </span>
            <span class="fn org">
                <?= $arResult['COMPANY_NAME'] ?>
            </span>
            <img class="photo" src="<?= $sSiteUrl.'include/logotype.png' ?>" alt="<?= $arResult['COMPANY_NAME'] ?>" />
        </div>
        <?php if (!empty($arTemplates['DESKTOP'])) { ?>
            <div class="widget-view widget-view-desktop">
                <?php $arData['type'] = 'DESKTOP' ?>
                <?php $arData['selector'] = '.widget-view.widget-view-desktop' ?>
                <?= $arTemplates['DESKTOP']->render(
                    $arParams,
                    $arResult,
                    $arData
                ) ?>
            </div>
            <div class="widget-overlay" data-role="overlay-desktop"></div>
        <?php } ?>
        <?php if (!defined('EDITOR') && !empty($arTemplates['FIXED'])) { ?>
            <div class="widget-view widget-view-fixed" data-role="top-menu">
                <?php $arData['type'] = 'FIXED' ?>
                <?php $arData['selector'] = '.widget-view.widget-view-fixed' ?>
                <?= $arTemplates['FIXED']->render(
                    $arParams,
                    $arResult,
                    $arData
                ) ?>
                <div class="widget-overlay" data-role="overlay-fixed"></div>
            </div>
        <?php } ?>
        <?php if (!defined('EDITOR') && !empty($arTemplates['MOBILE'])) { ?>
            <?php if ($arResult['REGIONALITY']['USE']) { ?>
                <div data-role="header-mobile-region-select"></div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-view' => true,
                    'widget-view-mobile' => true
                ], true)
            ]) ?>
                <?php $arData['type'] = 'MOBILE' ?>
                <?php $arData['selector'] = '.widget-view.widget-view-mobile' ?>
                <?= $arTemplates['MOBILE']->render(
                    $arParams,
                    $arResult,
                    $arData
                ) ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php if (!defined('EDITOR') && (!empty($arTemplates['FIXED']) || !empty($arTemplates['MOBILE']))) { ?>
            <script type="text/javascript">
                template.load(function (data) {
                    var _ = this.getLibrary('_');
                    var $ = this.getLibrary('$');

                    var root = data.nodes;
                    var area = $(window);
                    var parts = {
                        'desktop': {
                            'node': $('.widget-view.widget-view-desktop', root)
                        }
                    };

                    <?php if (!empty($arTemplates['FIXED'])) { ?>
                        parts.fixed = {
                            'isDisplay': false,
                            'node': undefined
                        };

                        parts.fixed.handle = function () {
                            var self = parts.fixed;
                            var bound = 0;

                            if (parts.desktop.node.is(':visible')) {
                                bound += parts.desktop.node.height();
                                bound += parts.desktop.node.offset().top;
                            }

                            if (area.scrollTop() > bound) {
                                self.show();
                            } else {
                                self.hide();
                            }
                        };

                        parts.fixed.handleThrottle = _.throttle(parts.fixed.handle, 250, {
                            'leading': false
                        });

                        parts.fixed.initialize = function () {
                            var self = parts.fixed;

                            self.node = $('.widget-view.widget-view-fixed', root);
                            self.node.css({
                                'display': 'none',
                                'top': -self.node.height()
                            });

                            self.handle();
                        };

                        parts.fixed.hide = function () {
                            var self = parts.fixed;

                            if (!self.isDisplay)
                                return;

                            self.isDisplay = false;
                            self.node.stop().animate({
                                'top': -self.node.height()
                            }, 500, function () {
                                self.node.css({
                                    'display': 'none'
                                });
                            });
                        };

                        parts.fixed.show = function () {
                            var self = parts.fixed;

                            if (self.isDisplay)
                                return;

                            self.isDisplay = true;
                            self.node.css({
                                'display': 'block'
                            }).stop().animate({
                                'top': 0
                            }, 500);
                        };
                    <?php } ?>

                    <?php if (!empty($arTemplates['MOBILE']) && $arResult['MOBILE']['FIXED']) { ?>
                        parts.mobile = {
                            'isDisplay': true,
                            'isFixed': false,
                            'scroll': 0,
                            'stub': undefined
                        };

                        parts.mobile.fix = function () {
                            var self = parts.mobile;

                            if (!self.isFixed) {
                                self.isFixed = true;

                                self.stub = $('<div></div>');
                                self.stub.css({
                                    'height': self.node.height()
                                });

                                self.node.after(self.stub);
                                self.node.addClass('widget-view-mobile-fixed');
                                self.hide(false);
                            }
                        };

                        parts.mobile.handle = function (event) {
                            var self = parts.mobile;
                            var bound = 0;
                            var scroll = area.scrollTop();

                            if (self.node.is(':visible')) {
                                if (self.stub !== undefined) {
                                    bound += self.stub.offset().top;
                                } else {
                                    bound += self.node.offset().top;
                                }

                                if (scroll > bound) {
                                    self.fix();
                                    self.refresh();

                                    <?php if ($arResult['MOBILE']['HIDDEN']) { ?>
                                        if (event && event.type === 'scroll') {
                                            if (scroll > self.scroll) {
                                                if (scroll > (bound + self.node.height())) {
                                                    self.hide(true);
                                                } else {
                                                    self.show(true);
                                                }
                                            } else {
                                                self.show(true);
                                            }
                                        } else {
                                            self.show(true);
                                        }

                                        self.scroll = scroll;
                                    <?php } else { ?>
                                        self.show(true);
                                    <?php } ?>
                                } else {
                                    self.unfix();
                                }
                            } else {
                                self.unfix();
                            }
                        };

                        parts.mobile.handleThrottle = _.throttle(parts.mobile.handle, 100, {
                            'leading': false
                        });

                        parts.mobile.hide = function (animate) {
                            var self = parts.mobile;

                            if (!self.isDisplay)
                                return;

                            self.isDisplay = false;

                            if (animate) {
                                self.node.stop().animate({
                                    'top': -self.node.height()
                                }, {
                                    'duration': 250,
                                    'complete': function () {
                                        self.node.css({
                                            'top': -self.node.height()
                                        });
                                    }
                                });
                            } else {
                                self.node.stop().css('top', -self.node.height());
                            }
                        };

                        parts.mobile.initialize = function () {
                            var self = parts.mobile;

                            self.node = $('.widget-view.widget-view-mobile', root);
                            self.scroll = area.scrollTop();
                            self.handle();
                        };

                        parts.mobile.refresh = function () {
                            var self = parts.mobile;

                            if (self.stub !== undefined) {
                                self.stub.css({
                                    'height': self.node.height()
                                });
                            }
                        };

                        parts.mobile.show = function (animate) {
                            var self = parts.mobile;

                            if (self.isDisplay)
                                return;

                            self.isDisplay = true;

                            if (animate) {
                                self.node.stop().animate({
                                    'top': 0
                                }, {
                                    'duration': 250,
                                    'complete': function () {
                                        self.node.css({
                                            'top': ''
                                        });
                                    }
                                });
                            } else {
                                self.node.stop().css('top', '');
                            }
                        };

                        parts.mobile.unfix = function () {
                            var self = parts.mobile;

                            if (self.isFixed) {
                                self.isFixed = false;

                                self.stub.remove();
                                self.stub = undefined;

                                self.node.removeClass('widget-view-mobile-fixed');
                                self.show(false);
                            }
                        };
                    <?php } ?>

                    <?php if (!empty($arTemplates['FIXED']) && !empty($arTemplates['MOBILE']) && $arResult['MOBILE']['FIXED']) { ?>
                        parts.fixed.initialize();
                        parts.mobile.initialize();

                        area.on('resize scroll', function (event) {
                            parts.fixed.handleThrottle(event);
                            parts.mobile.handleThrottle(event);
                        });
                    <?php } else if (!empty($arTemplates['FIXED'])) { ?>
                        parts.fixed.initialize();

                        area.on('resize scroll', parts.fixed.handleThrottle);
                    <?php } else if (!empty($arTemplates['MOBILE']) && $arResult['MOBILE']['FIXED']) { ?>
                        parts.mobile.initialize();

                        area.on('resize scroll', parts.mobile.handleThrottle);
                    <?php } ?>
                }, {
                    'name': '[Component] intec.universe:main.header (template.1)',
                    'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
                });
            </script>
        <?php } ?>
        <?php if (!empty($arTemplates['BANNER'])) { ?>
            <div class="widget-banner">
                <?php $arData['type'] = 'BANNER' ?>
                <?= $arTemplates['BANNER']->render(
                    $arParams,
                    $arResult,
                    $arData
                ) ?>
            </div>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>
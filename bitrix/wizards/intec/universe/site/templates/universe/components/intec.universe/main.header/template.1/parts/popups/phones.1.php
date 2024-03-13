<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<?= Html::beginTag('div', [
    'class' => Html::cssClassFromArray([
        'widget-popups-phones-1-button' => true,
        'intec-grid' => [
            '' => true,
            'a-v-center' => true
        ],
        'intec-cl-svg-path-stroke-hover' => !$arResult['MOBILE']['FILLED']
    ], true),
    'data' => [
        'role' => 'popups.phones1.button'
    ]
]) ?>
    <svg height="21" width="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M9.21292 11.787C7.89667 10.4707 6.90442 8.99698 6.24517 7.50185C6.10567 7.18573 6.18779 6.8156 6.43192 6.57148L7.3533 5.65123C8.10817 4.89635 8.10817 3.82873 7.44892 3.16948L6.12817 1.84873C5.24955 0.970102 3.82529 0.970102 2.94667 1.84873L2.21317 2.58223C1.37954 3.41585 1.03192 4.61848 1.25692 5.81098C1.81267 8.7506 3.52042 11.9692 6.27554 14.7244C9.03067 17.4795 12.2493 19.1872 15.1889 19.743C16.3814 19.968 17.584 19.6204 18.4177 18.7867L19.15 18.0544C20.0287 17.1757 20.0287 15.7515 19.15 14.8729L17.8304 13.5532C17.1712 12.894 16.1024 12.894 15.4443 13.5532L14.4284 14.5702C14.1843 14.8144 13.8142 14.8965 13.498 14.757C12.0029 14.0966 10.5292 13.1032 9.21292 11.787V11.787Z" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
<?= Html::endTag('div') ?>
<?= Html::beginTag('div', [
    'class' => 'widget-popups-phones-1',
    'data' => [
        'role' => 'popups.phones1.popup',
        'template' => $arResult['CONTACTS_MOBILE_FORM']['TEMPLATE']
    ]
]) ?>
    <div class="widget-popups-phones-1-overlay" data-type="overlay"></div>
    <div class="widget-popups-phones-1-window" data-type="window">
        <div class="widget-popups-phones-1-window-header">
            <div class="widget-popups-phones-1-window-title">
                <?= Loc::getMessage('C_HEADER_TEMP1_POPUPS_PHONES_1_TITLE') ?>
            </div>
            <div class="widget-popups-phones-1-window-close" data-type="button" data-action="close">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L9 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 1L1 9" fill="none" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="widget-popups-phones-1-window-content">
            <?php if ($arResult['CONTACTS_MOBILE_FORM']['TEMPLATE'] === 'template.1') { ?>
                <div class="widget-popups-phones-1-items">
                    <?php foreach ($arResult['CONTACTS']['ALL'] as $arKey => $arValue) { ?>
                    <?php
                        $sPicture = $arValue['ICON'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 16,
                                'height' => 16
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = $arData['folder'].'/images/phone.png';
                    ?>
                        <div class="widget-popups-phones-1-item">
                            <div class="widget-popups-phones-1-item-wrapper">
                                <div class="widget-popups-phones-1-item-border"></div>
                                <div class="widget-popups-phones-1-item-icon intec-ui-pucture">
                                    <img src="<?= $sPicture ?>">
                                </div>
                                <div class="widget-popups-phones-1-item-information">
                                    <?= Html::tag('a', $arValue['PHONE']['DISPLAY'], [
                                        'class' => 'widget-popups-phones-1-item-number',
                                        'href' => 'tel:'.$arValue['PHONE']['VALUE']
                                    ]) ?>
                                    <div class="widget-popups-phones-1-item-name">
                                        <?= $arValue['NAME'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="widget-popups-phones-1-control">
                    <button class="intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-mod-round-2 intec-ui-scheme-current" type="button" data-action="forms.call.open">
                        <?= Loc::getMessage('C_HEADER_TEMP1_POPUPS_PHONES_1_BUTTON') ?>
                    </button>
                </div>
                <div class="widget-popups-phones-1-text">
                    <div class="widget-popups-phones-1-text-content">
                        <?= Loc::getMessage('C_HEADER_TEMP1_POPUPS_PHONES_1_TEXT') ?>
                    </div>
                    <div class="widget-popups-phones-1-text-border"></div>
                </div>
                <div class="widget-popups-phones-1-items">
                    <?php foreach ($arResult['CONTACTS']['ALL'] as $arKey => $arValue) { ?>
                        <div class="widget-popups-phones-1-item">
                            <div class="widget-popups-phones-1-item-wrapper">
                                <?= Html::tag('a', $arValue['PHONE']['DISPLAY'], [
                                    'class' => 'widget-popups-phones-1-item-number',
                                    'href' => 'tel:'.$arValue['PHONE']['VALUE']
                                ]) ?>
                                <div class="widget-popups-phones-1-item-name">
                                    <?= $arValue['NAME'] ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');

        var elements = {};
        var popup;

        elements.root = data.nodes;
        elements.button = $('[data-role="popups.phones1.button"]', elements.root);
        elements.popup = $('[data-role="popups.phones1.popup"]', elements.root);

        popup = elements.popup.uiControl('popup', {
            'animation': {
                'duration': 350,
                'handlers': {
                    'init': null,
                    'open': function () {
                        var self = this;

                        return new Promise(function (resolve) {
                            self.nodes.overlay.stop().css({
                                'opacity': 0
                            }).animate({
                                'opacity': 1
                            }, {
                                'duration': self.options.animation.duration
                            });

                            self.nodes.window.stop().css({
                                'margin-bottom': -self.nodes.window.outerHeight()
                            }).animate({
                                'margin-bottom': 0
                            }, {
                                'duration': self.options.animation.duration,
                                'always': function () {
                                    resolve()
                                }
                            });
                        });
                    },
                    'close': function () {
                        var self = this;

                        return new Promise(function (resolve) {
                            self.nodes.overlay.stop().css({
                                'opacity': 1
                            }).animate({
                                'opacity': 0
                            }, {
                                'duration': self.options.animation.duration
                            });

                            self.nodes.window.stop().css({
                                'margin-bottom': 0
                            }).animate({
                                'margin-bottom': -self.nodes.window.outerHeight()
                            }, {
                                'duration': self.options.animation.duration,
                                'always': function () {
                                    resolve()
                                }
                            });
                        });
                    }
                }
            }
        })[0];

        popup.on('beforeOpen', function () {
            app.api.emit('popup.beforeOpen', popup);
        }).on('open', function () {
            app.api.emit('popup.open', popup);
        }).on('close', function () {
            app.api.emit('popup.close', popup);
        });

        elements.button.on('click', function () {
            popup.open();
        });
    }, {
        'name': '[Component] intec.universe:main.header (template.1) > popups-phones-1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>

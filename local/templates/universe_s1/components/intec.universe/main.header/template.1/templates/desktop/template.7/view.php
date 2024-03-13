<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */

$sTemplateId = $arData['id'];
$sTemplateType = $arData['type'];

$bBasketShow =
    $arResult['BASKET']['SHOW']['DESKTOP'] ||
    $arResult['DELAY']['SHOW']['DESKTOP'] ||
    $arResult['COMPARE']['SHOW']['DESKTOP'];

?>
<div class="widget-view-desktop-7">
    <?php if ($arResult['CONTACTS']['ADVANCED']) { ?>
        <?php $sScheduleString = ''; ?>
        <?php foreach ($arResult['CONTACTS']['VALUES'] as $arContact) {
            if (!empty($arContact['SCHEDULE'])) {
                if (Type::isArray($arContact['SCHEDULE'])) {
                    foreach ($arContact['SCHEDULE'] as $sSchedule) {
                        $sScheduleString .= $sSchedule.', ';
                    }
                } else {
                    $sScheduleString .= $arContact['SCHEDULE'].', ';
                }
            }
        }
        $sScheduleString = substr($sScheduleString, 0, (strlen($sScheduleString) - 2));
        ?>
        <span class="workhours">
            <span class="value-title" title="<?=$sScheduleString?>"></span>
        </span>
    <?php
        unset($sScheduleString);
    } ?>
    <?php //$APPLICATION->ShowViewContent('template-header-desktop-before') ?>
    <div class="intec-content intec-content-visible intec-content-primary">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-wrapper',
                    'intec-grid' => [
                        '',
                        'nowrap',
                        'a-v-center',
                        'i-h-15'
                    ]
                ]
            ]) ?>
                <?php if ($arResult['LOGOTYPE']['SHOW']['DESKTOP']) { ?>
                    <div class="widget-logotype-wrap intec-grid-item-auto">
                        <?= Html::beginTag($arResult['LOGOTYPE']['LINK']['USE'] ? 'a' : 'div', [
                            'href' => $arResult['LOGOTYPE']['LINK']['USE'] ? $arResult['LOGOTYPE']['LINK']['VALUE'] : null,
                            'class' => [
                                'widget-item',
                                'widget-logotype',
                                'intec-ui-picture'
                            ],
                            'style' => [
                                'width' => $arResult['LOGOTYPE']['DESKTOP']['WIDTH'].'px'
                            ]
                        ]) ?>
                            <?php include(__DIR__.'/../../../parts/logotype.php') ?>
                        <?= Html::endTag($arResult['LOGOTYPE']['LINK']['USE'] ? 'a' : 'div') ?>
                    </div>
                <?php } ?>
                <?php if ($arResult['MENU']['MAIN']['SHOW']['DESKTOP']) { ?>
                    <div class="widget-menu-wrap intec-grid-item intec-grid-item-a-stretch intec-grid-item-shrink-1">
                        <div class="widget-item widget-menu">
                            <?php $arMenuParams = ['TRANSPARENT' => 'Y'] ?>
                            <?php include(__DIR__.'/../../../parts/menu/main.horizontal.1.php') ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="intec-grid-item"></div>
                <?php } ?>
                <?php if ($arResult['REGIONALITY']['USE']) { ?>
                    <div class="widget-region-wrap intec-grid-item-auto">
                        <!--noindex-->
                        <div class="widget-item widget-region">
                            <div class="widget-region-icon intec-grid-item-auto intec-cl-svg-path-stroke">
                                <?= FileHelper::getFileData(__DIR__.'/../../../svg/region_icon.svg')?>
                            </div>
                            <div class="widget-region-text">
                                <?php $APPLICATION->IncludeComponent('intec.regionality:regions.select', $arResult['REGIONALITY']['TEMPLATE'], []) ?>
                            </div>
                        </div>
                        <!--/noindex-->
                    </div>
                <?php } ?>
                <?php if ($arResult['CONTACTS']['SHOW']['DESKTOP']) { ?>
                    <?php $arContact = $arResult['CONTACTS']['SELECTED'] ?>
                    <?php $arContacts = $arResult['CONTACTS']['VALUES'] ?>
                    <div class="widget-contacts-wrap intec-grid-item-auto">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item',
                                'widget-contacts'
                            ],
                            'data' => [
                                'multiple' => !empty($arContacts) ? 'true' : 'false',
                                'expanded' => 'false',
                                'block' => 'phone'
                            ]
                        ]) ?>
                            <div class="widget-phone">
                                <div class="widget-phone-content">
                                    <div class="widget-phone-content-wrapper intec-grid intec-grid-o-vertical">
                                        <?php if ($arResult['CONTACTS']['ADVANCED']) { ?>
                                            <?php foreach ($arContact as $arContactItem) { ?>
                                                <a href="tel:<?= $arContactItem['PHONE']['VALUE'] ?>" class="tel widget-phone-text intec-cl-text-hover" data-block-action="popup.open">
                                                    <span class="value"><?= $arContactItem['PHONE']['DISPLAY'] ?></span>
                                                </a>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php foreach ($arContact as $arContactItem) { ?>
                                                <a href="tel:<?= $arContactItem['VALUE'] ?>" class="tel widget-phone-text intec-cl-text-hover" data-block-action="popup.open">
                                                    <span class="value"><?= $arContactItem['DISPLAY'] ?></span>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <?php if (!empty($arContacts)) { ?>
                                        <div class="widget-phone-popup" data-block-element="popup">
                                            <div class="widget-phone-popup-wrapper scrollbar-inner" data-advanced="<?= $arResult['CONTACTS']['ADVANCED'] ? 'true' : 'false' ?>">
                                                <?php if ($arResult['CONTACTS']['ADVANCED']) { ?>
                                                    <?php foreach ($arContacts as $arContact) { ?>
                                                        <div class="widget-phone-popup-contacts">
                                                            <?php if (!empty($arContact['PHONE'])) { ?>
                                                                <a href="tel:<?= $arContact['PHONE']['VALUE'] ?>" class="tel widget-phone-popup-contact phone intec-cl-text-hover">
                                                                    <span class="value"><?= $arContact['PHONE']['DISPLAY'] ?></span>
                                                                </a>
                                                            <?php } ?>
                                                            <?php if (!empty($arContact['ADDRESS'])) { ?>
                                                                <div class="adr widget-phone-popup-contact address">
                                                                    <span class="locality"><?= $arContact['ADDRESS'] ?></span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (!empty($arContact['SCHEDULE'])) { ?>
                                                                <div class="widget-phone-popup-contact schedule">
                                                                    <?php if (Type::isArray($arContact['SCHEDULE'])) { ?>
                                                                        <?php foreach ($arContact['SCHEDULE'] as $sValue) { ?>
                                                                            <span><?= $sValue ?></span>
                                                                        <?php } ?>
                                                                    <?php } else { ?>
                                                                        <?= $arContact['SCHEDULE'] ?>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (!empty($arContact['EMAIL'])) { ?>
                                                                <a href="mailto:<?= $arContact['EMAIL'] ?>" class="email widget-phone-popup-contact email intec-cl-text-hover">
                                                                    <?= $arContact['EMAIL'] ?>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php foreach ($arContacts as $arContact) { ?>
                                                        <a href="tel:<?= $arContact['VALUE'] ?>" class="tel widget-phone-popup-item intec-cl-text-hover">
                                                            <span class="value"><?= $arContact['DISPLAY'] ?></span>
                                                        </a>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php if (!empty($arContacts)) { ?>
                                    <div class="widget-phone-arrow far fa-chevron-down" data-block-action="popup.open"></div>
                                <?php } ?>
                            </div>
                            <?php if ($arResult['FORMS']['CALL']['SHOW']) { ?>
                                <div class="widget-button-wrap">
                                    <div class="widget-button intec-cl-text-hover intec-cl-border-hover" data-action="forms.call.open">
                                        <?= Loc::getMessage('C_HEADER_TEMP1_DESKTOP_TEMP7_BUTTON') ?>
                                    </div>
                                    <?php include(__DIR__.'/../../../parts/forms/call.php') ?>
                                </div>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                        <?php if (!empty($arContacts) && !defined('EDITOR')) { ?>
                            <script type="text/javascript">
                                template.load(function (data) {
                                    var $ = this.getLibrary('$');
                                    var root = data.nodes;
                                    var block = $('[data-block="phone"]', root);
                                    var popup = $('[data-block-element="popup"]', block);
                                    var scrollContacts = $('.scrollbar-inner', popup);

                                    popup.open = $('[data-block-action="popup.open"]', block);
                                    popup.open.on('mouseenter', function () {
                                        block.attr('data-expanded', 'true');
                                    });

                                    block.on('mouseleave', function () {
                                        block.attr('data-expanded', 'false');
                                    });

                                    scrollContacts.scrollbar();
                                }, {
                                    'name': '[Component] intec.universe:main.header (template.1) > desktop (template.7) > phone.expand',
                                    'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                                    'loader': {
                                        'name': 'lazy'
                                    }
                                });
                            </script>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($arResult['AUTHORIZATION']['SHOW']['DESKTOP']) { ?>
                    <div class="widget-authorization-wrap intec-grid-item-auto">
                        <div class="widget-authorization widget-item">
                            <?php include(__DIR__.'/../../../parts/auth/panel.3.php') ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($bBasketShow) { ?>
                    <div class="widget-basket-wrap intec-grid-item-auto">
                        <div class="widget-item widget-basket">
                            <?php include(__DIR__.'/../../../parts/basket.php') ?>
                        </div>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <?php //$APPLICATION->ShowViewContent('template-header-desktop-after') ?>
</div>
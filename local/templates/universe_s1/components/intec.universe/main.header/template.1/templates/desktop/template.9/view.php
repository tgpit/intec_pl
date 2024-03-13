<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */

$sTemplateId = $arData['id'];
$sTemplateType = $arData['type'];
$bPanelShow = false;
$bContainerShow = false;

$arMenuMain = $arResult['MENU']['MAIN'];
$arMenuInfo = $arResult['MENU']['INFO'];

foreach (['AUTHORIZATION', 'CONTACTS'] as $sBlock)
    $bPanelShow = $bPanelShow || $arResult[$sBlock]['SHOW']['DESKTOP'];

$bPanelShow = $bPanelShow ||
    $arResult['SOCIAL']['SHOW'] ||
    $arMenuInfo['SHOW'] ||
    $arResult['FORMS']['CALL']['SHOW'];

foreach ([
    'LOGOTYPE',
    'SEARCH'
] as $sBlock) {
    $bContainerShow = $bContainerShow || $arResult[$sBlock]['SHOW']['DESKTOP'];
}

if ($arMenuMain['SHOW']['DESKTOP'])
    $bContainerShow = true;

$arContacts = [];
$arContact = null;

if ($arResult['CONTACTS']['SHOW']) {
    $arContacts = $arResult['CONTACTS']['VALUES'];
    $arContact = $arResult['CONTACTS']['SELECTED'];
}

?>
<div class="widget-view-desktop-9">
    <?php //$APPLICATION->ShowViewContent('template-header-desktop-before') ?>
    <?php if ($bPanelShow) { ?>
        <div class="widget-panel">
            <div class="intec-content intec-content-visible intec-content-primary">
                <div class="intec-content-wrapper">
                    <div class="widget-panel-wrapper">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid' => [
                                    '',
                                    'wrap',
                                    'a-h-center',
                                    'a-v-center',
                                    'i-h-20',
                                    'i-v-5'
                                ],
                                'widget-panel-wrapper-2'
                            ]
                        ])?>
                            <?php if ($arResult['SOCIAL']['SHOW']) { ?>
                                <?php if ($arResult['SOCIAL']['SHOW']) { ?>
                                    <?php include(__DIR__.'/../../../parts/social.php') ?>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($arResult['REGIONALITY']['USE']) { ?>
                                <div class="widget-region-wrap intec-grid-item-auto">
                                    <!--noindex-->
                                    <div class="widget-region intec-grid intec-grid-i-h-8 intec-grid-a-v-center"">
                                        <div class="widget-region-icon intec-grid-item-auto intec-cl-svg-path-stroke">
                                            <?= FileHelper::getFileData(__DIR__.'/../../../svg/region_icon.svg')?>
                                        </div>
                                        <div class="widget-region-text intec-grid-auto">
                                            <?php $APPLICATION->IncludeComponent('intec.regionality:regions.select', $arResult['REGIONALITY']['TEMPLATE'], []) ?>
                                        </div>
                                    </div>
                                    <!--/noindex-->
                                </div>
                            <?php } ?>
                            <?php if ($arMenuInfo['SHOW']) { ?>
                                <div class="widget-panel-menu-wrap intec-grid-item-auto">
                                    <?php $APPLICATION->IncludeComponent(
                                        'bitrix:menu',
                                        'info.1',
                                        [
                                            'ROOT_MENU_TYPE' => $arMenuInfo['ROOT'],
                                            'CHILD_MENU_TYPE' => $arMenuInfo['CHILD'],
                                            'MAX_LEVEL' => $arMenuInfo['LEVEL'],
                                            'MENU_CACHE_TYPE' => 'N',
                                            'USE_EXT' => 'Y',
                                            'DELAY' => 'N',
                                            'ALLOW_MULTI_SELECT' => 'N',
                                            'CACHE_SELECTED_ITEMS' => 'N'
                                        ],
                                        $this->getComponent()
                                    ); ?>
                                </div>
                            <?php } ?>
                            <div class="intec-grid-item"></div>
                            <?php if ($arResult['CONTACTS']['SHOW']['DESKTOP']) { ?>
                                <div class="widget-panel-phone-wrap intec-grid-item-auto">
                                    <div class="widget-panel-phone intec-ui-align" data-block="phone" data-multiple="<?= !empty($arContacts) ? 'true' : 'false' ?>" data-expanded="false">
                                        <div class="widget-panel-phone-content">
                                            <div class="widget-phone-content-wrapper intec-grid intec-grid-o-vertical">
                                                <?php if ($arResult['CONTACTS']['ADVANCED']) { ?>
                                                    <?php foreach ($arContact as $arContactItem) { ?>
                                                        <a href="tel:<?= $arContactItem['PHONE']['VALUE'] ?>" class="tel widget-panel-phone-text intec-cl-text-hover" data-block-action="popup.open">
                                                            <span class="value"><?= $arContactItem['PHONE']['DISPLAY'] ?></span>
                                                        </a>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php foreach ($arContact as $arContactItem) { ?>
                                                        <a href="tel:<?= $arContactItem['VALUE'] ?>" class="tel widget-panel-phone-text intec-cl-text-hover" data-block-action="popup.open">
                                                            <span class="value"><?= $arContactItem['DISPLAY'] ?></span>
                                                        </a>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <?php if (!empty($arContacts)) { ?>
                                                <div class="widget-panel-phone-popup" data-block-element="popup">
                                                    <div class="widget-panel-phone-popup-wrapper scrollbar-inner">
                                                        <?php if ($arResult['CONTACTS']['ADVANCED']) {
                                                            $sScheduleString = '';
                                                            ?>
                                                            <?php foreach ($arContacts as $arContact) { ?>
                                                                <div class="widget-panel-phone-popup-contacts">
                                                                    <?php if (!empty($arContact['PHONE'])) { ?>
                                                                        <a href="tel:<?= $arContact['PHONE']['VALUE'] ?>" class="tel widget-panel-phone-popup-contact phone intec-cl-text-hover">
                                                                            <span class="value"><?= $arContact['PHONE']['DISPLAY'] ?></span>
                                                                        </a>
                                                                    <?php } ?>
                                                                    <?php if (!empty($arContact['ADDRESS'])) { ?>
                                                                        <div class="widget-panel-phone-popup-contact address adr">
                                                                            <span class="locality"><?= $arContact['ADDRESS'] ?></span>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <?php if (!empty($arContact['SCHEDULE'])) { ?>
                                                                        <div  class="widget-panel-phone-popup-contact schedule">
                                                                            <?php if (Type::isArray($arContact['SCHEDULE'])) { ?>
                                                                                <?php foreach ($arContact['SCHEDULE'] as $sValue) { ?>
                                                                                    <div><?= $sValue ?></div>
                                                                                    <?php $sScheduleString .= $sValue.', '; ?>
                                                                                <?php } ?>
                                                                            <?php } else { ?>
                                                                                <?= $arContact['SCHEDULE'] ?>
                                                                                <?php $sScheduleString .= $arContact['SCHEDULE'].', '; ?>
                                                                            <?php } ?>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <?php if (!empty($arContact['EMAIL'])) { ?>
                                                                        <a href="mailto:<?= $arContact['EMAIL'] ?>" class="email widget-panel-phone-popup-contact email intec-cl-text-hover">
                                                                            <?= $arContact['EMAIL'] ?>
                                                                        </a>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php }
                                                                $sScheduleString = substr($sScheduleString, 0, (strlen($sScheduleString) - 2));
                                                            ?>
                                                            <span class="workhours">
                                                                <span class="value-title" title="<?=$sScheduleString?>"></span>
                                                            </span>
                                                            <?php unset($sScheduleString); ?>
                                                        <?php } else { ?>
                                                            <?php foreach ($arContacts as $arContact) { ?>
                                                                <a href="tel:<?= $arContact['VALUE'] ?>" class="tel widget-panel-phone-popup-item intec-cl-text-hover">
                                                                    <span class="value"><?= $arContact['DISPLAY'] ?></span>
                                                                </a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php if (!empty($arContacts)) { ?>
                                            <div class="widget-panel-phone-arrow far fa-chevron-down" data-block-action="popup.open"></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['BASKET']['SHOW']['DESKTOP'] || $arResult['DELAY']['SHOW']['DESKTOP'] || $arResult['COMPARE']['SHOW']['DESKTOP']) { ?>
                                <div class="widget-panel-basket-wrap intec-grid-item-auto">
                                    <div class="widget-panel-basket widget-item">
                                        <?php include(__DIR__.'/../../../parts/basket.php') ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['AUTHORIZATION']['SHOW']['DESKTOP'] || $arResult['FORMS']['CALL']['SHOW']) { ?>
                                <div class="widget-panel-buttons-wrap intec-grid-item-auto">
                                    <div class="widget-panel-buttons">
                                        <div class="widget-panel-buttons-wrapper">
                                            <?php if ($arResult['FORMS']['CALL']['SHOW']) { ?>
                                                <div class="widget-panel-button widget-panel-button-form intec-cl-background intec-cl-background-light-hover" data-action="forms.call.open">
                                                    <div class="widget-panel-button-wrapper intec-grid intec-grid-a-v-center">
                                                        <div class="widget-panel-button-text intec-grid-item-auto" style="color: #fff !important;">
                                                            <?= Loc::getMessage('C_HEADER_TEMP1_DESKTOP_TEMP9_BUTTON') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php include(__DIR__.'/../../../parts/forms/call.php') ?>
                                            <?php } ?>
                                            <?php if ($arResult['AUTHORIZATION']['SHOW']['DESKTOP']) { ?>
                                                <?php include(__DIR__.'/../../../parts/auth/panel.2.php') ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($bContainerShow) { ?>
        <div class="widget-container">
            <div class="intec-content intec-content-visible intec-content-primary">
                <div class="intec-content-wrapper">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-container-wrapper',
                            'intec-grid' => [
                                '',
                                'nowrap',
                                'a-h-start',
                                'a-v-center',
                                'i-h-15'
                            ]
                        ]
                    ]) ?>
                        <?php if ($arResult['LOGOTYPE']['SHOW']['DESKTOP']) { ?>
                            <div class="widget-container-logotype-wrap intec-grid-item-auto">
                                <?= Html::beginTag($arResult['LOGOTYPE']['LINK']['USE'] ? 'a' : 'div', [
                                    'href' => $arResult['LOGOTYPE']['LINK']['USE'] ? $arResult['LOGOTYPE']['LINK']['VALUE'] : null,
                                    'class' => [
                                        'widget-container-item',
                                        'widget-container-logotype',
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
                        <?php if ($arMenuMain['SHOW']['DESKTOP']) { ?>
                            <div class="widget-container-menu-wrap intec-grid-item intec-grid-item-shrink-1">
                                <div class="widget-container-item widget-container-menu">
                                    <?php $arMenuParams = [
                                        'TRANSPARENT' => 'Y'
                                    ] ?>
                                    <?php include(__DIR__.'/../../../parts/menu/main.horizontal.1.php') ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="intec-grid-item"></div>
                        <?php } ?>
                        <?php if ($arResult['SEARCH']['SHOW']['DESKTOP']) { ?>
                            <div class="widget-container-search-wrap intec-grid-item-auto">
                                <div class="widget-container-item widget-container-search">
                                    <?php include(__DIR__.'/../../../parts/search/popup.1.php') ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($arMenuMain['SHOW']['DESKTOP']) { ?>
                            <div class="widget-container-menu-popup-wrap intec-grid-item-auto">
                                <div class="widget-container-item widget-container-menu-popup">
                                    <?php $arMenuParams = [
                                            'BUTTONS_CLOSE_POSITION' => 'right'
                                    ] ?>
                                    <?php include(__DIR__.'/../../../parts/menu/main.popup.php') ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($arResult['CONTACTS']['SHOW']['DESKTOP'] && !defined('EDITOR')) { ?>
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
                'name': '[Component] intec.universe:main.header (template.1) > desktop (template.9) > phone.expand',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'lazy'
                }
            });
        </script>
    <?php } ?>
    <?php //$APPLICATION->ShowViewContent('template-header-desktop-after') ?>
</div>
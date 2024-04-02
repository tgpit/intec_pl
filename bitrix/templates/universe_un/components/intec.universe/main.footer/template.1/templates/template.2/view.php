<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */

$bPartLeftShow =
    $arResult['ICONS']['SHOW'] ||
    $arResult['COPYRIGHT']['SHOW'];

$bPartCenterShow = $arResult['MENU']['MAIN']['SHOW'];
$bPartRightShow =
    $arResult['SEARCH']['SHOW'] ||
    $arResult['PHONE']['SHOW'] ||
    $arResult['SOCIAL']['SHOW'] ||
    $arResult['LOGOTYPE']['SHOW'];

$bPartsShow =
    $bPartLeftShow ||
    $bPartCenterShow ||
    $bPartRightShow;

$bSecondPhoneShow = $arResult['PHONE']['SECOND']['SHOW'];

?>
<div class="widget-view-2 intec-content-wrap">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($bPartsShow) { ?>
                <div class="<?= Html::cssClassFromArray([
                    'widget-parts',
                    'intec-grid' => [
                        '',
                        'nowrap',
                        'a-h-start',
                        'a-v-start',
                        '768-wrap'
                    ]
                ]) ?>">
                    <?php if ($bPartLeftShow) { ?>
                        <div class="widget-part widget-part-left intec-grid-item-auto intec-grid-item-768-1">
                            <div class="widget-part-items">
                                <?php if ($arResult['ICONS']['SHOW']) { ?>
                                    <div class="widget-part-item">
                                        <div class="widget-icons intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-h-768-center intec-grid-a-v-center intec-grid-i-12">
                                            <?php foreach ($arResult['ICONS']['ITEMS'] as $arItem) { ?>
                                            <?php if (!$arItem['SHOW']) continue ?>
                                                <div class="widget-icon intec-grid-item-auto" data-icon="<?= StringHelper::toLowerCase($arItem['CODE']) ?>">
                                                    <div class="widget-icon-image"></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['COPYRIGHT']['SHOW']) { ?>
                                    <div class="widget-part-item">
                                        <!--noindex-->
                                        <div class="widget-copyright">
                                            <?= $arResult['COPYRIGHT']['VALUE'] ?>
                                        </div>
                                        <!--/noindex-->
                                    </div>
                                <?php } ?>
                                <div class="widget-part-item">
                                    <div id="bx-composite-banner"></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="widget-part widget-part-center intec-grid-item intec-grid-item-768-1">
                        <?php if ($bPartCenterShow) { ?>
                            <?php include(__DIR__.'/../../parts/menu/main.columns.1.php') ?>
                        <?php } ?>
                    </div>
                    <?php if ($bPartRightShow) { ?>
                        <div class="widget-part widget-part-right intec-grid-item-auto intec-grid-item-768-1">
                            <div class="widget-part-items">
                                <?php if ($arResult['SEARCH']['SHOW']) { ?>
                                    <div class="widget-part-item widget-search">
                                        <?php
                                            $arSearch = [
                                                'TEMPLATE' => 'input.3'
                                            ];

                                            include(__DIR__.'/../../parts/search.php');
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['PHONE']['SHOW']) { ?>
                                    <div class="<?= Html::cssClassFromArray([
                                        'widget-part-item',
                                        'widget-phone',
                                        $bSecondPhoneShow ? 'second-phone-show' : null
                                    ]) ?>">
                                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-center intec-grid-i-v-5 intec-grid-i-h-10">
                                            <div class="intec-grid-item-1 intec-grid-item-550-auto intec-grid-item-400-1 widget-item-phone-wrapper intec-grid intec-grid-a-v-center">
                                                <span class="widget-phone-icon widget-phone-icon-phone">
                                                    <?=FileHelper::getFileData(__DIR__."/../../svg/icon-phone.svg");?>
                                                </span>
                                                <div class="widget-part-item-phone-wrapper-2 intec-grid intec-grid-o-vertical">
                                                    <a class="tel widget-phone-text" href="tel:<?= $arResult['PHONE']['VALUE']['LINK'] ?>">
                                                        <span class="value"><?= $arResult['PHONE']['VALUE']['DISPLAY'] ?></span>
                                                    </a>
                                                    <?php if ($bSecondPhoneShow) { ?>
                                                        <a class="tel widget-phone-text" href="tel:<?= $arResult['PHONE']['SECOND']['VALUE']['LINK'] ?>">
                                                            <span class="value"><?= $arResult['PHONE']['SECOND']['VALUE']['DISPLAY'] ?></span>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php if ($arResult['FORMS']['CALL']['SHOW']) { ?>
                                                <div class="intec-grid-item-1 intec-grid-item-550-auto intec-grid-item-400-1">
                                                    <div class="widget-phone-order">
                                                        <div class="widget-phone-order-wrapper intec-cl-text intec-cl-border" data-action="forms.call.open">
                                                            <?= Loc::getMessage('C_MAIN_FOOTER_TEMPLATE_1_VIEW_2_FORMS_CALL_BUTTON') ?>
                                                        </div>
                                                    </div>
                                                    <?php include(__DIR__.'/../../parts/forms/call.php') ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['SOCIAL']['SHOW']) { ?>
                                    <!--noindex-->
                                    <div class="widget-part-item widget-social">
                                        <div class="widget-social-items intec-grid intec-grid-a-h-end intec-grid-a-h-768-center intec-grid-a-v-center intec-grid-wrap intec-grid-i-7">
                                            <?php foreach ($arResult['SOCIAL']['ITEMS'] as $arItem) { ?>
                                                <?php if (!$arItem['SHOW']) continue ?>
                                                <a rel="nofollow" target="_blank" href="<?= $arItem['LINK'] ?>" class="widget-social-item intec-image-effect intec-grid-item-auto">
                                                    <div class="widget-social-item-icon" data-grey="<?= $arResult['SOCIAL']['GREY'] ?>" data-social-icon="<?= $arItem['CODE'] ?>" data-social-icon-square="<?= $arResult['SOCIAL']['SQUARE'] ?>"></div>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <!--/noindex-->
                                <?php } ?>
                                <?php if ($arResult['LOGOTYPE']['SHOW']) { ?>
                                    <div class="widget-part-item widget-logotype">
                                        <a target="_blank" href="<?= $arResult['LOGOTYPE']['LINK'] ?>" class="widget-logotype-wrapper intec-ui-picture">
                                            <?php include(__DIR__.'/../../parts/logotype.php') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
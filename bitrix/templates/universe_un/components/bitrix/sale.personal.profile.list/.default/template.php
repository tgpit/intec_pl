<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
?>
<div id="<?= $sTemplateId?>" class="ns-bitrix c-sale-personal-profile-list c-sale-personal-profile-list-default">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="intec-ui-m-b-25">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-8">
                    <div class="intec-grid-item">
                        <?php
                        if(strlen($arResult["ERROR_MESSAGE"])>0)
                            ShowError($arResult["ERROR_MESSAGE"]);
                        ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?php if (!empty($arParams['PATH_TO_ADD'])) { ?>
                            <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_BUTTON_ADD_PROFILE'), [
                               'href' => $arParams['PATH_TO_ADD'],
                                'class' => [
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-2',
                                        'scheme-current'
                                    ],
                                    'sale-personal-profile-list-add-profile'
                                ]
                            ]) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php if (is_array($arResult["PROFILES"]) && !empty($arResult["PROFILES"])) { ?>
                <div class="sale-personal-profile-list-wrap intec-ui-markup-table-responsive">
                    <div class="sale-personal-profile-list-table">
                        <div class="sale-personal-profile-list-row sale-personal-profile-list-row-title">
                            <?php $dataColumns = ['ID', 'DATE_UPDATE', 'NAME', 'PERSON_TYPE'];

                            foreach ($dataColumns as $column) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'sale-personal-profile-list-cell' => [
                                            '' => true,
                                            'date' => $column == 'DATE_UPDATE'
                                        ]
                                    ], true)
                                ])?>
                                    <div class="intec-grid intec-grid-i-h-6 intec-grid-800-wrap">
                                        <div class="intec-grid-item-auto intec-grid-item-800-1">
                                            <?=Loc::getMessage('C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_COLUMN_'.$column)?>
                                        </div>
                                        <div class="sale-personal-profile-list-arrow-wrap intec-grid-item-auto">
                                            <a class="sale-personal-profile-list-arrow sale-personal-profile-list-arrow-up intec-cl-svg-path-stroke-hover" href="<?=$arResult['URL']?>by=<?=$column?>&order=asc#nav_start">
                                                <?= FileHelper::getFileData(__DIR__.'/images/arrow.svg')?>
                                            </a>
                                            <a class="sale-personal-profile-list-arrow sale-personal-profile-list-arrow-down intec-cl-svg-path-stroke-hover" href="<?=$arResult['URL']?>by=<?=$column?>&order=desc#nav_start">
                                                <?= FileHelper::getFileData(__DIR__.'/images/arrow.svg')?>
                                            </a>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <div class="sale-personal-profile-list-cell sale-personal-profile-list-cell-edit"></div>
                            <div class="sale-personal-profile-list-cell"></div>
                        </div>
                        <?php foreach($arResult["PROFILES"] as $arItem) { ?>
                            <div class="sale-personal-profile-list-row">
                                <div class="sale-personal-profile-list-cell"><?= $arItem["ID"] ?></div>
                                <div class="sale-personal-profile-list-cell sale-personal-profile-list-cell-date"><?= $arItem["DATE_UPDATE"] ?></div>
                                <div class="sale-personal-profile-list-cell sale-personal-profile-list-cell-name"><?= $arItem["NAME"] ?></div>
                                <div class="sale-personal-profile-list-cell"><?= $arItem["PERSON_TYPE"]["NAME"] ?></div>
                                <div class="sale-personal-profile-list-cell sale-personal-profile-list-cell-edit">
                                    <a class="sale-personal-profile-list-button sale-personal-profile-list-button-edit" title="<?= Loc::getMessage("C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_BUTTON_DETAIL_DESCR") ?>"
                                        href="<?= $arItem["URL_TO_DETAIL"] ?>"><?= GetMessage("C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_BUTTON_DETAIL") ?>
                                    </a>
                                </div>
                                <div class="sale-personal-profile-list-button sale-personal-profile-list-cell">
                                    <a class="sale-personal-profile-list-button-delete" title="<?= Loc::getMessage("C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_BUTTON_DELETE_DESCR") ?>"
                                        href="javascript:if(confirm('<?= Loc::getMessage("C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_DELETE_CONFIRM") ?>')) window.location='<?= $arItem["URL_TO_DETELE"] ?>'">
                                        <i class="fal fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if(strlen($arResult["NAV_STRING"]) > 0) { ?>
                    <p><?=$arResult["NAV_STRING"]?></p>
                <?php }
            } elseif ($arResult['USER_IS_NOT_AUTHORIZED'] !== 'Y') { ?>
                <div class="sale-personal-profile-list-empty">
                    <?=Loc::getMessage("C_SALE_PERSONAL_PROFILE_LIST_DEFAULT_EMPTY_PROFILE_LIST") ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

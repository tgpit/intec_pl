<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arSvg = [
    'RETURN' => FileHelper::getFileData(__DIR__.'/images/arrow_return.svg'),
    'DOWNLOAD' => FileHelper::getFileData(__DIR__.'/images/download.svg')
];
$arVisual = $arResult['VISUAL'];

?>

<?= ShowError($arResult['ERROR_MESSAGE']) ?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-support-ticket-edit c-support-ticket-edit-template-1">
    <div class="support-ticket-edit-wrapper intec-content">
        <div class="support-ticket-edit-wrapper-2 intec-content-wrapper">
            <?php include(__DIR__.'/parts/back_to_claims.php') ?>
            <?php if (!empty($arResult['TICKET']['ID'])) { ?>
                <div class="support-ticket-edit-header">
                    <div class="support-ticket-edit-header-top">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-8">
                            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                <span class="support-ticket-edit-title">
                                    <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_TITLE', [
                                        '#ID#' => $arResult['TICKET']['ID'],
                                        '#NAME#' => $arResult['TICKET']['TITLE']
                                    ]) ?>
                                </span>
                            </div>
                            <?php if (strlen($arResult["TICKET"]["DATE_CLOSE"]) > 0) { ?>
                                <div class="intec-grid-item-auto">
                                    <form name="support_edit" method="post" action="<?= $arResult["REAL_FILE_PATH"] ?>">
                                        <?= bitrix_sessid_post() ?>
                                        <input type="hidden" name="set_default" value="Y" />
                                        <input type="hidden" name="ID" value=<?= empty($arResult["TICKET"]) ? 0 : $arResult["TICKET"]["ID"] ?> />
                                        <input type="hidden" name="lang" value="<?= LANG ?>" />
                                        <input type="hidden" name="edit" value="1" />
                                        <input type="hidden" name="apply" value="Y">
                                        <input type="hidden" name="OPEN" value="Y" />
                                        <button type="submit" class="support-ticket-edit-open-ticket intec-ui intec-ui-control-button intec-ui-scheme-current">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_OPEN_TICKET') ?>
                                        </button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="support-ticket-edit-header-bottom">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-15 intec-grid-i-v-8">
                            <div class="support-ticket-edit-info-item intec-grid-item-auto intec-grid-item-550-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start">
                                <div class="support-ticket-edit-subtitle intec-grid-item-auto intec-grid-item-550-2">
                                    <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_TITLE_DATE') ?>
                                </div>
                                <div class="support-ticket-edit-text intec-grid-item-auto intec-grid-item-550-2">
                                    <?= $arResult['TICKET']['DATE_CREATE'] ?>
                                </div>
                            </div>
                            <?php if ($arVisual['SHOW_ORDER_INFO']) { ?>
                                <div class="support-ticket-edit-info-item intec-grid-item-auto intec-grid-item-550-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start">
                                    <div class="support-ticket-edit-subtitle intec-grid-item-auto intec-grid-item-550-2">
                                        <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_TITLE_ORDER') ?>
                                    </div>
                                    <div class="support-ticket-edit-text intec-grid-item-auto intec-grid-item-550-2 intec-grid intec-grid-nowrap intec-grid-550-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                                        <div class="intec-grid-item-auto">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_ORDER_NUMBER_TEXT') ?>
                                        </div>
                                        <?= Html::beginTag($arVisual['USE_ORDER_LINK'] ? 'a' : 'span', [
                                            'class' => [
                                                'intec-cl-text',
                                                'intec-grid-item-auto',
                                                'intec-grid-item-shrink-1'
                                            ],
                                            'href' => !empty($arResult['TICKET']['ORDER']['LINK']) ? $arResult['TICKET']['ORDER']['LINK'] : null
                                        ]) ?>
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_ORDER_NUMBER_NUM', [
                                                '#NUMBER#' => $arResult['TICKET']['ORDER']['ID']
                                            ]) ?>
                                        <?= Html::endTag($arVisual['USE_ORDER_LINK'] ? 'a' : 'span') ?>
                                        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                            <span class="support-ticket-edit-order-status">
                                                <?= $arResult['TICKET']['ORDER']['STATUS'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arResult['TICKET']['CATEGORY_NAME'])) { ?>
                                <div class="support-ticket-edit-info-item intec-grid-item-auto intec-grid-item-550-1 intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start">
                                    <div class="support-ticket-edit-subtitle intec-grid-item-auto intec-grid-item-550-2">
                                        <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_HEADER_TITLE_CATEGORY') ?>
                                    </div>
                                    <div class="support-ticket-edit-text intec-grid-item-auto intec-grid-item-550-2">
                                        <?= $arResult['TICKET']['CATEGORY_NAME'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php include(__DIR__.'/parts/messages.php') ?>
            <?php } ?>
            <?php if (strlen($arResult["TICKET"]["DATE_CLOSE"]) == 0) { ?>
                <?php include(__DIR__.'/parts/form.php') ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php') ?>
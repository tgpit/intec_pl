<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="support-ticket-edit-messages" data-role="messages">
    <div class="support-ticket-edit-messages-title">
        <div class="support-ticket-edit-messages-title-header">
            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_TITLE') ?>
        </div>
    </div>
    <div class="support-ticket-edit-messages-wrap">
        <?php foreach ($arResult['MESSAGES'] as $arMessage) { ?>
            <?php $client = $arResult['TICKET']['OWNER_USER_ID'] == $arMessage['OWNER_USER_ID'] ?>
            <div class="support-ticket-edit-messages-item">
                <div class="support-ticket-edit-messages-item-title">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                        <div class="intec-grid-item-auto">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_NUMBER', [
                                '#NUMBER#' => $arMessage['ID']
                            ]) ?>
                        </div>
                        <div class="intec-grid-item-auto">
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'support-ticket-edit-messages-item-status' => true,
                                    'support-ticket-edit-messages-item-status-client' => $client,
                                    'support-ticket-edit-messages-item-status-support' => !$client
                                ], true)
                            ]) ?>
                            <?= $client ? Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_CLIENT') : Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_SUPPORT') ?>
                            <?= Html::endTag('div') ?>
                        </div>
                        <div class="intec-grid-item-auto">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_LOGIN', [
                                '#LOGIN#' => $arMessage['OWNER_LOGIN'],
                                '#NAME#' => $arMessage['OWNER_NAME']
                            ]) ?>
                        </div>
                        <div class="intec-grid-item-auto">
                            <div class="support-ticket-edit-messages-item-date">
                                <?= $arMessage['DATE_CREATE'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="quotetd<?= $arMessage["ID"] ?>" class="support-ticket-edit-messages-item-text">
                    <?= $arMessage['MESSAGE'] ?>
                </div>
                <?php if (!empty($arMessage["FILES"]) || strlen($arResult["TICKET"]["DATE_CLOSE"]) == 0) { ?>
                    <div class="support-ticket-edit-messages-item-files">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-end intec-grid-i-4">
                            <?php if (!empty($arMessage["FILES"])) { ?>
                                <div class="intec-grid-item">
                                    <div class="support-ticket-edit-messages-item-files-title">
                                        <?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_FILES_TITLE') ?>
                                    </div>
                                    <?php $arImg = ['gif', 'png', 'jpg', 'jpeg', 'bmp'] ?>
                                    <?php foreach ($arMessage["FILES"] as $arFile) { ?>
                                        <div class="support-ticket-edit-messages-item-file">
                                            <?php if (in_array(strtolower(GetFileExtension($arFile["NAME"])), $arImg)) { ?>
                                                <a class="intec-cl-text" title="<?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_FILES_VIEW') ?>" href="<?= $componentPath ?>/ticket_show_file.php?hash=<?= $arFile["HASH"] ?>&amp;lang=<?= LANG ?>" target="_blank">
                                                    <?= $arFile["NAME"] ?>
                                                </a>
                                            <?php } else { ?>
                                                <span class="intec-cl-text">
                                                    <?= $arFile["NAME"] ?>
                                                </span>
                                            <?php } ?>
                                            <span class="support-ticket-edit-messages-item-file-size">(<?= CFile::FormatSize($arFile["FILE_SIZE"]); ?>)</span>
                                            <a class="intec-ui-picture intec-cl-svg-path-stroke" title="<?= Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_FILES_DOWNLOAD', [
                                                '#FILE_NAME#' => $arFile["NAME"]
                                            ]) ?>" href="<?= $componentPath ?>/ticket_show_file.php?hash=<?= $arFile["HASH"] ?>&amp;lang=<?= LANG ?>&amp;action=download">
                                                <?= $arSvg['DOWNLOAD'] ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php unset($arImg, $arFile) ?>
                                </div>
                            <?php } ?>
                            <?php if (strlen($arResult["TICKET"]["DATE_CLOSE"]) == 0) { ?>
                                <div class="intec-grid-item-auto intec-grid-item-550-1">
                                    <?= Html::tag('div', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_QUOTE'), [
                                        'class' => 'support-ticket-edit-messages-item-quote',
                                        'title' => Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_MESSAGES_QUOTE_DESC'),
                                        'data' => [
                                            'role' => 'quote',
                                            'id' => 'quotetd'.$arMessage["ID"]
                                        ]
                                    ]) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?= $arResult["NAV_STRING"] ?>
        <?php unset($client) ?>
    </div>
</div>

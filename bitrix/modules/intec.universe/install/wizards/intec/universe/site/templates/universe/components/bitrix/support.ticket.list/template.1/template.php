<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
use intec\core\helpers\StringHelper;
use intec\Core;

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
    'SEARCH' => FileHelper::getFileData(__DIR__.'/images/search.svg'),
    'SEARCH_RESET' => FileHelper::getFileData(__DIR__.'/images/search_reset.svg'),
    'SORT' => FileHelper::getFileData(__DIR__.'/images/sort.svg')
];

$arGet = Core::$app->getRequest()->get();
$bSearchApply = isset($arGet['MESSAGE']) && !empty($arGet['MESSAGE']);
$bDemo = (CTicket::IsDemo()) ? 'Y' : 'N';
$bAdmin = (CTicket::IsAdmin()) ? 'Y' : 'N';
$bSupportTeam = (CTicket::IsSupportTeam()) ? 'Y' : 'N';
$bADS = $bDemo == 'Y' || $bAdmin == 'Y' || $bSupportTeam == 'Y';

?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-support-ticket-list c-support-ticket-list-template-1">
    <div class="support-ticket-list-wrapper intec-content">
        <div class="support-ticket-list-wrapper-2 intec-content-wrapper">
            <?php include(__DIR__.'/parts/search.php'); ?>
            <?php include(__DIR__.'/parts/filter.php'); ?>
            <div class="support-ticket-list-items">
                <div class="support-ticket-list-items-title">
                    <div class="intec-grid intec-grid-i-5 intec-grid-wrap intec-grid-a-v-center">
                        <div class="intec-grid-item">
                            <div class="support-ticket-list-items-title-header">
                                <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_ITEMS') ?>
                            </div>
                        </div>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('a', Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_ADD_TICKET'), [
                                'class' => [
                                    'support-ticket-list-items-title-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-2',
                                        'scheme-current'
                                    ]
                                ],
                                'href' => $arResult["NEW_TICKET_PAGE"]
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="support-ticket-list-items-wrap">
                    <div class="support-ticket-list-item support-ticket-list-item-header">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-5">
                            <div class="support-ticket-list-item-wrap intec-grid-item" data-code="id">
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'support-ticket-list-item-text',
                                        'intec-grid' => [
                                            '',
                                            'item',
                                            'nowrap',
                                            'a-v-center',
                                            'i-h-4'
                                        ]
                                    ],
                                    'href' => $arResult['HEADERS']['ID']
                                ]) ?>
                                    <?= Html::tag('span', Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_ID'), [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid-item-auto' => true,
                                            'intec-cl-text' => isset($arGet['by']) && $arGet['by'] == 's_id'
                                        ], true)
                                    ]) ?>
                                    <?= Html::tag('span', $arSvg['SORT'], [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid-item-auto' => true,
                                            'intec-ui-picture' => true,
                                            'intec-cl-svg-path-stroke' => isset($arGet['by']) && $arGet['by'] == 's_id'
                                        ], true)
                                    ]) ?>
                                <?= Html::endTag('a') ?>
                            </div>
                            <div class="support-ticket-list-item-wrap intec-grid-item" data-code="status">
                                <?= Html::tag('span', Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_STATUS'), [
                                    'class' => 'support-ticket-list-item-text'
                                ]) ?>
                            </div>
                            <div class="support-ticket-list-item-wrap intec-grid-item" data-code="title">
                                <?= Html::tag('span', Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_TITLE'), [
                                    'class' => 'support-ticket-list-item-text'
                                ]) ?>
                            </div>
                            <div class="support-ticket-list-item-wrap intec-grid-item" data-code="category">
                                <?= Html::tag('span', Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_CATEGORY'), [
                                    'class' => 'support-ticket-list-item-text'
                                ]) ?>
                            </div>
                            <div class="support-ticket-list-item-wrap intec-grid-item" data-code="date">
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'support-ticket-list-item-text',
                                        'intec-grid' => [
                                            '',
                                            'item',
                                            'nowrap',
                                            'a-v-center',
                                            'i-h-4'
                                        ]
                                    ],
                                    'href' => $arResult['HEADERS']['DATE']
                                ]) ?>
                                    <?= Html::tag('span', Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_DATE'), [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid-item-auto' => true,
                                            'intec-cl-text' => isset($arGet['by']) && $arGet['by'] == 's_timestamp_x'
                                        ], true)
                                    ]) ?>
                                    <?= Html::tag('span', $arSvg['SORT'], [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid-item-auto' => true,
                                            'intec-ui-picture' => true,
                                            'intec-cl-svg-path-stroke' => isset($arGet['by']) && $arGet['by'] == 's_timestamp_x'
                                        ], true)
                                    ]) ?>
                                <?= Html::endTag('a') ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($arResult['TICKETS_ITEMS'])) { ?>
                        <?php foreach ($arResult['TICKETS_ITEMS'] as $arTicket) { ?>
                            <div class="support-ticket-list-item">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-5">
                                    <div class="support-ticket-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap" data-code="id">
                                        <div class="support-ticket-list-item-text-header intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_ID') ?>
                                        </div>
                                        <div class="intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Html::tag('a', $arTicket['ID'], [
                                                'class' => [
                                                    'support-ticket-list-item-text',
                                                    'intec-cl-text'
                                                ],
                                                'href' => $arTicket['TICKET_EDIT_URL']
                                            ]) ?>
                                            <?= Html::tag('div', $arTicket['TIMESTAMP_X'], [
                                                'class' => 'support-ticket-list-item-text',
                                                'style' => [
                                                    'color' => '#808080'
                                                ]
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="support-ticket-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap" data-code="status">
                                        <div class="support-ticket-list-item-text-header intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_STATUS') ?>
                                        </div>
                                        <div class="intec-grid-item-1 intec-grid-item-1024-2 intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-9">
                                            <div class="intec-grid-item-auto">
                                                <?= Html::tag('div', '', [
                                                    'class' => 'support-ticket-list-item-indicator',
                                                    'style' => [
                                                        'background-color' => $arTicket['LAMP']
                                                    ]
                                                ]) ?>
                                            </div>
                                            <div class="intec-grid-item">
                                                <?= Html::tag('span', $arTicket['STATUS_NAME'], [
                                                    'class' => 'support-ticket-list-item-text'
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="support-ticket-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap" data-code="title">
                                        <div class="support-ticket-list-item-text-header intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_TITLE') ?>
                                        </div>
                                        <div class="intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Html::tag('a', $arTicket['TITLE'], [
                                                'class' => [
                                                    'support-ticket-list-item-text',
                                                    'intec-cl-text'
                                                ],
                                                'href' => $arTicket['TICKET_EDIT_URL']
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="support-ticket-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap" data-code="category">
                                        <div class="support-ticket-list-item-text-header intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_CATEGORY') ?>
                                        </div>
                                        <div class="intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Html::tag('span', $arTicket['CATEGORY_NAME'], [
                                                'class' => 'support-ticket-list-item-text'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="support-ticket-list-item-wrap intec-grid-item intec-grid intec-grid-nowrap" data-code="date">
                                        <div class="support-ticket-list-item-text-header intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_HEADER_DATE') ?>
                                        </div>
                                        <div class="intec-grid-item-1 intec-grid-item-1024-2">
                                            <?= Html::tag('span', $arTicket['TIMESTAMP_X'], [
                                                'class' => 'support-ticket-list-item-text'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="support-ticket-list-item">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_TICKET_EMPTY') ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="support-ticket-list-statuses">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-15 intec-grid-i-v-10">
                    <div class="intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-425-1 intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-9">
                        <div class="intec-grid-item-auto">
                            <div class="support-ticket-list-status-indicator" data-code="red"></div>
                        </div>
                        <div class="intec-grid-item">
                            <div class="support-ticket-list-status-text">
                                <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_STATUS_RED') ?>
                                <?= $bADS ? Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_STATUS_RED_ADMIN_SUPPORT') : '' ?>
                            </div>
                        </div>
                    </div>
                    <div class="intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-425-1 intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-9">
                        <div class="intec-grid-item-auto">
                            <div class="support-ticket-list-status-indicator" data-code="green"></div>
                        </div>
                        <div class="intec-grid-item">
                            <div class="support-ticket-list-status-text">
                                <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_STATUS_GREEN') ?>
                            </div>
                        </div>
                    </div>
                    <div class="intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-425-1 intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-9">
                        <div class="intec-grid-item-auto">
                            <div class="support-ticket-list-status-indicator" data-code="grey"></div>
                        </div>
                        <div class="intec-grid-item">
                            <div class="support-ticket-list-status-text">
                                <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_STATUS_GREY') ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($bADS) { ?>
                        <div class="intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-425-1 intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-9">
                            <div class="intec-grid-item-auto">
                                <div class="support-ticket-list-status-indicator" data-code="yellow"></div>
                            </div>
                            <div class="intec-grid-item">
                                <div class="support-ticket-list-status-text">
                                    <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_STATUS_YELLOW') ?>
                                </div>
                            </div>
                        </div>
                        <div class="intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-425-1 intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-9">
                            <div class="intec-grid-item-auto">
                                <div class="support-ticket-list-status-indicator" data-code="green"></div>
                            </div>
                            <div class="intec-grid-item">
                                <div class="support-ticket-list-status-text">
                                    <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_STATUS_GREEN_ADMIN_SUPPORT') ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if (!empty($arResult['TICKETS_PAGENAVIGATION'])) { ?>
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:main.pagenavigation',
                    'template.1', [
                        'NAV_OBJECT' => $arResult['TICKETS_PAGENAVIGATION'],
                        'SEF_MODE' => 'N'
                    ],
                    $this
                ) ?>
            <?php } ?>
        </div>
    </div>
</div>

<?php include(__DIR__.'/parts/script.php'); ?>
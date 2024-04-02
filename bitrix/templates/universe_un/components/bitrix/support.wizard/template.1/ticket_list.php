<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

?>
<div class="ns-bitrix c-support-wizard c-support-wizard-1 p-ticket-list">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:support.ticket.list',
                'template.1',
                [
                    'TICKET_EDIT_TEMPLATE' => !empty($arParams['TICKET_EDIT_TEMPLATE']) ? $arParams['TICKET_EDIT_TEMPLATE'] : $arResult['FOLDER'].$arResult['URL_TEMPLATES']['ticket_edit'],
                    'TICKETS_PER_PAGE' => $arParams['TICKETS_PER_PAGE'],
                    'SET_PAGE_TITLE' => $arParams['SET_PAGE_TITLE'],
                    'TICKET_ID_VARIABLE' => $arResult['ALIASES']['ID'],
                    'SITE_ID' => $arParams['SITE_ID'],
                    'SET_SHOW_USER_FIELD' => $arParams['SET_SHOW_USER_FIELD'],
                    'AJAX_ID' => $arParams['AJAX_ID'],
                    'FILTER_USER_FIELD' => $arParams['FILTER_USER_FIELD']
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            ) ?>
        </div>
    </div>
</div>

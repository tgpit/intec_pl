<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

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
<div class="ns-bitrix c-support-wizard c-support-wizard-1 p-ticket-edit">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arParams['SHOW_RESULT']=='Y' && $arResult['DISPLAY_MESSAGE']) { ?>
	            <div class="text"><?= Loc::getMessage('WZ_RESULT') ?></div>
		        <div class="wizard-result"><?= $arResult['DISPLAY_MESSAGE'] ?></div><br>
            <?php } ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:support.ticket.edit',
                'template.1',
                [
                    'ID' => $arResult['VARIABLES']['ID'],
                    'TICKET_LIST_URL' => !empty($arParams['TICKET_LIST_URL']) ? $arParams['TICKET_LIST_URL'] : $arResult['FOLDER'].$arResult['URL_TEMPLATES']['ticket_list'],
                    'TICKET_EDIT_TEMPLATE' => !empty($arParams['TICKET_EDIT_TEMPLATE']) ? $arParams['TICKET_EDIT_TEMPLATE'] : $arResult['FOLDER'].$arResult['URL_TEMPLATES']['ticket_edit'],
                    'MESSAGES_PER_PAGE' => $arParams['MESSAGES_PER_PAGE'],
                    'MESSAGE_SORT_ORDER' => $arParams['MESSAGE_SORT_ORDER'],
                    'MESSAGE_MAX_LENGTH' => $arParams['MESSAGE_MAX_LENGTH'],
                    'SET_PAGE_TITLE' => $arParams['SET_PAGE_TITLE'],
                    'SHOW_COUPON_FIELD' => $arParams['SHOW_COUPON_FIELD'],
                    'SET_SHOW_USER_FIELD' => $arParams['SET_SHOW_USER_FIELD'],
                    'PROPERTY_ORDER_ID' => $arParams['FILTER_USER_FIELD'],
                    'ORDER_DETAIL_URL' => $arParams['ORDER_DETAIL_URL']
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            ) ?>
        </div>
    </div>
</div>

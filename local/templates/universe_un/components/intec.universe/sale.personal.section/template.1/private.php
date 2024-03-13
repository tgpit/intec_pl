<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<div class="ns-intec c-sale-personal-section c-sale-personal-section-template-1 p-orders">
    <div class="sale-personal-section-wrapper intec-content">
        <div class="sale-personal-section-wrapper intec-content-wrapper">
            <?php $APPLICATION->IncludeComponent(
	"bitrix:main.profile", 
	"template.1",
                   array(
                        "CHECK_RIGHTS" => "N",
                        "SEND_INFO" => "N",
                        "SET_TITLE" => $arParams['SET_TITLE'],
                        "USER_PROPERTY" => array(
                        ),
                        "USER_PROPERTY_NAME" => "",
                        "USER_URL_CHANGE_PSW" => "",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "COMPONENT_TEMPLATE" => "template.1"
                   ),
	false
); ?>
        </div>
    </div>
</div>

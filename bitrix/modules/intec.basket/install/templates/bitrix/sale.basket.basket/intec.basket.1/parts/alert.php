<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-notify intec-basket-notify-warning intec-basket-notify-closable" id="basket-warning" style="display: none;">
    <div class="intec-basket-notify-body">
        <div class="intec-basket-grid intec-basket-grid-stretch">
            <div class="intec-basket-grid-item-auto">
                <div class="intec-basket-notify-icon intec-basket-picture intec-basket-align-middle">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.6099 15.5965H11.3808L11.065 6H13.9257L13.6099 15.5965ZM12.4954 17.0992C12.9474 17.0992 13.3096 17.2355 13.582 17.5082C13.8607 17.7808 14 18.1295 14 18.5543C14 18.9728 13.8607 19.3184 13.582 19.591C13.3096 19.8637 12.9474 20 12.4954 20C12.0495 20 11.6873 19.8637 11.4087 19.591C11.1362 19.3184 11 18.9728 11 18.5543C11 18.1359 11.1362 17.7903 11.4087 17.5177C11.6873 17.2387 12.0495 17.0992 12.4954 17.0992Z" fill="#FFF"/>
                    </svg>
                </div>
            </div>
            <div class="intec-basket-grid-item-auto intec-basket-grid-item-shrink">
                <div class="intec-basket-notify-content">
                    <div data-entity="basket-general-warnings" style="display: none;"></div>
                    <div data-entity="basket-item-warnings" style="display: none;">
                        <span>
                            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ALERT_GLOBAL_PART_1') ?>
                        </span>
                        <span class="intec-basket-notify-active intec-basket-scheme-color intec-basket-scheme-color-light-hover" data-entity="basket-items-warning-count"></span>
                        <span>
                            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ALERT_GLOBAL_PART_2') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="intec-basket-grid-item-auto">
                <div class="intec-basket-notify-close intec-basket-picture intec-basket-align-middle" data-entity="basket-items-warning-notification-close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.72656 2.72729L9.27202 9.27275M9.27202 2.72729L2.72656 9.27275" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-coupon intec-basket-picture">
    <div class="intec-basket-coupon-content">
        <input class="intec-basket-coupon-field" type="text" data-entity="basket-coupon-input" placeholder="<?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_COUPON_FIELD_NAME') ?>">
        <button class="intec-basket-coupon-confirm">
            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.7481 8.71981L16.7473 8.71899L12.2569 4.25025C11.9205 3.91548 11.3764 3.91672 11.0415 4.25317C10.7067 4.58957 10.708 5.13368 11.0444 5.4685L14.0592 8.46874H2.97463C2.5 8.46874 2.11526 8.85349 2.11526 9.32812C2.11526 9.80275 2.5 10.1875 2.97463 10.1875H14.0592L11.0444 13.1877C10.708 13.5226 10.7067 14.0667 11.0415 14.4031C11.3764 14.7396 11.9206 14.7407 12.2569 14.406L16.7473 9.93724L16.7481 9.93643C17.0847 9.6005 17.0836 9.05462 16.7481 8.71981Z" fill="#B0B0B0"/>
            </svg>
        </button>
    </div>
</div>
<div class="intec-basket-coupon-messages">
    {{#COUPON_LIST}}
    <div class="intec-basket-coupon-message" data-state="{{CLASS}}">
        <div class="intec-basket-coupon-message-content">
            <div class="intec-basket-coupon-message-part intec-basket-coupon-message-part-default">
                <div class="intec-basket-coupon-message-icon intec-basket-scheme-svg-fill intec-basket-picture">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.7501 10.8335C23.1091 10.8335 23.4001 10.5425 23.4001 10.1835V6.71688C23.4001 6.35789 23.1091 6.06689 22.7501 6.06689L3.25009 6.06689C2.89114 6.06689 2.6001 6.35789 2.6001 6.71688V10.1835C2.6001 10.5425 2.89114 10.8335 3.25009 10.8335C4.32531 10.8335 5.2001 11.7083 5.2001 12.7835C5.2001 13.8588 4.32535 14.7336 3.25009 14.7336C2.89114 14.7336 2.6001 15.0246 2.6001 15.3835V18.8502C2.6001 19.2092 2.89114 19.5002 3.25009 19.5002H22.7501C23.1091 19.5002 23.4001 19.2092 23.4001 18.8502V15.3835C23.4001 15.0246 23.1091 14.7336 22.7501 14.7336C21.6749 14.7336 20.8001 13.8588 20.8001 12.7835C20.8001 11.7083 21.6748 10.8335 22.7501 10.8335ZM22.1001 15.9682V18.2002H10.1834V16.2502C10.1834 15.8912 9.8924 15.6002 9.53345 15.6002C9.17451 15.6002 8.88346 15.8913 8.88346 16.2502V18.2002H3.90008V15.9682C5.38169 15.6662 6.50008 14.353 6.50008 12.7835C6.50008 11.2141 5.38169 9.90082 3.90008 9.59886V7.36687H8.88342V9.31689C8.88342 9.67588 9.17446 9.96688 9.53341 9.96688C9.89236 9.96688 10.1834 9.67584 10.1834 9.31689V7.36687H22.1001V9.59886C20.6185 9.90086 19.5001 11.2141 19.5001 12.7835C19.5001 14.353 20.6185 15.6663 22.1001 15.9682Z"/>
                        <path d="M9.53329 11.2666C9.1743 11.2666 8.8833 11.5576 8.8833 11.9166V13.6499C8.8833 14.0089 9.17434 14.2999 9.53329 14.2999C9.89224 14.2999 10.1833 14.0089 10.1833 13.6499V11.9166C10.1833 11.5576 9.89228 11.2666 9.53329 11.2666Z"/>
                    </svg>
                </div>
            </div>
            <div class="intec-basket-coupon-message-part intec-basket-coupon-message-part-shrink">
                <div class="intec-basket-coupon-message-information">
                    <span>
                        {{COUPON}}
                    </span>
                    <span>-</span>
                    <span class="intec-basket-coupon-message-information-colored intec-basket-scheme-color">
                        {{JS_CHECK_CODE}}
                    </span>
                    {{#DISCOUNT_NAME}}
                        <span class="intec-basket-coupon-message-information-colored intec-basket-scheme-color">
                            ({{DISCOUNT_NAME}})
                        </span>
                    {{/DISCOUNT_NAME}}
                </div>
            </div>
            <div class="intec-basket-coupon-message-part intec-basket-coupon-message-part-default">
                <button class="intec-basket-coupon-message-delete intec-basket-picture" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 5L11 11M11 5L5 11" stroke="#B0B0B0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    {{/COUPON_LIST}}
</div>
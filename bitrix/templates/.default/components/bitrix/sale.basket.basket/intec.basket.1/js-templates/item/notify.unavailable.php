<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
{{#NOT_AVAILABLE}}
    <div class="intec-basket-notify intec-basket-notify-warning">
        <div class="intec-basket-notify-title">
            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_UNAVAILABLE') ?>
        </div>
        <button class="intec-basket-notify-body" data-entity="basket-item-delete" data-item-action="delete">
            <span class="intec-basket-grid intec-basket-grid-stretch">
                <span class="intec-basket-grid-item-auto">
                    <span class="intec-basket-notify-icon intec-basket-picture intec-basket-align-middle">
                        <svg width="2" height="11" viewBox="0 0 2 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.37207 7.19043H0.230469L0.141602 0.046875H1.46777L1.37207 7.19043ZM0.09375 9.36426C0.09375 9.15918 0.155273 8.98828 0.27832 8.85156C0.405924 8.71029 0.592773 8.63965 0.838867 8.63965C1.08496 8.63965 1.27181 8.71029 1.39941 8.85156C1.52702 8.98828 1.59082 9.15918 1.59082 9.36426C1.59082 9.56934 1.52702 9.74023 1.39941 9.87695C1.27181 10.0091 1.08496 10.0752 0.838867 10.0752C0.592773 10.0752 0.405924 10.0091 0.27832 9.87695C0.155273 9.74023 0.09375 9.56934 0.09375 9.36426Z" fill="#FFF"/>
                        </svg>
                    </span>
                </span>
                <span class="intec-basket-grid-item-auto intec-basket-grid-item-shrink">
                    <span class="intec-basket-notify-content">
                        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_UNAVAILABLE_REMOVE') ?>
                    </span>
                </span>
            </span>
        </button>
    </div>
{{/NOT_AVAILABLE}}
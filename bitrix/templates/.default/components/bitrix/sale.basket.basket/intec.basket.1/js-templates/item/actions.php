<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 */

?>
<div class="intec-basket-actions">
    <div class="intec-basket-actions-content intec-basket-grid intec-basket-grid-wrap intec-basket-grid-a-v-center intec-basket-grid-a-h-end">
        <?php if (ArrayHelper::isIn('DELAY', $arParams['COLUMNS_LIST'])) { ?>
            {{^DELAYED}}
                <div class="intec-basket-actions-item intec-basket-grid-item-auto">
                    <button class="intec-basket-action" data-entity="basket-item-add-delayed">
                        <span class="intec-basket-action-body intec-basket-grid intec-basket-grid-a-v-center intec-basket-grid-a-h-center">
                            <span class="intec-basket-action-icon intec-basket-action-icon-stroke intec-basket-picture intec-basket-grid-item-auto">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.4667 2.66667C12.58 2.66667 14 4.65334 14 6.50667C14 10.26 8.10667 13.3333 8 13.3333C7.89333 13.3333 2 10.26 2 6.50667C2 4.65334 3.42 2.66667 5.53333 2.66667C6.74667 2.66667 7.54 3.27334 8 3.80667C8.46 3.27334 9.25333 2.66667 10.4667 2.66667Z" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="intec-basket-action-content intec-basket-grid-item-auto">
                                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_DELAY') ?>
                            </span>
                        </span>
                    </button>
                </div>
            {{/DELAYED}}
        <?php } ?>
        <?php if (ArrayHelper::isIn('DELETE', $arParams['COLUMNS_LIST'])) { ?>
            <div class="intec-basket-actions-item intec-basket-grid-item-auto">
                <button class="intec-basket-action" data-entity="basket-item-delete">
                    <span class="intec-basket-action-body intec-basket-grid intec-basket-grid-a-v-center intec-basket-grid-a-h-center">
                        <span class="intec-basket-action-icon intec-basket-action-icon-fill intec-basket-picture intec-basket-grid-item-auto">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.80658 6.19745C9.63724 6.19745 9.5 6.33469 9.5 6.50403V12.2984C9.5 12.4676 9.63724 12.6049 9.80658 12.6049C9.97592 12.6049 10.1132 12.4676 10.1132 12.2984V6.50403C10.1132 6.33469 9.97592 6.19745 9.80658 6.19745Z"/>
                                <path d="M6.18744 6.19745C6.0181 6.19745 5.88086 6.33469 5.88086 6.50403V12.2984C5.88086 12.4676 6.0181 12.6049 6.18744 12.6049C6.35678 12.6049 6.49402 12.4676 6.49402 12.2984V6.50403C6.49402 6.33469 6.35678 6.19745 6.18744 6.19745Z"/>
                                <path d="M3.55237 5.35182V12.9052C3.55237 13.3517 3.71608 13.771 4.00206 14.0718C4.28672 14.3735 4.68288 14.5447 5.09748 14.5454H10.898C11.3127 14.5447 11.7089 14.3735 11.9934 14.0718C12.2794 13.771 12.4431 13.3517 12.4431 12.9052V5.35182C13.0116 5.20092 13.38 4.65172 13.3039 4.06838C13.2278 3.48517 12.7309 3.04889 12.1427 3.04877H10.573V2.66555C10.5748 2.34329 10.4474 2.03383 10.2192 1.80618C9.9911 1.57864 9.68117 1.45193 9.35891 1.45457H6.63659C6.31433 1.45193 6.00439 1.57864 5.77626 1.80618C5.54812 2.03383 5.4207 2.34329 5.4225 2.66555V3.04877H3.85284C3.2646 3.04889 2.76772 3.48517 2.69156 4.06838C2.61551 4.65172 2.98389 5.20092 3.55237 5.35182ZM10.898 13.9323H5.09748C4.5733 13.9323 4.16553 13.482 4.16553 12.9052V5.37876H11.83V12.9052C11.83 13.482 11.4222 13.9323 10.898 13.9323ZM6.03565 2.66555C6.03362 2.50592 6.09637 2.35227 6.20966 2.23958C6.32283 2.12688 6.47684 2.06497 6.63659 2.06772H9.35891C9.51866 2.06497 9.67267 2.12688 9.78584 2.23958C9.89913 2.35215 9.96188 2.50592 9.95985 2.66555V3.04877H6.03565V2.66555ZM3.85284 3.66193H12.1427C12.4474 3.66193 12.6945 3.90899 12.6945 4.21377C12.6945 4.51855 12.4474 4.76561 12.1427 4.76561H3.85284C3.54806 4.76561 3.301 4.51855 3.301 4.21377C3.301 3.90899 3.54806 3.66193 3.85284 3.66193Z"/>
                                <path d="M7.99603 6.19745C7.82669 6.19745 7.68945 6.33469 7.68945 6.50403V12.2984C7.68945 12.4676 7.82669 12.6049 7.99603 12.6049C8.16536 12.6049 8.3026 12.4676 8.3026 12.2984V6.50403C8.3026 6.33469 8.16536 6.19745 7.99603 6.19745Z"/>
                            </svg>
                        </span>
                        <span class="intec-basket-action-content intec-basket-grid-item-auto">
                            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_DELETE') ?>
                        </span>
                    </span>
                </button>
            </div>
        <?php } ?>
    </div>
</div>
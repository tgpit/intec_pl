<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>
{{#WARNINGS.length}}
    {{#WARNINGS}}
        <div class="intec-basket-notify intec-basket-notify-warning intec-basket-notify-closable" data-entity="basket-item-warning-node">
            <div class="intec-basket-notify-body">
                <span class="intec-basket-grid intec-basket-grid-stretch">
                    <span class="intec-basket-grid-item-auto">
                        <span class="intec-basket-notify-icon intec-basket-picture intec-basket-align-middle">
                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.6099 15.5965H11.3808L11.065 6H13.9257L13.6099 15.5965ZM12.4954 17.0992C12.9474 17.0992 13.3096 17.2355 13.582 17.5082C13.8607 17.7808 14 18.1295 14 18.5543C14 18.9728 13.8607 19.3184 13.582 19.591C13.3096 19.8637 12.9474 20 12.4954 20C12.0495 20 11.6873 19.8637 11.4087 19.591C11.1362 19.3184 11 18.9728 11 18.5543C11 18.1359 11.1362 17.7903 11.4087 17.5177C11.6873 17.2387 12.0495 17.0992 12.4954 17.0992Z" fill="#FFF"/>
                            </svg>
                        </span>
                    </span>
                    <span class="intec-basket-grid-item-auto intec-basket-grid-item-shrink">
                        <span class="intec-basket-notify-content" data-entity="basket-item-alert-text">
                            {{{.}}}
                        </span>
                    </span>
                    <span class="intec-basket-grid-item-auto">
                        <span class="intec-basket-notify-close intec-basket-picture intec-basket-align-middle" data-entity="basket-item-warning-close">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.72656 2.72729L9.27202 9.27275M9.27202 2.72729L2.72656 9.27275" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </span>
                </span>
            </div>
        </div>
    {{/WARNINGS}}
{{/WARNINGS.length}}
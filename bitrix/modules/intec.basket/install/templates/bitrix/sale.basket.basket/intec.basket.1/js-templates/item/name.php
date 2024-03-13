<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<h2 class="intec-basket-name">
    {{#DETAIL_PAGE_URL}}
        <a class="intec-basket-scheme-color-hover" href="{{DETAIL_PAGE_URL}}" target="_blank" data-entity="basket-item-name">
            {{NAME}}
        </a>
    {{/DETAIL_PAGE_URL}}
    {{^DETAIL_PAGE_URL}}
        <span data-entity="basket-item-name">
            {{NAME}}
        </span>
    {{/DETAIL_PAGE_URL}}
</h2>
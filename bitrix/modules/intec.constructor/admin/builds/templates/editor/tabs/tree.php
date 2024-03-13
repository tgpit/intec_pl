<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<component is="v-interface-menu-tab" code="tree" v-bind:name="$localization.getMessage('menu.items.tree.name')">
    <template v-slot:icon>
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22 14.5V9.5H8.25V10.75H5.75V7H17V2H2V7H4.5V19.5H8.25V22H22V17H8.25V18.25H5.75V12H8.25V14.5H22Z" stroke="none"/>
        </svg>
    </template>
    <template v-slot:default>

    </template>
</component>
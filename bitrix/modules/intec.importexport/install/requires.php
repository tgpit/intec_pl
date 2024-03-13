<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<div class="adm-info-message-wrap adm-info-message-red">
    <div class="adm-info-message">
        <div class="adm-info-message-title">
            <?= Loc::getMessage('intec.importexport.install.requires.error') ?>
            <a href="http://marketplace.1c-bitrix.ru/solutions/intec.core/" target="_blank">Intec.core</a>.
        </div>
        <div class="adm-info-message-icon"></div>
    </div>
</div>
<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<form action="<?= $APPLICATION->GetCurPage() ?>" method="POST">
    <?=bitrix_sessid_post(); ?>
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="go" value="Y">
    <h3><?= Loc::getMessage('intec.basket.install.uninstall.title') ?></h3>
    <div>
        <input type="submit" value="<?= Loc::getMessage('intec.basket.install.uninstall.go')?>">
    </div>
<form>
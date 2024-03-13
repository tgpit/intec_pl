<?php
if (!check_bitrix_sessid()) return;

IncludeModuleLangFile(__FILE__);

/** @global CMain $APPLICATION */
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?=LANG?>">
    <?=GetMessage("PEC_DELIVERY_DEL_TEXT")?><br>
    <input type="submit" name="" value="Ok">
</form>
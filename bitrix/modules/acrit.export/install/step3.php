<?php
if( !check_bitrix_sessid() ) return;
echo CAdminMessage::ShowMessage( array( "MESSAGE" => GetMessage( "MOD_INST_OK" ), "TYPE" => "OK" ) );
?>

<?
include $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/acrit.core/install/module_installed.php';
print $strExportInstalledMessage;
?>

<form action="<?=$APPLICATION->GetCurPage()?>" method="get">
	<p>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="submit" value="<?=GetMessage( "MOD_BACK" )?>" />
	</p>
</form>

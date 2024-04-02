<? if (!check_bitrix_sessid()) {
    return;
}
    \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
    
    echo \CAdminMessage::ShowNote(Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.MODULE_INSTALL_SUCCESS"));

?>
<table cellpadding="0" cellspacing="0" class="module_install_install">
    <tr>
        <td>
            <form action="/bitrix/admin/settings.php" type="GET" name="ok">
                <input type="hidden" name="mid" value="vkapi.market">
                <input type="hidden" name="mid_menu" value="1">
                <input type="hidden" name="lang" value="<?= LANG ?>">
                <input type="submit" name="btn" value="<?= \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.TO_SETTINGS_PAGE'); ?>"/>
            </form>
        </td>
    </tr>
</table>

<?php
IncludeModuleLangFile(__FILE__);

$types = $obProfileUtils->GetTypes();

$yandex_market = array(
    "ym_simple",
    "ym_vendormodel",
    "ym_book",
    "ym_audiobook",
    "ym_multimedia",
    "ym_tour",
    "ym_clothes",
    "ym_cosmetics",
    "ym_event_ticket",
    "ym_medicine",
    "ym_webmaster",
);

$yandex_realty = array(
    "ym_realty"
);

$yandex_dynamic = array(
    "ym_dynamic_simple",
    "ym_dynamic_vendormodel"
);

$google = array(
    "google",
    "google_online",
);
?>

<tr class="heading" align="center">
	<td colspan="2">
		<b><?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE" )?></b>
	</td>
</tr>
<tr>
	<td>
        <span id="hint_PROFILE[TYPE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[TYPE]' ), '<?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_LABEL_HELP" )?>' );</script>
        <?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_LABEL" )?>
    </td>
	<td>
		<select name="PROFILE[TYPE]">
            <? $selected = ""; ?>
			<optgroup label="<?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_YANDEX" )?>">
                <optgroup label="&nbsp;&nbsp;&nbsp;<?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_YANDEX_MARKET" )?>">
                    <?foreach( $yandex_market as $typeCode ){?>
                        <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                        <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                    <?}?>
                </optgroup>
                <optgroup label="&nbsp;&nbsp;&nbsp;<?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_YANDEX_REALTY" )?>">
                    <?foreach( $yandex_realty as $typeCode ){?>
                        <?$selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                        <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                    <?}?>
                </optgroup>
                <optgroup label="&nbsp;&nbsp;&nbsp;<?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_YANDEX_DYNAMIC" )?>">
                    <?foreach( $yandex_dynamic as $typeCode ){?>
                        <?$selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                        <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                    <?}?>
                </optgroup>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORT_EXPORTTYPE_GOOGLE" )?>">
                <?foreach( $google as $typeCode ){?>
                     <?$selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : "";?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                <?}?>
            </optgroup>
		</select>
	</td>
</tr>
<tr class="heading"><td colspan="2"><?=GetMessage( "ACRIT_EXPORT_EXPORT_REQUIREMENTS" );?></td></tr>
<tr>
    <td colspan="2" id="portal_requirements" style="text-align: center;">
        <a href="<?=$types[$arProfile["TYPE"]]["PORTAL_REQUIREMENTS"];?>" target="_blank"><?=$types[$arProfile["TYPE"]]["PORTAL_REQUIREMENTS"];?></a>
    </td>
</tr>
<tr class="heading"><td colspan="2"><?=GetMessage( "ACRIT_EXPORT_EXPORT_EXAMPLE" )?></td></tr>
<tr>
	<td colspan="2" style="background:#FDF6E3" id="description">
		<?if( $siteEncoding[SITE_CHARSET] != "utf8" )
			echo "<pre>",  htmlspecialchars( $types[$arProfile["TYPE"]]["EXAMPLE"], ENT_COMPAT | ENT_HTML401, $siteEncoding[SITE_CHARSET] ), "</pre>";
		else
			echo "<pre>",  htmlspecialchars( $types[$arProfile["TYPE"]]["EXAMPLE"] ), "</pre>";?>
	</td>
</tr>


<?php
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Json;
use Bitrix\Main\Text\Encoding;
?>
<script>
    $(document).ready(function() {
        function bindingJs() {
            const selectIblockId = $('select[name="iblock-id"]');
            const selectIblockSections = $('select[name="iblock-sections[]"]');
            const selectIblockElements = $('select[name="iblock-elements[]"]');
            const selectIblockProperty = $('select[name="select-property"]');

            function printSectionToSelect(elem, additionalString) {
                let id = elem['ID'];
                let name = elem['NAME'];
                let count = elem['ELEMENT_COUNT'];
                let additional = additionalString;

                selectIblockSections.append('<option value="'+id+'">'+additional+'['+id+'] '+name+' ('+count+')'+'</option>');

                if (elem['CHILDREN'].length > 0) {
                    additional = additional + ' . ';
                    let sections = elem['CHILDREN'];
                    for (var key in sections) {
                        printSectionToSelect(sections[key], additional);
                    }
                }
            }

            selectIblockId.on('change', function() {
                let iblockId = $(this).val();

                if (iblockId == 'none') {
                    BX.showWait();

                    selectIblockSections.children().not('[value="0"]').remove();
                    selectIblockSections.closest("tr").fadeOut(0);

                    selectIblockElements.children().remove();
                    selectIblockElements.closest("tr").fadeOut(0);

                    selectIblockProperty.children().not('.default-option').remove();
                    selectIblockProperty.closest("tr").fadeOut(0);
                    selectIblockProperty.closest("tr").fadeIn(200);

                    BX.closeWait();
                    return;
                } else {
                    BX.showWait();

                    let itemData = {
                        'iblockId': iblockId
                    };

                    $.ajax({
                        url: "/bitrix/tools/intec.ai/texts/ajax/get_sections.php",
                        type: "post",
                        data: itemData,
                        dataType: 'json',
                        success: function(json) {
                            if(json['OK'] == "Y") {
                                selectIblockSections.children().not('[value="0"]').remove();
                                selectIblockSections.closest("tr").fadeOut(0);
                                selectIblockSections.closest("tr").fadeIn(200);

                                selectIblockProperty.children().not('.default-option').remove();
                                selectIblockProperty.closest("tr").fadeOut(0);
                                selectIblockProperty.closest("tr").fadeIn(200);

                                let rootCount = json['ROOT_ELEMENT_COUNT'];
                                selectIblockSections.find('[value="0"]').text('<?= Loc::getMessage('intec.ai.admin.texts.iblock-sections.root') ?> ('+rootCount+')');

                                let sections = json['SECTIONS'];
                                for (var key in sections) {
                                    printSectionToSelect(sections[key], '');
                                };

                                let customProperties = json['CUSTOM_PROPERTIES'];
                                for (var key in customProperties) {
                                    customProperties[key];
                                    selectIblockProperty.append('<option value="[CUSTOM_PROPERTY]'+key+'">'+customProperties[key]+' ['+key+']</option>');
                                }

                                <?php
                                    $iblockSections = COption::GetOptionString("intec.ai", "ai.iblockSections");
                                    if (!empty($iblockSections)) {
                                        $iblockSectionsArray = Json::encode(unserialize($iblockSections), JSON_HEX_APOS, true);
                                ?>
                                    selectIblockSections.val(<?= $iblockSectionsArray ?>).change();
                                <?php } ?>

                                <?php
                                    $selectProperty = COption::GetOptionString("intec.ai", "ai.selectProperty");
                                    if (!empty($selectProperty)) {
                                ?>
                                    let selectPropertyValue = "<?= $selectProperty ?>";
                                    var optionExists = selectIblockProperty.find("option[value='" + selectPropertyValue + "']").length > 0;
                                    if(optionExists) {
                                        selectIblockProperty.val('<?= $selectProperty ?>').change();
                                    }
                                <?php } ?>
        
                                BX.closeWait();
                            }
                        }
                    });
                }
            });

            selectIblockSections.on('change', function() {
                BX.showWait();

                let sectionsId = $(this).val();
                let iblockId = parseInt(selectIblockId.val());

                let itemData = {
                    'sectionsId': JSON.stringify(sectionsId),
                    'iblockId': iblockId
                };

                $.ajax({
                    url: "/bitrix/tools/intec.ai/texts/ajax/get_elements.php",
                    type: "post",
                    data: itemData,
                    dataType: 'json',
                    success: function(json) {
                        if (json['OK'] == "Y") {
                            selectIblockElements.children().remove();
                            selectIblockElements.closest("tr").fadeOut(0);

                            let elements = json['ELEMENTS'];

                            if (elements.length > 0) selectIblockElements.closest("tr").fadeIn(200);

                            for (var key in elements) {
                                let id = elements[key]['ID'];
                                let name = elements[key]['NAME'];
                                selectIblockElements.append('<option value="'+id+'">['+id+'] '+name+'</option>');
                            }

                            <?php
                                $iblockElements = COption::GetOptionString("intec.ai", "ai.iblockElements");
                                if (!empty($iblockElements)) {
                                    $iblockElementsArray = Json::encode(unserialize($iblockElements), JSON_HEX_APOS, true);
                            ?>
                                selectIblockElements.val(<?= $iblockElementsArray ?>).change();
                            <?php } ?>
                        } else if (json['OK'] == "N") {
                            selectIblockElements.children().remove();
                            selectIblockElements.closest("tr").fadeOut(0);
                        }
                    }
                });

                BX.closeWait();
            });
        }

        bindingJs();

        const selectPropertyRadio = $('input[name="prompt-example"]');
        selectPropertyRadio.change(function() {
            let elemVal = $(this).attr('id');
            let text = '';
            switch (elemVal) {
                case ('rewritingAnons'):
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.rewritingAnons') ?>';
                    break;
                case ('rewritingDetail'):
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.rewritingDetail') ?>';
                    break;
                case ('h1'):
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.h1') ?>';
                    break;
                case ('title'):
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.title') ?>';
                    break;
                case ('keywords'):
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.keywords') ?>';
                    break;
                case ('description'):
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.description') ?>';
                    break;
                default:
                    text = '<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.default') ?>';
            }
            $('[name="prompt-mask"]').val(text);
        });

        <?php 
			$promptMask = COption::GetOptionString("intec.ai", "ai.promptMask");
            if (Encoding::detectUtf8($promptMask))
                $promptMask = Encoding::convertEncoding($promptMask, 'UTF-8', LANG_CHARSET);
            
			if (empty($promptMask)) {
		?>
			$('[name="prompt-mask"]').val('<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.default') ?>');
		<?php } else { ?>
			$('[name="prompt-mask"]').val(`<?= $promptMask ?>`);
		<?php } ?>
		
		<?php
			$iblockId = COption::GetOptionString("intec.ai", "ai.iblockId");
			if (!empty($iblockId)) {
		?>
			setTimeout(() => {
                $selectIblockId = $('select[name="iblock-id"]');
				$selectIblockId.val('<?= $iblockId ?>');
				$selectIblockId.change();
			}, "0");
		<?php } ?>
    });
</script>
<?php

if (!isset($bIsMenu))
    IntecImportexport::Initialize();

$sUrlRoot = '/bitrix/admin';

$arUrlTemplates = [
    'excel.export.templates' => $sUrlRoot.'/intec_importexport_export_templates.php?lang='.LANGUAGE_ID,
    'excel.export.templates.add' => $sUrlRoot.'/intec_importexport_export_templates_edit.php?lang='.LANGUAGE_ID,
    'excel.export.templates.edit' => $sUrlRoot.'/intec_importexport_export_templates_edit.php?template=#template#&lang='.LANGUAGE_ID,
    'excel.export.templates.create' => $sUrlRoot.'/intec_importexport_export_templates_edit.php?template=#template#&step=#step#&lang='.LANGUAGE_ID,
    'excel.export.templates.create.with.error' => $sUrlRoot.'/intec_importexport_export_templates_edit.php?template=#template#&isNew=#isNew#&step=#step#&lang='.LANGUAGE_ID,
    'excel.export.templates.copy' => $sUrlRoot.'/intec_importexport_export_templates_edit.php?template=#template#&action=copy&lang='.LANGUAGE_ID,

    'excel.import.templates' => $sUrlRoot.'/intec_importexport_import_templates.php?lang='.LANGUAGE_ID,
    'excel.import.templates.add' => $sUrlRoot.'/intec_importexport_import_templates_edit.php?lang='.LANGUAGE_ID,
    'excel.import.templates.edit' => $sUrlRoot.'/intec_importexport_import_templates_edit.php?template=#template#lang='.LANGUAGE_ID,
    'excel.import.templates.create' => $sUrlRoot.'/intec_importexport_import_templates_edit.php?template=#template#&step=#step#&lang='.LANGUAGE_ID,
    'excel.import.templates.create.with.error' => $sUrlRoot.'/intec_importexport_import_templates_edit.php?template=#template#&isNew=#isNew#&step=#step#&lang='.LANGUAGE_ID,
    'excel.import.templates.copy' => $sUrlRoot.'/intec_importexport_import_templates_edit.php?template=#template#&action=copy&lang='.LANGUAGE_ID,
];

unset($sUrlRoot);
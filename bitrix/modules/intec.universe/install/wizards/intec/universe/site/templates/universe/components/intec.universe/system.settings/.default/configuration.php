<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\core\net\Url;
use intec\constructor\models\Font;

/**
 * @var array $arParams
 * @var array $arResult
 * @var boolean $bSave
 * @var IntecSystemSettingsComponent $this
 */

global $APPLICATION;

$arParams = ArrayHelper::merge([
    'VARIABLES_VARIANT' => 'variant'
], $arParams);

$arResult['VARIABLES']['VARIANT'] = $arParams['VARIABLES_VARIANT'];

foreach ($arResult['PROPERTIES'] as $sKey => &$arProperty) {
    $sType = ArrayHelper::getValue($arProperty, 'type');

    if ($sType === 'blocks') {
        $arBlocks = ArrayHelper::getValue($arProperty, 'blocks');
        $arValue = $arProperty['value'];

        if (!Type::isArray($arBlocks))
            $arBlocks = [];

        if (!Type::isArray($arValue))
            $arValue = [];

        foreach ($arBlocks as $sBlockKey => $arBlock) {
            $bActive = ArrayHelper::getValue($arValue, [$sBlockKey, 'active']);
            $arTemplates = ArrayHelper::getValue($arBlock, 'templates');
            $sTemplate = ArrayHelper::getValue($arValue, [$sBlockKey, 'template']);

            if ($bActive === null) {
                $bActive = true;
            } else {
                $bActive = Type::toBoolean($bActive);
            }

            if (empty($arTemplates) || !Type::isArray($arTemplates)) {
                $sTemplate = null;
            } else {
                $bFirst = true;
                $bSet = false;
                $sTemplateFirst = null;

                foreach ($arTemplates as $arTemplate) {
                    $sTemplateValue = ArrayHelper::getValue($arTemplate, 'value');

                    if ($sTemplateValue === null)
                        continue;

                    if ($bFirst)
                        $sTemplateFirst = $sTemplateValue;

                    if ($sTemplate == $sTemplateValue) {
                        $bSet = true;
                        $sTemplate = $sTemplateValue;

                        break;
                    }

                    $bFirst = false;
                }

                if (!$bSet)
                    $sTemplate = $sTemplateFirst;

                unset($arTemplate);
                unset($sTemplateFirst);
                unset($bSet);
                unset($bFirst);
            }

            $arValue[$sBlockKey] = [
                'active' => $bActive,
                'template' => $sTemplate
            ];

            unset($sTemplate);
            unset($arTemplates);
            unset($bActive);
        }

        $arProperty['value'] = $arValue;

        unset($arValue);
        unset($arBlocks);
    }

    unset($sType);
}

unset($arProperty);

if ($arResult['MODE'] === 'configure') {
    $oFonts = Font::findAvailable()->indexBy('code');
    $oFont = $arResult['PROPERTIES']['template-font']['value'];

    if (!empty($oFont)) {
        /** @var Font $oFont */
        $oFont = $oFonts->get($oFont);

        if (!empty($oFont))
            $oFont->register();
    }
}

/* Получение вариантов настроек */

$arResult['VARIANTS'] = [];
$sPath = __DIR__.'/variants';

if (FileHelper::isDirectory($sPath)) {
    $arVariants = FileHelper::getDirectoryEntries($sPath, false);

    foreach ($arVariants as $sVariant) {
        $sVariantPath = $sPath.'/'.$sVariant;

        if (
            !FileHelper::isDirectory($sVariantPath) ||
            !FileHelper::isFile($sVariantPath.'/meta.php')
        ) continue;

        $arVariant = include($sVariantPath.'/meta.php');

        if (
            !Type::isArray($arVariant) ||
            empty($arVariant['name']) ||
            empty($arVariant['values']) ||
            !Type::isArray($arVariant['values'])
        ) continue;

        if (!isset($arVariant['sort']))
            $arVariant['sort'] = 500;

        $arVariant['code'] = $sVariant;
        $arVariant['picture'] = null;
        $arVariant['sort'] = Type::toInteger($arVariant['sort']);

        if (FileHelper::isFile($sVariantPath.'/picture.png'))
            $arVariant['picture'] = (new Path($sVariantPath.'/picture.png'))
                ->toRelative()
                ->asAbsolute()
                ->getValue('/');

        $arVariant['link'] = new Url(Core::$app->request->getUrl());
        $arVariant['link']->getQuery()->set($arResult['VARIABLES']['VARIANT'], $arVariant['code']);
        $arVariant['link'] = $arVariant['link']->build();

        $arResult['VARIANTS'][$arVariant['code']] = $arVariant;
    }

    unset($sVariant);
    unset($sVariantPath);
    unset($arVariant);
    unset($arVariants);
}

uasort($arResult['VARIANTS'], function ($arVariant1, $arVariant2) {
    return $arVariant1['sort'] - $arVariant2['sort'];
});

/* Применение варианта от GET-запроса */

if ($arResult['MODE'] === IntecSystemSettingsComponent::MODE_CONFIGURE && !empty($arResult['VARIANTS'])) {
    $arVariant = Core::$app->request->get($arResult['VARIABLES']['VARIANT']);

    if (!empty($arVariant) && !empty($arResult['VARIANTS'][$arVariant])) {
        $arVariant = $arResult['VARIANTS'][$arVariant];
        $arResult['ACTION'] = null;

        foreach ($arVariant['values'] as $sKey => $mValue)
            $arResult['PROPERTIES'][$sKey]['value'] = $mValue;

        if ($USER->IsAdmin()) {
            $this->saveToFile($arVariant['values']);
        } else {
            $this->saveToSession($arVariant['values']);
        }

        $oUrl = new Url(Core::$app->request->getUrl());
        $oUrl->getQuery()->removeAt($arResult['VARIABLES']['VARIANT']);

        LocalRedirect($oUrl->build(), true);
    }

    unset($arVariant);
}
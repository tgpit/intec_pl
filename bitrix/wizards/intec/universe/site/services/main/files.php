<? include(__DIR__.'/../.begin.php') ?>
<?

use Bitrix\Main\Loader;
use intec\core\base\Collection;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\io\Path;

/**
 * @var Collection $data
 * @var CWizardBase $wizard
 * @var CWizardStep $this
 */

$pages = [];

/** CUSTOM START */
$pages['index/wide'] = 'main.wide';
$pages['index/narrow.left'] = 'main.narrow';
/** CUSTOM END */

$rewrite = $mode !== WIZARD_MODE_UPDATE;

$macros = $data->get('macros');
$macros['SITE_DIR'] = WIZARD_SITE_DIR;
$macros['SITE_DIR_MACROS'] = WIZARD_SITE_DIR;
$macros['SITE_NAME'] = Html::encode($wizard->GetVar('siteName'));
$macros['SITE_PHONE'] = Html::encode($wizard->GetVar('sitePhone'));
$macros['SITE_MAIL'] = Html::encode($wizard->GetVar('siteMail'));
$macros['SITE_ADDRESS'] = Html::encode($wizard->GetVar('siteAddress'));
$macros['SITE_META_DESCRIPTION'] = Html::encode($wizard->GetVar('siteMetaDescription'));
$macros['SITE_META_KEYWORDS'] = Html::encode($wizard->GetVar('siteMetaKeywords'));

$pathFrom = FileHelper::normalizePath(WIZARD_ABSOLUTE_PATH.'/site/public/'.LANGUAGE_ID, '/').'/';
$pathTo = FileHelper::normalizePath(WIZARD_SITE_PATH, '/').'/';

if (FileHelper::isDirectory($pathFrom)) {
    CopyDirFiles($pathFrom, $pathTo, $rewrite, $recursive = true, $remove = false);

    $buildPath = Path::from(WIZARD_ABSOLUTE_PATH.'/site/builds/'.WIZARD_TEMPLATE_ID);

    if (FileHelper::isDirectory($buildPath->getValue('/'))) {
        $generatePageBlockProperties = function ($properties) {
            $result = [];
            $handler = function ($properties, $path = null) use (&$handler, &$result) {
                if (Type::isArray($properties) && !isset($properties['value']) && !isset($properties['measure'])) {
                    foreach ($properties as $key => $value) {
                        $handler($value, ($path ? $path.'-' : '').$key);
                    }
                } else {
                    if (empty($path))
                        return;

                    if (Type::isArray($properties)) {
                        $properties = ArrayHelper::merge(['value' => null, 'measure' => null], $properties);
                        $properties = $properties['value'].$properties['measure'];
                    }

                    $result[$path] = $properties;
                }
            };

            $handler($properties);

            return $result;
        };

        $generatePageBlockComponentContent = function ($blockProperties, $componentCode, $componentTemplate, $componentProperties) use (&$generatePageBlockProperties) {
            $blockProperties = $generatePageBlockProperties($blockProperties);

            $result = '<?php if (!defined(\'B_PROLOG_INCLUDED\') || B_PROLOG_INCLUDED !== true) die(); ?>'."\r\n".'<?php'."\r\n\r\n";

            $result .= 'use intec\core\collections\Arrays;'."\r\n";
            $result .= 'use intec\core\helpers\Html;'."\r\n";
            $result .= 'use intec\core\io\Path;'."\r\n\r\n";
            $result .= '/**'."\r\n";
            $result .= ' * @var Arrays $blocks'."\r\n";
            $result .= ' * @var array $block'."\r\n";
            $result .= ' * @var array $data'."\r\n";
            $result .= ' * @var string $page'."\r\n";
            $result .= ' * @var Path $path'."\r\n";
            $result .= ' * @global CMain $APPLICATION'."\r\n";
            $result .= ' */'."\r\n\r\n";
            $result .= '?>'."\r\n";

            if (!empty($blockProperties)) {
                $result .= '<?= Html::beginTag(\'div\', [\'style\' => ' . var_export($blockProperties, true) . ']) ?>' . "\r\n";
            } else {
                $result .= '<?= Html::beginTag(\'div\') ?>' . "\r\n";
            }

            $result .= '<?php $APPLICATION->IncludeComponent('.
                var_export($componentCode, true).', '.
                var_export($componentTemplate, true).', '.
                var_export($componentProperties, true).', '.
                'false'.
            ') ?>'."\r\n";

            $result .= '<?= Html::endTag(\'div\') ?>'."\r\n";
            $result = Encoding::convert($result, null, Encoding::UTF8);

            return $result;
        };

        foreach ($pages as $pagePath => $pageCode) {
            $buildPagePath = $buildPath->add('pages/'.$pageCode);
            $buildPageBlocksPath = $buildPagePath->add('blocks');
            $pageBlocks = FileHelper::getDirectoryEntries($buildPageBlocksPath->getValue(), false);

            foreach ($pageBlocks as $pageBlockCode) {
                $pageBlockCode = Path::getNameFrom($pageBlockCode, false);
                $buildPageBlockPath = $buildPageBlocksPath->add($pageBlockCode.'.php');
                $pageBlock = include($buildPageBlockPath->getValue());

                if ($pageBlock['type'] !== 'simple' && $pageBlock['type'] !== 'variable')
                    continue;

                if (isset($pageBlock['skip']) && $pageBlock['skip'] === true)
                    continue;

                if ($pageBlock['type'] === 'simple') {
                    if (empty($pageBlock['component']))
                        continue;

                    $pageBlockPath = Path::from($pathTo)->add('include/'.$pagePath.'/'.$pageBlockCode.'.php');

                    if ($mode === WIZARD_MODE_UPDATE && FileHelper::isFile($pageBlockPath->getValue()))
                        continue;

                    FileHelper::setFileData($pageBlockPath->getValue(), $generatePageBlockComponentContent(
                        $pageBlock['properties'],
                        $pageBlock['component']['code'],
                        $pageBlock['component']['template'],
                        $pageBlock['component']['properties']
                    ));
                } else {
                    foreach ($pageBlock['variants'] as $pageBlockVariantCode => $pageBlockVariant) {
                        if (empty($pageBlockVariant['component']))
                            continue;

                        if (isset($pageBlockVariant['skip']) && $pageBlockVariant['skip'] === true)
                            continue;

                        $pageBlockVariantPath = Path::from($pathTo)->add('include/'.$pagePath.'/'.$pageBlockCode.'/'.$pageBlockVariantCode.'.php');

                        if ($mode === WIZARD_MODE_UPDATE && FileHelper::isFile($pageBlockVariantPath->getValue()))
                            continue;

                        FileHelper::setFileData($pageBlockVariantPath->getValue(), $generatePageBlockComponentContent(
                            $pageBlockVariant['properties'],
                            $pageBlockVariant['component']['code'],
                            $pageBlockVariant['component']['template'],
                            $pageBlockVariant['component']['properties']
                        ));
                    }
                }
            }
        }
    }

    CWizardUtil::ReplaceMacrosRecursive($pathTo, $macros);
    CWizardUtil::ReplaceMacros($pathTo.'_index.php', $macros);
    CWizardUtil::ReplaceMacros($pathTo.'.section.php', $macros);

    if ($mode === WIZARD_MODE_UPDATE)
        if (FileHelper::isFile($pathTo.'_index.php'))
            unlink($pathTo.'_index.php');
}

$data->set('macros', $macros);
?>
<? include(__DIR__.'/../.end.php') ?>
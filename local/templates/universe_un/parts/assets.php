<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;
use intec\Core;
use intec\core\base\Collection;
use intec\core\helpers\FileHelper;
use intec\constructor\models\Build;
use intec\constructor\models\build\File;

/**
 * @var Build $build
 * @var Collection $properties
 * @var string $directory
 */

$asset = Asset::getInstance();
$files = $build->getFiles();
$web = Core::$app->web;

if (FileHelper::isFile($directory.'/parts/custom/assets.start.php'))
    include($directory.'/parts/custom/assets.start.php');

$web->js->loadExtensions(['popup']);

if (FileHelper::isFile($directory.'/css/custom.css'))
    $files[] = new File($build, File::TYPE_CSS, 'css/custom.css');

if (FileHelper::isFile($directory.'/css/custom.scss'))
    $files[] = new File($build, File::TYPE_SCSS, 'css/custom.scss');

if (FileHelper::isFile($directory.'/js/custom.js'))
    $files[] = new File($build, File::TYPE_JAVASCRIPT, 'js/custom.js');

$hash = md5(serialize($properties->asArray()));

/**
 * @var File[] $files
 */
foreach ($files as $file) {
    if ($file->getType() === File::TYPE_JAVASCRIPT) {
        Core::$app->web->js->addFile($file->getPath(true, '/'));
    } else if ($file->getType() === File::TYPE_CSS) {
        Core::$app->web->css->addFile($file->getPath(true, '/'));
    } else if ($file->getType() === File::TYPE_SCSS) {
        $fileCache = Cache::createInstance();
        $fileData = null;

        if ($fileCache->initCache(360000, $file->getPath(true, '/').$hash, SITE_ID.'/templates/'.SITE_TEMPLATE_ID.'/scss')) {
            $fileData = $fileCache->getVars();
        } else if ($fileCache->startDataCache()) {
            $fileData = [
                'hash' => $hash,
                'content' => Core::$app->web->scss->compileFile(
                    $file->getPath(),
                    null,
                    $properties->asArray(),
                    true
                )
            ];

            $fileCache->endDataCache($fileData);
        }

        if (!empty($fileData))
            Core::$app->web->css->addString($fileData['content']);

        unset($fileData);
        unset($fileCache);
    } else if ($file->getType() === File::TYPE_VIRTUAL) {
        $asset->addString($file->getContent(), false, AssetLocation::AFTER_JS);
    }
}

if (FileHelper::isFile($directory.'/parts/custom/assets.end.php'))
    include($directory.'/parts/custom/assets.end.php');

unset($hash);
unset($web);
unset($files);
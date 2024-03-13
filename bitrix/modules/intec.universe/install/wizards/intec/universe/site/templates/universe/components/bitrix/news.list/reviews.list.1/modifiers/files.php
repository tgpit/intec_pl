<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;

/**
 * @var array $arCollection
 */

$arPictures = Arrays::fromDBResult(CFile::GetList([], [
    '@ID' => implode(',', $arCollection['FILES'])
]))->indexBy('ID')->each(function ($key, &$value) {
    $value['SRC'] = CFile::GetFileSRC($value);
});

if (!$arPictures->isEmpty())
    $arCollection['FILES'] = $arPictures->asArray();

unset($arPictures);
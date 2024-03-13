<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var array $arCollection
 */

$arVideo = new ElementsQuery();

$hParseVideoUrl = function ($url) {
    $url = new Url($url);
    $video = null;

    if ($url->getQuery()->exists('v'))
        $video = $url->getQuery()->get('v');

    if (empty($video))
        $video = $url->getPath()->getLast();

    if (empty($video))
        return null;

    return [
        'ID' => $video,
        'LINKS' => [
            'embed' => 'https://www.youtube.com/embed/'.$video,
            'page' => 'https://www.youtube.com/watch?v='.$video
        ],
        'PICTURES' => [
            'mqdefault' => 'https://img.youtube.com/vi/'.$video.'/mqdefault.jpg',
            'hqdefault' => 'https://img.youtube.com/vi/'.$video.'/hqdefault.jpg',
            'sddefault' => 'https://img.youtube.com/vi/'.$video.'/sddefault.jpg',
            'maxresdefault' => 'https://img.youtube.com/vi/'.$video.'/maxresdefault.jpg'
        ]
    ];
};

$arVideo->setIBlockType($arParams['VIDEO_IBLOCK_TYPE'])
    ->setIBlockId($arParams['VIDEO_IBLOCK_ID'])
    ->setIBlockElementsId($arCollection['VIDEO'])
    ->setWithProperties(false)
    ->setFilter([
        'ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R'
    ])
    ->setSelect([
        'ID',
        'IBLOCK_ID',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'PROPERTY_'.strtoupper($arParams['VIDEO_PROPERTY_URL'])
    ]);

$arVideo = $arVideo->execute()->asArray();
$arVideoResult = [];

foreach ($arVideo as $video) {
    if (!empty($video['PROPERTY_'.strtoupper($arParams['VIDEO_PROPERTY_URL']).'_VALUE'])) {
        $result = [
            'PICTURE' => null,
            'SERVICE' => []
        ];

        $url = $video['PROPERTY_'.strtoupper($arParams['VIDEO_PROPERTY_URL']).'_VALUE'];

        if (Type::isArray($url))
            $url = ArrayHelper::getFirstValue($url);

        $url = $hParseVideoUrl($url);

        if (empty($url))
            continue;

        $result['SERVICE'] = $url;

        if (ArrayHelper::isIn('detail', $arVisual['VIDEO']['SOURCES']) && !empty($video['DETAIL_PICTURE'])) {
            $result['PICTURE'] = $video['DETAIL_PICTURE'];
            $arCollection['FILES'][] = $video['DETAIL_PICTURE'];
        } else if (ArrayHelper::isIn('preview', $arVisual['VIDEO']['SOURCES']) && !empty($video['PREVIEW_PICTURE'])) {
            $result['PICTURE'] = $video['PREVIEW_PICTURE'];
            $arCollection['FILES'][] = $video['PREVIEW_PICTURE'];
        }

        $arVideoResult[$video['ID']] = $result;

        unset($result, $url);
    }
}

if (!empty($arVideoResult))
    $arCollection['VIDEO'] = $arVideoResult;

unset($arVideo, $arVideoResult, $video, $hParseVideoUrl);
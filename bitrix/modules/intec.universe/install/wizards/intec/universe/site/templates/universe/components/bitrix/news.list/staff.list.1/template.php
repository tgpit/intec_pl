<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'CONTACTS' => [
        'PHONE' => FileHelper::getFileData(__DIR__.'/svg/contact.phone.svg'),
        'EMAIL' => FileHelper::getFileData(__DIR__.'/svg/contact.email.svg')
    ],
    'SOCIAL' => [
        'VK' => FileHelper::getFileData(__DIR__.'/svg/social.vk.svg'),
        'FB' => FileHelper::getFileData(__DIR__.'/svg/social.fb.svg'),
        'INST' => FileHelper::getFileData(__DIR__.'/svg/social.inst.svg'),
        'TW' => FileHelper::getFileData(__DIR__.'/svg/social.tw.svg'),
        'SKYPE' => FileHelper::getFileData(__DIR__.'/svg/social.skype.svg')
    ]
];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

/**
 * @var Closure $vItem(&$arItem)
 */
$vItem = include(__DIR__.'/parts/item.php');

?>
<div class="ns-bitrix c-news-list c-news-list-staff-list-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['SHOW']['TOP']) { ?>
                <div class="news-list-pagination-top" data-pagination-num="<?= $arResult['NAVIGATION']['NUMBER'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => 'news-list-content',
                'data' => [
                    'picture' => $arVisual['PICTURE']['SHOW'] ? 'true' : 'false'
                ]
            ]) ?>
                <?php if ($arVisual['SECTIONS']['MODE'])
                    include(__DIR__.'/parts/sections.php');
                else
                    include(__DIR__.'/parts/items.php');
                ?>
            <?= Html::endTag('div') ?>
            <?php if ($arVisual['NAVIGATION']['SHOW']['BOTTOM']) { ?>
                <div class="news-list-pagination-bottom" data-pagination-num="<?= $arResult['NAVIGATION']['NUMBER'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($arResult['FORM']['ASK']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>

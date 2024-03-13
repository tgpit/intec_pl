<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\Core;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var CAllMain $APPLICATION
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

include(__DIR__.'/parts/list.php');

$arSort['PROPERTY'] = Core::$app->request->get('sort');

if (!empty($arSort['PROPERTY'])) {
    foreach ($arResult['SECTIONS'] as $arSection) {
        if ($arSection['CODE'] == $arSort['PROPERTY']) {
            $APPLICATION->SetTitle($arSection['NAME']);
        }
    }
}

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news',
        'c-news-images-1',
        'p-news'
    ]
]) ?>
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'intec-grid' => [
                        '' => true,
                        'wrap' => $arVisual['MENU']['POSITION'] == 'top' ? true : false,
                        '1024-wrap' => $arVisual['MENU']['POSITION'] == 'left' ? true : false,
                        'a-v-start' => true,
                    ],
                    'news-content' => true
                ], true)
            ]) ?>
                <?php if (!empty($arResult['IBLOCK_DESCRIPTION'])) { ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'news-description' => true,
                            'news-description-mobile' => true,
                            'news-description-mobile-menu-top' => $arVisual['MENU']['POSITION'] == 'top' ? true : false
                        ], true)
                    ]) ?>
                        <?= $arResult['IBLOCK_DESCRIPTION'] ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>

                <?php if (!empty($arResult['IBLOCK_DESCRIPTION']) && $arVisual['MENU']['POSITION'] == 'top') { ?>
                    <div class="news-description news-description-desktop news-description-desktop-menu-top">
                        <?= $arResult['IBLOCK_DESCRIPTION'] ?>
                    </div>
                <?php } ?>
                <?php
                    if (!empty($arResult['SECTIONS'])) {
                        include(__DIR__.'/parts/menu.php');
                    }
                ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid-item' => [
                            '' => true,
                            '1' => $arVisual['MENU']['POSITION'] == 'top',
                            '1024-1' => $arVisual['MENU']['POSITION'] == 'left'
                        ]
                    ], true)
                ])?>
                    <?php if (!empty($arResult['IBLOCK_DESCRIPTION']) && $arVisual['MENU']['POSITION'] == 'left') { ?>
                        <div class="news-description news-description-desktop">
                            <?= $arResult['IBLOCK_DESCRIPTION'] ?>
                        </div>
                    <?php } ?>
                    <?php if ($arList['SHOW']) {
                        if (!empty($arSort['PROPERTY'])) {
                            if (is_numeric($arSort['PROPERTY'])) {
                                $GLOBALS['arrFilterMenu'] = ['SECTION_ID' => $arSort['PROPERTY']];
                            } else if (is_string($arSort['PROPERTY'])) {
                                $GLOBALS['arrFilterMenu'] = ['SECTION_CODE' => $arSort['PROPERTY']];
                            }
                        }

                         $APPLICATION->IncludeComponent(
                            'bitrix:news.list',
                            $arList['TEMPLATE'],
                            $arList['PARAMETERS'],
                            $component
                        ); ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>

<?php include(__DIR__.'/parts/script.php'); ?>
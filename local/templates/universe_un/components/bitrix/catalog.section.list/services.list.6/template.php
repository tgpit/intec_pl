<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use Bitrix\Main\Localization\Loc;

/**
 * @var $arResult
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-services-list-6'
    ],
    'data' => [
        'wide' => $arVisual['WIDE'] ? 'true' : 'false'
    ]
]) ?>
    <?php if($arVisual['DESCRIPTION']['SHOW'] && !empty($arVisual['DESCRIPTION']['VALUE'])) { ?>
        <div class="catalog-section-list-description">
            <?php if($arVisual['DESCRIPTION']['LINK']) {
                $APPLICATION->IncludeComponent(
                    'bitrix:main.include',
                    '', [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => $arVisual['DESCRIPTION']['VALUE'],
                    'EDIT_TEMPLATE' => ''
                ],
                    $component
                );
            } else {
                echo $arVisual['DESCRIPTION']['VALUE'];
            } ?>
        </div>
    <?php } ?>
    <div class="catalog-section-list-items">
        <?php foreach ($arResult['SECTIONS'] as $arSection) {

            $sId = $sTemplateId.'_'.$arSection['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arSection['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

            $arSection['DESCRIPTION'] = Html::stripTags($arSection['DESCRIPTION']);
            $arPicture = $arSection['PICTURE'];

            if (!empty($arPicture)) {
                $arPicture = CFile::ResizeImageGet($arPicture, [
                    'width' => 450,
                    'height' => 450
                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                if (!empty($arPicture))
                    $arPicture = [
                        'ALT' => $arSection['PICTURE']['ALT'],
                        'SRC' => $arPicture['src'],
                        'TITLE' => $arSection['PICTURE']['TITLE']
                    ];
            }

            if (empty($arPicture)) {
                $arPicture = [
                    'ALT' => null,
                    'SRC' => SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                    'TITLE' => null
                ];
            }

        ?>
            <div class="catalog-section-list-item">
                <div id="<?= $sAreaId ?>" class="catalog-section-list-item-wrapper intec-grid intec-grid-wrap">
                    <div class="catalog-section-list-item-information intec-grid-item intec-grid-item-750-1">
                        <?= Html::tag('a', $arSection['NAME'], [
                            'class' => 'catalog-section-list-item-name',
                            'href' => $arSection['SECTION_PAGE_URL']
                        ]) ?>
                        <?= $arVisual['ELEMENTS']['SHOW'] ? $arSection['ELEMENT_CNT'] : null ?>
                        <?php if (!empty($arSection['DESCRIPTION']) && $arVisual['SECTION']['DESCRIPTION']['SHOW']) { ?>
                            <div class="catalog-section-list-item-description">
                                <?= StringHelper::truncate($arSection['DESCRIPTION'], 300) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['ELEMENTS']['SHOW']) { ?>
                            <div class="catalog-section-list-item-elements">
                                <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                                    <?php if ($arSection['ID'] == $arItem['IBLOCK_SECTION_ID']) { ?>
                                        <div class="catalog-section-list-item-element">
                                            <?= Html::tag('a', $arItem['NAME'], [
                                                'class' => 'catalog-section-item-element-name',
                                                'href' => $arItem['DETAIL_PAGE_URL']
                                            ]) ?>
                                        </div>
                                    <?php } elseif (!empty($arSection['LIST_ID'])) { ?>
                                        <?php foreach ($arSection['LIST_ID'] as $sId) { ?>
                                            <?php if ($sId == $arItem['IBLOCK_SECTION_ID']) { ?>
                                                <div class="catalog-section-list-item-element">
                                                    <?= Html::tag('a', $arItem['NAME'], [
                                                        'class' => 'catalog-section-item-element-name',
                                                        'href' => $arItem['DETAIL_PAGE_URL']
                                                    ]) ?>
                                                </div>
                                                <?php break; ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="catalog-section-list-button-wrap">
                            <?= Html::tag('a',
                                Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_MORE_DETAILS'), [
                                    'class' => [
                                        'intec-ui',
                                        'intec-ui-control-button',
                                        'intec-ui-mod-transparent',
                                        'intec-ui-scheme-current',
                                        'intec-ui-mod-round-2'
                                    ],
                                    'href' => $arSection['SECTION_PAGE_URL']
                                ]) ?>
                        </div>
                    </div>
                    <div class="catalog-section-list-item-picture intec-grid-item intec-grid-item-750-1">
                        <?= Html::tag('a', null, [
                            'href' => $arSection['SECTION_PAGE_URL'],
                            'class' => 'intec-image-effect',
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SRC'] : null
                            ],
                            'style' => [
                                'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$arPicture['SRC'].'\')'
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>
<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var Closure $vItems()
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];
$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

include(__DIR__ . '/parts/items.php');

$arForm = $arResult['FORM'];
$arForm['PARAMETERS'] = [
    'id' => $arForm['ID'],
    'template' => $arForm['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
        'CONSENT_URL' => $arForm['CONSENT']
    ],
    'settings' => [
        'title' => $arForm['TITLE']
    ],
    'fields' => [
        $arForm['FIELD'] => null
    ]
];

if (empty($arForm['BUTTON']))
    $arForm['BUTTON'] = Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_4_ORDER_BUTTON_DEFAULT');

$arSvg = [
    'LINK' => FileHelper::getFileData(__DIR__.'/svg/icon.link.svg'),
    'SEE_ALL_MOBILE' => FileHelper::getFileData(__DIR__ . '/svg/header.list.mobile.svg')
];

?>
<div class="widget c-projects c-projects-template-4" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['BUTTON_ALL']['SHOW'] || $arVisual['SLIDER']['NAV']) { ?>
                <?= Html::beginTag('div', [
                    'class' => 'widget-header',
                    'data' => [
                        'link-all' => $arVisual['BUTTON_ALL']['SHOW'] ? 'true' : 'false',
                        'slider-nav' => $arVisual['SLIDER']['NAV'] ? 'true' : 'false'
                    ]
                ]) ?>
                    <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?> intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                'class' => 'intec-grid-item'
                            ]) ?>
                        <?php } ?>
                        <?php if ($arVisual['BUTTON_ALL']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('a', [
                                    'class' => 'widget-list',
                                    'href' => $arVisual['BUTTON_ALL']['LINK']
                                ])?>
                                    <span class="widget-list-desktop intec-cl-text-light-hover">
                                        <?= $arVisual['BUTTON_ALL']['TEXT'] ?>
                                    </span>
                                    <span class="widget-list-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                        <?= $arSvg['SEE_ALL_MOBILE'] ?>
                                    </span>
                                <?= Html::endTag('a') ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW'] || $arVisual['SLIDER']['NAV']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?> intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                                    'class' => 'intec-grid-item'
                                ]) ?>
                            <?php } ?>
                            <?php if ($arVisual['SLIDER']['NAV']) { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::tag('div','', [
                                        'class' => 'widget-items-navigation',
                                        'data' => [
                                            'role' => 'slider.navigation'
                                        ]
                                    ]) ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'owl-carousel' => $arVisual['SLIDER']['USE'],
                        'intec-grid' => [
                            '' => true,
                            'wrap' => true,
                            'i-8' => !$arVisual['SLIDER']['USE'],
                            'a-v-stretch' => true
                        ]
                    ], true),
                    'data' => [
                        'role' => 'slider',
                        'form-use' => $arForm['USE'] ? 'true' : 'false',
                        'grid' => $arVisual['COLUMNS']
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {
                        $sId = $sTemplateId . '_' . $arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 700,
                                'height' => 700
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';

                        $arData = $arItem['DATA'];

                        ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-2' => $arVisual['COLUMNS'] > 2,
                                    '768-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                                <?= Html::tag($sTag, '', [
                                    'class' => [
                                        'widget-item-picture',
                                        'intec-image-effect'
                                    ],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\'' . $sPicture . '\')' : null
                                    ],
                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                ]) ?>
                                <?php if ($arVisual['ADDITIONAL']['SHOW'] && !empty($arData['ADDITIONAL'])) { ?>
                                    <div class="widget-item-additional-list">
                                        <?php foreach ($arData['ADDITIONAL'] as $arAdditional) { ?>
                                            <div class="widget-item-additional">
                                                <div class="widget-item-additional-wrapper">
                                                    <div class="widget-item-additional-value intec-cl-text">
                                                        <?= $arAdditional['VALUE'] ?>
                                                    </div>
                                                    <?php if (!empty($arAdditional['DESCRIPTION'])) { ?>
                                                        <div class="widget-item-additional-description">
                                                            <?= $arAdditional['DESCRIPTION'] ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?= Html::tag($sTag, $arItem['NAME'], [
                                    'class' => [
                                        'widget-item-name',
                                        $sTag === 'a' ? 'intec-cl-text-hover' : null,
                                    ],
                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                ]) ?>
                                <?php if ($arVisual['SITE']['SHOW'] && !empty($arData['SITE']['LINK'])) { ?>
                                    <div class="widget-item-site">
                                        <?php if (!empty($arData['SITE']['NAME'])) { ?>
                                            <div class="widget-item-site-name">
                                                <?= $arData['SITE']['NAME'] ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (!empty($arData['SITE']['LINK'])) { ?>
                                            <div class="widget-item-site-link">
                                                <?= Html::beginTag('a', [
                                                    'class' => [
                                                        'intec-cl-svg'
                                                    ],
                                                    'link' => '',
                                                    'target' => '_blank'
                                                ]) ?>
                                                    <?= $arSvg['LINK'] ?>
                                                    <span><?= $arData['SITE']['LINK'] ?></span>
                                                <?= Html::endTag('a') ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['RESULT']['SHOW'] && !empty($arData['RESULT'])) { ?>
                                    <div class="widget-item-description">
                                        <div class="widget-item-description-title">
                                            <?= Loc::getMessage('C_MAIN_PROJECTS_TEMPLATE_4_RESULT_TITLE') ?>
                                        </div>
                                        <div class="widget-item-description-text">
                                            <?= $arData['RESULT'] ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['PROPERTIES_LIST']['SHOW'] && !empty($arData['PROPERTIES'])) { ?>
                                <div class="widget-item-properties">
                                    <?php foreach ($arData['PROPERTIES'] as $arProperty) { ?>
                                        <div class="widget-item-property">
                                            <div class="widget-item-property-name">
                                                <?= $arProperty['NAME'] ?>
                                            </div>
                                            <div class="widget-item-property-value">
                                                <?= $arProperty['VALUE'] ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                <?php if ($arForm['USE']) { ?>
                                    <?= Html::tag('div', Html::stripTags($arForm['BUTTON']), [
                                        'class' => [
                                            'widget-item-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'scheme-current',
                                                'mod-round-2'
                                            ]
                                        ],
                                        'data' => [
                                            'role' => 'form',
                                            'name' => $arItem['NAME']
                                        ]
                                    ]) ?>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php if ($arForm['USE'] || $arVisual['SLIDER']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>
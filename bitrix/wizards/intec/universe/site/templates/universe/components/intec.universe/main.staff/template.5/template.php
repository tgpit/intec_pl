<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

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
    $arForm['BUTTON'] = Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_16_ORDER_BUTTON_DEFAULT');

?>
<div class="widget c-staff c-staff-template-5" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper">
        <div class="widget-wrapper-2">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-content">
                        <div class="intec-content-wrapper">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-content-wrapper'
                    ],
                    'data-wide' => $arVisual['WIDE'] ? 'true' : 'false'
                ]) ?>
                    <div class="intec-content intec-content-visible">
                        <div class="intec-content-wrapper widget-content">
                            <div class="owl-carousel" data-role="items">
                                <?php foreach ($arResult['ITEMS'] as $arItem) {

                                    $sId = $sTemplateId.'_'.$arItem['ID'];
                                    $sAreaId = $this->GetEditAreaId($sId);
                                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                    $sPicture = $arItem['PREVIEW_PICTURE'];

                                    if (empty($sPicture))
                                        $sPicture = $arItem['DETAIL_PICTURE'];

                                    if (!empty($sPicture)) {
                                        $sPicture = CFile::ResizeImageGet($sPicture, [
                                            'width' => 750,
                                            'height' => 750
                                        ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                        if (!empty($sPicture))
                                            $sPicture = $sPicture['src'];
                                    }

                                    if (empty($sPicture))
                                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                    $arData = $arItem['DATA'];

                                ?>
                                    <div class="widget-element" data-role="item">
                                        <div class="widget-element-wrapper" id="<?= $sAreaId ?>">
                                            <div class="intec-grid intec-grid-600-wrap intec-grid-i-25 intec-grid-a-v-center">
                                                <div class="widget-element-picture-wrap intec-grid-item-auto intec-grid-item-600-1">
                                                    <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', '', [
                                                        'class' => [
                                                            'widget-element-picture',
                                                            'intec-image-effect'
                                                        ],
                                                        'data' => [
                                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                        ],
                                                        'style' => [
                                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                                        ],
                                                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                                    ]) ?>
                                                </div>
                                                <div class="intec-grid-item">
                                                    <?php if ($arVisual['POSITION']['SHOW'] && !empty($arData['POSITION']['VALUE'])) { ?>
                                                        <div class="widget-element-position">
                                                            <?= $arData['POSITION']['VALUE'] ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', $arItem['NAME'], [
                                                        'class' => [
                                                            'widget-element-name',
                                                            $arVisual['LINK']['USE'] ? 'intec-cl-text-hover' : null
                                                        ],
                                                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                                    ]) ?>
                                                    <?php if (($arVisual['PHONE']['SHOW'] && !empty($arData['PHONE']['VALUE']))
                                                    || ($arVisual['EMAIL']['SHOW'] && !empty($arData['EMAIL']['VALUE']))) { ?>
                                                        <div class="intec-grid intec-grid-wrap intec-grid-i-h-17 intec-grid-i-v-5">
                                                            <?php if ($arVisual['PHONE']['SHOW'] && !empty($arData['PHONE']['VALUE'])) { ?>
                                                                <div class="widget-element-phone intec-grid-item-auto">
                                                                    <div class="intec-grid">
                                                                        <div class="intec-grid-item-auto">
                                                                            <i class="fas fa-phone"></i>
                                                                        </div>
                                                                        <div class="intec-grid-item">
                                                                            <?php foreach ($arData['PHONE']['VALUE'] as $arProp) {
                                                                                $sPhone = StringHelper::replace($arProp, [
                                                                                    '(' => '',
                                                                                    ')' => '',
                                                                                    ' ' => '',
                                                                                    '-' => ''
                                                                                ]);
                                                                                $sDisplayPhone = $arProp;
                                                                            ?>
                                                                                <a class="widget-element-phone-value" href="tel:<?= $sPhone ?>"><?= $sDisplayPhone ?></a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arData['EMAIL']['VALUE'])) { ?>
                                                                <div class="widget-element-email intec-grid-item-auto">
                                                                    <div class="intec-grid">
                                                                        <div class="intec-grid-item-auto">
                                                                            <i class="fas fa-envelope"></i>
                                                                        </div>
                                                                        <div class="intec-grid-item">
                                                                            <?php foreach ($arData['EMAIL']['VALUE'] as $arProp) { ?>
                                                                                <a class="widget-element-email-value" href="email:<?= $arProp ?>">
                                                                                    <?= $arProp ?>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                                        <div class="widget-element-description">
                                                            <?= $arItem['PREVIEW_TEXT'] ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($arForm['SHOW']) { ?>
                                                    <div class="widget-element-button-wrap">
                                                        <?= Html::tag('div', Html::stripTags($arForm['BUTTON']), [
                                                            'class' => [
                                                                'widget-element-button',
                                                                'intec-cl-text',
                                                                'intec-cl-border',
                                                                'intec-cl-background-hover'
                                                            ],
                                                            'data' => [
                                                                'role' => 'form',
                                                                'name' => $arItem['NAME']
                                                            ]
                                                        ]) ?>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="intec-ui intec-ui-control-navigation" data-role="navigation"></div>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
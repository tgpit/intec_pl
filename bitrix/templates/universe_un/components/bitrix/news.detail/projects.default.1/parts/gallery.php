<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 * @var string $sTemplateId
 */

$arImages = ArrayHelper::getValue($arResult, 'GALLERY');

if (empty($arImages))
    return;

?>
<div class="project-section project-section-gallery">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-a-h-start" data-role="gallery">
                <?php foreach ($arImages as $arImage) { ?>
                <?php
                    $sImageBig = $arImage['SRC'];
                    $sImageSmall = CFile::ResizeImageGet($arImage, array(
                        'width' => 600,
                        'height' => 600
                    ));
                    $sImageSmall = $sImageSmall['src'];
                    $sDescription = $arImage['DESCRIPTION'];

                    if (empty($sDescription))
                        $sDescription = $arImage['ORIGINAL_NAME'];
                ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'project-gallery-image',
                            'intec-grid-item' => [
                                '4',
                                '1000-3',
                                '768-2',
                                '550-1'
                            ]
                        ],
                        'data' => [
                            'preview-src' => $sImageSmall,
                            'src' => $sImageBig
                        ]
                    ]) ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'project-gallery-image-wrapper'
                            ],
                            'data' => [
                                'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                                'original' => $arLazyLoad['USE'] ? $sImageSmall : null
                            ],
                            'style' => [
                                'background-image' => !$arLazyLoad['USE'] ? 'url(\''.$sImageSmall.'\')' : null
                            ]
                        ]) ?>
                            <?= Html::img($arLazyLoad['USE'] ? $arLazyLoad['STUB'] : $sImageSmall, [
                                'alt' => $sDescription,
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                                    'original' => $arLazyLoad['USE'] ? $sImageSmall : null
                                ]
                            ]) ?>
                        <?= Html::endTag('div') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var gallery = $('[data-role="gallery"]', data.nodes);

            gallery.lightGallery({
                exThumbImage: 'data-preview-src',
                autoplay: false,
                share: false
            });
        }, {
            'name': '[Component] bitrix:news (projects) > bitrix:news.detail (.default)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>
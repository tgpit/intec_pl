<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

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

$arImages = ArrayHelper::getValue($arResult, 'IMAGES');

if (empty($arImages))
    return;

?>
<div class="project-section project-section-images">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-a-h-start intec-grid-i-11" data-role="images">
                <?php foreach ($arImages as $arImage) {

                    $sImageBig = $arImage['SRC'];
                    $sImageSmall = CFile::ResizeImageGet($arImage, array(
                        'width' => 750,
                        'height' => 750
                    ));
                    $sImageSmall = $sImageSmall['src'];
                    $sDescription = $arImage['DESCRIPTION'];

                    if (empty($sDescription))
                        $sDescription = $arImage['ORIGINAL_NAME'];

                ?>
                    <div class="project-images-image intec-grid-item-2 intec-grid-item-450-1">
                        <?= Html::beginTag('div', [
                            'class' => 'project-images-image-wrapper',
                            'data' => [
                                'preview-src' => $sImageSmall,
                                'src' => $sImageBig,
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
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var gallery = $('[data-role="images"]', data.nodes);

            gallery.lightGallery({
                selector: '.project-images-image-wrapper',
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

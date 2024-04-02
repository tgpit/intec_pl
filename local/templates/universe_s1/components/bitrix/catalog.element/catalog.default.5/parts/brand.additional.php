<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arFields
 */

$arBrandPicture = CFile::ResizeImageGet($arFields['BRAND']['VALUE']['PICTURE'], [
    'width' => 80,
    'height' => 80
], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

$bUrl = !empty($arFields['BRAND']['VALUE']['URL']['DETAIL']);

$sLinkName = StringHelper::replaceMacros($arVisual['BRAND']['ADDITIONAL']['LINK']['NAME'], [
    'BRAND' => $arFields['BRAND']['VALUE']['NAME']
]);

?>
<div class="catalog-element-brand-additional-container catalog-element-additional-block">
    <div class="catalog-element-brand-additional">
        <?php if (!empty($arBrandPicture['src'])) { ?>
            <?= Html::beginTag($bUrl ? 'a' : 'div', [
                'class' => [
                    'catalog-element-brand-additional-picture',
                    'catalog-element-brand-additional-block',
                    'intec-ui-picture'
                ],
                'href' => $bUrl ? $arFields['BRAND']['VALUE']['URL']['DETAIL'] : null,
                'target' => $bUrl ? '_blank' : null
            ]) ?>
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arBrandPicture['src'], [
                    'alt' => $arFields['BRAND']['VALUE']['NAME'],
                    'title' => $arFields['BRAND']['VALUE']['NAME'],
                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $arBrandPicture['src'] : null
                ]) ?>
            <?= Html::endTag($bUrl ? 'a' : 'div') ?>
        <?php } ?>
        <div class="catalog-element-brand-additional-description catalog-element-brand-additional-block">
            <?= $arFields['BRAND']['VALUE']['TEXT'] ?>
        </div>
        <div class="catalog-element-brand-additional-block">
            <?php if ($bUrl) { ?>
                <div class="catalog-element-brand-additional-link">
                    <?= Html::tag('a', $sLinkName, [
                        'class' => [
                            'intec-cl-text',
                            'intec-cl-text-light-hover'
                        ],
                        'href' => $arFields['BRAND']['VALUE']['URL']['DETAIL'],
                        'target' => '_blank'
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php unset($arBrandPicture, $bUrl, $sLinkName) ?>
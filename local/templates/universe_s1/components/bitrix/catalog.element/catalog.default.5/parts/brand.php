<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arFields
 */

$arPicture = CFile::ResizeImageGet($arFields['BRAND']['VALUE']['PICTURE'], [
    'width' => 110,
    'height' => 40
], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

if (!isset($arPicture['src']) || empty($arPicture['src']))
    return;

?>
<div class="catalog-element-brand-container">
    <a class="catalog-element-brand intec-ui-picture" href="<?= $arFields['BRAND']['VALUE']['URL']['DETAIL'] ?>">
        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPicture['src'], [
            'alt' => $arFields['BRAND']['VALUE']['NAME'],
            'title' => $arFields['BRAND']['VALUE']['NAME'],
            'loading' => 'lazy',
            'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
            'data-original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['src'] : null
        ]) ?>
    </a>
</div>
<?php unset($arPicture) ?>
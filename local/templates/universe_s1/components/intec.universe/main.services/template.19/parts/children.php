<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arItems) use (&$arVisual) {

    if (empty($arItems))
        return;

?>
    <?php foreach ($arItems as $arItem) {

        $sTag = $arVisual['LINK']['USE'] ? 'a' : 'span';

    ?>
        <div class="widget-item-child">
            <?= Html::tag($sTag, $arItem['NAME'], [
                'class' => Html::cssClassFromArray([
                    'widget-item-child-name' => true,
                    'intec-cl-text' => [
                        '' => true,
                        'light-hover' => $sTag === 'a' ? true : false
                    ],
                ], true),
                'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
            ]) ?>
        </div>
    <?php } ?>
<?php } ?>
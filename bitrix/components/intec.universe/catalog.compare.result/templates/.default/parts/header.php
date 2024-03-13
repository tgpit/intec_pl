<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var callable $vHeaderItems
 */

$vHeaderItems = include(__DIR__.'/header.items.php');

?>

<?php return function ($arSection) use (&$vHeaderItems) { ?>
    <div class="catalog-compare-result-header" data-role="header" data-active="false">
        <div class="intec-content intec-content-primary intec-content-visible">
            <div class="intec-content-wrapper">
                <div class="catalog-compare-result-header-wrapper">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-grid' => [
                                '',
                                'nowrap',
                                'a-h-start',
                                'a-v-start',
                                'i-h-10'
                            ]
                        ]
                    ]) ?>
                        <div class="intec-grid-item-1 intec-grid-item-768-2">
                            <?php $vHeaderItems($arSection['ITEMS']) ?>
                        </div>
                        <div class="catalog-compare-result-mobile-block intec-grid-item-1 intec-grid-item-768-2">
                            <?php if (count($arSection['ITEMS']) > 1) { ?>
                                <?php $vHeaderItems($arSection['ITEMS'], 'right') ?>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

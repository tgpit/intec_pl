<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

include(__DIR__.'/parts/image.php');

?>
<?php if (isset($arResult['ITEM'])) {

    $arItem = &$arResult['ITEM'];
    $bOffers = !empty($arItem['OFFERS']);

    if (!empty($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']))
        $sName = $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'];
    else
        $sName = $arItem['NAME'];

    ?>
    <?= Html::beginTag('div', [
        'id' => $sTemplateId,
        'class' => Html::cssClassFromArray([
            'ns-bitrix' => true,
            'c-catalog-item' => true,
            'c-catalog-item-template-2' => true,
        ], true)
    ]) ?>
    <div class="catalog-item-body">
        <?php $vImage($arItem) ?>
        <?php if ($arVisual['VOTE']['SHOW']) { ?>
            <div class="catalog-item-block-container catalog-item-vote-container">
                <?php include(__DIR__ . '/parts/vote.php') ?>
            </div>
        <?php } ?>
        <div class="catalog-item-block-container catalog-item-name-container">
            <?= Html::tag('a', $sName, [
                'class' => [
                    'catalog-item-name',
                    'intec-cl-text-hover'
                ],
                'href' => $arItem['DETAIL_PAGE_URL']
            ]) ?>
        </div>
        <?php if ($arVisual['QUANTITY']['SHOW'] || $arItem['ARTICLE']['SHOW']) { ?>
            <div class="catalog-item-block-container">
                <div class="catalog-item-quantity-wrap">
                    <?php if ($arVisual['QUANTITY']['SHOW']) {
                        include(__DIR__ . '/parts/quantity.php');
                    } ?>
                </div>
                <div class="catalog-item-article-wrap">
                    <?php if ($arItem['ARTICLE']['SHOW']) {
                        include(__DIR__.'/parts/article.php');
                    } ?>
                </div>
            </div>
        <?php } ?>
        <div class="catalog-item-block-container catalog-item-price-container">
            <?php include(__DIR__.'/parts/price.php') ?>
        </div>
        <div class="catalog-item-button-container-wrap">
            <?php include(__DIR__.'/parts/buttons.php') ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
    <?= Html::endTag('div') ?>
<?php } ?>
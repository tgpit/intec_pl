<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(false);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$vItem = include(__DIR__.'/parts/item.php');
$vForm = include(__DIR__.'/parts/form.php');

?>
<div class="ns-intec-universe c-reviews c-reviews-template-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="reviews-content">
                <?php if ($arResult['FORM']['USE']) { ?>
                    <div class="reviews-form-container" data-role="form.root">
                        <?php if ($arResult['FORM']['ACCESS']) { ?>
                            <!--form-->
                            <?php $vForm($arResult['FORM']) ?>
                            <!--form-->
                        <?php } else { ?>
                            <div class="reviews-form-open">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'reviews-form-button-open',
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'scheme-gray',
                                            'size-4',
                                            'mod-round-half',
                                            'state-disabled'
                                        ]
                                    ],
                                    'title' => Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_OPEN_UNAVAILABLE'),
                                    'data-state' => 'disabled'
                                ]) ?>
                                    <span class="reviews-form-button-open-icon intec-ui-part-icon"></span>
                                    <span class="reviews-form-button-open-text intec-ui-part-content">
                                        <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_OPEN') ?>
                                    </span>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['ITEMS']['SHOW']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'reviews-items',
                        'data' => [
                            'role' => 'reviews.items',
                            'state' => 'none'
                        ]
                    ]) ?>
                        <!--items-all-->
                        <?php if (!empty($arResult['USER_ITEM']))
                            $vItem($arResult['USER_ITEM'], true);
                        ?>
                        <!--items-->
                        <?php if (!empty($arResult['ITEMS'])) {
                            foreach ($arResult['ITEMS'] as &$arItem)
                                $vItem($arItem);
                        } ?>
                        <?php if (empty($arResult['USER_ITEM']) && empty($arResult['ITEMS'])) {
                            $arItem = [];
                            $vItem(($arItem));
                        } ?>
                        <!--items-->
                        <!--items-all-->
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
            <?php if ($arResult['NAVIGATION']['USE']) { ?>
                <div class="reviews-pagination" data-role="navigation">
                    <?= $arResult['NAVIGATION']['PRINT'] ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
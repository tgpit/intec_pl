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

if (!$arVisual['ITEMS']['HIDE'])
    $vItem = include(__DIR__.'/parts/item.php');

?>
<div class="ns-intec-universe c-reviews c-reviews-template-3" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="reviews-content">
                <?php if ($arResult['FORM']['USE']) { ?>
                    <?php if ($arResult['FORM']['ACCESS']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'reviews-form-container',
                            'data' => [
                                'role' => 'reviews.form',
                                'state' => 'none'
                            ]
                        ]) ?>
                            <!--form-->
                            <?php include(__DIR__.'/parts/form.php') ?>
                            <!--form-->
                        <?= Html::endTag('div') ?>
                    <?php } else { ?>
                        <div class="reviews-form-container">
                            <div class="reviews-form-message reviews-form-closed">
                                <?php if (!empty($arVisual['FORM']['AUTHORIZATION'])) { ?>
                                    <?= Loc::getMessage('C_REVIEWS_TEMPLATE_3_FORM_ACCESS_CLOSED_MACROS', [
                                        '#AUTH#' => $arVisual['FORM']['AUTHORIZATION']
                                    ]) ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_REVIEWS_TEMPLATE_3_FORM_ACCESS_CLOSED') ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if (!$arVisual['ITEMS']['HIDE']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'reviews-items',
                        'data' => [
                            'role' => 'reviews.content',
                            'state' => 'none'
                        ]
                    ]) ?>
                        <!--items-all-->
                        <?php if (!empty($arResult['USER_ITEM']))
                            $vItem($arResult['USER_ITEM'], true);
                        ?>
                        <!--items-->
                        <?php if (!empty($arResult['ITEMS'])) { ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $vItem($arItem);

                            } ?>
                        <?php } ?>
                        <?php if (empty($arResult['ITEMS']) && empty($arResult['USER_ITEM']))
                            $arItem = [];
                            $vItem(($arItem));
                        ?>
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

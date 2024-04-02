<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<div class="widget-items-list" data-role="contacts">
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'widget-items-content' => true,
            'owl-carousel' => $arResult['DATA']['SLIDER']['USE']
        ], true),
        'data-role' => 'contacts.slider'
    ]) ?>
        <?php foreach ($arResult['ITEMS'] as $arItem) {

            $sId = $sTemplateId.'_'.$arItem['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

            if (!$arItem['DATA']['MAP']['SHOW'])
                continue;

            $isMain = false;

            if ($arItem[$arVisual['MODE']] === $arResult['MAIN'][$arVisual['MODE']])
                $isMain = true;

        ?>
            <div class="widget-item" id="<?= $sAreaId ?>">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-item-content' => true,
                        'intec-cl-background' => $isMain,
                        'intec-cl-background-hover' => true
                    ], true),
                    'data' => [
                        'role' => 'contacts.item',
                        'state' => $isMain ? 'enabled' : 'disabled',
                        'latitude' => $arItem['DATA']['MAP']['VALUES']['LAT'],
                        'longitude' => $arItem['DATA']['MAP']['VALUES']['LON']
                    ]
                ]) ?>
                    <div class="widget-item-name">
                        <?= $arItem['NAME'] ?>
                    </div>
                    <?php if ($arItem['DATA']['PHONE']['SHOW'] || $arItem['DATA']['ADDRESS']['SHOW']) { ?>
                        <div class="widget-item-information">
                            <?php if ($arItem['DATA']['PHONE']['SHOW']) { ?>
                                <div class="widget-item-information-item">
                                    <?= $arItem['DATA']['PHONE']['VALUE'] ?>
                                </div>
                            <?php } ?>
                            <?php if ($arItem['DATA']['ADDRESS']['SHOW']) { ?>
                                <div class="widget-item-information-item">
                                    <?= $arItem['DATA']['ADDRESS']['VALUE'] ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
</div>
<?php unset($arItem, $sId, $sAreaId, $isMain) ?>
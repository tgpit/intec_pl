<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\helpers\RegExp;

/**
 * @var array $arDescriptionProperties
 */
?>

<div class="news-detail-content-header-info-properties">
    <?php if (Type::isArray($arDescriptionProperties)) { ?>
        <div class="news-detail-content-header-info-properties-item intec-grid">
            <div class="news-detail-content-header-info-properties-item-names intec-grid-item-auto intec-grid-item-shrink-1">
                <?php $iCount = 0 ?>
                <?php foreach ($arDescriptionProperties as $sPropertyCode) {
                    if ($iCount > 4) break;

                    $arProperty = ArrayHelper::getValue($arResult, ['PROPERTIES', $sPropertyCode]);

                    if (empty($arProperty)) continue;

                    $sName = ArrayHelper::getValue($arProperty, 'NAME');
                    $sValue = ArrayHelper::getValue($arProperty, 'VALUE');

                    if (empty($sValue)) continue;

                    $sName = Html::encode($sName);

                    if (RegExp::isMatchBy('/^http(s)?\\:\\/\\//', $sValue)) {
                        $sValue = Html::a($sValue, $sValue, [
                            'target' => '_blank'
                        ]);
                    } else {
                        $sValue = Html::encode($sValue);
                    }

                    $iCount++; ?>
                    <div class="news-detail-content-header-info-properties-item-name">
                        <?= $sName ?>
                    </div>
                <?php } ?>
            </div>
            <div class="news-detail-content-header-info-properties-item-values intec-grid-item-auto intec-grid-item-shrink-1">
                <?php $iCount = 0 ?>
                <?php foreach ($arDescriptionProperties as $sPropertyCode) {
                    if ($iCount > 4) break;

                    $arProperty = ArrayHelper::getValue($arResult, ['PROPERTIES', $sPropertyCode]);

                    if (empty($arProperty)) continue;

                    $sName = ArrayHelper::getValue($arProperty, 'NAME');
                    $sValue = ArrayHelper::getValue($arProperty, 'VALUE');

                    if (empty($sValue)) continue;

                    $sName = Html::encode($sName);

                    if (RegExp::isMatchBy('/^http(s)?\\:\\/\\//', $sValue)) {
                        $sValue = Html::a($sValue, $sValue, [
                            'target' => '_blank'
                        ]);
                    } else {
                        $sValue = Html::decode($sValue);
                    }

                    $iCount++; ?>
                    <div class="news-detail-content-header-info-properties-item-value">
                        <?= $sValue ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

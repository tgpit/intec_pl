<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arData
 * @var array $arSvg
 */

?>
<div class="news-detail-contact-standard">
    <div class="intec-grid intec-grid-wrap intec-grid-i-h-20 intec-grid-i-v-8">
        <?php if ($arData['PHONE']['SHOW']) { ?>
            <div class=" intec-grid-item-auto intec-grid-item-shrink-1">
                <div class="news-detail-contact">
                    <div class="news-detail-contact-icon">
                        <?= $arSvg['CONTACT']['PHONE'] ?>
                    </div>
                    <div class="news-detail-contact-value-container">
                        <?php foreach ($arData['PHONE']['VALUES'] as $arValue) { ?>
                            <div class="news-detail-contact-value">
                                <?= Html::tag('a', $arValue['VALUE'], [
                                    'class' => 'intec-cl-text-hover',
                                    'href' => 'tel:'.$arValue['HTML'],
                                    'title' => $arValue['VALUE']
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php unset($arValue) ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($arData['EMAIL']['SHOW']) { ?>
            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                <div class="news-detail-contact">
                    <div class="news-detail-contact-icon">
                        <?= $arSvg['CONTACT']['EMAIL'] ?>
                    </div>
                    <div class="news-detail-contact-value-container">
                        <?php foreach ($arData['EMAIL']['VALUES'] as $sValue) { ?>
                            <div class="news-detail-contact-value">
                                <?= Html::tag('a', $sValue, [
                                    'class' => 'intec-cl-text-hover',
                                    'href' => 'mailto:'.$sValue,
                                    'title' => $sValue
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php unset($sValue) ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

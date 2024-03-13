<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arData
 * @var array $arSvg
 */

?>
<div class="news-detail-contact-social">
    <div class="intec-grid intec-grid-wrap intec-grid-i-6">
        <?php foreach ($arData['SOCIAL']['VALUES'] as $key => $sValue) { ?>
            <div class="intec-grid-item-auto">
                <?php if ($key !== 'SKYPE') { ?>
                    <?= Html::tag('a', $arSvg['SOCIAL'][$key], [
                        'class' => [
                            'news-detail-contact-social-icon',
                            'intec-cl-svg-path-fill-hover'
                        ],
                        'href' => $sValue,
                        'target' => '_blank'
                    ]) ?>
                <?php } else { ?>
                    <?= Html::tag('a', $arSvg['SOCIAL'][$key], [
                        'class' => [
                            'news-detail-contact-social-icon',
                            'intec-cl-svg-path-fill-hover'
                        ],
                        'href' => 'skype:'.$sValue.'?'.$arVisual['SOCIAL']['SKYPE']['ACTION']
                    ]) ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
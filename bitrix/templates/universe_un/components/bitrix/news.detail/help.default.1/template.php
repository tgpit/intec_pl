<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$arVisual = $arResult['VISUAL'];

Loc::loadMessages(__FILE__);

?>
<div class="ns-bitrix c-news-detail c-news-detail-help-default-1">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="news-detail-content">
                <?php if ($arVisual['BANNER']['SHOW']) { ?>
                    <?= Html::tag('div', null, [
                        'class' => 'news-detail-banner',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['BANNER']['SRC'] : null
                        ],
                        'style' => [
                            'background-image' => 'url(\''.($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arVisual['BANNER']['SRC']).'\')'
                        ]
                    ]) ?>
                <?php } ?>
                <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                    <div class="news-detail-description">
                        <?= $arVisual['DESCRIPTION']['VALUE'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

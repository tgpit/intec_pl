<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<div class="widget-feedback">
    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
        <?php if ($arResult['DATA']['STAFF']['SHOW']) { ?>
            <div class="intec-grid-item-auto">
                <?= Html::tag('div', null, [
                    'class' => 'widget-feedback-image',
                    'data' => [
                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arResult['DATA']['STAFF']['IMAGE'] : null
                    ],
                    'style' => [
                        'background-image' => 'url(\''.(
                            $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arResult['DATA']['STAFF']['IMAGE']
                        ).'\')'
                    ]
                ]) ?>
            </div>
        <?php } ?>
        <div class="intec-grid-item">
            <div class="widget-feedback-text">
                <?= $arVisual['FEEDBACK']['TEXT'] ?>
            </div>
            <?php if ($arResult['DATA']['FORM']['SHOW']) {

                if (empty($arVisual['FEEDBACK']['BUTTON']['TEXT']))
                    $arVisual['FEEDBACK']['BUTTON']['TEXT'] = Loc::getMessage('C_NEWS_LIST_CONTACTS_2_TEMPLATE_FEEDBACK_BUTTON_TEXT_DEFAULT');

            ?>
                <div class="widget-feedback-buttons">
                    <?= Html::tag('div', $arVisual['FEEDBACK']['BUTTON']['TEXT'], [
                        'class' => [
                            'widget-feedback-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current',
                                'mod-round-2'
                            ]
                        ],
                        'data-role' => 'form'
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
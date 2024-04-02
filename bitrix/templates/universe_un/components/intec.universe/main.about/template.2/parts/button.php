<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function () use (&$arResult, &$arVisual) { ?>
    <?php if ($arVisual['BUTTON']['SHOW']) { ?>
        <?php if (empty($arVisual['BUTTON']['TEXT']))
            $arVisual['BUTTON']['TEXT'] = Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_TEMPLATE_BUTTON_TEXT_DEFAULT');
        ?>
        <div class="widget-buttons">
            <?php if ($arVisual['BUTTON']['VIEW'] === 1) { ?>
                <?= Html::tag('a', $arVisual['BUTTON']['TEXT'], [
                    'class' => [
                        'widget-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'size-2',
                            'mod-round-2',
                            'scheme-current'
                        ]
                    ],
                    'href' => $arResult['LINK'],
                    'target' => $arVisual['BUTTON']['BLANK'] ? '_blank' : null
                ]) ?>
            <?php } else if ($arVisual['BUTTON']['VIEW'] === 2) { ?>
                <?= Html::tag('a', $arVisual['BUTTON']['TEXT'], [
                    'class' => [
                        'widget-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'size-2',
                            'mod-round-2',
                            'mod-transparent',
                            'scheme-current'
                        ]
                    ],
                    'href' => $arResult['LINK'],
                    'target' => $arVisual['BUTTON']['BLANK'] ? '_blank' : null
                ]) ?>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>
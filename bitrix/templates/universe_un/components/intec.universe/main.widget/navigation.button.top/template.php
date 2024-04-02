<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<?php if (!defined('EDITOR')) { ?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-widget',
        'c-widget-navigation-button-top'
    ],
    'id' => $sTemplateId
]) ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-button',
            'intec-ui' => [
                '',
                'control-button',
                'scheme-current'
            ]
        ],
        'data' => [
            'role' => 'button'
        ],
        'style' => [
             'border-radius' => $arParams['RADIUS'] > 0 ? $arParams['RADIUS'].'px' : null
        ]
    ]) ?>
        <div class="widget-button-wrapper">
            <svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 6L6 1L1 6" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6 16V1" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
    <?= Html::endTag('div') ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var elements = {};
            var refresh = function () {
                var height = document.documentElement.clientHeight;

                if (elements.window.scrollTop() > height) {
                    elements.button.fadeIn();
                } else {
                    elements.button.fadeOut();
                }
            };

            elements.root = data.nodes;
            elements.button = $('[data-role="button"]', elements.root);
            elements.window = $(window);

            elements.window.on('scroll', refresh);

            elements.button.on('click', function () {
                $('html, body').stop().animate({
                    'scrollTop': 0
                }, 600);
            });

            refresh();
        }, {
            'name': '[Component] intec.universe:main.widget (navigation.button.top)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
        });
    </script>
<?= Html::endTag('div') ?>
<?php } else { ?>
    <div class="intec-editor-element-stub">
        <div class="intec-editor-element-stub-wrapper">
            <?= Loc::getMessage('C_MAIN_WIDGET_NAVIGATION_BUTTON_TOP_STUB') ?>
        </div>
    </div>
<?php } ?>

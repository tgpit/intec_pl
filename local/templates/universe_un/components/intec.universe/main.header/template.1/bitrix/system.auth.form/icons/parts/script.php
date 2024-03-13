<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function () {
        var $ = this.getLibrary('$');
        var app = this;
        var root = arguments[0].nodes;
        var buttons;
        var modal = {};
        var data;

        data = <?= JavaScript::toObject([
            'id' => $sTemplateId.'-modal',
            'title' => Loc::getMessage('W_HEADER_S_A_F_AUTH_FORM_TITLE')
        ]) ?>;


        <?php
        $arComponentParams = [
            'component' => $arParams['MODAL_COMPONENT'],
            'template' => $arParams['MODAL_TEMPLATE'],
            'parameters' => [
                'AUTH_URL' => $arParams['LOGIN_URL'],
                'AUTH_FORGOT_PASSWORD_URL' => $arParams['FORGOT_PASSWORD_URL'],
                'AUTH_REGISTER_URL' => $arParams['REGISTER_URL'],
                'AJAX_MODE' => 'N',
                'CONSENT_URL' => $arParams['CONSENT_URL']
            ],
            'settings' => [
                'title' => Loc::getMessage('W_HEADER_S_A_F_AUTH_FORM_TITLE'),
                'parameters' => [
                    'width' => 800,
                    'zIndex' => 0,
                    'offsetLeft' => 0,
                    'offsetTop' => 0,
                    'overlay' => true
                ]
            ]
        ];
        ?>

        var modalParams = <?= JavaScript::toObject($arComponentParams) ?>;

        modalParams.settings.parameters.closeIcon = {
            'right': '20px',
            'top': '22px'
        };

        modalParams.settings.parameters.titleBar = {
            'content': BX.create('span', {
                'html': data.title,
                'props': {
                    'className': 'access-title-bar'
                }
            })
        };

        modal.open = function () {
            app.api.components.show(modalParams);
        };

        buttons = {};
        buttons.login = $('[data-action="login"]', root);
        buttons.login.on('click', modal.open);
    }, {
        'name': '[Component] intec.universe:main.header (template.1) > bitrix:system.auth.form (icons)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'options': {
                'await': [
                    'composite'
                ]
            }
        }
    });
</script>

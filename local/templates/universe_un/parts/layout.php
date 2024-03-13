<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\FileHelper;
use intec\constructor\models\build\layout\Renderer;
use intec\template\Properties;

global $APPLICATION;

/**
 * @var Renderer $this
 * @var boolean $isInEditor
 * @var string $part
 */

if (FileHelper::isFile(__DIR__.'/custom/layout.php'))
    if (include(__DIR__.'/custom/layout.php') === false)
        return;

if (!$isInEditor) {
    if ($part === 'headerBegin') {
        $properties = Properties::getCollection();
        $blocks = [
            'basketFixed' => [
                'use' => $properties->get('basket-use') && $properties->get('basket-position') === 'fixed.right',
                'template' => $properties->get('basket-fixed-template')
            ],
            'basketNotifications' => [
                'use' => $properties->get('basket-use') && $properties->get('basket-notifications-use'),
                'template' => 'template.1'
            ],
            'buttonTop' => [
                'use' => true
            ],
            'mobilePanel' => [
                'use' => false,
                'template' => null
            ]
        ];

        $mobileBlocks = $properties->get('mobile-blocks');

        if (!empty($mobileBlocks)) {
            if (!empty($mobileBlocks['panel'])) {
                $blocks['mobilePanel']['use'] = $mobileBlocks['panel']['active'];
                $blocks['mobilePanel']['template'] = $mobileBlocks['panel']['template'];
            }
        }

        $APPLICATION->ShowPanel();

        if ($blocks['basketFixed']['use']) {
            $APPLICATION->IncludeComponent(
                'bitrix:main.include',
                '.default',
                [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => SITE_DIR . 'include/header/basket/fixed/' . $blocks['basketFixed']['template'] . '.php'
                ],
                false,
                ['HIDE_ICONS' => 'Y']
            );
        }

        if ($blocks['basketNotifications']['use']) {
            $APPLICATION->IncludeComponent(
                'bitrix:main.include',
                '.default',
                [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => SITE_DIR . 'include/header/basket/notifications/' . $blocks['basketNotifications']['template'] . '.php'
                ],
                false,
                ['HIDE_ICONS' => 'Y']
            );
        }

        if ($blocks['buttonTop']['use']) {
            $APPLICATION->IncludeComponent(
                'bitrix:main.include',
                '.default',
                [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => SITE_DIR . 'include/header/button.top.php'
                ],
                false,
                ['HIDE_ICONS' => 'Y']
            );
        }

        if ($blocks['mobilePanel']['use']) {
            $APPLICATION->IncludeComponent(
                'bitrix:main.include',
                '.default',
                [
                    'AREA_FILE_SHOW' => 'file',
                    'PATH' => SITE_DIR . 'include/header/mobile/panel/' . $blocks['mobilePanel']['template'] . '.php'
                ],
                false,
                ['HIDE_ICONS' => 'Y']
            );
        }
    }
}

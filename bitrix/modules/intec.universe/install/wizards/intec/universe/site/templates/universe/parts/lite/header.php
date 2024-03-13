<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\constructor\models\Build;
use intec\constructor\models\build\layout\renderers\ClosureRenderer;
use intec\constructor\models\build\layout\Zone;
use intec\template\Properties;

(function ($directory) {
    global $APPLICATION;

    $properties = Properties::getCollection();
    $layouts = Build::getCurrent()->getLayouts();
    $template = $properties->get('pages-main-template');
    $page = [];

    $page['path'] = $APPLICATION->GetCurPage(false);
    $page['main'] = $page['path'] === SITE_DIR;
    $page['menu'] = $properties->get('template-menu-show');
    $page['layout'] = 'wide';
    $page['blocks'] = [
        'breadcrumb' => [
            'show' => !$page['main']
        ],
        'title' => [
            'show' => !$page['main']
        ]
    ];

    if (empty($template))
        $template = 'wide';

    if ($page['main']) {
        if ($template === 'narrow.left')
            $page['layout'] = 'narrow.left';
    } else {
        if ($page['menu'])
            $page['layout'] = 'narrow.left';
    }

    if (FileHelper::isFile($directory.'/parts/custom/header.php'))
        include($directory.'/parts/custom/header.php');

    /** @var ClosureRenderer $renderer */
    $renderer = new ClosureRenderer(function ($zone) use (&$renderer, &$directory, &$page, &$properties, &$template) {
        global $APPLICATION;
        global $DB;
        global $USER;

        /** @var Zone $zone */

        $include = true;

        if ($zone->getCode() === 'default' || $zone->getCode() === 'column' || $zone->getCode() === 'footer') {
            if ($page['main']) {
                if ($zone->getCode() !== 'footer')
                    $include = false;

                if ($renderer->getIsRenderAllowed()) {
                    $blocks = $properties->get('pages-main-blocks');

                    foreach ($blocks as $code => &$block)
                        $block['code'] = $code;

                    $blocks = Arrays::from($blocks);
                    $render = function ($block, $data = []) use (&$blocks, &$template, &$zone) {
                        global $APPLICATION;
                        global $DB;
                        global $USER;

                        if (!Type::isArray($block))
                            return;

                        if (!$block['active'])
                            return;

                        if (!Type::isArray($data))
                            $data = [];

                        if (!isset($data['path']))
                            $data['path'] = $block['code'];

                        $path = Path::from('@root'.SITE_DIR.'include/index/'.$template);

                        if (empty($block['template'])) {
                            $path = $path->add($data['path'].'.php');
                        } else {
                            $path = $path->add($data['path'])->add($block['template'].'.php');
                        }

                        if (FileHelper::isFile($path->value))
                            include($path->value);
                    };

                    if (FileHelper::isFile($directory.'/parts/custom/blocks.php'))
                        include($directory.'/parts/custom/blocks.php');

                    $path = Path::from('@root'.SITE_DIR .'include/index/'.$template.($zone->getCode() !== 'default' ? '.'.$zone->getCode() : null).'.php');

                    if (FileHelper::isFile($path->value))
                        include($path->value);
                }
            }

            if ($zone->getCode() === 'default') {
                $include = false;
                $renderer->setIsRenderAllowed(false);
            }
        }

        if ($include && $renderer->getIsRenderAllowed()) {
            if ($zone->getCode() === 'footer') {
                $blocks = Arrays::from($properties->get('footer-blocks'));
                $blocks->each(function ($code, $block) {
                    global $APPLICATION;
                    global $DB;
                    global $USER;

                    if (!$block['active'])
                        return;

                    $APPLICATION->IncludeComponent('bitrix:main.include', '.default', [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_DIR.'include/footer/blocks/'.$code.'/'.$block['template'].'.php'
                    ], false, ['HIDE_ICONS' => 'Y']);
                });
            }

            $path = Path::from('@root'.SITE_DIR.'include/layout/'.$zone->getCode().'.php');

            if (FileHelper::isFile($path->value))
                include($path->value);

            if ($zone->getCode() === 'header') {
                if ($page['blocks']['breadcrumb']['show']) {
                    echo Html::beginTag('div', [
                        'id' => 'navigation',
                        'class' => 'intec-template-breadcrumb'
                    ]);

                    $APPLICATION->IncludeComponent('bitrix:main.include', '.default', [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_DIR.'include/header/breadcrumb.php'
                    ], false, ['HIDE_ICONS' => 'Y']);

                    echo Html::endTag('div');
                }

                if ($page['blocks']['title']['show']) {
                    echo Html::beginTag('div', [
                        'class' => 'intec-template-title'
                    ]);

                    $APPLICATION->IncludeComponent('bitrix:main.include', '.default', [
                        'AREA_FILE_SHOW' => 'file',
                        'PATH' => SITE_DIR.'include/header/title.php'
                    ], false, ['HIDE_ICONS' => 'Y']);

                    echo Html::endTag('div');
                }
            }
        }
    });

    $renderer->setIsRenderAllowed(true);

    if (isset($layouts[$page['layout']]))
        $layouts[$page['layout']]->render($renderer, Build::getCurrent()->getTemplate());
})($directory);

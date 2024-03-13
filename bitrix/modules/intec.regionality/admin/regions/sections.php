<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\regionality\models\Region;

/**
 * @var array $arUrlTemplates
 */

Loc::loadMessages(__FILE__);

if (empty($arUrlTemplates))
    include(Core::getAlias('@intec/regionality/module/admin/url.php'));

return function ($region, $current = null, $style = null) use (&$arUrlTemplates) {
    if (!($region instanceof Region))
        return;

    $sections = [
        'edit' => [
            'link' => StringHelper::replaceMacros($arUrlTemplates['regions.edit'], [
                'region' => $region->id
            ])
        ],
        'domains' => [
            'link' => StringHelper::replaceMacros($arUrlTemplates['regions.domains'], [
                'region' => $region->id
            ])
        ]
    ];

    foreach ($sections as $code => &$section) {
        $section['code'] = $code;
        $section['active'] = $code === $current;
        $section['name'] = Loc::getMessage('sections.'.$code.'.name');
    }

    unset($code);
    unset($section);

    $sections['domains']['name'] .= ' ('.$region->getDomains(false)->count().')';

    echo Html::beginTag('div', [
        'class' => 'adm-list-table-top',
        'style' => $style
    ]);

    foreach ($sections as $section) {
        echo Html::tag('a', $section['name'], [
            'href' => $section['link'],
            'class' => Html::cssClassFromArray([
                'adm-btn' => true,
                'adm-btn-active' => $section['active']
            ], true)
        ]);
    }

    echo Html::endTag('div');
};
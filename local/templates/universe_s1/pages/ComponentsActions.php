<?php
namespace intec\template\pages;

use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\handling\Actions;

class ComponentsActions extends Actions
{
    public function actionGet ()
    {
        /** @var /CMain $APPLICATION */
        global $APPLICATION;

        $data = $this->data;
        $data = ArrayHelper::merge([
            'component' => null,
            'template' => null,
            'parameters' => null
        ], $data);

        if (empty($data['component']))
            return;

        if (empty($data['template']))
            $data['template'] = '.default';

        if (!Type::isArray($data['parameters']))
            $data['parameters'] = [];

        foreach ($data['parameters'] as $key => $parameter)
            if (StringHelper::startsWith($key, '~'))
                unset($data['parameters'][$key]);

        $data['parameters'] = ArrayHelper::merge([
            'AJAX_MODE' => 'Y',
            'AJAX_OPTION_ADDITIONAL' => 'COMPONENT',
            'AJAX_OPTION_SHADOW' => 'N',
            'AJAX_OPTION_JUMP' => 'N',
            'AJAX_OPTION_STYLE' => 'Y'
        ], $data['parameters']);

        $APPLICATION->ShowAjaxHead();
        $APPLICATION->IncludeComponent(
            $data['component'],
            $data['template'],
            $data['parameters'],
            null,
            ['HIDE_ICONS' => 'Y']
        );
    }
}
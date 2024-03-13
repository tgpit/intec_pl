<?php
namespace intec\constructor\builds\templates\editor\ajax;

use CComponentParamsManager;
use CComponentUtil;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class ComponentActions extends Actions
{
    public function actionGetData()
    {
        return $this->successResponse([
            'name' => 'test'
        ]);
    }

    public function actionGetList()
    {
        $getSection = null;
        $getSections = null;
        $getComponent = null;
        $getComponents = null;

        /**
         * Возвращает модель раздела из данных.
         * @param $code
         * @param $data
         * @return array|null
         */
        $getSection = function ($code, $data) use (&$getSections, &$getComponent, &$getComponents) {
            $information = ArrayHelper::getValue($data, '@');

            if (empty($code))
                return null;

            if (!Type::isArray($information))
                return null;

            $result = [];
            $result['code'] = $code;
            $result['type'] = 'section';
            $result['name'] = ArrayHelper::getValue($information, 'NAME');
            $result['sort'] = Type::toInteger(
                ArrayHelper::getValue($information, 'SORT')
            );

            $result['children'] = ArrayHelper::merge(
                $getSections($data),
                $getComponents($data)
            );

            return $result;
        };

        /**
         * Возвращает список моделей раздела из данных.
         * @param $data
         * @return array
         */
        $getSections = function ($data) use (&$getSection) {
            $result = [];
            $list = ArrayHelper::getValue($data, '#');

            if (Type::isArray($list))
                foreach ($list as $code => $item) {
                    $item = $getSection($code, $item);

                    if ($item !== null)
                        $result[] = $item;
                }

            return $result;
        };

        /**
         * Возвращает модель компонента из данных.
         * @param $code
         * @param $data
         * @return array
         */
        $getComponent = function ($code, $data) {
            $result = [];

            if (empty($code))
                return null;

            $result['code'] = $code;
            $result['type'] = 'component';
            $result['name'] = ArrayHelper::getValue($data, 'TITLE');
            $result['namespace'] = ArrayHelper::getValue($data, 'NAMESPACE');
            $result['description'] = ArrayHelper::getValue($data, 'DESCRIPTION');
            $result['complex'] = ArrayHelper::getValue($data, 'COMPLEX') == 'Y' ? true : false;
            $result['sort'] = Type::toInteger(ArrayHelper::getValue($data, 'SORT'));

            return $result;
        };

        /**
         * Возвращает список моделей компонента из данных.
         * @param $data
         * @return array
         */
        $getComponents = function ($data) use (&$getComponent) {
            $result = [];
            $list = ArrayHelper::getValue($data, '*');

            if (Type::isArray($list))
                foreach ($list as $code => $item) {
                    $item = $getComponent($code, $item);

                    if ($item !== null)
                        $result[] = $item;
                }

            return $result;
        };

        $tree = \CComponentUtil::GetComponentsTree();
        $data = $getSections($tree);

        return $this->successResponse($data);
    }

    public function actionGetParameters()
    {
        Loader::includeModule("fileman");

        if (isset($_POST['src_site']))
            $_GET['src_site'] = $_POST['src_site'];

        if (isset($_POST['siteTemplateId']))
            $_GET['siteTemplateId'] = $_POST['siteTemplateId'];

        $request = Core::$app->request;
        $component = $request->post('component');
        $template = $request->post('template');
        $properties = $request->post('values');
        $clear = $request->post('clear');
        $clear = $clear == 1;

        $component = Encoding::convert($component, null, Encoding::UTF8);
        $template = Encoding::convert($template, null, Encoding::UTF8);

        if (!Type::isArray($properties))
            $properties = [];

        $properties = Encoding::convert($properties, null, Encoding::UTF8);
        $parameters = CComponentParamsManager::GetComponentProperties(
            $component,
            $template,
            $this->build->code,
            $properties
        );

        $description = CComponentUtil::GetComponentDescr($component);

        $result = [];
        $result['name'] = ArrayHelper::getValue($description, 'NAME');
        $result['description'] = ArrayHelper::getValue($description, 'DESCRIPTION');
        $result['scripts'] = [];
        $result['templates'] = [];
        $result['template'] = $template;
        $result['groups'] = [];

        $data = ArrayHelper::getValue($parameters, 'templates');
        $templates = [];

        if (Type::isArray($data))
            foreach ($data as $template) {
                $array = [
                    'code' => ArrayHelper::getValue($template, 'NAME'),
                    'name' => ArrayHelper::getValue($template, 'DISPLAY_NAME')
                ];

                $templates[$array['code']] = $array;
            }

        $data = ArrayHelper::getValue($parameters, 'groups');
        $groups = [];
        $groups['COMPONENT_TEMPLATE'] = null;

        if (Type::isArray($data))
            foreach ($data as $group) {
                $array = [
                    'code' => $group['ID'],
                    'name' => $group['NAME'],
                    'sort' => Type::toInteger($group['SORT']),
                    'parameters' => []
                ];

                $groups[$array['code']] = $array;
            }

        $groups['COMPONENT_TEMPLATE'] = [
            'code' => 'COMPONENT_TEMPLATE',
            'name' => Loc::getMessage('dialogs.componentSettings.groups.template.name'),
            'sort' => 0
        ];

        if (empty($groups['ADDITIONAL_SETTINGS']))
            $groups['ADDITIONAL_SETTINGS'] = [
                'code' => 'ADDITIONAL_SETTINGS',
                'name' => Loc::getMessage('dialogs.componentSettings.groups.additionalSettings.name'),
                'parameters' => [],
                'sort' => 700
            ];

        $data = ArrayHelper::getValue($parameters, 'parameters');
        $parameters = [];
        $types = [
            'CHECKBOX',
            'STRING',
            'LIST',
            'CUSTOM',
            'COLORPICKER'
        ];

        if (Type::isArray($data))
            foreach ($data as $parameter) {
                $group = ArrayHelper::getValue($parameter, 'PARENT');
                $array = [
                    'code' => ArrayHelper::getValue($parameter, 'ID'),
                    'name' => ArrayHelper::getValue($parameter, 'NAME'),
                    'type' => ArrayHelper::getValue($parameter, 'TYPE'),
                    'default' => ArrayHelper::getValue($parameter, 'DEFAULT'),
                    'multiple' => ArrayHelper::getValue($parameter, 'MULTIPLE') == 'Y',
                    'refresh' => ArrayHelper::getValue($parameter, 'REFRESH') == 'Y',
                    'hidden' => ArrayHelper::getValue($parameter, 'HIDDEN') == 'Y',
                    'value' => null,
                    'raw' => $parameter
                ];

                $array['value'] = ArrayHelper::getValue($properties, $array['code']);

                if ($array['value'] === null)
                    $array['value'] = $array['default'];

                if ($array['multiple']) {
                    if (!Type::isArray($array['value']))
                        $array['value'] = [];
                } else {
                    if (Type::isArray($array['value']))
                        $array['value'] = null;
                }

                if (!ArrayHelper::isIn($array['type'], $types))
                    $array['type'] = 'STRING';

                if ($array['type'] == 'LIST') {
                    $values = ArrayHelper::getValue($parameter, 'VALUES');
                    $array['values'] = [];

                    if (Type::isArray($values))
                        foreach ($values as $value => $name)
                            $array['values'][] = [
                                'value' => $value,
                                'name' => $name
                            ];

                    if ($array['multiple'])
                        $array['value'] = array_unique($array['value']);

                    $array['extended'] = ArrayHelper::getValue($parameter, 'ADDITIONAL_VALUES') == 'Y';
                } else if ($array['type'] == 'CUSTOM') {
                    $array['javascript'] = [
                        'file' => ArrayHelper::getValue($parameter, 'JS_FILE'),
                        'event' => ArrayHelper::getValue($parameter, 'JS_EVENT'),
                        'data' => ArrayHelper::getValue($parameter, 'JS_DATA')
                    ];

                    if (empty($array['javascript']['file']) || empty($array['javascript']['event']))
                        continue;
                } else if ($array['type'] == 'FILE') {
                    $array['type'] = 'STRING';
                } else if ($array['type'] == 'CHECKBOX') {
                    unset($array['multiple']);
                }

                $group = ArrayHelper::getValue($groups, $group);

                if (empty($group)) {
                    $group = ArrayHelper::getValue($groups, 'ADDITIONAL_SETTINGS');
                }

                if (!empty($group))
                    $groups[$group['code']]['parameters'][] = $array;

                $parameters[$array['code']] = $array;
            }

        if (!$clear)
            foreach ($properties as $code => $value) {
                if (ArrayHelper::keyExists($code, $parameters))
                    continue;

                if (empty($value) && !Type::isNumeric($value))
                    continue;

                $groups['ADDITIONAL_SETTINGS']['parameters'][] = [
                    'code' => $code,
                    'value' => $value,
                    'hidden' => true
                ];
            }

        if (count($groups['ADDITIONAL_SETTINGS']['parameters']) == 0)
            unset($groups['ADDITIONAL_SETTINGS']);

        $result['templates'] = ArrayHelper::getValues($templates);
        $result['groups'] = ArrayHelper::getValues($groups);

        return $this->successResponse($result);
    }

    public function actionGetContent()
    {
        global $APPLICATION;

        $_SESSION['SESS_CLEAR_CACHE'] = 'Y';

        $request = Core::$app->request;
        $code = $request->post('code');
        $template = $request->post('template');
        $parameters = $request->post('parameters');

        if (empty($code))
            exit();

        if (empty($template))
            $template = '';

        if (!Type::isArray($parameters))
            $parameters = [];

        Html::setIdentifier(microtime(true) * 10000);

        $code = Encoding::convert($code, null, Encoding::UTF8);
        $template = Encoding::convert($template, null, Encoding::UTF8);
        $parameters = Encoding::convert($parameters, null, Encoding::UTF8);

        $APPLICATION->ShowAjaxHead();
        $APPLICATION->includeComponent(
            $code,
            $template,
            $parameters,
            false,
            [
                'HIDE_ICONS' => 'Y'
            ]
        );

        $_SESSION['SESS_CLEAR_CACHE'] = 'N';

        exit();
    }
}
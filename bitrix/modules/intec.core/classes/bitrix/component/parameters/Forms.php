<?php

namespace intec\core\bitrix\component\parameters;

use Bitrix\Main\Loader;
use CComponentUtil;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

if (!Loader::includeModule('form'))
    return;

/**
 * Class Forms
 * @package intec\core\bitrix\component\parameters
 * @deprecated
 */
class Forms
{
    const FIELD_ALL = null;
    const FIELD_TEXT = 'text';
    const FIELD_TEXT_AREA = 'textarea';
    const FIELD_RADIO = 'radio';
    const FIELD_CHECKBOX = 'checkbox';
    const FIELD_DROP_DOWN = 'dropdown';
    const FIELD_MULTI_SELECT = 'multiselect';
    const FIELD_DATE = 'date';
    const FIELD_IMAGE = 'image';
    const FIELD_FILE = 'file';
    const FIELD_EMAIL = 'email';
    const FIELD_URL = 'url';
    const FIELD_PASSWORD = 'password';
    const FIELD_HIDDEN = 'hidden';

    /**
     * Возвращает список форм
     * @return array
     */
    public static function getFormsList()
    {
        $result = [];

        $forms = \CForm::GetList($by = 'sort', $order = 'asc', [], $filtered = false);

        while ($form = $forms->GetNext()) {
            $result[$form['ID']] = '['.$form['ID'].'] '.$form['NAME'];
        }

        return $result;
    }

    /**
     * Возвращает список шаблонов компонента
     * @return array
     */
    public static function getTemplates()
    {
        $result = [];

        $templates = CComponentUtil::GetTemplatesList('bitrix:form.result.new');

        foreach ($templates as $template) {
            $result[$template['NAME']] = $template['NAME'].(
                !empty($template['TEMPLATE']) ? ' ('.$template['TEMPLATE'].')' : null
            );
        }

        return $result;
    }

    /**
     * Возвращает список полей формы
     * @param int|string $form
     * @param null|string|array $type
     * @param array $filter
     * @param string $sort
     * @param string $order
     * @return array
     */
    public static function getFieldsList($form, $type = self::FIELD_ALL, $filter = [], $sort = 's_sort', $order = 'asc')
    {
        $form = Type::toInteger($form);

        if (empty($form) || $form < 1)
            return [];

        if (empty($type) && $type !== null) {
            $type = null;
        }

        if (empty($filter) || !Type::isArray($filter)) {
            $filter = [];
        }

        $filter = ArrayHelper::merge(['ACTIVE' => 'Y'], $filter);

        if (empty($sort) || !Type::isString($sort)) {
            $sort = 's_sort';
        }

        if (empty($order) || !Type::isString($order)) {
            $order = ArrayHelper::fromRange(['asc', 'desc'], $order);
        }

        $result = [];
        $fields = \CFormField::GetList(
            $form,
            'N',
            $by = $sort,
            $asc = $order,
            $filter,
            $filtered = false
        );

        while ($field = $fields->GetNext()) {
            $return = false;

            if ($type === null) {
                $return = true;
            } else {
                if (
                    Type::isArray($type) && ArrayHelper::isIn($field['FIELD_TYPE'], $type) ||
                    Type::isString($type) && $field['FIELD_TYPE'] === $type
                ) {
                    $return = true;
                }
            }

            if ($return) {
                $result['form_'.$field['FIELD_TYPE'].'_'.$field['ID']] = '['.$field['ID'].'] '.$field['TITLE'];
            }
        }

        return $result;
    }
}
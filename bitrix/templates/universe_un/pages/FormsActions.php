<?php
namespace intec\template\pages;

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\handling\Actions;

class FormsActions extends Actions
{
    public function actionGet()
    {
        /** @var /CMain $APPLICATION */
        global $APPLICATION;

        $data = $this->data;
        $data = ArrayHelper::merge([
            'id' => null,
            'template' => null,
            'parameters' => null,
            'fields' => null
        ], $data);

        if (empty($data['id']))
            return;

        if (empty($data['template']))
            $data['template'] = '.default';

        if (!Type::isArray($data['parameters']))
            $data['parameters'] = [];

        foreach ($data['parameters'] as $key => $value)
            if (StringHelper::startsWith($key, '~'))
                unset($data['parameters'][$key]);

        if (Loader::includeModule('form')) {
            if (Type::isArray($data['fields'])) {
                unset($_POST['fields']);
                unset($_REQUEST['fields']);

                $_POST = ArrayHelper::merge($_POST, $data['fields']);
                $_REQUEST = ArrayHelper::merge($_REQUEST, $data['fields']);
                $_REQUEST['WEB_FORM_ID'] = $_POST['WEB_FORM_ID'] = $data['id'];
                $_REQUEST['web_form_submit'] = $_POST['web_form_submit'] = 'SUBMIT';
            }

            $data['parameters'] = ArrayHelper::merge([
                'SEF_MODE' => 'N',
                'START_PAGE' => 'new',
                'SHOW_LIST_PAGE' => 'N',
                'SHOW_EDIT_PAGE' => 'N',
                'SHOW_VIEW_PAGE' => 'N',
                'SUCCESS_URL' => '',
                'SHOW_ANSWER_VALUE' => 'N',
                'SHOW_ADDITIONAL' => 'N',
                'SHOW_STATUS' => 'Y',
                'EDIT_ADDITIONAL' => 'N',
                'EDIT_STATUS' => 'N',
                'NOT_SHOW_FILTER' => array(),
                'NOT_SHOW_TABLE' => array(),
                'CHAIN_ITEM_TEXT' => '',
                'CHAIN_ITEM_LINK' => '',
                'IGNORE_CUSTOM_TEMPLATE' => 'N',
                'USE_EXTENDED_ERRORS' => 'Y',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '3600',
                'AJAX_OPTION_ADDITIONAL' => 'FORM'
            ], $data['parameters'], [
                'WEB_FORM_ID' => $data['id'],
                'AJAX_MODE' => 'Y',
                'AJAX_OPTION_SHADOW' => 'N',
                'AJAX_OPTION_JUMP' => 'N',
                'AJAX_OPTION_STYLE' => 'Y'
            ]);

            $APPLICATION->ShowAjaxHead();
            $APPLICATION->IncludeComponent(
                'bitrix:form.result.new',
                $data['template'],
                $data['parameters'],
                null,
                ['HIDE_ICONS' => 'Y']
            );
        } else if (Loader::includeModule('intec.startshop')) {
            if (Type::isArray($data['fields'])) {
                unset($_POST['fields']);
                unset($_REQUEST['fields']);

                $_POST = ArrayHelper::merge($_POST, $data['fields']);
                $_REQUEST = ArrayHelper::merge($_REQUEST, $data['fields']);
                $_REQUEST['FORM_ID'] = $_POST['FORM_ID'] = $data['id'];
            }

            $data['parameters'] = ArrayHelper::merge([
                'SEF_MODE' => 'N',
                'START_PAGE' => 'new',
                'SHOW_LIST_PAGE' => 'N',
                'SHOW_EDIT_PAGE' => 'N',
                'SHOW_VIEW_PAGE' => 'N',
                'SUCCESS_URL' => '',
                'SHOW_ANSWER_VALUE' => 'N',
                'SHOW_ADDITIONAL' => 'N',
                'SHOW_STATUS' => 'Y',
                'EDIT_ADDITIONAL' => 'N',
                'EDIT_STATUS' => 'N',
                'NOT_SHOW_FILTER' => array(),
                'NOT_SHOW_TABLE' => array(),
                'CHAIN_ITEM_TEXT' => '',
                'CHAIN_ITEM_LINK' => '',
                'IGNORE_CUSTOM_TEMPLATE' => 'N',
                'USE_EXTENDED_ERRORS' => 'Y',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '3600',
                'AJAX_OPTION_ADDITIONAL' => 'FORM'
            ], $data['parameters'], [
                'FORM_ID' => $data['id'],
                'AJAX_MODE' => 'Y',
                'AJAX_OPTION_SHADOW' => 'N',
                'AJAX_OPTION_JUMP' => 'N',
                'AJAX_OPTION_STYLE' => 'Y',
                'FIELDS' => $data['fields']
            ]);

            $APPLICATION->ShowAjaxHead();
            $APPLICATION->IncludeComponent(
                'intec:startshop.forms.result.new',
                $data['template'],
                $data['parameters'],
                null,
                ['HIDE_ICONS' => 'Y']
            );
        }
    }
}
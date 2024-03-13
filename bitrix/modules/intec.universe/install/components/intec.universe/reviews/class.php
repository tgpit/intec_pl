<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UserTable;
use Bitrix\Main\Web\Json;
use intec\core\bitrix\Component;
use intec\core\bitrix\components\IBlockElements;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class IntecReviewsNewComponent extends IBlockElements
{
    /**
     * Режим работы компонента.
     * @var string
     */
    private $COMPONENT_MODE = 'default';

    /**
     * Имя идентификатора для обработчика формы.
     * @var null
     */
    private $FORM_ACTION_NAME = null;

    /**
     * Значение идентификатора обработчика формы.
     * @var null
     */
    private $FORM_ACTION_VALUE = null;

    /**
     * Имя пользователя.
     * @var null
     */
    private $FORM_USER_NAME = null;

    /**
     * Ошибка неправильности CAPTCHA.
     * @var string
     */
    private $FORM_ERROR_CAPTCHA = 'captcha';

    /**
     * Ошбика неверности данных одного из полей формы.
     * @var string
     */
    private $FORM_ERROR_INVALID = 'invalid';

    /**
     * Ошибка незаполенности всех обязательных полей формы.
     * @var string
     */
    private $FORM_ERROR_REQUIRED = 'required';

    /**
     * Ошибка во время добавления элемента.
     * @var string
     */
    private $FORM_ERROR_ADD_FAILURE = 'add-failure';

    /**
     * Статус формы, если нет отзыва от пользователя.
     * @var string
     */
    private $FORM_STATUS_EMPTY = 'empty';

    /**
     * Статус формы, если отзыв от пользователя существует.
     * @var string
     */
    private $FORM_STATUS_EXISTS = 'exists';

    /**
     * Статус формы, если отзыв от пользователя был добавлен.
     * @var string
     */
    private $FORM_STATUS_ADDED = 'added';

    /**
     * Существует ли отзыв от данного пользователя.
     * @var bool
     */
    private $REVIEW_EXISTS = false;

    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Loader::includeModule('intec.core') || !Loader::includeModule('iblock'))
            return [];

        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'ELEMENTS_COUNT' => 0,
            'MODE' => 'default',
            'FORM_USE' => 'N',
            'CAPTCHA_USE' => 'N',
            'FORM_ACCESS' => 'registered',
            'FORM_ACCESS_AUTHORIZATION' => null,
            'PROPERTY_FORM_FIELDS' => [],
            'PROPERTY_FORM_REQUIRED' => [],
            'FORM_ADD_MODE' => 'disabled',
            'PROPERTY_ID' => null,
            'ID' => null,
            'ITEMS_HIDE' => 'N',
            'NAVIGATION_USE' => 'N',
            'NAVIGATION_ID' => null,
            'NAVIGATION_MODE' => 'standard',
            'NAVIGATION_ALL' => 'N',
            'NAVIGATION_TEMPLATE' => '.default',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'CACHE_TYPE' => 'N',
            'CACHE_TIME' => 0
        ], $arParams);

        $arParams['PROPERTY_FORM_FIELDS'] = array_filter($arParams['PROPERTY_FORM_FIELDS']);
        $arParams['PROPERTY_FORM_REQUIRED'] = array_filter($arParams['PROPERTY_FORM_REQUIRED']);

        if (!empty($arParams['ID']))
            $arParams['ID'] = Type::toInteger($arParams['ID']);

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        if (empty($this->arParams) || empty($this->arParams['IBLOCK_ID']))
            return;

        $this->arResult = [
            'FORM' => [],
            'USER_ITEM' => [],
            'ITEMS' => [],
            'NAVIGATION' => []
        ];

        $this->setComponentMode();
        $this->setNavigation();

        if ($this->startResultCache(false, $this->setCacheId($this->arResult['NAVIGATION']))) {
            $this->arResult['FORM'] = $this->setForm();

            if ($this->arResult['FORM']['USE']) {
                $arFields = $this->computeFormFields();
                $this->arResult['FORM']['FIELDS'] = ArrayHelper::merge($this->arResult['FORM']['FIELDS'], $arFields);
            }

            if ($this->arParams['ITEMS_HIDE'] !== 'Y') {
                $this->arResult['ITEMS'] = $this->getItems();
                $this->getReviewExisting();

                if (!empty($this->arResult['ITEMS']))
                    $this->setItemsNameByLogin();
            }

            $this->endResultCache();
        }

        if ($this->arResult['FORM']['USE']) {
            if ($this->arParams['ITEMS_HIDE'] === 'Y')
                $this->getReviewExisting();

            $this->setFormAction();
            $this->setFormDynamicValues();
            $this->setFormDynamicStatus();

            if ($this->isPostMatch() && $this->validateForm()) {
                $this->reviewAdd();
            }
        }

        $this->includeComponentTemplate();

        return null;
    }

    /**
     * Устанавливает режим работы компонента и проверяет минимальную необходимую заданность параметров для выбранного режима.
     * Если проверка не выполняется, то компонент возвращается в режим работы по умолчанию.
     */
    public function setComponentMode()
    {
        $this->COMPONENT_MODE = ArrayHelper::fromRange(['default', 'linked'], $this->arParams['MODE']);

        if ($this->COMPONENT_MODE === 'linked' &&
            (empty($this->arParams['PROPERTY_ID']) || empty($this->arParams['ID']) || !Type::isNumeric($this->arParams['ID']))
        ) {
            $this->COMPONENT_MODE = 'default';
        }
    }

    /**
     * Конструирует массив формы обратной связи.
     * @return array
     */
    private function setForm()
    {
        $arForm = [
            'USE' => $this->arParams['FORM_USE'] === 'Y',
            'ACCESS' => false,
            'STATUS' => null,
            'ERROR' => [],
            'FIELDS' => [],
            'CAPTCHA' => []
        ];

        return $arForm;
    }

    /**
     * Задает идентификатор для обработчика формы.
     */
    private function setFormAction()
    {
        $this->FORM_ACTION_NAME = md5('form-action-'.bitrix_sessid());
        $this->FORM_ACTION_VALUE = md5('add-review-'.bitrix_sessid());
    }

    /**
     * Определяет доступ пользователя к форме.
     * @return bool
     */
    private function setFormAccess()
    {
        global $USER;

        if ($this->arParams['FORM_ACCESS'] === 'registered')
            return $USER->IsAuthorized();

        return true;
    }

    /**
     * Задает поля формы с динамическими значениями.
     */
    private function setFormDynamicValues ()
    {
        global $APPLICATION;

        $this->arResult['FORM']['ACCESS'] = $this->setFormAccess();
        $this->arResult['FORM']['FIELDS'][] = [
            'TYPE' => 'hidden',
            'REQUIRED' => true,
            'NAME' => $this->FORM_ACTION_NAME,
            'VALUE' => $this->FORM_ACTION_VALUE
        ];
        $this->arResult['FORM']['FIELDS'][] = [
            'TYPE' => 'hidden',
            'REQUIRED' => true,
            'NAME' => 'USER_NAME',
            'VALUE' => $this->setFormUser(),
            'ERROR' => null
        ];

        if ($this->arParams['CAPTCHA_USE'] === 'Y') {
            $this->arResult['FORM']['CAPTCHA']['SID'] = [
                'TYPE' => 'hidden',
                'REQUIRED' => true,
                'NAME' => 'CAPTCHA_SID',
                'VALUE' => $APPLICATION->CaptchaGetCode(),
                'ERROR' => null
            ];
            $this->arResult['FORM']['CAPTCHA']['WORD'] = [
                'TYPE' => 'text',
                'REQUIRED' => true,
                'NAME' => 'CAPTCHA_WORD',
                'CAPTION' => Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_CAPTCHA_WORD'),
                'VALUE' => null,
                'ERROR' => null
            ];
        }
    }

    /**
     * Задает статус форме обратной связи.
     * Для целостной работы статусов необходимо передавать свойства класса типа FORM_STATUS_...
     * @param string $status - Статус, который будет применен к форме обратной связи.
     * @return string
     */
    private function setFormDynamicStatus($status = '')
    {
        if (!empty($status))
            $this->arResult['FORM']['STATUS'] = $status;
        else
            $this->arResult['FORM']['STATUS'] =  $this->REVIEW_EXISTS ? $this->FORM_STATUS_EXISTS : $this->FORM_STATUS_EMPTY;

        return;
    }

    /**
     * Задает имя пользователя для формы.
     * @return null|string
     */
    private function setFormUser()
    {
        global $USER;

        if (!empty($this->FORM_USER_NAME))
            return $this->FORM_USER_NAME;

        $name = null;

        if ($USER->IsAuthorized()) {
            $name = $USER->GetLogin();
        } else {
            $name = Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_USER_NAME_UNREGISTERED', [
                '{{ID}}' => session_id()
            ]);
        }

        $this->FORM_USER_NAME = $name;

        return $this->FORM_USER_NAME;
    }

    /**
     * Задает доп. поля формы и типы для них из выбранных свойств инфоблока.
     * @return array
     */
    private function computeFormFields()
    {
        $arFields = [];
        $arFieldsDefault = [
            'TYPE' => 'textarea',
            'HIDDEN' => false,
            'REQUIRED' => true,
            'NAME' => 'PREVIEW_TEXT',
            'CAPTION' => Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_PREVIEW_TEXT'),
            'VALUE' => null,
            'ERROR' => null
        ];

        if (!empty($this->arParams['PROPERTY_FORM_FIELDS'])) {
            if (ArrayHelper::isIn('PREVIEW_TEXT', $this->arParams['PROPERTY_FORM_FIELDS']))
                $arFields[] = $arFieldsDefault;

            $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
            ]))->indexBy('CODE')->asArray();

            if (!empty($arProperties)) {
                foreach ($arProperties as $arValue) {
                    if (empty($arValue['CODE']))
                        continue;

                    if (ArrayHelper::isIn($arValue['CODE'], $this->arParams['PROPERTY_FORM_FIELDS'])) {
                        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] == 'L' && $arValue['MULTIPLE'] === 'N') {
                            $arField = $this->computeFormFieldText($arValue);
                        } else if ($arValue['PROPERTY_TYPE'] == 'L' && $arValue['LIST_TYPE'] == 'L' && $arValue['MULTIPLE'] === 'N') {
                            $arField = $this->computeFormFieldList($arValue);
                        } else
                            continue;

                        if (!empty($arField))
                            $arFields[] = $arField;
                    }
                }
            } else {
                $arFields[] = $arFieldsDefault;
            }
        } else {
            $arFields[] = $arFieldsDefault;
        }

        return $arFields;
    }

    /**
     * Задает массив значений по умолчанию для доп. поля формы типа "текст".
     * @param array $arField - Массив свойств текстового поля инфоблока
     * @return array
     */
    private function computeFormFieldText($arField = [])
    {
        $type = 'text';

        if ($arField['USER_TYPE'] === 'HTML')
            $type = 'textarea';

        return [
            'TYPE' => $type,
            'REQUIRED' => ArrayHelper::isIn($arField['CODE'], $this->arParams['PROPERTY_FORM_REQUIRED']),
            'NAME' => $arField['CODE'],
            'CAPTION' => $arField['NAME'],
            'VALUE' => null,
            'ERROR' => null
        ];
    }

    /**
     * Задает массив значений по умолчанию для доп. поля формы типа "список".
     * @param array $arField - Массив свойств текстового поля инфоблока
     * @return array
     */
    private function computeFormFieldList($arField = [])
    {
        $arOptions = Arrays::fromDBResult(CIBlockProperty::GetPropertyEnum($arField['ID']))
            ->indexBy('ID')
            ->asArray(function ($key, $arValue) {
                if (!empty($arValue['VALUE']))
                    return [
                        'key' => $key,
                        'value' => $arValue['VALUE']
                    ];

                return ['skip' => true];
            });

        return [
            'TYPE' => 'select',
            'REQUIRED' => ArrayHelper::isIn($arField['CODE'], $this->arParams['PROPERTY_FORM_REQUIRED']),
            'NAME' => $arField['CODE'],
            'CAPTION' => $arField['NAME'],
            'OPTIONS' => $arOptions,
            'VALUE' => null,
            'ERROR' => null
        ];
    }

    /**
     * Проверяет принадлежность $_POST компоненту.
     * @return bool
     */
    private function isPostMatch()
    {
        $match = false;

        if (!empty($_POST) && !empty($_POST['sessid']) && !empty($_POST[$this->FORM_ACTION_NAME]))
            $match = $_POST['sessid'] === bitrix_sessid() && $_POST[$this->FORM_ACTION_NAME] === $this->FORM_ACTION_VALUE;

        return $match;
    }

    /**
     * Валидирует полученные данные формы.
     */
    private function validateForm()
    {
        if ($this->REVIEW_EXISTS)
            return false;

        global $APPLICATION;

        if (!empty($_POST['USER_NAME'])) {
            if ($_POST['USER_NAME'] !== $this->setFormUser())
                $this->addFormError(
                    $this->FORM_ERROR_INVALID
                );
        }

        foreach ($this->arResult['FORM']['FIELDS'] as $key => &$arField) {
            if ($key === 'sessid' || $key === $this->FORM_ACTION_NAME || $key === 'USER_NAME')
                continue;

            if ($arField['TYPE'] === 'text' || $arField['TYPE'] === 'textarea')
                $this->validateFormFieldText($arField);
            else if ($arField['TYPE'] === 'select')
                $this->validateFormFieldList($arField);
        }

        if ($this->arParams['CAPTCHA_USE'] === 'Y') {
            if (!$APPLICATION->CaptchaCheckCode($_POST['CAPTCHA_WORD'], $_POST['CAPTCHA_SID']))
                $this->addFormFieldError(
                    $this->arResult['FORM']['CAPTCHA']['WORD'],
                    $this->FORM_ERROR_CAPTCHA
                );
        }

        if (ArrayHelper::keyExists('CONSENT', $_POST))
            $this->arResult['FORM']['CONSENT'] = $_POST['CONSENT'];// ? true : false;

        return empty($this->arResult['FORM']['ERROR']);
    }

    /**
     * Валидирует поле формы типа "текст".
     * @param $arField - Массив поля формы
     */
    private function validateFormFieldText(&$arField)
    {
        if (ArrayHelper::keyExists($arField['NAME'], $_POST)) {
            $_POST[$arField['NAME']] = trim($_POST[$arField['NAME']]);


            if (empty($_POST[$arField['NAME']]) && $arField['REQUIRED']) {
                $this->addFormFieldError($arField, $this->FORM_ERROR_REQUIRED);
            } else if (!empty($_POST[$arField['NAME']])) {
                $arField['VALUE'] = $_POST[$arField['NAME']];
            }
        }
    }

    /**
     * Валидирует поле формы типа "список".
     * @param $arField - Массив поля формы
     */
    private function validateFormFieldList(&$arField)
    {
        if (empty($_POST[$arField['NAME']]) && $arField['REQUIRED']) {
            $this->addFormFieldError($arField, $this->FORM_ERROR_REQUIRED);
        } else if (!empty($_POST[$arField['NAME']])) {
            if (!ArrayHelper::keyExists($_POST[$arField['NAME']], $arField['OPTIONS'])) {
                $this->addFormFieldError($arField, $this->FORM_ERROR_INVALID);
            } else {
                $arField['VALUE'] = $_POST[$arField['NAME']];
            }
        }
    }

    /**
     * Добавляет код ошибки обработки формы.
     * Для целостной работы с ошибками необходимо передавать свойства класса типа FORM_ERROR_...
     * @param string $error - Код ошибки обработки формы
     */
    private function addFormError($error = '')
    {
        if (!empty($error) && !ArrayHelper::isIn($error, $this->arResult['FORM']['ERROR']))
            $this->arResult['FORM']['ERROR'][] = $error;
    }

    /**
     * Добавляет код ошибки обработки поля формы.
     * Для целостной работы с ошибками необходимо передавать свойства класса типа FORM_ERROR_...
     * @param string $error - Код ошибки обработки поля
     * @param $arField - Массив поля формы
     */
    private function addFormFieldError(&$arField, $error = '')
    {
        if (empty($arField['ERROR']) && !empty($error)) {
            $arField['ERROR'] = $error;

            $this->addFormError($error);
        }
    }

    /**
     * Получает оставленный пользователем отзыв, если он есть.
     * @param string $id - ID элемента
     */
    private function getReviewExisting($id = '')
    {
        global $USER;

        $arQuery = [
            'SORT' => ['SORT' => 'ASC'],
            'FILTER' => [
                'CHECK_PERMISSIONS' => 'Y',
                'MIN_PERMISSION' => 'R'
            ]
        ];

        if (empty($id))
            $arQuery['FILTER']['NAME'] = $this->setFormUser();
        else
            $arQuery['FILTER']['ID'] = Type::toInteger($id);

        if ($this->COMPONENT_MODE === 'linked')
            $arQuery['FILTER']['PROPERTY_'.$this->arParams['PROPERTY_ID']] = $this->arParams['ID'];

        $this->setIBlockType($this->arParams['IBLOCK_TYPE']);
        $this->setIBlockId($this->arParams['IBLOCK_ID']);

        $arUserItem = $this->getElements(
            $arQuery['SORT'],
            $arQuery['FILTER']
        );

        if (!empty($arUserItem)) {
            $arUserItem = ArrayHelper::getFirstValue($arUserItem);
            $arPropertiesListId = [];
            $arPropertiesListValues = [];

            foreach ($arUserItem['PROPERTIES'] as &$arProperty) {
                if ($arProperty['PROPERTY_TYPE'] == 'L' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N') {
                    $arPropertiesListId[] = [
                        'ID' => $arProperty['ID'],
                        'CODE' => $arProperty['CODE']
                    ];
                }
            }

            unset($arProperty);

            if (!empty($arPropertiesListId)) {
                foreach ($arPropertiesListId as $arProperty) {
                    $arPropertiesListValues[$arProperty['CODE']] = Arrays::fromDBResult(CIBlockProperty::GetPropertyEnum($arProperty['ID']))
                        ->indexBy('ID')
                        ->asArray(function ($key, $arValue) {
                            if (!empty($arValue['VALUE']))
                                return [
                                    'key' => $key,
                                    'value' => $arValue['VALUE']
                                ];

                            return ['skip' => true];
                        });
                }
            }

            if (!empty($arPropertiesListValues)) {
                foreach ($arPropertiesListValues as $key => $arValue) {
                    if (ArrayHelper::keyExists($key, $arUserItem['PROPERTIES']))
                        $arUserItem['PROPERTIES'][$key]['VALUES_LIST'] = $arValue;
                }

                unset($key, $arValue);
            }

            unset($arPropertiesListId, $arPropertiesListValues);

            if ($USER->IsAuthorized()) {
                $sUserName = $USER->GetFullName();

                if (!empty($sUserName))
                    $arUserItem['NAME'] = $sUserName;
            } else {
                $arUserItem['NAME'] = Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_USER_NAME_UNREGISTERED', [
                    '{{ID}}' => $arUserItem['ID']
                ]);
            }

            $this->arResult['USER_ITEM'] = $arUserItem;
            $this->REVIEW_EXISTS = true;
        }
    }

    /**
     * Производит добавление отзыва в инфоблок на основе данных из формы.
     */
    private function reviewAdd()
    {
        $arData = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'NAME' => $_POST['USER_NAME'],
            'CODE' => CUtil::translit($_POST['USER_NAME'], 'ru'),
            'ACTIVE' => $this->arParams['FORM_ADD_MODE'] === 'active' ? 'Y' : 'N',
            'PROPERTY_VALUES' => []
        ];

        if ($this->COMPONENT_MODE === 'linked')
            $arData['PROPERTY_VALUES'][$this->arParams['PROPERTY_ID']] = $this->arParams['ID'];

        foreach ($this->arResult['FORM']['FIELDS'] as $arField) {
            if (!empty($arField['VALUE']) &&
                $arField['NAME'] !== 'sessid' &&
                $arField['NAME'] !== $this->FORM_ACTION_NAME &&
                $arField['NAME'] !== 'USER_NAME'
            ) {
                if ($arField['NAME'] === 'PREVIEW_TEXT')
                    $arData['PREVIEW_TEXT'] = $arField['VALUE'];

                if ($arField['TYPE'] !== 'PREVIEW_TEXT' && $arField['TYPE'] === 'textarea')
                    $arField['VALUE'] = [
                        'TEXT' => nl2br($arField['VALUE']),
                        'TYPE' => 'html'
                    ];

                $arData['PROPERTY_VALUES'][$arField['NAME']] = [
                    'VALUE' => $arField['VALUE']
                ];
            }
        }

        $element = new CIBlockElement();

        $id = $element->Add($arData);

        if (empty($id)) {
            $this->addFormError($this->FORM_ERROR_ADD_FAILURE);
        } else {
            $this->setFormDynamicStatus($this->FORM_STATUS_ADDED);
            $this->getReviewExisting($id);
        }
    }

    /**
     * Задает параметры постраничной навигации и передает шаблон пагинатора в шаблон компонента.
     */
    private function setNavigation()
    {
        global $APPLICATION;

        $arNavigation = [
            'USE' => $this->arParams['NAVIGATION_USE'] === 'Y',
            'ID' => $this->arParams['NAVIGATION_ID'],
            'MODE' => ArrayHelper::fromRange([
                'standard',
                'ajax'
            ], $this->arParams['NAVIGATION_MODE']),
            'ALL' => $this->arParams['NAVIGATION_ALL'] === 'Y',
            'TEMPLATE' => $this->arParams['NAVIGATION_TEMPLATE'],
            'PAGE' => [
                'SIZE' => $this->arParams['ELEMENTS_COUNT'],
                'CURRENT' => 1,
                'COUNT' => 1
            ],
            'ITEMS' => 0,
            'PRINT' => null
        ];

        if ($arNavigation['USE'] && empty($arNavigation['ID']))
            $arNavigation['USE'] = false;

        if ($arNavigation['MODE'] === 'ajax' && !StringHelper::startsWith($arNavigation['TEMPLATE'], 'lazy.'))
            $arNavigation['USE'] = false;

        if ($arNavigation['MODE'] !== 'standard' && $arNavigation['ALL'])
            $arNavigation['ALL'] = false;

        if ($arNavigation['PAGE']['SIZE'] < 1)
            $arNavigation['USE'] = false;

        if ($arNavigation['USE']) {
            $navigation = new PageNavigation($arNavigation['ID']);

            $navigation->setPageSize($arNavigation['PAGE']['SIZE'])
                ->allowAllRecords($arNavigation['ALL'])
                ->initFromUri();

            $this->setIBlockType($this->arParams['IBLOCK_TYPE']);
            $this->setIBlockId($this->arParams['IBLOCK_ID']);

            $count = CIBlockElement::GetList(
                [], [
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    '!=NAME' => $this->setFormUser(),
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R',
                    'PROPERTY_'.$this->arParams['PROPERTY_ID'] => $this->arParams['ID']
                ], []
            );

            $navigation->setRecordCount(Type::toInteger($count));

            if ($navigation->getCurrentPage() > $navigation->getPageCount())
                $navigation->setCurrentPage($navigation->getPageCount());

            $arNavigation['PAGE']['CURRENT'] = $navigation->getCurrentPage();
            $arNavigation['PAGE']['COUNT'] = $navigation->getPageCount();

            if ($navigation->allRecordsShown())
                $arNavigation['PAGE']['SIZE'] = $navigation->getRecordCount();
            else
                $arNavigation['PAGE']['SIZE'] = $navigation->getPageSize();

            $arNavigation['ITEMS'] = $navigation->getRecordCount();

            if ($navigation->getPageCount() < 2 && !$navigation->allRecordsShown())
                $arNavigation['USE'] = false;

            if ($arNavigation['PAGE']['COUNT'] > 1 || $navigation->allRecordsShown()) {
                ob_start();

                $APPLICATION->IncludeComponent(
                    'bitrix:main.pagenavigation',
                    $arNavigation['TEMPLATE'], [
                        'NAV_OBJECT' => $navigation,
                        'SEF_MODE' => 'N'
                    ],
                    $this
                );

                $arNavigation['PRINT'] = ob_get_contents();

                ob_end_clean();
            }

            unset($navigation);
        }

        $this->arResult['NAVIGATION'] = $arNavigation;
    }

    /**
     * Задает уникальный идентификатор кеша компонента на основе переданных данных.
     * @param $data - Данные для генерации идентификатора (любой массив или строка)
     * @return string
     */
    private function setCacheId($data)
    {
        global $USER;

        if (Type::isArray($data))
            $data = serialize($data);

        return Component::getUniqueId($this).'-'.md5($data).'-'.$USER->GetGroups();
    }

    /**
     * Возвращает список элементов.
     * @return array
     */
    private function getItems()
    {
        $arItems = [];

        $this->setIBlockType($this->arParams['IBLOCK_TYPE']);
        $this->setIBlockId($this->arParams['IBLOCK_ID']);

        $arIBlock = $this->getIBlock();

        if (!empty($arIBlock) && $arIBlock['ACTIVE'] === 'Y') {
            $arQuery = [
                'SORT' => [],
                'FILTER' => [
                    'IBLOCK_LID' => $this->getSiteId(),
                    '!=NAME' => $this->setFormUser(),
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'MIN_PERMISSION' => 'R'
                ]
            ];

            if ($this->COMPONENT_MODE === 'linked')
                $arQuery['FILTER']['PROPERTY_'.$this->arParams['PROPERTY_ID']] = $this->arParams['ID'];

            if (!empty($this->arParams['SORT_BY']) && !empty($this->arParams['ORDER_BY']))
                $arQuery['SORT'] = [
                    $this->arParams['SORT_BY'] => $this->arParams['ORDER_BY']
                ];

            $arItems = $this->getElements(
                $arQuery['SORT'],
                $arQuery['FILTER'],
                $this->arResult['NAVIGATION']['PAGE']['SIZE'],
                $this->arResult['NAVIGATION']['PAGE']['CURRENT']
            );

            unset($arQuery);

            $arItem = ArrayHelper::getFirstValue($arItems);

            if (!empty($arItem)) {
                $arPropertiesListId = [];
                $arPropertiesListValues = [];

                foreach ($arItem['PROPERTIES'] as &$arProperty) {
                    if ($arProperty['PROPERTY_TYPE'] == 'L' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N') {
                        $arPropertiesListId[] = [
                            'ID' => $arProperty['ID'],
                            'CODE' => $arProperty['CODE']
                        ];
                    }
                }

                unset($arItem, $arProperty);

                if (!empty($arPropertiesListId)) {
                    foreach ($arPropertiesListId as $arProperty) {
                        $arPropertiesListValues[$arProperty['CODE']] = Arrays::fromDBResult(CIBlockProperty::GetPropertyEnum($arProperty['ID']))
                            ->indexBy('ID')
                            ->asArray(function ($key, $arValue) {
                                if (!empty($arValue['VALUE']))
                                    return [
                                        'key' => $key,
                                        'value' => $arValue['VALUE']
                                    ];

                                return ['skip' => true];
                            });
                    }
                }

                if (!empty($arPropertiesListValues)) {
                    foreach ($arItems as &$arItem) {
                        foreach ($arPropertiesListValues as $key => $arValue) {
                            if (ArrayHelper::keyExists($key, $arItem['PROPERTIES']))
                                $arItem['PROPERTIES'][$key]['VALUES_LIST'] = $arValue;
                        }
                    }

                    unset($arItem);
                }

                unset($arPropertiesListId, $arPropertiesListValues);
            }
        }

        return $arItems;
    }

    /**
     * Заменяет значение NAME элементов инфоблока
     * на Имя-Фамилию пользователя, если в качестве имени записан логин.
     */
    private function setItemsNameByLogin()
    {
        $arUsers = [];

        foreach ($this->arResult['ITEMS'] as &$arItem) {
            if (StringHelper::startsWith(
                $arItem['NAME'],
                Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_USER_NAME_UNREGISTERED', [
                    '{{ID}}' => null
                ])
            )) {
                $arItem['NAME'] = Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_USER_NAME_UNREGISTERED', [
                    '{{ID}}' => $arItem['ID']
                ]);

                continue;
            }

            $arUsers[] = $arItem['NAME'];
        }

        unset($arItem);

        if (!empty($arUsers)) {
            $arUsers = Arrays::from(
                UserTable::getList([
                    'select' => [
                        'LOGIN',
                        'NAME',
                        'LAST_NAME',
                        'SECOND_NAME'
                    ],
                    'filter' => [
                        'LOGIN' => $arUsers
                    ]
                ])->fetchAll()
            )->indexBy('LOGIN');

            if (!$arUsers->isEmpty()) {
                $arUsers = $arUsers->each(function ($key, &$value) {
                    $name = [];

                    if (!empty($value['NAME']))
                        $name[] = $value['NAME'];

                    if (!empty($value['LAST_NAME']))
                        $name[] = $value['LAST_NAME'];

                    if (!empty($name))
                        $value['FULL_NAME'] = implode(' ', $name);
                    else
                        $value['FULL_NAME'] = Loc::getMessage('C_REVIEWS_COMPONENT_STANDARD_USER_NAME_UNNAMED');
                })->asArray(function ($key, $value) {
                    return [
                        'key' => $key,
                        'value' => $value['FULL_NAME']
                    ];
                });

                foreach ($this->arResult['ITEMS'] as &$arItem) {
                    if (ArrayHelper::keyExists($arItem['NAME'], $arUsers))
                        $arItem['NAME'] = $arUsers[$arItem['NAME']];
                }

                unset($arItem);
            }
        }
    }

    /**
     * Возвращает JSON ответ для AJAX запроса.
     * @param array $content
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function sendJson($content = []) {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        echo Json::encode($content);

        CMain::FinalActions();

        die();
    }
}
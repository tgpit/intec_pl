<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

/**
 * @var $APPLICATION CMain
 * @var $USER CUser
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.core') || !Loader::includeModule('sale'))
    return;

CBitrixComponent::includeComponentClass("bitrix:sale.personal.profile.detail");

class ProfileAdd extends PersonalProfileDetail
{
    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new Main\ErrorCollection();

        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'SET_TITLE' => 'N'
        ], $arParams);

        if (isset($arParams['PATH_TO_LIST'])) {
            $arParams['PATH_TO_LIST'] = trim($arParams['PATH_TO_LIST']);
        }

        return $arParams;
    }
    
    public function getPersonTypes($arPersonTypes) {
        if (!empty($arPersonTypes)) {
            $arResult = Arrays::fromDBResult(CSalePersonType::GetList(['ID' => 'ASC'], ['ID' => $arPersonTypes], false, false, []))
                ->indexBy('ID')
                ->asArray();

            return $arResult;
        }

        return null;
    }

    public function executeComponent() {
        global $USER, $APPLICATION;
        $arRequest = Main\Application::getInstance()->getContext()->getRequest();
        $arPersonTypeInfo = [];
        $arTypes = [];

        if (!$USER->IsAuthorized()) {
            if (!$this->arParams['AUTH_FORM_IN_TEMPLATE']) {
                $APPLICATION->AuthForm(Loc::getMessage('C_PROFILE_ADD_ERROR_ACCESS_DENIED'), false, false, 'N', false);
            } else {
                $this->errorCollection->setError(new Main\Error(Loc::getMessage('C_PROFILE_ADD_ERROR_ACCESS_DENIED'), self::E_NOT_AUTHORIZED));
            }
        }

        if ($this->arParams['SET_TITLE'] === 'Y') {
            $APPLICATION->SetTitle(!empty($this->arParams['TITLE']) ? $this->arParams['TITLE'] : Loc::getMessage('C_PROFILE_ADD_TITLE'));
        }

        if (!empty($this->arParams['PERSONS_ID'])) {
            $arTypes = $this->getPersonTypes($this->arParams['PERSONS_ID']);

            foreach ($arTypes as &$arType) {
                $arType['IS_SELECTED'] = 'N';
            }

            unset($arType);

            if (!empty($arRequest->get('PERSON_ID')) && ArrayHelper::keyExists($arRequest->get('PERSON_ID'), $arTypes)) {
                $sSelectedKey = $_REQUEST['PERSON_ID'];
            } else {
                $sSelectedKey = ArrayHelper::getFirstKey($arTypes);
            }

            $arPersonTypeInfo = [
                'PERSON_TYPE_ID' => $sSelectedKey
            ];
            $arTypes[$sSelectedKey]['IS_SELECTED'] = 'Y';
            $this->fillResultArray($arPersonTypeInfo);
            $this->arResult['PERSON_TYPES'] = $arTypes;
        } else {
            $this->errorCollection->setError(new Main\Error(Loc::getMessage('C_PROFILE_ADD_ERROR_EMPTY_PERSON_TYPE')));
        }

        if (!empty($arPersonTypeInfo) && $arRequest->isPost() && $arRequest->get('save') && check_bitrix_sessid()) {
            if (empty($arRequest->get('NAME'))) {
                $arRequest->set('NAME', Loc::getMessage('C_PROFILE_ADD_PROFILE_NAME', [
                    '#LOGIN#' => $USER->GetLogin()
                ]));
            }

            if (!empty($arRequest->get('PERSON_ID')) && ArrayHelper::keyExists($arRequest->get('PERSON_ID'), $arTypes)) {
                $arFields = [
                    'NAME' => $arRequest->get('NAME'),
                    'USER_ID' => $USER->GetID(),
                    'PERSON_TYPE_ID' => $arRequest->get('PERSON_ID')
                ];
                $sProfileId = CSaleOrderUserProps::Add($arFields);
                $arRequestResult = $arRequest->toArray();

                if ($sProfileId !== false) {
                    $this->idProfile = (int) $sProfileId;
                    $arUpdateFields = $this->prepareUpdatingProperties($arRequest, $arPersonTypeInfo);

                    if ($this->errorCollection->isEmpty()) {
                        foreach ($arUpdateFields as $value) {
                            unset($value['MULTIPLE']);

                            if (is_array($value['VALUE'])) {
                                if (ArrayHelper::keyExists('ORDER_PROP_'.$value['ORDER_PROPS_ID'].'_default', $arRequestResult)) {
                                    $arCurrentValues = explode(';', $arRequest->get('ORDER_PROP_'.$value['ORDER_PROPS_ID'].'_default'));

                                    foreach ($arCurrentValues as $sCurrentValue) {
                                        if (!empty($sCurrentValue)) {
                                            array_push($value['VALUE'], $sCurrentValue);
                                        }
                                    }

                                    unset($arCurrentValues, $arCurrentValue);
                                }

                                $value['VALUE'] = serialize($value['VALUE']);
                            }

                            CSaleOrderUserPropsValue::Add($value);
                        }

                        if (!empty($this->arParams['PATH_TO_LIST'])) {
                            LocalRedirect($this->arParams['PATH_TO_LIST']);
                        } else {
                            if ($USER->IsAdmin()) {
                                $this->errorCollection->setError(new Main\Error(Loc::getMessage('C_PROFILE_ADD_ERROR_EMPTY_PATH_TO_LIST')));
                            } else {
                                $this->errorCollection->setError(new Main\Error(Loc::getMessage('C_PROFILE_ADD_ERROR_PROFILE_ADD')));
                            }
                        }
                    } else {
                        CSaleOrderUserProps::Delete($sProfileId);
                    }
                } else {
                    $this->errorCollection->setError(new Main\Error(Loc::getMessage('C_PROFILE_ADD_ERROR_EMPTY_PERSON_TYPE')));
                }
            } else {
                $this->errorCollection->setError(new Main\Error(Loc::getMessage('C_PROFILE_ADD_ERROR_EMPTY_PERSON_TYPE')));
            }
        }

        $this->formatResultErrors();
        $this->includeComponentTemplate();
    }
}
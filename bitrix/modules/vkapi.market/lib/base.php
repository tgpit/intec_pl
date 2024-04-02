<?php

namespace VKapi\Market;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
class Base
{
    private $moduleId = '';
    private $oOption = null;
    private $siteId = null;
    private $arRuntimeParams = array();
    public function __construct($moduleId)
    {
        $this->moduleId = $moduleId;
    }
    public function getModuleId()
    {
        return $this->moduleId;
    }
    public function isUtf()
    {
        return defined('BX_UTF') && BX_UTF === true;
    }
    public function isAdminSection()
    {
        return defined('ADMIN_SECTION') && defined('ADMIN_SECTION') === true;
    }
    public function restoreEncoding($data)
    {
        if ($this->isUtf()) {
            return $data;
        }
        return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'UTF-8', 'WINDOWS-1251');
    }
    public function prepareEncoding($data)
    {
        if ($this->isUtf()) {
            return $data;
        }
        return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'WINDOWS-1251', 'UTF-8');
    }
    public function getToLowerKeys($ar)
    {
        if (is_array($ar)) {
            $ar = array_change_key_case($ar, CASE_LOWER);
            foreach ($ar as $key => &$val) {
                $val = $this->getToLowerKeys($val);
            }
            unset($val);
        }
        return $ar;
    }
    public function showJson($arResult)
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');
        if (!empty($arResult['error'])) {
            echo json_encode(array('error' => $this->prepareEncoding($this->getToLowerKeys($arResult['error']))));
        } else {
            echo json_encode(array('response' => $this->prepareEncoding($this->getToLowerKeys($arResult['response']))));
        }
        self::finish();
    }
    /**
 * ���������� �� ����� ������ json, ����� ������ ������� �� ����������
 */
    public static function finish()
    {
        if (\Bitrix\Main\Loader::includeModule("compression")) {
            \CCompress::DisableCompression();
        }
        \Bitrix\Main\Context::getCurrent()->getResponse()->writeHeaders();
        \Bitrix\Main\Application::getConnection()->disconnect();
        die;
    }
    public function getCurrentSiteId()
    {
        if ($this->isAdminSection() || SITE_ID == LANGUAGE_ID) {
            if (is_null($this->siteId)) {
                $host = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getHttpHost();
                $host = preg_replace('/(:[\\d]+)/', '', $host);
                $oSite = new \CSite();
                $dbr = $oSite->GetList($by = 'sort', $order = 'asc', array('ACTIVE' => 'Y', 'DOMAIN' => $host));
                if ($ar = $dbr->Fetch()) {
                    $this->siteId = $ar['LID'];
                } else {
                    $dbr = $oSite->GetList($by = 'sort', $order = 'asc', array('DEFAULT' => 'Y'));
                    if ($ar = $dbr->Fetch()) {
                        $this->siteId = $ar['LID'];
                    } else {
                        $dbr = $oSite->GetList($by = 'sort', $order = 'asc', array());
                        if ($ar = $dbr->Fetch()) {
                            // ���� ���� �� ������ ����� �� ���������, �� ������� ���� �� ������������ - �� ��������
                            $oSite->Update($ar['ID'], array('DEFAULT' => 'Y'));
                            $this->siteId = $ar['LID'];
                        }
                    }
                }
            }
            return $this->siteId;
        }
        return SITE_ID;
    }
    /**
 * ���������� �������� ���������
 * @param $name
 * @param null $default_value
 * @return null|string
 */
    public function getParam($name, $default_value = null, $siteId = null)
    {
        if (is_null($this->oOption)) {
            $this->oOption = new \Bitrix\Main\Config\Option();
        }
        if (is_null($siteId)) {
            $siteId = $this->getCurrentSiteId();
        }
        try {
            if (array_key_exists($siteId . '|' . $name, $this->arRuntimeParams)) {
                return $this->arRuntimeParams[$siteId . '|' . $name];
            } else {
                $this->arRuntimeParams[$siteId . '|' . $name] = $this->oOption->get($this->getModuleId(), $name, $default_value, $siteId);
            }
            return $this->arRuntimeParams[$siteId . '|' . $name];
        } catch (\Exception $e) {
            return $default_value;
        }
    }
    /**
 * ��������� ���������� �� ����� ����������
 * @param $name
 * @param $value
 * @param null $siteId
 * @return bool
 */
    public function setParam($name, $value, $siteId = null)
    {
        if (is_null($siteId)) {
            $siteId = $this->getCurrentSiteId();
        }
        $this->arRuntimeParams[$siteId . '|' . $name] = $value;
        return true;
    }
    /**
 * ���������� ����� ���������
 * 
 * @param $name
 * @param null $arReplace
 * @return mixed|string
 */
    public function getMessage($name, $arReplace = null)
    {
        return \Bitrix\Main\Localization\Loc::getMessage($this->getModuleId() . '.' . $name, $arReplace);
    }
    public function showAdminPageCssJs()
    {
        \CUtil::InitJSCore('jquery');
        echo self::getAdminPageCssJs();
    }
    public function getAdminPageCssJs()
    {
        $return = '';
        $path = getLocalPath('modules/' . $this->getModuleId());
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css')) {
            $return .= '<style type="text/css" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/css/style.css') . '</style>';
        }
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js')) {
            $return .= '<script type="text/javascript" >' . file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path . '/admin/js/script.js') . '</script>';
        }
        return $return;
    }
    /**
 * �������� ���� �������, ���� ������������ - �� ���������� ����� �����������
 * @param $right
 */
    public function checkAccess($right = 'D')
    {
        global $APPLICATION;
        // W > D
        if (self::getPermission() <= $right) {
            $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
            die;
        }
    }
    /**
 * ��������� ������ � �������� ������ � ������ $right � ����
 * @param $right
 */
    public function checkLevelAccess($right = 'W')
    {
        global $APPLICATION;
        if (self::getPermission() < $right) {
            $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
            die;
        }
    }
    /**
 * �������� ������� ����, ���� ������������� ��� ���� ��� $right - ������ true
 * @param $right
 * @return bool
 */
    public function canActionRight($right)
    {
        // W > D
        return self::getPermission() >= $right;
    }
    /**
 * �������� ����� �������
 * @return bool|null|string
 */
    public function getPermission()
    {
        static $PERMISSION;
        if (empty($PERMISSION)) {
            global $APPLICATION;
            $PERMISSION = $APPLICATION->GetGroupRight($this->getModuleId());
        }
        return $PERMISSION;
    }
    /**
 * ������������� ��������� �� ������
 * @param $text
 */
    public function showAdminPageSuccess($text)
    {
        ?>
            <div class="adm-info-message-wrap adm-info-message-gree">
                <div class="adm-info-message">
                    <div class="adm-info-message-title"><?php 
        echo $text;
        ?></div>
                    <div class="adm-info-message-icon"></div>
                </div>
            </div>
            <?php 
    }
    /**
 * ������������� ��������� � �����������
 * @param $text
 */
    public function showAdminPageInfo($text)
    {
        ?>
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <div class="adm-info-message-title"><?php 
        echo $text;
        ?></div>
                </div>
            </div>
            <?php 
    }
    public function showAdminPageError($text)
    {
        ?>
            <div class="adm-info-message-wrap adm-info-message-red">
                <div class="adm-info-message">
                    <div class="adm-info-message-title"><?php 
        echo $text;
        ?></div>
                    <div class="adm-info-message-icon"></div>
                </div>
            </div>
            <?php 
    }
}
?>
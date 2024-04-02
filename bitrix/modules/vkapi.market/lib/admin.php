<?php

namespace VKapi\Market;

use Bitrix\Main\Localization\Loc;
/**
     * Класс для сокращения опреаций в дадминистративном разделе
     * Class Admin
     * 
     * @package VKapi\Market
     */
class Admin
{
    protected $moduleId = '';
    protected $tableId = '';
    protected $arSortFields = array();
    // массив полей сортируемых в списке записей
    protected $defaultSortField = '';
    // поле сортировки по умолчанию в списке значений
    /**
         * Массив описания полей фильтра
         * 
         * @var array
         */
    protected $arFilterFields = array();
    /**
         * @var \VKapi\Market\Message
         */
    private $oMessage = null;
    /**
         * @var \CAdminList
         */
    private $oAdminList = null;
    /**
         * Массив с параметрами фильтрации списка
         * @var array
         */
    private $arFilter = array();
    /**
         * Класс для сокращения опреаций в дадминистративном разделе
         * Admin constructor.
         * 
         * @param $moduleId - идентификатор текущего модуля
         */
    public function __construct($moduleId)
    {
        $this->moduleId = $moduleId;
    }
    /**
         * Передача объекта для работы соссписком записей административной страницы
         * 
         * @param \CAdminList $oAdminList
         */
    public function setAdminList($oAdminList)
    {
        $this->oAdminList = $oAdminList;
    }
    /**
         * Передача объект работы с языковыми сообщениями
         * 
         * @param \VKapi\Market\Message $oMessage
         */
    public function setMessage(\VKapi\Market\Message $oMessage)
    {
        $this->oMessage = $oMessage;
    }
    public function getModuleId()
    {
        return $this->moduleId;
    }
    /**
         * Объект дял вывода языковых сообщений
         * 
         * @return \VKapi\Market\Message
         */
    public function message()
    {
        return $this->oMessage;
    }
    /**
         * Установка идентификатора таблицы/скписка
         * 
         * @param $tableId
         */
    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
    }
    /**
         * Вернет идентификтаор таблицы/списка
         * 
         * @return string
         */
    public function getTableId()
    {
        return $this->tableId;
    }
    /**
         * Поля для сортировки в спсике записей
         * 
         * @param $arSortFields - массив полей для сортировки
         * @param string $defaultSortField - поле для сортировки по умолчанию
         */
    public function setSortFields($arSortFields, $defaultSortField = null)
    {
        $this->arSortFields = $arSortFields;
        $this->defaultSortField = !is_null($defaultSortField) ? $defaultSortField : reset($arSortFields);
    }
    /**
         * Вернет массив сайтов
         * 
         * @return mixed
         */
    public function getSiteList()
    {
        static $arSite;
        if (!isset($arSite)) {
            $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
            while ($ar = $dbr->Fetch()) {
                $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
            }
        }
        return $arSite;
    }
    /**
         * Вернет массив для зарпоса списка записей на странице списка
         * 
         * @return array
         */
    public function getListQuery()
    {
        global $APPLICATION;
        $arQuery = array('select' => array('*'), 'filter' => $this->getFilter());
        $by = $this->defaultSortField;
        if (isset($_GET['by']) && in_array($_GET['by'], $this->arSortFields)) {
            $by = $_GET['by'];
        }
        $arOrder = array($by => strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC');
        $arQuery['order'] = $arOrder;
        $navyParams = \CDBResult::GetNavParams(\CAdminResult::GetNavSize($this->getTableId(), array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())));
        $usePageNavigation = true;
        if ($navyParams['SHOW_ALL']) {
            $usePageNavigation = false;
        } else {
            $navyParams['PAGEN'] = (int) $navyParams['PAGEN'];
            $navyParams['SIZEN'] = (int) $navyParams['SIZEN'];
        }
        if ($usePageNavigation) {
            $arQuery['limit'] = $navyParams['SIZEN'];
            $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
        }
        return $arQuery;
    }
    /**
         * Сформирует url страницы на которую необходдимо преейти, напрмиер {module_id}_list.php?lang=ru
         * 
         * @param $pageCode - код страницы, напрмиер list
         * @param array $arParams - парамтеры в url, по умолчаниб передается идентфиикатор языка
         * @param array $arDeleteParams - параметры для удаления из url
         * @return string
         */
    public function getPageUrl($pageCode, $arParams = array(), $arDeleteParams = array())
    {
        $arParams['lang'] = LANG;
        $arParams = array_diff_key($arParams, array_flip($arDeleteParams));
        return $this->getModuleId() . '_' . $pageCode . '.php?' . http_build_query($arParams);
    }
    /**
         * Вернет полный url для админки включая /bitrix/admin/...
         * 
         * @param $pageCode - код страницы, напрмиер list
         * @param array $arParams - парамтеры в url, по умолчаниб передается идентфиикатор языка
         * @param array $arDeleteParams - параметры для удаления из url
         * @return string
         */
    public function getFullPageUrl($pageCode, $arParams = array(), $arDeleteParams = array())
    {
        return '/bitrix/admin/' . $this->getPageUrl($pageCode, $arParams, $arDeleteParams);
    }
    // Работа с фильтром ------------------------
    /**
         * Добавления поля для фильтрации
         * 
         * @param $name
         */
    public function addFilterField($code, $arParams = array())
    {
        $this->arFilterFields[$code] = $arParams;
    }
    /**
         * Выводит html фильтра над списком элементов
         */
    public function showFilter()
    {
        global $APPLICATION;
        $arFilterNames = array();
        foreach ($this->arFilterFields as $fieldCode => $arField) {
            $arFilterNames[] = $this->message()->get('FILTER.' . $fieldCode);
        }
        // создадим объект фильтра
        $oFilter = new \CAdminFilter($this->getTableId() . "_filter", $arFilterNames);
        ?>
            <form name="filter_form" method="get" action="<?php 
        echo $APPLICATION->GetCurPage();
        ?>">
                <?php 
        $oFilter->Begin();
        foreach ($this->arFilterFields as $fieldCode => $arField) {
            $arFilterNames[] = $this->message()->get('FILTER.' . $fieldCode);
            $filterCode = 'filter_field_' . $fieldCode;
            $fieldValue = $GLOBALS['filter_field_' . $filterCode];
            $fieldValueFrom = $GLOBALS['filter_field_' . $filterCode . '_from'];
            $fieldValueTo = $GLOBALS['filter_field_' . $filterCode . '_to'];
            ?>
                        <tr>
                            <td><?php 
            echo $this->message()->get('FILTER.' . $fieldCode);
            ?>:</td>
                            <td>
                                <?php 
            switch ($arField['TYPE']) {
                case 'LIST':
                    echo SelectBoxFromArray($filterCode, $arField['VALUES'], $fieldValue);
                    break;
                case 'PERIOD':
                    echo CalendarPeriod($filterCode . "_from", $fieldValueFrom, $filterCode . "_to", $fieldValueTo, "filter_form", "Y");
                    break;
                default:
                    echo InputType("text", $filterCode, $fieldValue, '');
                    break;
            }
            ?>
                            </td>
                        </tr>
                        <?php 
        }
        $oFilter->Buttons(array("table_id" => $this->getTableId(), "url" => $APPLICATION->GetCurPage(), "form" => "filter_form"));
        $oFilter->End();
        ?>
            </form>

            <?php 
    }
    public function checkFilter()
    {
        $this->arFilter = array();
        if (empty($this->oAdminList)) {
            return false;
        }
        $arPrepiredFields = array();
        foreach (array_keys($this->arFilterFields) as $filterCode) {
            $arPrepiredFields[] = 'filter_field_' . $filterCode;
        }
        // инициализируем фильтр
        $this->oAdminList->InitFilter($arPrepiredFields);
        // если все значения фильтра корректны, обработаем его
        if (count($this->oAdminList->arFilterErrors) == 0) {
            // подготовим значения
            foreach ($this->arFilterFields as $filterCode => $arField) {
                $fieldValue = $GLOBALS['filter_field_' . $filterCode];
                $fieldValueFrom = $GLOBALS['filter_field_' . $filterCode . '_from'];
                $fieldValueTo = $GLOBALS['filter_field_' . $filterCode . '_to'];
                switch ($arField['TYPE']) {
                    case 'LIST':
                        if (strlen(trim($fieldValue)) > 0 && in_array(trim($fieldValue), $arField['VALUES']['REFERENCE_ID'])) {
                            $this->arFilter[$filterCode] = trim($fieldValue);
                        }
                        break;
                    case 'PERIOD':
                        if (strlen(trim($fieldValueFrom)) > 0 || strlen(trim($fieldValueTo)) > 0) {
                            if (strlen(trim($fieldValueFrom)) > 0 && strlen(trim($fieldValueTo)) > 0) {
                                $dateStart = new \Bitrix\Main\Type\DateTime($fieldValueFrom);
                                $dateStart->setTime(0, 0, 0);
                                $dateStop = new \Bitrix\Main\Type\DateTime($fieldValueTo);
                                $dateStop->setTime(23, 23, 59);
                                $this->arFilter['><' . $filterCode] = array($dateStart, $dateStop);
                            } elseif (strlen(trim($fieldValueFrom)) > 0) {
                                $dateStart = new \Bitrix\Main\Type\DateTime($fieldValueFrom);
                                $dateStart->setTime(0, 0, 0);
                                $this->arFilter['>' . $filterCode] = $dateStart;
                            } elseif (strlen(trim($fieldValueTo)) > 0) {
                                $dateStop = new \Bitrix\Main\Type\DateTime($fieldValueTo);
                                $dateStop->setTime(23, 23, 59);
                                $this->arFilter['<' . $filterCode] = $dateStop;
                            }
                        }
                        break;
                    default:
                        if (strlen(trim($fieldValue)) > 0) {
                            $this->arFilter[$filterCode] = $fieldValue;
                        }
                        break;
                }
            }
        }
    }
    /**
         * Вернет массив с парамтерами фильтрации списка элементов в списке
         * @return array
         */
    public function getFilter()
    {
        return $this->arFilter;
    }
}
?>
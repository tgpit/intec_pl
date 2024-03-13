<?php
require(dirname(__FILE__).'/../../../../lib/PHPExcel/PHPExcel.php');

use Bitrix\Main\Loader;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\helpers\Json;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\base\InvalidParamException;
use intec\importexport\models\excel\TableHelper;
use intec\importexport\models\excel\IBlockHelper;
use intec\importexport\models\excel\export\Filter;
use intec\importexport\models\excel\export\Template;

class Export
{
    /**
     * Содержит основные настройки таблицы(цвет, шрифт, описание и тд.).
     * @var array
     */
    private $settings = [

    ];

    /**
     * Cодержит данные для выборки из инфоблока(IBLOCK_ID, количество и тд.).
     * @var array
     */
    private $baseData = [];

    /**
     * Содержит основные данные(Первая строка таблицы, какие свойства выводить, в каком порядке, оформление и тд).
     * @var array
     */
    private $tableData = [];

    /**
     * Содержит настройки калонок.
     * @var array
     */
    private $columnSettings = [];

    /**
     * Содержит имена для первой строчки.
     * @var array
     */
    private $propertiesList = [];

    private $objPhpExcel;

    /**
     * Массив ‘errors’ должен содержать массив вида
     * 'message' => Loc::getMessage(),
     * 'systemMessage' => $e->getMessage(), - если есть
     * 'exception' => $e - если есть
     * @var array
     */
    private $error = [
        'is' => false,
        'errors' => []
    ];

    private $isBase = true;

    public function __construct($baseData = [], $tableData = [], $columnSettings = [], $settings = [])
    {
        if (empty($baseData) || empty($tableData))
            return;

        $this->isBase = Loader::includeModule('catalog');

        $this->setBaseData($baseData);
        $this->setTableData($tableData);
        $this->setSetting($settings);
        $this->setColumnSettings($columnSettings);
    }

    public function init() {

    }

    public function getErrors ()
    {
        return $this->error;
    }

    private function setSetting ($data)
    {
        if (!empty($data))
            $this->settings = $data;
    }

    public function getSettings ()
    {
        return $this->settings;
    }

    private function setBaseData ($data)
    {
        if (!empty($data))
            $this->baseData = $data;
    }

    public function getBaseData ()
    {
        return $this->baseData;
    }

    private function setTableData ($data)
    {
        if (!empty($data))
            $this->tableData = $data;
    }

    public function getTableData ()
    {
        return $this->tableData;
    }

    private function setColumnSettings ($data)
    {
        if (!empty($data))
            $this->columnSettings = $data;
    }

    public function getColumnSettings ()
    {
        return $this->columnSettings;
    }

    /**********************************************************************************************************************/


    private function getCsvEnclosure ()
    {
        $enclosure = '"';

        switch ($this->baseData['csvEnclosure']) {
            case 'dquote':
                $enclosure = '"';
                break;
            case 'quote':
                $enclosure = '\'';
                break;
            case 'none':
                $enclosure = '';
                break;
        }

        return $enclosure;
    }

    /**
     * Создает первую строку в файле.
     */
    private function generateFirstRow ()
    {
        $counter = 0;

        $propertiesList = IBlockHelper::getAllProperties($this->baseData['iblock'], false, $this->baseData['level']);
        $propertiesList = TableHelper::getIndexByCode($propertiesList);

        foreach ($this->tableData['data'] as $value) {
            $counter++;

            if (!empty($this->columnSettings['newName'][$counter - 1]))
                $name = $this->columnSettings['newName'][$counter - 1];
            else
                $name = $propertiesList[$value]['name'];

            if (Encoding::detectUtf8($name))
                $this->objPhpExcel->setActiveSheetIndex(0)->setCellValue(TableHelper::getLetter($counter) . '1', $name);
            else
                $this->objPhpExcel->setActiveSheetIndex(0)->setCellValue(TableHelper::getLetter($counter) . '1', Encoding::convertEncoding($name, LANG_CHARSET, 'UTF-8'));

            $this->objPhpExcel->getActiveSheet()->getColumnDimension(TableHelper::getLetter($counter))->setWidth(20);
            $this->objPhpExcel->getActiveSheet()->getStyle(TableHelper::getLetter($counter) . '1')->getFont()->setBold(true);
        }

        unset($counter);
    }

    /**
     * Создает строку в файле.
     * @param $cell - ячейка.
     * @param $content - содержание ячейки
     */
    private function generateRow ($cell, $content, $objSheet)
    {
        if (Encoding::detectUtf8($content))
            $objSheet->setCellValue($cell, $content);
        else
            $objSheet->setCellValue($cell, Encoding::convertEncoding($content, LANG_CHARSET, 'UTF-8'));
    }

    /**
     * Создает все строки в файле кроме первой.
     */
    private function generateRows ()
    {
        $sortBy = $this->tableData['param']['general']['sortBy'];
        $sortBy = !empty($sortBy) ? $sortBy : ArrayHelper::getFirstValue($this->tableData['data']);
        $sortOrder = $this->tableData['param']['general']['sortOrder'];
        $sortOrder = !empty($sortOrder) ? $sortOrder : 'ASC';

        $sort = [
            $sortBy => $sortOrder
        ];

        $select = $this->getSelect();
        /* /resolve */

        $filter = [];

        if (!empty($this->tableData['filter'])) {
            $filter = Filter::getFilter($this->tableData['filter'], $this->baseData['iblock']);
        }

        $elements = IBlockHelper::getElements($this->baseData['iblock'], $sort, $filter, $select, 0, 0, true, true);

        $elements = IBlockHelper::prepareElement($elements, $this->baseData['delimiter']);
        $elements = IBlockHelper::getCatalogPropertyElements($elements);
        $elements = IBlockHelper::getSeoPropertyElements($elements);
        $elements = IBlockHelper::getSectionsProperties($elements, $this->baseData['iblock'], $this->baseData['level']);
        $elements = IBlockHelper::getOffers($elements, $this->baseData['iblock'], $this->baseData['offersFormat'], $this->baseData['offersPriceType'], $this->baseData['delimiter'], $this->baseData['useNonPrice']);

        $result = IBlockHelper::getselected($elements, $this->tableData['data'], $this->columnSettings['settings'], $this->baseData['delimiter']);

        unset($elements);

        $row = 1;
        $column = 0;
        $objSheet = $this->objPhpExcel->getActiveSheet();

        foreach ($result as $i) {
            $row++;

            foreach ($i as $j) {
                $column++;

                $this->generateRow(TableHelper::getLetter($column) . $row, $j, $objSheet);
                $this->objPhpExcel->getActiveSheet()->getStyle(TableHelper::getLetter($column) .''. $row)->getAlignment()->setWrapText(true);
            }

            $column = 0;
        }
    }

    /**
     * Устанавливает основные настройки файла.
     */
    private function setExcelSettings ()
    {
        $this->objPhpExcel->getProperties()->setCreator("Intec")
            ->setLastModifiedBy("Intec")
            ->setTitle("Intec")
            ->setSubject("Intec")
            ->setDescription("Intec")
            ->setKeywords("Intec")
            ->setCategory("Intec");
    }

    /**
     * Записывает файл.
     */
    private function saveExcel ()
    {
        if ($this->baseData['type'] === 'xlsx') {
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPhpExcel, 'Excel2007');
        } else if ($this->baseData['type'] === 'xls') {
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPhpExcel, 'Excel5');
        } else if ($this->baseData['type'] === 'csv') {
            $objWriter = PHPExcel_IOFactory::createWriter($this->objPhpExcel, 'CSV')->setDelimiter($this->baseData['csvDelimiter'])
                                                                                    ->setEnclosure(self::getCsvEnclosure())
                                                                                    ->setSheetIndex(0);
        } else {
            return;
        }

        TableHelper::checkAndCreateDirectory($this->baseData['path']);

        try {
            $objWriter->save($_SERVER['DOCUMENT_ROOT'] . $this->baseData['path']);
        } catch (Exception $e) {
            $this->error['is'] = false;
            $this->error['errors'][] = [
                'message' => Loc::getMessage('importexport.export.error.save'),
                'systemMessage' => $e->getMessage(),
                'exception' => $e
            ];
        }
    }

    /**
     * @param null $id - id шаблона экспорта
     *
     */
    public function generateExcelByTemplateId ($id = null)
    {
        if (empty($id))
            return;

        $template = Template::findOne($id);

        if (empty($template))
            return;

        try {
            $parameters = Json::decode($template->getAttribute('params'), true, true);
        } catch (InvalidParamException $exception) {
            $parameters = [];
        }

        try {
            $tableParameters = Json::decode($template->getAttribute('tableParams'), true, true);
        } catch (InvalidParamException $exception) {
            $tableParameters = [];
        }

        $this->generateExcel();
    }

    /**
     * @return null
     */
    public function generateExcel ()
    {
        if (empty($this->baseData) || empty($this->tableData['data']))
            return null;

        // Create new PHPExcel object
        $this->objPhpExcel = new PHPExcel();

        // Set document properties
        $this->setExcelSettings();

        // Add some data
        $this->generateFirstRow();
        $this->generateRows();

        // Save Excel file
        $this->saveExcel();

        $return  = [
            'downloadLink' => $this->baseData['path'],
            'resultMessage' => !$this->error['is'] ?
                Loc::getMessage('intec.importexport.models.excel.export.export.result.success') :
                Loc::getMessage('intec.importexport.models.excel.export.export.result.failed'),
            'error' => $this->error
        ];

        return $return;
    }

    public function generateExcelCron ($id)
    {
        $return = null;

        $template = Template::findOne($id);

        if (empty($template))
            return null;

        if (empty($this->baseData)) {
            try {
                $this->setBaseData(Json::decode($template->getAttribute('params')));

                if (empty($this->baseData['path'])) {
                    $this->error['is'] = true;
                    $this->error['errors'][] = [
                        'message' => Loc::getMessage('intec.importexport.export.error.non.path')
                    ];
                }

                if (empty($this->baseData['iblock'])) {
                    $this->error['is'] = true;
                    $this->error['errors'][] = [
                        'message' => Loc::getMessage('intec.importexport.export.error.non.iblock')
                    ];
                }
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (empty($this->tableData)) {
            try {
                $this->setTableData(Json::decode($template->getAttribute('tableParams')));
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (empty($this->columnSettings)) {
            try {
                $this->setColumnSettings(Json::decode($template->getAttribute('columnSettings'), true, true));
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (!$this->error['is']) {
            unset($_SESSION['INTEC_EXPORT']);

            $return = $this->generateExcel();
        }

        if (empty($return)) {
            $return = [
                'resultMessage' => Loc::getMessage('intec.importexport.models.excel.export.export.result.failed'),
                'error' => $this->error
            ];
        }

        return $return;
    }

    public function generateExcelByIdOnSteps ($id = null, $isNew = true)
    {
        if (empty($id))
            return null;

        $template = Template::findOne($id);

        if (empty($template))
            return null;

        ini_set('memory_limit', '2048M');

        if (empty($this->baseData)) {
            try {
                $this->setBaseData(Json::decode($template->getAttribute('params')));
            } catch (InvalidParamException $exception) {
                return null;
            }

            $this->error['errors'][] = [
                'message' => Loc::getMessage('importexport.export'),
            ];
        }

        if (empty($this->tableData)) {
            try {
                $this->setTableData(Json::decode($template->getAttribute('tableParams')));
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        if (empty($this->columnSettings)) {
            try {
                $this->setColumnSettings(Json::decode($template->getAttribute('columnSettings'), true, true));
            } catch (InvalidParamException $exception) {
                return null;
            }
        }

        // Create new PHPExcel object
        if($isNew && $_SESSION['INTEC_EXPORT']['CURRENT_STEP'] <= 1) {
            $this->objPhpExcel = new PHPExcel();

            $_SESSION['INTEC_EXPORT'] = [
                'STEP' => $this->baseData['unloadInStep'],
                'CURRENT_STEP' => 1,
                'FIRST_ELEMENT_ID' => null,
                'STATUS' => 'begin'
            ];
        } else {
            try {
                if ($this->baseData['type'] === 'csv') {
                    $objReader = PHPExcel_IOFactory::createReader('CSV')->setDelimiter($this->baseData['csvDelimiter'])
                                                                        ->setEnclosure(self::getCsvEnclosure())
                                                                        ->setSheetIndex(0);
                    $this->objPhpExcel = $objReader->load($_SERVER['DOCUMENT_ROOT'] . $this->baseData['path']);
                } else {
                    $this->objPhpExcel = PHPExcel_IOFactory::load($_SERVER['DOCUMENT_ROOT'] . $this->baseData['path']);
                }
            } catch (Exception $e) {
                $this->objPhpExcel = new PHPExcel();

                $_SESSION['INTEC_EXPORT'] = [
                    'STEP' => $this->baseData['unloadInStep'],
                    'CURRENT_STEP' => 1,
                    'FIRST_ELEMENT_ID' => null,
                    'STATUS' => 'begin'
                ];
            }
        }

        if ($_SESSION['INTEC_EXPORT']['CURRENT_STEP'] <= 1)
            $this->generateFirstRow();

        $this->generateRowsOnStep();

        if ($_SESSION['INTEC_EXPORT']['STATUS'] !== 'end')
            $this->saveExcel();

        $return  = [
            'step' => Type::toInteger($_SESSION['INTEC_EXPORT']['STEP']),
            'currentStep' => Type::toInteger($_SESSION['INTEC_EXPORT']['CURRENT_STEP']),
            'status' => $_SESSION['INTEC_EXPORT']['STATUS'],
            'downloadLink' => $this->baseData['path'],
            'resultMessage' => !$this->error['is'] ?
                Loc::getMessage('intec.importexport.models.excel.export.export.result.success') :
                Loc::getMessage('intec.importexport.models.excel.export.export.result.failed'),
            'error' => $this->error
        ];

        if ($_SESSION['INTEC_EXPORT']['STATUS'] === 'end') {
            unset($_SESSION['INTEC_EXPORT']);
        }

        return $return;
    }

    private function getSelect ()
    {
        $defaultList = [
            'ID',
            'NAME',
            'CODE',
            'XML_ID',
            'PREVIEW_PICTURE',
            'PREVIEW_PICTURE_DESCRIPTION',
            'PREVIEW_TEXT',
            'PREVIEW_TEXT_TYPE',
            'DETAIL_PICTURE',
            'DETAIL_PICTURE_DESCRIPTION',
            'DETAIL_TEXT',
            'DETAIL_TEXT_TYPE',
            'ACTIVE',
            'ACTIVE_FROM',
            'ACTIVE_TO',
            'SORT',
            'TAGS',
            'DATE_CREATE',
            'CREATED_BY',
            'TIMESTAMP_X',
            'MODIFIED_BY',
            'SHOW_COUNTER',
            'DETAIL_PAGE_URL',
            'SECTION_PATH'
        ];

        $result = $defaultList;

        foreach ($this->tableData['data'] as $field) {
            if (!empty($field)) {
                if (StringHelper::startsWith($field, 'PROPERTY_'))
                    $result[] = $field;
            }
        }

        foreach ($this->columnSettings['settings'] as $setting) {
            if (!empty($setting)) {
                try {
                    $setting = Json::decode($setting, true, true);

                    if (!empty($setting)) {
                        foreach ($setting as $settingItems) {
                            foreach ($settingItems as $settingItem) {
                                if (StringHelper::startsWith($settingItem, 'PROPERTY_'))
                                    $result[] = $settingItem;
                            }
                        }
                    }
                } catch (InvalidParamException $exception) {
                    $setting = [];
                }
            }
        }

        $result = array_unique($result);

        return $result;
    }

    private function generateRowsOnStep ()
    {
        $sortBy = $this->tableData['param']['general']['sortBy'];
        $sortBy = !empty($sortBy) ? $sortBy : ArrayHelper::getFirstValue($this->tableData['data']);
        $sortOrder = $this->tableData['param']['general']['sortOrder'];
        $sortOrder = !empty($sortOrder) ? $sortOrder : 'ASC';
        $sort = [
            $sortBy => $sortOrder
        ];

        $step = $_SESSION['INTEC_EXPORT']['STEP'];
        $offset = $_SESSION['INTEC_EXPORT']['CURRENT_STEP'];

        /* resolve problem in many properties*/
        $select = $this->getSelect();
        /* /resolve */

        $filter = [];

        if (!empty($this->tableData['filter'])) {
            $filter = Filter::getFilter($this->tableData['filter'], $this->baseData['iblock']);
        }

        $elements = IBlockHelper::getElements($this->baseData['iblock'], $sort, $filter, $select, $step, $offset, true, true);

        if (empty($_SESSION['INTEC_EXPORT']['FIRST_ELEMENT_ID'])) {
            $_SESSION['INTEC_EXPORT']['FIRST_ELEMENT_ID'] = ArrayHelper::getFirstValue($elements)['ID'];
        } elseif ($_SESSION['INTEC_EXPORT']['FIRST_ELEMENT_ID'] === ArrayHelper::getFirstValue($elements)['ID']) {
            $_SESSION['INTEC_EXPORT']['STATUS'] = 'end';
            return null;
        }

        $elements = IBlockHelper::prepareElement($elements, $this->baseData['delimiter']);
        $elements = IBlockHelper::getCatalogPropertyElements($elements);
        $elements = IBlockHelper::getSeoPropertyElements($elements);
        $elements = IBlockHelper::getSectionsProperties($elements, $this->baseData['iblock'], $this->baseData['level']);
        $elements = IBlockHelper::getOffers($elements, $this->baseData['iblock'], $this->baseData['offersFormat'], $this->baseData['offersPriceType'], $this->baseData['delimiter'], $this->baseData['useNonPrice']);

        $result = IBlockHelper::getselected($elements, $this->tableData['data'], $this->columnSettings['settings'], $this->baseData['delimiter']);

        unset($elements);

        $row = 1;

        if ($offset > 1)
            $row = $step * ($offset -1);

        $column = 0;
        $objSheet = $this->objPhpExcel->getActiveSheet();

        foreach ($result as $i) {
            $row++;

            foreach ($i as $j) {
                $column++;

                $this->generateRow(TableHelper::getLetter($column) . $row, $j, $objSheet);
                $objSheet->getStyle(TableHelper::getLetter($column) .''. $row)->getAlignment()->setWrapText(true);
            }

            $column = 0;
        }

        $_SESSION['INTEC_EXPORT']['CURRENT_STEP']++;
    }
}
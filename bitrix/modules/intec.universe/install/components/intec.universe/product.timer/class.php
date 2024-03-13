<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Sale\Internals\DiscountTable;
use intec\core\bitrix\Component;
use intec\core\bitrix\components\IBlockElements;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

class IntecProductTimerComponent extends IBlockElements
{
    /**
     * ID элемента.
     * @var string
     */
    private $ELEMENT_ID = null;

    /**
     * Режим работы.
     * @var string
     */
    private $TIMER_MODE = 'discount';

    /**
     * Дата завершения скидка.
     * @var object DateTime
     */
    private $DISCOUNT_END_DATE = null;

    /**
     * Статус даты.
     * @var null|string
     */
    private $DATE_STATUS = null;

    /**
     * ID скидки товара.
     * @var null|integer
     */
    private $DISCOUNT_ID = null;

    /**
     * Название скидки.
     * @var null|string
     */
    private $DISCOUNT_NAME = null;


    private $PRODUCT_QUANTITY = null;

    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'TIME_OUT_SHOW' => 'N',
            'MODE' => 'discount',
            'ELEMENT_ID_INTRODUCE' => 'N',
            'ELEMENT_ID_INTRODUCE_VALUE' => null,
            'ELEMENT_ID' => null,
            'PROPERTY_TIMER' => null,
            'TIMER_FORMAT' => 'hours',
            'TIMER_SECONDS_SHOW' => 'N',
            'TIMER_QUANTITY_SHOW' => 'Y',
            'TIMER_QUANTITY_ENTER_VALUE' => 'N',
            'TIMER_PRODUCT_UNITS_USE' => 'Y',
            'QUANTITY' => 0,
            'TIMER_QUANTITY_HEADER_SHOW' => 'Y',
            'TIMER_QUANTITY_HEADER' => null,
            'TIMER_HEADER_SHOW' => 'Y',
            'TIMER_HEADER' => null,
            'ONLY_FOR_NEW_USER_USE' => 'N',
            'IS_SECTION' => false
        ], $arParams);

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent()
    {
        if (!Loader::includeModule('iblock'))
            return;

        if(!CModule::IncludeModule("catalog") && $this->arParams['MODE'] !== 'set')
            return;

        $this->arResult = [
            'VISUAL' => [
                'TITLE' => [
                    'VALUE' => null,
                ],
                'HEADER' => [
                    'SHOW' => $this->arParams['TIMER_HEADER_SHOW'] === 'Y',
                    'VALUE' => !empty($this->arParams['TIMER_HEADER']) ?
                        $this->arParams['TIMER_HEADER'] :
                        Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_HEADER_DEFAULT'),
                ],
                'QUANTITY' => [
                    'SHOW' => $this->arParams['TIMER_QUANTITY_SHOW'] === 'Y',
                    'HEADER' => [
                        'SHOW' => $this->arParams['TIMER_QUANTITY_HEADER_SHOW'] === 'Y',
                        'VALUE' => !empty($this->arParams['TIMER_QUANTITY_HEADER']) ?
                            $this->arParams['TIMER_QUANTITY_HEADER'] :
                            Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_HEADER_DEFAULT'),
                    ],
                    'UNITS' => [
                        'USE' => $this->arParams['TIMER_PRODUCT_UNITS_USE'] === 'Y',
                        'VALUE' => null
                    ]
                ],
                'BLOCKS' => [
                    'SECONDS' => $this->arParams['TIMER_SECONDS_SHOW'] === 'Y',
                    'SECTION' => $this->arParams['IS_SECTION'] === 'Y'
                ]
            ],
            'ITEM' => [],
            'DATA' => [
                'TIMER' => [
                    'ZERO' => $this->arParams['TIME_ZERO_HIDE'] === 'Y',
                    'PRODUCT' => [
                        'QUANTITY' => null,
                    ]
                ]
            ],
            'DATE' => [
                'STATUS' => null,
                'INCORRECT' => null,
                'COMPLETE' => null,
                'VALUE' => [],
                'REMAINING' => [],
                'END' => null,
            ]
        ];

        $this->setMode($this->arParams['MODE']);

        $oDateEnd = null;

        if ($this->TIMER_MODE === 'set') {
            if ($this->isDate($this->arParams['UNTIL_DATE'])) {
                $oDateEnd = new DateTime($this->arParams['UNTIL_DATE']);
            } else {
                $this->arResult['DATE']['INCORRECT'] = $this->arParams['UNTIL_DATE'];

                if (!empty($this->arParams['UNTIL_DATE']))
                    $this->setDateStatus("incorrect");
            }

            if ($this->arParams['TIMER_QUANTITY_ENTER_VALUE'] === 'Y' && !empty($this->arParams['QUANTITY'])) {
                $this->setProductQuantity($this->arParams['QUANTITY']);
            } else {

                if ($this->setElementId()) {
                    $this->arResult['ITEM'] = $this->getItem();

                    if ($this->arResult['VISUAL']['QUANTITY']['UNITS']['USE'])
                        $this->setProductUnit($this->arResult['ITEM']);

                    $this->setProductQuantity($this->arResult['ITEM']['QUANTITY']);
                } else {
                    $this->setProductQuantity(0);
                }
            }
        }

        if ($this->TIMER_MODE === 'discount') {
            if ($this->setElementId()) {
                $this->arResult['ITEM'] = $this->getItem();

                if ($this->arResult['VISUAL']['QUANTITY']['UNITS']['USE'])
                    $this->setProductUnit($this->arResult['ITEM']);

                $this->setProductQuantity($this->arResult['ITEM']['QUANTITY']);

                if ($this->getDiscount()) {
                    $oDateEnd = $this->DISCOUNT_END_DATE;
                } else {
                    $this->setDateStatus("none-discount"); //or discount time end
                }
            } else {
                $this->setDateStatus("error");
            }
        }

        $this->arResult['DATA']['TIMER']['PRODUCT']['QUANTITY'] = $this->PRODUCT_QUANTITY;

        if (!empty($oDateEnd)) {
            $this->setDiscountEndDate($oDateEnd);
            $this->arResult['DATE']['COMPLETE'] = $oDateEnd;
            $this->setDateEndInResult($oDateEnd);
            $this->calculateRemainingTime($oDateEnd);

            if ($this->compareDate($this->DISCOUNT_END_DATE)) {
                $this->arResult['DATE']['VALUE'] = $this->dateFormat($oDateEnd);
                $this->setDiscountEndDate($oDateEnd);
            } elseif (empty($this->DATE_STATUS)) {
                $this->setDateStatus("passed");
            }
        }

        if (empty($oDateEnd) && empty($this->DATE_STATUS)) {
            $this->setDateStatus("error");
        }

        if (!empty($this->DATE_STATUS)) {
            $this->arResult['DATE']['STATUS'] = $this->DATE_STATUS;
        }

        $this->includeComponentTemplate();

        return null;
    }

    /**
     * Проверка строки на возможность создания даты(корректность).
     * @param string $sDate - дата
     * @return bool
     */
    private function isDate($sDate = null)
    {
        if (!empty($sDate)) {
            return is_numeric(strtotime($sDate));
        } else {
            return false;
        }
    }

    /**
     * Устанавливает режим работы.
     * @param string $sMode - режим работы
     */
    private function setMode($sMode = 'discount')
    {
        if (!empty($sMode)) {
            $this->TIMER_MODE = $sMode;
        } else {
            $this->TIMER_MODE = 'discount';
        }
    }

    /**
     * Преобразует дату в массив.
     * @param object DataTime $oDate - дата
     * @return  array $arDate- массив
     */
    private function dateFormat($oDate)
    {
        if (empty($oDate))
            return null;

        $arDate = explode(',', $oDate->format('Y,m,d,H,i,s'));

        foreach ($arDate as &$value) {
            $value = Type::toInteger($value);
        }

        return $arDate;
    }

    /**
     * Записывает в arResult дату завершения скидки.
     * @param $oDate
     * @return null
     */
    private function setDateEndInResult($oDate)
    {
        if (empty($oDate))
            return null;

        $this->arResult['DATE']['END'] = $oDate->format('Y-m-d H:i:s');

        return $this->arResult['DATE']['END'];
    }

    /**
     * Задает ID элемента.
     * @return bool
     */
    private function setElementId()
    {
        if (empty($this->arParams['ELEMENT_ID']))
            return false;

        $this->ELEMENT_ID = $this->arParams['ELEMENT_ID'];

        return true;
    }

    /**
     * Получает элемент.
     * @return array
     */
    private function getItem()
    {
        if (empty($this->ELEMENT_ID))
            return null;

        if (!CModule::IncludeModule("catalog") && Loader::includeModule('intec.startshop'))
            $arItem = Arrays::fromDBResult(CStartShopCatalogProduct::GetByID($this->ELEMENT_ID))->asArray();
        else if (CModule::IncludeModule("catalog"))
            $arItem = CCatalogProduct::GetByID($this->ELEMENT_ID);

        return $arItem;
    }

    /**
     * Задает количество товара
     * @param int $iQuantity
     */
    private function setProductQuantity($iQuantity = 0)
    {
        $iQuantity = trim($iQuantity);

        if (empty($iQuantity))
            $iQuantity = 0;

        if ($this->arParams['TIMER_QUANTITY_ENTER_VALUE'] === 'Y') {
            if (!empty($this->arParams['QUANTITY']))
                $this->PRODUCT_QUANTITY = $this->arParams['QUANTITY'];
            else
                $this->PRODUCT_QUANTITY = $iQuantity;
        }

        if (empty($this->PRODUCT_QUANTITY)) {
            $this->PRODUCT_QUANTITY = !empty($iQuantity) ? $iQuantity : 0;
        }
    }

    /**
     * Задает единицы измерения товара
     * @param $arItem
     * @return null
     */
    private function setProductUnit($arItem)
    {
        if (empty($arItem) || empty($arItem['MEASURE']))
            return null;

        $arMeasure = Arrays::fromDBResult(CCatalogMeasure::getList())->indexBy('ID')->asArray();

        if (ArrayHelper::keyExists($arItem['MEASURE'], $arMeasure)) {
            if (!empty($arMeasure[$arItem['MEASURE']]['SYMBOL_RUS']))
                $this->arResult['VISUAL']['QUANTITY']['UNITS']['VALUE'] = $arMeasure[$arItem['MEASURE']]['SYMBOL_RUS'];
            else
                $this->arResult['VISUAL']['QUANTITY']['UNITS']['USE'] = false;
        } else {
            $this->arResult['VISUAL']['QUANTITY']['UNITS']['USE'] = false;
        }
    }

    /**
     * Сравнивает дату завершения скидки с нынешней датой.
     * Вернет true если скидка еще не завершилась.
     * @param $oDateEnd
     * @return bool
     */
    private function compareDate($oDateEnd)
    {
        $dateNow = new DateTime();
        return $dateNow < $oDateEnd;
    }

    /**
     * Расчет времени до завершения скидки.
     * @param $oDateEnd
     * @return array
     */
    private function calculateRemainingTime($oDateEnd)
    {
        $oDateNow = new DateTime();
        $arTime = [
            'DAYS' => '0',
            'HOURS' => '00',
            'MINUTES' => '00',
            'SECONDS' => '00',
        ];

        if ($this->compareDate($oDateEnd)) {
            $diff = $oDateEnd->diff($oDateNow);

            $arTime['DAYS'] = $diff->days;

            $arTime['HOURS'] = $diff->h < 10 ? '0' . $diff->h : $diff->h;
            $arTime['MINUTES'] = $diff->i < 10 ? '0' . $diff->i : $diff->i;
            $arTime['SECONDS'] = $diff->s < 10 ? '0' . $diff->s : $diff->s;
        }

        $this->arResult['DATE']['REMAINING'] = $arTime;

        return $arTime;
    }

    /**
     * Записывает дату завершения скидки.
     * @param object DateTime $oDateEnd - дата
     */
    private function setDiscountEndDate($oDateEnd)
    {
        if (!empty($oDateEnd)) {
            $this->DISCOUNT_END_DATE = $oDateEnd;
        }
    }

    /**
     * Записывает статус.
     * @param string $sStatus - статус
     */
    private function setDateStatus($sStatus = null)
    {
        $this->DATE_STATUS = $sStatus;
    }

    /**
     * Записывает название скидки.
     * @param $sName
     */
    private function setDiscountName($sName)
    {
        $this->DISCOUNT_NAME = $sName;
    }

    /**
     * Получает скидку.
     * @return bool
     */
    private function getDiscount()
    {
        if (empty($this->arParams['IBLOCK_ID'])) {
            if (!empty($this->ELEMENT_ID)) {

                $arElement = ArrayHelper::getFirstValue(Arrays::fromDBResult(CIBlockElement::GetByID($this->ELEMENT_ID))->asArray());

                $this->arParams['IBLOCK_ID'] = $arElement['IBLOCK_ID'];
            } else {
                return false;
            }
        }

        global $USER;
        $arUserGroup = $USER->GetUserGroupArray();

        $arDiscount = CCatalogDiscount::GetDiscountByProduct(
            $this->ELEMENT_ID,
            $arUserGroup,
            "N"
        );

        if (empty($arDiscount))
            return false;

        $arDiscount = ArrayHelper::getFirstValue($arDiscount);

        if (!empty($arDiscount['NAME'])) {
            $this->setDiscountName($arDiscount['NAME']);
            $this->arResult['VISUAL']['TITLE']['VALUE'] = $this->DISCOUNT_NAME;
        }

        if (empty($arDiscount['ACTIVE_TO']))
            return false;

        $this->setDiscountId($arDiscount['ID']);
        $this->setDiscountEndDate(new DateTime($arDiscount['ACTIVE_TO']));

        return true;
    }

    /**
     * Устонавливает ID товара
     * @param string $sId - ID товара
     */
    private function setDiscountId($sId)
    {
        if (!empty($sId))
            $this->DISCOUNT_ID = $sId;
    }
}
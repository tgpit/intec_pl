<?

use Bitrix\Main\Loader;
use intec\core\bitrix\components\IBlockElements;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

class IntecTimerComponent extends IBlockElements
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'DATE_END' => null,
            'TIME_ZERO_HIDE' => 'N'
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

        $this->arResult = [
            'DATE' => [
                'STATUS' => null,
                'END' => null,
                'VALUE' => [],
            ]
        ];

        $oDateEnd = $this->arParams['DATE_END'];

        if ($this->isDate($oDateEnd)) {
            $oDateEnd = new DateTime($oDateEnd);
            if ($this->compareDate($oDateEnd)) {
                $this->calculateRemainingTime($oDateEnd);
                $this->arResult['DATE']['VALUE'] = $this->dateFormat($oDateEnd);
                $this->arResult['DATE']['STATUS'] = true;
            } else {
                $this->arResult['DATE']['STATUS'] = false;
            }
        } else {
            $this->arResult['DATE']['STATUS'] = false;
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
     * Сравнивает даты завершения отсчета с нынешней датой.
     * @param $oDateEnd
     * @return bool
     */
    private function compareDate($oDateEnd)
    {
        $dateNow = new DateTime();
        return $dateNow < $oDateEnd;
    }

    /**
     * Расчет времени до завершения отсчета.
     * @param $oDateEnd
     * @return array
     */
    private function calculateRemainingTime($oDateEnd)
    {
        $oDateNow = new DateTime();
        $arTime = [];

        if ($this->compareDate($oDateEnd)) {
            $diff = $oDateEnd->diff($oDateNow);

            $arTime['DAYS'] = ($diff->y * 365 ) +
                ($diff->m * 30) +
                ($diff->d);

            $arTime['HOURS'] = $diff->h < 10 ? '0' . $diff->h : $diff->h;
            $arTime['MINUTES'] = $diff->i < 10 ? '0' . $diff->i : $diff->i;
            $arTime['SECONDS'] = $diff->s < 10 ? '0' . $diff->s : $diff->s;
        } else {
            $arTime = [
                'DAYS' => '0',
                'HOURS' => '00',
                'MINUTES' => '00',
                'SECONDS' => '00'
            ];
        }

        $this->arResult['DATE']['VALUE'] = $arTime;

        return $arTime;
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
}
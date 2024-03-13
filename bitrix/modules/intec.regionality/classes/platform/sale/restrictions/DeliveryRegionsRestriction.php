<?php
namespace intec\regionality\platform\sale\restrictions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Delivery\Restrictions\Base as Restriction;

Loc::loadMessages(__FILE__);

/**
 * Предстваляет собой объявление ограничения по регионам для способов оплаты.
 * Class DeliveryRegionsRestriction
 * @package intec\regionality\platform\sale\restrictions
 * @author apocalypsisdimon@gmail.com
 */
class DeliveryRegionsRestriction extends Restriction
{
    use RegionsRestrictionTrait;
}
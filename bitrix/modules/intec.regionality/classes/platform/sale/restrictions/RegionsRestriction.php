<?php
namespace intec\regionality\platform\sale\restrictions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Services\Base\Restriction;

Loc::loadMessages(__FILE__);

/**
 * Предстваляет собой объявление ограничения по регионам.
 * Class PaySystemRegionsRestriction
 * @package intec\regionality\platform\sale\restrictions
 * @author apocalypsisdimon@gmail.com
 */
class RegionsRestriction extends Restriction
{
    use RegionsRestrictionTrait;
}
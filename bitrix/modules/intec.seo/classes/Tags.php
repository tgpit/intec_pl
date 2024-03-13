<?php
namespace intec\seo;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Iblock\Template\Functions\FunctionBase;

/**
 * Класс, необходимый для разрешения обработчиков тегов.
 * Class Tags
 * @package intec\seo
 */
class Tags extends FunctionBase
{
    /**
     * Разрешает обработчик тегов.
     * @param Event $event
     * @return EventResult|null
     */
    public static function resolve($event)
    {
        $result = null;
        $tag = $event->getParameter(0);

        if ($tag === 'filterproperty') {
            $result = new EventResult(EventResult::SUCCESS, '\\intec\\seo\\filter\\tags\\PropertyTag');
        } else if ($tag === 'filterofferproperty') {
            $result = new EventResult(EventResult::SUCCESS, '\\intec\\seo\\filter\\tags\\OfferPropertyTag');
        } else if ($tag === 'filterprice') {
            $result = new EventResult(EventResult::SUCCESS, '\\intec\\seo\\filter\\tags\\PriceTag');
        }

        return $result;
    }
}
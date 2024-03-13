<?php
namespace intec\template;

use Bitrix\Main\Composite\Page;
use intec\core\base\BaseObject;
use intec\core\helpers\StringHelper;

class Template extends BaseObject
{
    public static function optimize(&$content)
    {
        Page::getInstance()->markNonCacheable();

        $excludes = [
            'http://',
            'https://',
            '/bitrix/js/',
            '/bitrix/panel/',
            '/bitrix/themes/.default/'
        ];

        $content = preg_replace_callback('/<link([^>]*)>/is', function ($matches) use (&$excludes) {
            $internalMatches = [];

            if (preg_match('/href="(.*?)"/i', $matches[1], $internalMatches)) {
                foreach ($excludes as $exclude)
                    if (StringHelper::startsWith($internalMatches[1], $exclude))
                        return '';
            }

            return $matches[0];
        }, $content);

        $content = preg_replace_callback('/<script([^>]*)>(.*?)<\/script>/is', function ($matches) use (&$excludes) {
            $internalMatches = [];

            if (preg_match('/src="(.*?)"/i', $matches[1], $internalMatches)) {
                foreach ($excludes as $exclude)
                    if (StringHelper::startsWith($internalMatches[1], $exclude))
                        return '';
            }

            return $matches[0];
        }, $content);
    }
}

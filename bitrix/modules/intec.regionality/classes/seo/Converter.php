<?php
namespace intec\regionality\seo;

use intec\core\base\BaseObject;

/**
 * Класс, предназначенный для преобразования содержимого в исполняемый код.
 * Class Converter
 * @property string $prologue Начало контента содержимого. Только для чтения.
 * @property string $epilogue Окончание контента содержимого. Только для чтения.
 * @package intec\regionality\seo
 * @author apocalypsisdimon@gmail.com
 */
abstract class Converter extends BaseObject
{
    /**
     * Возвращает начало контента содержимого.
     * @return string
     */
    public function getPrologue()
    {
        return '<?php define(\'INTEC_REGIONALITY_REGION_RESOLVE\', false) ?>'."\r\n".
            '<?php define(\'INTEC_REGIONALITY_MACROS_REPLACE\', false) ?>'."\r\n".
            '<?php require_once($_SERVER[\'DOCUMENT_ROOT\'].\'/bitrix/modules/main/include/prolog_before.php\') ?>'."\r\n".
            '<?php'."\r\n\r\n".

            'use Bitrix\\Main\\Context;'."\r\n".
            'use Bitrix\\Main\\Loader;'."\r\n".
            'use intec\\regionality\\models\\Region;'."\r\n".
            'use intec\\regionality\\models\\SiteSettings;'."\r\n\r\n".

            'if (!Loader::includeModule(\'intec.core\') || !Loader::includeModule(\'intec.regionality\'))'."\r\n".
            "\t".'return;'."\r\n\r\n".

            '$site = Context::getCurrent()->getSite();'."\r\n\r\n".

            'if (empty($site))'."\r\n".
            "\t".'return;'."\r\n\r\n".

            '$settings = SiteSettings::get($site);'."\r\n".
            '$region = null;'."\r\n".
            '$domain = null;'."\r\n\r\n".

            'if ($settings->domainsUse) {'."\r\n".
            "\t".'$region = Region::resolveByDomain(null, $site);'."\r\n\r\n".

            "\t".'if (!empty($region))'."\r\n".
            "\t\t".'$domain = $region->resolveDomain($site, true);'."\r\n".
            '}'."\r\n".

            'if (empty($domain))'."\r\n".
            "\t".'$domain = $settings->getDomain(true);'."\r\n\r\n".

            'if (empty($domain))'."\r\n".
            "\t".'return;'."\r\n\r\n".

            '?>';
    }

    /**
     * Возвращает окончание контента содержимого.
     * @return string
     */
    public function getEpilogue()
    {
        return '';
    }

    /**
     * Оборачивает содержимое в начало и конец.
     * @param string $content Содержимое.
     * @return string
     */
    protected function wrap($content)
    {
        return $this->getPrologue().$content.$this->getEpilogue();
    }

    /**
     * Конвертирует содержимое в исполняемый код.
     * @param string $content Содержимое.
     * @return string
     */
    public abstract function convert($content);
}
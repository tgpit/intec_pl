<?php
namespace intec\regionality\seo\converters;

use CSite;
use intec\core\helpers\StringHelper;
use intec\regionality\seo\Converter;

/**
 * Класс, предназначенный для преобразования robots.txt в исполняемый код.
 * Class RobotsConverter
 * @package intec\regionality\seo\converters
 * @author apocalypsisdimon@gmail.com
 */
class RobotsConverter extends Converter
{
    /**
     * @inheritdoc
     */
    public function convert($content)
    {
        $domains = [];
        $sites = CSite::GetList($by = 'sort', $order = 'asc', [
            'ACTIVE' => 'Y'
        ]);

        while ($site = $sites->Fetch())
            if (!empty($site['SERVER_NAME']))
                $domains[] = $site['SERVER_NAME'];

        $domains = array_unique($domains);
        $rules = [];

        foreach ($domains as $domain)
            $rules[$domain] = '<?= $domain ?>';

        $result = '<?php header(\'Content-Type: text/plain\'); ?>'.
            StringHelper::replace($content, $rules);

        return $this->wrap($result);
    }
}
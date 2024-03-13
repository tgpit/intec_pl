<?php
namespace intec\regionality\seo\converters;

use CSite;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\seo\Converter;

/**
 * Класс, предназначенный для преобразования карты сайта в исполняемый код.
 * Class SitemapConverter
 * @package intec\regionality\seo\converters
 * @author apocalypsisdimon@gmail.com
 */
class SitemapConverter extends Converter
{
    /**
     * @param string $content
     * @param array $parts
     */
    protected function getParts(&$content, &$parts)
    {
        $node = simplexml_load_string($content);
        $result = [];

        if (empty($node))
            return $result;

        if ($node->getName() === 'sitemapindex') {
            foreach ($node->sitemap as $part) {
                if (empty($part->loc))
                    continue;

                $code = Type::toString($part->loc);
                $code = explode('/', $code);

                if (count($code) < 4)
                    continue;

                unset($code[0], $code[1], $code[2]);

                $code = implode('/', $code);

                if (!isset($parts[$code]))
                    continue;

                $result = array_merge(
                    $result,
                    $this->getParts($parts[$code], $parts)
                );
            }
        } else if ($node->getName() === 'urlset') {
            foreach ($node->url as $part) {
                if (empty($part->loc))
                    continue;

                $loc = Type::toString($part->loc);
                $loc = Html::encode($loc);
                $loc = explode('/', $loc);

                if (count($loc) < 4)
                    continue;

                $loc[2] = '<?= $domain ?>';
                $loc = implode('/', $loc);

                $string = "\t".'<url>'."\r\n".
                    "\t\t".'<loc>'.$loc.'</loc>'."\r\n";

                if (!empty($part->lastmod))
                    $string .= "\t\t".'<lastmod>'.$part->lastmod.'</lastmod>'."\r\n";

                if (!empty($part->changefreq))
                    $string .= "\t\t".'<changefreq>'.$part->changefreq.'</changefreq>'."\r\n";

                if (!empty($part->priority))
                    $string .= "\t\t".'<priority>'.$part->priority.'</priority>'."\r\n";

                $string .= "\t".'</url>';
                $result[] = $string;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function convert($content, $parts = [])
    {
        if (!Type::isArray($parts))
            $parts = [];

        libxml_use_internal_errors(true);

        $result = $this->getParts($content, $parts);

        if (empty($result))
            return null;

        $result = '<?php header(\'Content-Type: text/xml\'); ?>'.'<?= \'<?xml version="1.0" encoding="UTF-8"?>\'."\\r\\n" ?>'.
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\r\n".
            implode("\r\n", $result)."\r\n".
            '</urlset>';

        return $this->wrap($result);
    }
}
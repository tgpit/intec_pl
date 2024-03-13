<?php

use Ninja\Project\Regionality\Cities;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class CatalogSections extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['city'] = Cities::getCityByHost();
        $this->arResult['list'] = $this->getList();
        $this->arResult['type'] = $this->arParams['TYPE'] === 'index' ? 'sitemapindex' : 'urlset';

        $this->includeComponentTemplate();
    }

    private function getList(): array
    {
        $resultSections = [];
        $resultSectionsForSiteMap = [];

        $sections = \Ninja\Project\Catalog\CatalogSections::getList();

        if ($this->arParams['TYPE'] === 'index') {
            $resultSectionsForSiteMap[] = [
                'type' => 'sitemap',
                'src' => $this->arResult['city']['domain'] . '/sitemap/catalog/sections.xml',
            ];
        }

        foreach ($sections['list'] as $item) {
            if ($this->arParams['TYPE'] !== 'index') {
                $resultSections[] = [
                    'type' => 'url',
                    'src' => $this->arResult['city']['domain'] . $item['url'],
                ];
            }
            else if (!empty($item['cnt'])) {
                $resultSectionsForSiteMap[] = [
                    'type' => 'sitemap',
                    'src' => $this->arResult['city']['domain'] . '/sitemap/catalog/' . $item['code'] . '/elements.xml'
                ];
            }
        }

        return array_merge($resultSections, $resultSectionsForSiteMap);
    }
}

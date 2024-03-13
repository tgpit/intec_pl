<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\Query;
use Bitrix\Highloadblock\HighloadBlockTable;
use intec\Core;
use intec\core\base\conditions\GroupCondition;
use intec\core\bitrix\conditions\CatalogPriceCondition;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\bitrix\conditions\IBlockSectionCondition;
use intec\core\collections\Arrays;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\filter\conditions\CatalogPriceFilteredMaximalCondition;
use intec\seo\filter\conditions\CatalogPriceFilteredMinimalCondition;
use intec\seo\filter\conditions\CatalogPriceMaximalCondition;
use intec\seo\filter\conditions\CatalogPriceMinimalCondition;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\condition\TagRelinkingCondition;
use intec\seo\models\filter\condition\Section;
use intec\seo\models\filter\condition\AutofillSection;
use intec\seo\models\filter\condition\Article;
use intec\seo\models\filter\condition\Site;
use intec\seo\models\filter\url\Scan;
use intec\seo\models\filter\Url as FilterUrl;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

Loader::includeModule('fileman');

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

Core::$app->web->js->loadExtensions(['jquery', 'jquery_extensions', 'intec_core', 'knockout', 'knockout_extensions']);
Core::$app->web->css->addFile('@intec/seo/resources/css/filter/conditions/conditions.css');

$request = Core::$app->request;
$action = $request->get('action');
$error = null;

/** @var Condition $condition */
$condition = $request->get('condition');

if (!empty($condition)) {
    $condition = Condition::findOne($condition);

    if (empty($condition))
        LocalRedirect($arUrlTemplates['filter.conditions']);
} else {
    if ($action !== 'copy') {
        $condition = new Condition();
        $condition->loadDefaultValues();
    } else {
        LocalRedirect($arUrlTemplates['filter.conditions']);
    }
}

if ($action === 'copy')
    $condition->name = $condition->name.' ('.Loc::getMessage('fields.name.copy').')';

$conditions = Condition::find()->where([
    'active' => 1
])->all();

if ($action === 'copy') {
    $APPLICATION->SetTitle(Loc::getMessage('title.copy'));
} else if (!$condition->getIsNewRecord()) {
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));
}

$urls = $condition->getUrl()->orderBy(['sort' => SORT_ASC])->all();
$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'));
$conditionSites = $condition->getSites(true);
$conditionSections = $condition->getSections(true);
$conditionArticles = $condition->getArticles(true);
$conditionAutofillSections = $condition->getAutofillSections(true);
$conditionTagRelinkingConditionsLinks = $condition->getTagRelinkingConditionsLinks(true);

$arArticlesItems = $conditionArticles->asArray(function ($index, $element) {
    return [
        'value' => $element->iBlockElementId
    ];
});

if (!empty($arArticlesItems)) {
    $arArticlesItems = Arrays::fromDBResult(CIBlockElement::GetList([], [
        'ID' => $arArticlesItems
    ], false, [], ['ID', 'NAME']))->asArray(function ($index, $item) {
        return [
            'value' => [
                'id' => Type::toInteger($item['ID']),
                'name' => $item['NAME']
            ]
        ];
    });
}

$createPopupItems = function ($macros, $selector) use (&$createPopupItems) {
    $items = [];

    foreach ($macros as $part) {
        $item = [
            'TEXT' => $part['name']
        ];

        if ($part['type'] === 'group') {
            $item['MENU'] = $createPopupItems($part['items'], $selector);
        } else if ($part['type'] === 'macro') {
            $item['ONCLICK'] = 'window.page.insertMacroToField(' . JavaScript::toObject($selector) . ', ' . JavaScript::toObject($part['value']) . ')';
        }

        $items[] = $item;
    }

    return $items;
};

if ($request->getIsAjax()) {
    if ($request->getIsPost()) {
        $action = $request->post('action');
        $response = [
            'status' => 'error'
        ];

        if ($action === 'get.iblocks') {
            $response['status'] = 'success';
            $response['data'] = [
                'iblocks' => Arrays::fromDBResult(CIBlock::GetList([
                    'SORT' => 'ASC'
                ]))->asArray(function ($index, $iBlock) {
                    return [
                        'value' => [
                            'id' => Type::toInteger($iBlock['ID']),
                            'code' => $iBlock['CODE'],
                            'name' => $iBlock['NAME']
                        ]
                    ];
                })
            ];
        } else if ($action === 'get.autofillIblocks') {
            $response['status'] = 'success';
            $response['data'] = [
                'iblocks' => Arrays::fromDBResult(CIBlock::GetList([
                    'SORT' => 'ASC'
                ]))->asArray(function ($index, $iBlock) {
                    return [
                        'value' => [
                            'id' => Type::toInteger($iBlock['ID']),
                            'code' => $iBlock['CODE'],
                            'name' => $iBlock['NAME']
                        ]
                    ];
                })
            ];
        } else if ($action === 'get.autofillIblocks.entities') {
            $iBlock = $request->post('autofillIblocks');

            if (!empty($iBlock))
                $iBlock = CIBlock::GetList([], [
                    'ID' => $iBlock
                ])->Fetch();

            if (!empty($iBlock)) {
                $response['status'] = 'success';
                $response['data'] = [
                    'autofillSections' => Arrays::fromDBResult(CIBlockSection::GetList([
                        'LEFT_MARGIN' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'GLOBAL_ACTIVE' => 'Y'
                    ]))->asArray(function ($index, $iBlockSection) {
                        return [
                            'value' => [
                                'id' => Type::toInteger($iBlockSection['ID']),
                                'code' => $iBlockSection['CODE'],
                                'name' => $iBlockSection['NAME'],
                                'level' => $iBlockSection['DEPTH_LEVEL']
                            ]
                        ];
                    }),
                    'popups' => null
                ];

                $condition->autofillIBlockId = $iBlock['ID'];
                $macros = $condition->getMacros();
                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaTitle-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaTitle')
                );

                $popup->Show();

                $response['data']['popups'] = ob_get_contents();

                ob_end_clean();
            } else {
                $response['message'] = 'Unknown information block';
            }
        } else if ($action === 'get.iblock.entities') {
            $iBlock = $request->post('iblock');

            if (!empty($iBlock))
                $iBlock = CIBlock::GetList([], [
                    'ID' => $iBlock
                ])->Fetch();

            if (!empty($iBlock)) {
                $sku = false;

                if (Loader::includeModule('catalog'))
                    $sku = CCatalogSku::GetInfoByProductIBlock($iBlock['ID']);

                $response['status'] = 'success';
                $response['data'] = [
                    'sections' => Arrays::fromDBResult(CIBlockSection::GetList([
                        'LEFT_MARGIN' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'GLOBAL_ACTIVE' => 'Y'
                    ]))->asArray(function ($index, $iBlockSection) {
                        return [
                            'value' => [
                                'id' => Type::toInteger($iBlockSection['ID']),
                                'code' => $iBlockSection['CODE'],
                                'name' => $iBlockSection['NAME'],
                                'level' => $iBlockSection['DEPTH_LEVEL']
                            ]
                        ];
                    }),
                    'properties' => Arrays::fromDBResult(CIBlockProperty::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'ACTIVE' => 'Y'
                    ])),
                    'popups' => null
                ];

                if ($sku !== false)
                    $response['data']['properties']->addRange(Arrays::fromDBResult(CIBlockProperty::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $sku['IBLOCK_ID'],
                        'ACTIVE' => 'Y'
                    ])));

                $response['data']['properties'] = $response['data']['properties']->asArray(function ($index, $iBlockProperty) {
                    $values = null;

                    if ($iBlockProperty['PROPERTY_TYPE'] === 'L' && empty($iBlockProperty['USER_TYPE']) && !Type::isNumeric($iBlockProperty['USER_TYPE'])) {
                        $values = [];
                        $result = CIBlockPropertyEnum::GetList([
                            'SORT' => 'ASC'
                        ], [
                            'PROPERTY_ID' => $iBlockProperty['ID']
                        ]);

                        while ($value = $result->Fetch())
                            $values[] = [
                                'value' => $value['ID'],
                                'text' => $value['VALUE']
                            ];
                    } else if ($iBlockProperty['PROPERTY_TYPE'] === 'S' && $iBlockProperty['USER_TYPE'] === 'directory') {
                        $values = [];

                        try {
                            $entity = null;
                            $block = HighloadBlockTable::getList([
                                'filter' => [
                                    'TABLE_NAME' => $iBlockProperty['USER_TYPE_SETTINGS']['TABLE_NAME']
                                ]
                            ])->fetch();

                            if (!empty($block))
                                $entity = HighloadBlockTable::compileEntity($block);

                            if (!empty($entity)) {
                                $query = new Query($entity);
                                $query->setSelect(['*']);
                                $result = $query->exec();

                                while ($value = $result->fetch())
                                    if (!empty($value['UF_XML_ID']))
                                        $values[] = [
                                            'value' => $value['UF_XML_ID'],
                                            'text' => !empty($value['UF_NAME']) || Type::isInteger($value['UF_NAME']) ? $value['UF_NAME'] : $value['UF_XML_ID']
                                        ];
                            }
                        } catch (\Exception $exception) {}
                    }

                    return [
                        'value' => [
                            'id' => Type::toInteger($iBlockProperty['ID']),
                            'code' => $iBlockProperty['CODE'],
                            'type' => $iBlockProperty['PROPERTY_TYPE'],
                            'userType' => !empty($iBlockProperty['USER_TYPE']) || Type::isNumeric($iBlockProperty['USER_TYPE']) ? $iBlockProperty['USER_TYPE'] : null,
                            'name' => $iBlockProperty['NAME'],
                            'values' => $values
                        ]
                    ];
                });

                $condition->iBlockId = $iBlock['ID'];
                $macros = $condition->getMacros();
                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaTitle-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaKeywords-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaKeywords')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaDescription-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaDescription')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaSearchTitle-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaSearchTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaPageTitle-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaPageTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaBreadcrumbName-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaBreadcrumbName')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaDescriptionTop-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaDescriptionTop')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaDescriptionBottom-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaDescriptionBottom')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-metaDescriptionAdditional-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-metaDescriptionAdditional')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-filter-conditions-edit-tagName-menu',
                    $createPopupItems($macros, '#intec-seo-filter-conditions-edit-tagName')
                );

                $popup->Show();

                $response['data']['popups'] = ob_get_contents();

                ob_end_clean();
            } else {
                $response['message'] = 'Unknown information block';
            }
        } else if ($action === 'get.iblock.section') {
            $iBlockSection = $request->post('section');

            if (!empty($iBlockSection)) {
                $iBlockSection = CIBlockSection::GetList([], [
                    'ID' => $iBlockSection
                ])->Fetch();

                if (!empty($iBlockSection)) {
                    $response['status'] = 'success';
                    $response['data'] = [
                        'id' => Type::toInteger($iBlockSection['ID']),
                        'code' => $iBlockSection['CODE'],
                        'name' => $iBlockSection['NAME'],
                        'depth' => $iBlockSection['DEPTH_LEVEL']
                    ];
                }
            }

            if (empty($iBlockSection))
                $response['message'] = 'Unknown section of information block';
        } else if ($action === 'get.iblock.element') {
            $iBlockElement = $request->post('element');

            if (!empty($iBlockElement)) {
                $iBlockElement = CIBlockElement::GetList([], [
                    'ID' => $iBlockElement
                ])->Fetch();

                if (!empty($iBlockElement)) {
                    $response['status'] = 'success';
                    $response['data'] = [
                        'id' => Type::toInteger($iBlockElement['ID']),
                        'code' => $iBlockElement['CODE'],
                        'name' => $iBlockElement['NAME']
                    ];
                }
            }

            if (empty($iBlockElement))
                $response['message'] = 'Unknown element of information block';
        } else if ($action === 'get.prices') {
            $response['status'] = 'success';
            $response['data'] = [
                'prices' => []
            ];

            if (Loader::includeModule('catalog')) {
                $response['data']['prices'] = Arrays::fromDBResult(CCatalogGroup::GetList(['SORT' => 'ASC']))->asArray(function ($index, $price) {
                    return [
                        'value' => [
                            'id' => Type::toInteger($price['ID']),
                            'name' => !empty($price['NAME_LANG']) || Type::isNumeric($price['NAME_LANG']) ? $price['NAME_LANG'] : $price['NAME']
                        ]
                    ];
                });
            }
        } else if ($action === 'configure') {   /** События для отладчика */
            $total = FilterUrl::find()->where([
                'active' => 1,
                'conditionId' => $condition->id
            ])->count();

            $total = Type::toInteger($total);

            $response = [
                'status' => 'success',
                'data' => [
                    'total' => $total
                ]
            ];
        } else if ($action === 'run') {
            $current = $request->post('current');
            $current = Type::toInteger($current);
            $count = $request->post('count');
            $count = Type::toInteger($count);

            if ($current < 0)
                $current = 0;

            if ($count < 1)
                $count = 1;

            $urls = FilterUrl::find()->where([
                'active' => 1,
                'conditionId' => $condition->id
            ]);

            $total = $urls->count();
            $total = Type::toInteger($total);

            $urls = $urls
                ->offset($current)
                ->limit($count)
                ->all();

            foreach ($urls as $url) {
                $scan = $url->scan();

                if (!empty($scan))
                    $scan->save();

                usleep(300000);
            }

            $current = $current + $count;
            $current = Type::toInteger($current);

            if ($current > $total)
                $current = $total;

            $response = [
                'status' => 'success',
                'data' => [
                    'total' => $total,
                    'current' => $current
                ]
            ];
        } else if ($action === 'clear') {

            $arIds = [];
            $arFilterUrls = FilterUrl::find()->where([
                'active' => 1,
                'conditionId' => $condition->id
            ])->all();

            foreach ($arFilterUrls as $arFilterUrl) {
                $arIds[] = $arFilterUrl->getAttribute('id');
            }

            $scans = Scan::find()->where([
                'urlId' => $arIds
            ])->all();

            foreach ($scans as $scan)
                $scan->delete();

            $response = [
                'status' => 'success'
            ];
        }

        echo Json::encode($response, 320, true);
        return;
    }
}

if ($request->getIsPost()) {
    $post = $request->post();
    $data = ArrayHelper::getValue($post, $condition->formName());
    $return = $request->post('apply');
    $return = empty($return);
    $condition->load($post);
    $condition->metaDescriptionTop = $request->post('MetaDescriptionTop');
    $condition->metaDescriptionBottom = $request->post('MetaDescriptionBottom');
    $condition->metaDescriptionAdditional = $request->post('MetaDescriptionAdditional');

    if (!Type::isArray($data))
        $data = [];

    $convert = function ($rule) use (&$convert) {
        $result = null;
        $type = ArrayHelper::getValue($rule, 'type');

        if ($type === 'group') {
            $result = new GroupCondition();
            $result->operator = ArrayHelper::getValue($rule, 'operator');
            $result->result = ArrayHelper::getValue($rule, 'result');

            if (!empty($rule['conditions']) && Type::isArray($rule['conditions']))
                foreach ($rule['conditions'] as $child)
                    $result->conditions->add($convert($child));
        } else if ($type === 'section') {
            $value = ArrayHelper::getValue($rule, 'value');

            if (empty($value) && !Type::isNumeric($value))
                $value = null;

            $result = new IBlockSectionCondition();
            $result->operator = ArrayHelper::getValue($rule, 'operator');
            $result->value = $value;
        } else if ($type === 'property') {
            $value = ArrayHelper::getValue($rule, 'value');

            if (empty($value) && !Type::isNumeric($value))
                $value = null;

            $result = new IBlockPropertyCondition();
            $result->id = ArrayHelper::getValue($rule, 'id');
            $result->operator = ArrayHelper::getValue($rule, 'operator');
            $result->value = $value;

            if (empty($result->id))
                $result = null;
        } else if ($type === 'price') {
            $variety = ArrayHelper::getValue($rule, 'variety');
            $value = ArrayHelper::getValue($rule, 'value');

            if (empty($value) && !Type::isNumeric($value))
                $value = null;

            if ($variety === 'minimal') {
                $result = new CatalogPriceMinimalCondition();
            } else if ($variety === 'maximal') {
                $result = new CatalogPriceMaximalCondition();
            } else if ($variety === 'filteredMinimal') {
                $result = new CatalogPriceFilteredMinimalCondition();
            } else if ($variety === 'filteredMaximal') {
                $result = new CatalogPriceFilteredMaximalCondition();
            }

            if (!empty($result)) {
                /** @var CatalogPriceCondition $result */
                $result->id = ArrayHelper::getValue($rule, 'id');
                $result->operator = ArrayHelper::getValue($rule, 'operator');
                $result->value = $value;
            }
        }

        return $result;
    };

    $condition->setRules(null);

    if (!empty($data['rule']))
        $condition->setRules($convert($data['rule']));

    if ($action === 'copy') {
        $condition->id = null;
        $condition->setIsNewRecord(true);
    }

    if ($condition->save()) {
        if ($action !== 'copy') {
            if (isset($data['sites']))
                foreach ($conditionSites as $conditionSite)
                    $conditionSite->delete();

            if (isset($data['sections']))
                foreach ($conditionSections as $conditionSection)
                    $conditionSection->delete();

            if (isset($data['articles']))
                foreach ($conditionArticles as $conditionArticle)
                    $conditionArticle->delete();

            if (isset($data['autofillSectionId']))
                foreach ($conditionAutofillSections as $conditionAutofillSection)
                    $conditionAutofillSection->delete();

            if (isset($data['tagRelinkingConditions']))
                foreach ($conditionTagRelinkingConditionsLinks as $conditionTagRelinkingConditionLink)
                    $conditionTagRelinkingConditionLink->delete();
        }

        if (!empty($data['sites']) && Type::isArray($data['sites'])) {
            foreach ($data['sites'] as $site) {
                $site = new Site([
                    'conditionId' => $condition->id,
                    'siteId' => $site
                ]);

                $site->save();
            }

            unset($site);
        }

        if (!empty($data['sections']) && Type::isArray($data['sections'])) {
            foreach ($data['sections'] as $section) {
                $section = new Section([
                    'conditionId' => $condition->id,
                    'iBlockSectionId' => $section
                ]);

                $section->save();
            }
        }

        if (!empty($data['articles']) && Type::isArray($data['articles'])) {
            $data['articles'] = array_unique($data['articles'], SORT_NUMERIC);

            foreach ($data['articles'] as $article) {
                $articles = new Article([
                    'conditionId' => $condition->id,
                    'iBlockElementId' => $article
                ]);

                $articles->save();
            }
        }

        if (!empty($data['autofillSectionId']) && Type::isArray($data['autofillSectionId'])) {
            foreach ($data['autofillSectionId'] as $section) {
                $section = new AutofillSection([
                    'conditionId' => $condition->id,
                    'iBlockSectionId' => $section
                ]);

                $section->save();
            }
        }

        if (!empty($data['tagRelinkingConditions']) && Type::isArray($data['tagRelinkingConditions'])) {
            foreach ($data['tagRelinkingConditions'] as $tagRelinkingCondition) {
                $tagRelinkingCondition = new TagRelinkingCondition([
                    'conditionId' => $condition->id,
                    'relinkingConditionId' => $tagRelinkingCondition
                ]);

                $tagRelinkingCondition->save();
            }
        }

        if ($return)
            LocalRedirect($arUrlTemplates['filter.conditions']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
            'condition' => $condition->id
        ]));
    } else {
        $error = $condition->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
} else {
    if (!$condition->getIsNewRecord()) {
        if ($action === 'generate') {
            if (empty($condition->urlSource)) {
                $error = Loc::getMessage('errors.generate.source');
            } else if (empty($condition->urlTarget)) {
                $error = Loc::getMessage('errors.generate.target');
            } else {
                foreach ($urls as $url)
                    $url->delete();

                $condition->generateUrl();

                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
                    'condition' => $condition->id
                ]));
            }
        } else if ($action === 'urls.activate') {
            $ids = $request->get('ID');
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    foreach ($urls as $url) {
                        $urlId = $url->getAttribute('id');

                        if ($urlId == $id) {
                            $url->active = 1;
                            $url->save();
                        }

                    }
                }
            } else {
                foreach ($urls as $url) {
                    $url->active = 1;
                    $url->save();
                }
            }

            LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
                'condition' => $condition->id,
                'tab' => 'urls'
            ]));
        } else if ($action === 'urls.deactivate') {
            $ids = $request->get('ID');

            if (!empty($ids)) {
                foreach ($ids as $id) {
                    foreach ($urls as $url) {
                        $urlId = $url->getAttribute('id');

                        if ($urlId == $id) {
                            $url->active = 0;
                            $url->save();
                        }
                    }
                }
            } else {
                foreach ($urls as $url) {
                    $url->active = 0;
                    $url->save();
                }
            }

            LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
                'condition' => $condition->id,
                'tab' => 'urls'
            ]));
        } else if ($action === 'urls.delete') {
            $ids = $request->get('ID');

            if (!empty($ids)) {
                foreach ($ids as $id) {
                    foreach ($urls as $url) {
                        $urlId = $url->getAttribute('id');

                        if ($urlId == $id) {
                            $url->delete();
                        }

                    }
                }
            } else {
                foreach ($urls as $url)
                    $url->delete();
            }

            LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
                'condition' => $condition->id,
                'tab' => 'urls'
            ]));
        }
    }
}

$scans = new ActiveRecords();

if (!$urls->isEmpty()) {
    $urlsId = [];

    foreach ($urls as $url)
        $urlsId[] = $url->id;

    $scans = Scan::findLatest()
        ->andWhere(['in', Scan::tableName().'.`urlId`', $urlsId])
        ->indexBy('urlId')
        ->all();

    unset($urlsId);
}

$form = [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
], [
    'DIV' => 'meta',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.meta'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.meta'))
], [
    'DIV' => 'tags',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.tags'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.tags'))
], [
    'DIV' => 'generation',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.generation'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.generation'))
]];

if (!$condition->getIsNewRecord() && $action !== 'copy') {
    $form[] = [
        'DIV' => 'urls',
        'ICON' => null,
        'TAB' => Loc::getMessage('tabs.urls'),
        'TITLE' => Html::encode(Loc::getMessage('tabs.urls'))
    ];
}

$form[] = [
    'DIV' => 'autofill',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.autofill'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.autofill'))
];

$form[] = [
    'DIV' => 'articles',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.autofill.articles'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.autofill.articles'))
];

$form = new CAdminForm('filterConditionEditForm', $form);

$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['filter.conditions']
]];

if (!$condition->getIsNewRecord() && $action !== 'copy') {
    $url = new Url($request->getUrl());
    $url->getQuery()->set('action', 'generate');

    $panel[] = [
        'TEXT' => Loc::getMessage('panel.actions.generateUrl'),
        'ICON' => 'btn_green',
        'LINK' => $url->build(),
        'TARGET' => '_blank'
    ];

    $url = new Url($arUrlTemplates['filter.url']);
    $url->getQuery()->setRange([
        'set_filter' => 'Y',
        'filterConditionIdValue' => $condition->id
    ]);

    $panel[] = [
        'TEXT' => Loc::getMessage('panel.actions.showUrl'),
        'ICON' => null,
        'LINK' => $url->build()
    ];

    $panel[] = [
        'TEXT' => Loc::getMessage('panel.actions.copy'),
        'ICON' => null,
        'LINK' => StringHelper::replaceMacros($arUrlTemplates['filter.conditions.copy'], [
            'condition' => $condition->id
        ])
    ];

    unset($url);
}

$panel[] = [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['filter.conditions.add']
];

$panel = new CAdminContextMenu($panel);

$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php $panel->Show() ?>
<?php if (!empty($error)) { ?>
    <?php CAdminMessage::ShowMessage($error) ?>
<?php } ?>
<?php $form->Begin([
    'FORM_ACTION' => $request->getUrl()
]) ?>
<?php $form->BeginNextFormTab() ?>
    <?php if (!$condition->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $condition->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $condition->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('active', $condition->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($condition->formName().'[active]', 0) ?>
                <?= Html::checkbox($condition->formName().'[active]', $condition->active, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('active') ?>
    <?php $form->BeginCustomField('name', $condition->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($condition->formName().'[name]', $condition->name) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('sites', Loc::getMessage('fields.sites').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::dropDownList($condition->formName().'[sites][]', $conditionSites->asArray(function ($index, $site) {
                /** @var Site $site */

                return [
                    'value' => $site->siteId
                ];
            }), ArrayHelper::merge([
                '' => '('.Loc::getMessage('answers.unset').')'
            ], $sites->asArray(function ($index, $site) {
                return [
                    'key' => $site['ID'],
                    'value' => '['.$site['ID'].'] '.(!empty($site['NAME_LANG']) ? $site['NAME_LANG'] : $site['NAME'])
                ];
            })), [
                'multiple' => 'multiple'
            ]) ?></td>
        </tr>
    <?php $form->EndCustomField('sites') ?>
    <?php $form->BeginCustomField('rules', $condition->getAttributeLabel('rules').':', false) ?>
        <tr>
            <td width="40%"><?= $condition->getAttributeLabel('iBlockId').':' ?></td>
            <td>
                <!-- ko if: !iblocks.refreshing() -->
                    <select name="<?= $condition->formName().'[iBlockId]' ?>" data-bind="{
                        options: iblocks,
                        optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                        optionsText: function (iblock) {
                            return '[' + iblock.id + '] ' + iblock.name;
                        },
                        optionsValue: 'id',
                        value: iblock.id
                    }"></select>
                <!-- /ko -->
                <!-- ko if: iblocks.refreshing() -->
                    <select name="<?= $condition->formName().'[iBlockId]' ?>">
                        <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                    </select>
                <!-- /ko -->
            </td>
        </tr>
        <tr>
            <td width="40%"><?= Loc::getMessage('fields.sections').':' ?></td>
            <td>
                <!-- ko if: !iblock.refreshing() -->
                    <select name="<?= $condition->formName().'[sections][]' ?>" multiple="multiple" size="10" data-bind="{
                        options: iblock.sections,
                        optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                        optionsText: function (section) {
                            var level = '';

                            for (var i = 0; i < section.level; i++)
                                level = level + '. ';

                            return '[' + section.id + '] ' + level + section.name;
                        },
                        optionsValue: 'id',
                        selectedOptions: iblock.sections.selected
                    }"></select>
                <!-- /ko -->
                <!-- ko if: iblock.refreshing() -->
                    <select name="<?= $condition->formName().'[sections][]' ?>" multiple="multiple" size="10">
                        <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                    </select>
                <!-- /ko -->
            </td>
        </tr>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <div class="m-intec-seo p-filter-conditions" data-bind="{
                    template: {
                        'name': 'intec-seo-filter-conditions-templates-condition',
                        'data': rule
                    }
                }"></div>
                <script type="text/html" id="intec-seo-filter-conditions-templates-condition">
                    <!-- ko function: function () {
                        $context.$inputName = function (name) {
                            if ($parent === $root) {
                                return 'Condition[rule][' + name + ']';
                            } else {
                                return $parentContext.$inputName('conditions') + '[' + $parent.conditions.indexOf($data) + '][' + name + ']';
                            }
                        };
                    } -->
                    <!-- /ko -->
                    <div class="filter-conditions-item" data-bind="{
                        attr: {
                            'data-type': type
                        }
                    }">
                        <input type="hidden" data-bind="{
                            attr: {
                                'name': $inputName('type')
                            },
                            value: type
                        }" />
                        <!-- ko if: type() === 'property' || type() === 'price' -->
                            <input type="hidden" data-bind="{
                                attr: {
                                    'name': $inputName('id')
                                },
                                value: id
                            }" />
                            <!-- ko if: type() === 'price' -->
                                <input type="hidden" data-bind="{
                                    attr: {
                                        'name': $inputName('variety')
                                    },
                                    value: variety
                                }" />
                            <!-- /ko -->
                        <!-- /ko -->
                        <!-- ko if: $context.$index && $index() > 0 && $parent !== $root; -->
                            <div class="filter-conditions-item-operator" data-bind="{
                                attr: {
                                    'data-operator': $parent.operator
                                },
                                event: {
                                    'click': function () {
                                        $parent.operator.toggle();
                                    }
                                },
                                text: (function () {
                                    var text = $parent.operator() === 'and' ? '<?= Loc::getMessage('conditions.operators.and') ?>' : '<?= Loc::getMessage('conditions.operators.or') ?>';

                                    if (!$parent.result())
                                        text = text + ' <?= Loc::getMessage('conditions.results.not') ?>';

                                    return text;
                                })()
                            }"></div>
                        <!-- /ko -->
                        <!-- ko if: type() === 'group' -->
                            <div class="filter-conditions-item-conditions">
                                <input type="hidden" data-bind="{
                                    attr: {
                                        'name': $inputName('operator')
                                    },
                                    value: operator
                                }" />
                                <select data-bind="{
                                    options: (function () {
                                        return [{
                                            'value': 'and',
                                            'text': '<?= Loc::getMessage('conditions.items.operators.and') ?>'
                                        }, {
                                            'value': 'or',
                                            'text': '<?= Loc::getMessage('conditions.items.operators.or') ?>'
                                        }];
                                    })(),
                                    optionsValue: 'value',
                                    optionsText: 'text',
                                    value: operator
                                }" style="margin-right: 5px"></select>
                                <input type="hidden" data-bind="{
                                    attr: {
                                        'name': $inputName('result')
                                    },
                                    value: result() ? 1 : 0
                                }" />
                                <select data-bind="{
                                    options: (function () {
                                        return [{
                                            'value': true,
                                            'text': '<?= Loc::getMessage('conditions.items.results.true') ?>'
                                        }, {
                                            'value': false,
                                            'text': '<?= Loc::getMessage('conditions.items.results.false') ?>'
                                        }];
                                    })(),
                                    optionsValue: 'value',
                                    optionsText: 'text',
                                    value: result
                                }"></select>
                            </div>
                        <!-- /ko -->
                        <!-- ko if: type() === 'group' -->
                            <!-- ko if: conditions.active().length > 0 -->
                                <div class="filter-conditions-item-items" data-bind="{
                                    template: {
                                        'name': 'intec-seo-filter-conditions-templates-condition',
                                        'foreach': conditions.active
                                    }
                                }"></div>
                            <!-- /ko -->
                        <!-- /ko -->
                        <!-- ko if: type() === 'section' || type() === 'property' || type() === 'price' -->
                            <div class="filter-conditions-item-content">
                                <div class="filter-conditions-item-field">
                                    <div class="filter-conditions-item-name" data-bind="{
                                        text: (function () {
                                            var result = '';

                                            if (type() === 'section') {
                                                result = '<?= Loc::getMessage('conditions.items.types.section') ?>';
                                            } else if (type() === 'property') {
                                                var property = $data.property();

                                                result = '<?= Loc::getMessage('conditions.items.types.property') ?>';

                                                if (property !== null)
                                                    result = result + ' [' + property.id + '] ' + property.name;
                                            } else {
                                                var price = $data.price();

                                                if ($data.variety() === 'minimal') {
                                                    result = '<?= Loc::getMessage('conditions.items.types.price.minimal') ?>';
                                                } else if ($data.variety() === 'maximal') {
                                                    result = '<?= Loc::getMessage('conditions.items.types.price.maximal') ?>';
                                                } else if ($data.variety() === 'filteredMinimal') {
                                                    result = '<?= Loc::getMessage('conditions.items.types.price.filteredMinimal') ?>';
                                                } else if ($data.variety() === 'filteredMaximal') {
                                                    result = '<?= Loc::getMessage('conditions.items.types.price.filteredMaximal') ?>';
                                                }

                                                if (price !== null)
                                                    result = result + ' [' + price.id + '] ' + price.name;
                                            }

                                            return result;
                                        })()
                                    }"></div>
                                </div>
                                <div class="filter-conditions-item-field">
                                    <div class="filter-conditions-item-compare">
                                        <select data-bind="{
                                            attr: {
                                                'name': $inputName('operator')
                                            },
                                            options: operators,
                                            optionsText: 'text',
                                            optionsValue: 'value',
                                            value: operator
                                        }"></select>
                                    </div>
                                </div>
                                <div class="filter-conditions-item-field">
                                    <!-- ko if: type() === 'section' -->
                                        <div class="filter-conditions-item-value">
                                            <!-- ko if: !$root.iblock.refreshing() -->
                                                <select data-bind="{
                                                    attr: {
                                                        'name': $inputName('value')
                                                    },
                                                    options: $root.iblock.sections,
                                                    optionsText: function (section) {
                                                        var level = '';

                                                        for (var i = 0; i < section.level; i++)
                                                            level = level + '. ';

                                                        return '[' + section.id + '] ' + level + section.name;
                                                    },
                                                    optionsValue: 'id',
                                                    optionsCaption: '<?= Loc::getMessage('conditions.items.types.section.select') ?>',
                                                    value: value
                                                }"></select>
                                            <!-- /ko -->
                                            <!-- ko if: $root.iblock.refreshing() -->
                                                <select data-bind="{
                                                    attr: {
                                                        'name': $inputName('value')
                                                    }
                                                }">
                                                    <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                                                </select>
                                            <!-- /ko -->
                                        </div>
                                    <!-- /ko -->
                                    <!-- ko if: type() === 'property' && property() !== null -->
                                        <!-- ko if: property().values -->
                                            <select data-bind="{
                                                attr: {
                                                    'name': $inputName('value')
                                                },
                                                options: property().values,
                                                optionsText: 'text',
                                                optionsValue: 'value',
                                                optionsCaption: '(<?= Loc::getMessage('answers.unset') ?>)',
                                                value: value
                                            }"></select>
                                        <!-- /ko -->
                                        <!-- ko if: !property().values && property().type !== 'E' && property().type !== 'G' -->
                                            <input type="text" data-bind="{
                                                attr: {
                                                    'name': $inputName('value')
                                                },
                                                value: value
                                            }" />
                                        <!-- /ko -->
                                        <!-- ko if: property().type === 'E' || property().type === 'G' -->
                                            <input type="hidden" data-bind="{
                                                attr: {
                                                    'name': $inputName('value')
                                                },
                                                value: value
                                            }" />
                                            <div class="filter-conditions-link" data-bind="{
                                                event: {
                                                    'click': function () {
                                                        value(null);

                                                        if (property().type === 'G') {
                                                            $root.iblock.sections.selector.select(value);
                                                        } else {
                                                            $root.iblock.elements.selector.select(value);
                                                        }
                                                    }
                                                },
                                                text: value.object() ? value.text : '...'
                                            }"></div>
                                        <!-- /ko -->
                                    <!-- /ko -->
                                    <!-- ko if: type() === 'price' && price() !== null -->
                                        <input type="text" data-bind="{
                                            attr: {
                                                'name': $inputName('value')
                                            },
                                            value: value
                                        }" />
                                    <!-- /ko -->
                                </div>
                                <div class="filter-conditions-item-field">
                                    <div class="filter-conditions-link" data-bind="{
                                        event: {
                                            'click': function () {
                                                $parent.conditions.remove($data);
                                            }
                                        }
                                    }"><?= Loc::getMessage('conditions.items.remove') ?></div>
                                </div>
                            </div>
                        <!-- /ko -->
                        <!-- ko if: type() === 'group' -->
                            <div class="filter-conditions-item-controls">
                                <!-- ko function: function () {
                                    $context.$conditions = ko.computed(function () {
                                        var result = [];

                                        result.push({
                                            'type': 'group',
                                            'name': '<?= Loc::getMessage('conditions.items.types.group') ?>',
                                            'operator': 'and',
                                            'result': true
                                        });

                                        result.push({
                                            'type': 'section',
                                            'name': '<?= Loc::getMessage('conditions.items.types.section') ?>'
                                        });

                                        intec.each($root.iblock.properties(), function (index, property) {
                                            result.push({
                                                'type': 'property',
                                                'name': '<?= Loc::getMessage('conditions.items.types.property') ?> [' + property.id + '] ' + property.name,
                                                'id': property.id
                                            });
                                        });

                                        intec.each($root.prices(), function (index, price) {
                                            result.push({
                                                'type': 'price',
                                                'name': '<?= Loc::getMessage('conditions.items.types.price.minimal') ?> [' + price.id + '] ' + price.name,
                                                'id': price.id,
                                                'variety': 'minimal'
                                            });

                                            result.push({
                                                'type': 'price',
                                                'name': '<?= Loc::getMessage('conditions.items.types.price.maximal') ?> [' + price.id + '] ' + price.name,
                                                'id': price.id,
                                                'variety': 'maximal'
                                            });

                                            result.push({
                                                'type': 'price',
                                                'name': '<?= Loc::getMessage('conditions.items.types.price.filteredMinimal') ?> [' + price.id + '] ' + price.name,
                                                'id': price.id,
                                                'variety': 'filteredMinimal'
                                            });

                                            result.push({
                                                'type': 'price',
                                                'name': '<?= Loc::getMessage('conditions.items.types.price.filteredMaximal') ?> [' + price.id + '] ' + price.name,
                                                'id': price.id,
                                                'variety': 'filteredMaximal'
                                            });
                                        });

                                        return result;
                                    });

                                    $context.$conditions.add = function (condition) {
                                        conditions.create(condition);
                                        $context.$conditions.adding(false);
                                    };

                                    $context.$conditions.adding = ko.observable(false);
                                } -->
                                <!-- /ko -->
                                <!-- ko if: !$conditions.adding() -->
                                    <div class="filter-conditions-link" data-bind="{
                                        event: {
                                            'click': function () {
                                                $conditions.adding(true);
                                            }
                                        }
                                    }" style="margin-right: 10px"><?= Loc::getMessage('conditions.items.add') ?></div>
                                <!-- /ko -->
                                <!-- ko if: $conditions.adding() -->
                                    <select data-bind="{
                                        options: $conditions,
                                        optionsText: 'name',
                                        optionsCaption: '<?= Loc::getMessage('conditions.items.add.caption') ?>',
                                        value: (function () {
                                            var observer = ko.observable();

                                            observer.subscribe(function (value) {
                                                $context.$conditions.add(value);
                                            });

                                            return observer;
                                        })();
                                    }" style="margin-right: 10px"></select>
                                    <div class="filter-conditions-link" data-bind="{
                                        event: {
                                            'click': function () {
                                                $conditions.adding(false);
                                            }
                                        }
                                    }" style="margin-right: 10px"><?= Loc::getMessage('conditions.items.add.cancel') ?></div>
                                <!-- /ko -->
                                <!-- ko if: $parent !== $root -->
                                    <div class="filter-conditions-link" data-bind="{
                                        event: {
                                            'click': function () {
                                                $parent.conditions.remove($data);
                                            }
                                        }
                                    }"><?= Loc::getMessage('conditions.items.remove') ?></div>
                                <!-- /ko -->
                            </div>
                        <!-- /ko -->
                    </div>
                </script>
            </td>
        </tr>

    <?php $form->EndCustomField('rules') ?>
    <?php $form->BeginCustomField('searchable', $condition->getAttributeLabel('searchable').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">1</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($condition->formName().'[searchable]', 0) ?>
                <?= Html::checkbox($condition->formName().'[searchable]', $condition->searchable, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('searchable') ?>
    <?php $form->BeginCustomField('indexing', $condition->getAttributeLabel('indexing').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">2</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($condition->formName().'[indexing]', 0) ?>
                <?= Html::checkbox($condition->formName().'[indexing]', $condition->indexing, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('indexing') ?>
    <?php $form->BeginCustomField('strict', $condition->getAttributeLabel('strict').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">3</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($condition->formName().'[strict]', 0) ?>
                <?= Html::checkbox($condition->formName().'[strict]', $condition->strict, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('strict') ?>
    <?php $form->BeginCustomField('recursive', $condition->getAttributeLabel('recursive').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">4</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($condition->formName().'[recursive]', 0) ?>
                <?= Html::checkbox($condition->formName().'[recursive]', $condition->recursive, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('recursive') ?>
    <?php $form->BeginCustomField('priority', $condition->getAttributeLabel('priority').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">5</span></b>
            </td>
            <td><?= Html::textInput($condition->formName().'[priority]', $condition->priority) ?></td>
        </tr>
    <?php $form->EndCustomField('priority') ?>
    <?php $form->BeginCustomField('frequency', $condition->getAttributeLabel('frequency').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">6</span></b>
            </td>
            <td><?= Html::dropDownList($condition->formName().'[frequency]', $condition->frequency, Condition::getFrequencies()) ?></td>
        </tr>
    <?php $form->EndCustomField('frequency') ?>
    <?php $form->BeginCustomField('sort', $condition->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($condition->formName().'[sort]', $condition->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('metaTitle', $condition->getAttributeLabel('metaTitle').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[metaTitle]', $condition->metaTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaTitle') ?>
    <?php $form->BeginCustomField('metaKeywords', $condition->getAttributeLabel('metaKeywords').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaKeywords">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[metaKeywords]', $condition->metaKeywords, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaKeywords-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaKeywords') ?>
    <?php $form->BeginCustomField('metaDescription', $condition->getAttributeLabel('metaDescription').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaDescription">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textarea($condition->formName().'[metaDescription]', $condition->metaDescription, [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '150px',
                        'resize' => 'vertical',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaDescription-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaDescription') ?>
    <?php $form->BeginCustomField('metaSearchTitle', $condition->getAttributeLabel('metaSearchTitle').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaSearchTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[metaSearchTitle]', $condition->metaSearchTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaSearchTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaSearchTitle') ?>
    <?php $form->BeginCustomField('metaPageTitle', $condition->getAttributeLabel('metaPageTitle').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaPageTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[metaPageTitle]', $condition->metaPageTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaPageTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaPageTitle') ?>
    <?php $form->BeginCustomField('metaBreadcrumbName', $condition->getAttributeLabel('metaBreadcrumbName').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaBreadcrumbName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[metaBreadcrumbName]', $condition->metaBreadcrumbName, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaBreadcrumbName-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaBreadcrumbName') ?>
    <?php $form->BeginCustomField('metaDescriptionTop', $condition->getAttributeLabel('metaDescriptionTop').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaDescriptionTop">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?php CFileMan::AddHTMLEditorFrame(
                    'MetaDescriptionTop',
                    $condition->metaDescriptionTop,
                    null,
                    'html',
                    [
                        'height' => 150,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    SITE_ID,
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaDescriptionTop-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaDescriptionTop') ?>
    <?php $form->BeginCustomField('metaDescriptionBottom', $condition->getAttributeLabel('metaDescriptionBottom').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaDescriptionBottom">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?php CFileMan::AddHTMLEditorFrame(
                    'MetaDescriptionBottom',
                    $condition->metaDescriptionBottom,
                    null,
                    'html',
                    [
                        'height' => 150,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    SITE_ID,
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaDescriptionBottom-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaDescriptionBottom') ?>
    <?php $form->BeginCustomField('metaDescriptionAdditional', $condition->getAttributeLabel('metaDescriptionAdditional').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-metaDescriptionAdditional">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?php CFileMan::AddHTMLEditorFrame(
                    'MetaDescriptionAdditional',
                    $condition->metaDescriptionAdditional,
                    null,
                    'html',
                    [
                        'height' => 150,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    SITE_ID,
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-metaDescriptionAdditional-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('metaDescriptionAdditional') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('tagName', $condition->getAttributeLabel('tagName').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-tagName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[tagName]', $condition->tagName, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-filter-conditions-edit-tagName-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('tagName') ?>
    <?php $form->BeginCustomField('tagMode', $condition->getAttributeLabel('tagMode').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">7</span></b>
            </td>
            <td><?= Html::dropDownList($condition->formName().'[tagMode]', $condition->tagMode, Condition::getTagModes()) ?></td>
        </tr>
    <?php $form->EndCustomField('tagMode') ?>
    <?php $form->BeginCustomField('tagRelinkingStrict', $condition->getAttributeLabel('tagRelinkingStrict').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">8</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($condition->formName().'[tagRelinkingStrict]', 0) ?>
                <?= Html::checkbox($condition->formName().'[tagRelinkingStrict]', $condition->tagRelinkingStrict, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('tagRelinkingStrict') ?>
    <?php $form->BeginCustomField('tagRelinkingConditions', Loc::getMessage('fields.tagRelinkingConditions').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::dropDownList($condition->formName().'[tagRelinkingConditions][]',
                    $conditionTagRelinkingConditionsLinks->asArray(function ($index, $tagRelinkingConditionLink) {
                        /** @var TagRelinkingCondition $tagRelinkingConditionLink */
                        return [
                            'value' => $tagRelinkingConditionLink->relinkingConditionId
                        ];
                    }),
                    ArrayHelper::merge([
                        '' => '('.Loc::getMessage('answers.unset').')'
                    ], $conditions->asArray(function ($index, $relinkingCondition) use (&$condition) {
                        /** @var Condition $relinkingCondition */
                        if ($relinkingCondition->id == $condition->id)
                            return ['skip' => true];

                        return [
                            'key' => $relinkingCondition->id,
                            'value' => '['.$relinkingCondition->id.'] '.$relinkingCondition->name
                        ];
                    })), [
                    'multiple' => 'multiple'
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('tagRelinkingConditions') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('urlActive', $condition->getAttributeLabel('urlActive').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2">
                <?= Html::hiddenInput($condition->formName().'[urlActive]', 0) ?>
                <?= Html::checkbox($condition->formName().'[urlActive]', $condition->urlActive, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('urlActive') ?>
    <?php $form->BeginCustomField('urlName', $condition->getAttributeLabel('urlName').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-urlName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[urlName]', $condition->urlName, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td width="1px">
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-edit-urlName-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('urlName') ?>
    <?php $form->BeginCustomField('urlSource', $condition->getAttributeLabel('urlSource').':', true) ?>
        <tr id="intec-seo-filter-conditions-edit-urlSource">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[urlSource]', $condition->urlSource, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td width="1px">
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-edit-urlSource-menu" value="..." />
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.urlSource.description') ?>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    <?php $form->EndCustomField('urlSource') ?>
    <?php $form->BeginCustomField('urlTarget', $condition->getAttributeLabel('urlTarget').':', true) ?>
        <tr id="intec-seo-filter-conditions-edit-urlTarget">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($condition->formName().'[urlTarget]', $condition->urlTarget, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td width="1px">
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-edit-urlTarget-menu" value="..." />
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.urlTarget.description') ?>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    <?php $form->EndCustomField('urlTarget') ?>
    <?php $form->BeginCustomField('urlGenerator', $condition->getAttributeLabel('urlGenerator').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2">
                <?= Html::dropDownList($condition->formName().'[urlGenerator]', $condition->urlGenerator, Condition::getUrlGenerators()) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('urlGenerator') ?>
<?php if (!$condition->getIsNewRecord() && $action !== 'copy') { ?>
    <?php $form->BeginNextFormTab() ?>
        <?php $form->BeginCustomField('urls', Loc::getMessage('fields.urls').':', false) ?>
            <tr>
                <td colspan="2">

                    <div class="adm-detail-content-item-block" style="width: 630px; margin-bottom: 20px">
                        <div class="adm-detail-title">
                            <?= Loc::getMessage('debug.title') ?>
                        </div>
                        <div id="debug-message-view">

                        </div>
                        <div id="debug-status-view" style="margin-bottom: 20px">
                            <div id="debug-status-title" style="margin-bottom: 5px">
                                <?= Loc::getMessage('debug.panel.status.title') ?>
                            </div>
                            <div id="debug-status-bar" style="width: 100%" class="adm-progress-bar-outer">
                                <div id="debug-status-bar-progress" style="width: 0" class="adm-progress-bar-inner"></div>
                                <div id="debug-status-bar-text" style="width: 100%" class="adm-progress-bar-inner-text">0%</div>
                            </div>
                            <div id="debug-status-message" style="display: none; margin-top: 5px"></div>
                        </div>
                        <div id="debug-control-view">
                            <table>
                                <tr>
                                    <td style="padding-bottom: 10px; padding-right: 10px">
                                        <?= Loc::getMessage('debug.panel.control.option.count') ?>
                                    </td>
                                    <td style="padding-bottom: 10px">
                                        <input id="debug-control-option-count" type="text" value="20" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div style="margin: -2px">
                                            <span id="debug-control-button-start" class="adm-btn adm-btn-green" style="margin: 2px">
                                                <?= Loc::getMessage('debug.panel.control.button.start') ?>
                                            </span>
                                                                <span id="debug-control-button-clear" class="adm-btn" style="margin: 2px">
                                                <?= Loc::getMessage('debug.panel.control.button.clear') ?>
                                            </span>
                                                                <span id="debug-control-button-stop" class="adm-btn" style="display: none; margin: 2px">
                                                <?= Loc::getMessage('debug.panel.control.button.stop') ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px; display: flex">
                    <?php
                        $url = new Url($request->getUrl());
                        $url->getQuery()->set('action', 'urls.activate');

                        echo Html::a(Loc::getMessage('fields.urls.buttons.activate'), $url->build(), [
                            'class' => [
                                'adm-btn'
                            ],
                            'data' => [
                                'role' => 'urls.activate.button'
                            ],
                            'style' => [
                                'margin' => '2px'
                            ]
                        ]);

                        $url->getQuery()->set('action', 'urls.deactivate');

                        echo Html::a(Loc::getMessage('fields.urls.buttons.deactivate'), $url->build(), [
                            'class' => [
                                'adm-btn'
                            ],
                            'data' => [
                                'role' => 'urls.deactivate.button'
                            ],
                            'style' => [
                                'margin' => '2px'
                            ]
                        ]);

                        $url->getQuery()->set('action', 'urls.delete');

                        echo Html::a(Loc::getMessage('fields.urls.buttons.delete'), $url->build(), [
                            'class' => [
                                'adm-btn'
                            ],
                            'data' => [
                                'role' => 'urls.delete.button'
                            ],
                            'style' => [
                                'margin' => '2px'
                            ]
                        ]);

                        $url = new Url($arUrlTemplates['filter.url']);
                        $url->getQuery()->setRange([
                            'set_filter' => 'Y',
                            'filterConditionIdValue' => $condition->id
                        ]);

                        echo Html::a(Loc::getMessage('fields.urls.buttons.show'), $url->build(), [
                            'class' => [
                                'adm-btn'
                            ],
                            'target' => '_blank',
                            'style' => [
                                'margin' => '2px'
                            ]
                        ]);

                        unset($url);
                    ?>
                    <div class="adm-small-button adm-table-setting" style="position: relative; top:0; right: 0; margin: 2px 0 2px auto" onclick="this.blur();BX.adminList.ShowMenu(this, [
                        {'GLOBAL_ICON':'adm-menu-excel','TEXT':'Excel','TITLE':'Export in Excel','ONCLICK':'location.href=\'/bitrix/admin/seo_filter_url.php?mode=excel&amp;lang=ru&amp;set_filter=Y&amp;filterConditionIdValue=<?= $condition->id ?>\''}
                        ]); return false;"></div>
                    </div>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="internal">
                        <tr class="heading">
                            <td>
                                <input type="checkbox" data-role="url.id.checkbox.all" class="adm-checkbox adm-designed-checkbox" name="urlId_all" id="seo_filter_url_all" value="all" autocomplete="off">
                                <label class="adm-designed-checkbox-label adm-checkbox" for="seo_filter_url_all"></label>
                            </td>
                            <td><?= Loc::getMessage('fields.urls.fields.id') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.active') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.name') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.source') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.target') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.dateCreate') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.iBlockElementsCount') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.debugMetaTitle') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.debugMetaKeywords') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.debugMetaDescription') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.debugMetaPageTitle') ?></td>
                            <td><?= Loc::getMessage('fields.urls.fields.sort') ?></td>
                        </tr>
                        <?php if (!$urls->isEmpty()) { ?>
                            <?php $iCounter = 0 ?>
                            <?php foreach ($urls as $url) { ?>
                            <?php
                                /** @var Scan $scan */
                                $scan = $scans->get($url->id)
                            ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" data-role="url.id.checkbox" class="adm-checkbox adm-designed-checkbox" name="urlId_<?= Html::encode($url->id) ?>" id="seo_filter_url_<?= Html::encode($url->id) ?>" value="<?= Html::encode($url->id) ?>" autocomplete="off">
                                        <label class="adm-designed-checkbox-label adm-checkbox" for="seo_filter_url_<?= Html::encode($url->id) ?>"></label>
                                    </td>
                                    <td><?= Html::encode($url->id) ?></td>
                                    <td><?= $url->active ? Loc::getMessage('answers.yes') : Loc::getMessage('answers.no') ?></td>
                                    <td><?= Html::encode($url->name) ?></td>
                                    <td><?= Html::a($url->source, $url->source, ['target' => '_blank']) ?></td>
                                    <td><?= Html::a($url->target, $url->target, ['target' => '_blank']) ?></td>
                                    <td><?= !empty($url->dateCreate) ? Core::$app->formatter->asDate($url->dateCreate, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('answers.no').')' ?></td>
                                    <td><?= Html::encode($url->iBlockElementsCount) ?></td>
                                    <td><?= !empty($scan) ? Html::encode($scan->metaTitle) : '('.Loc::getMessage('answers.no').')' ?></td>
                                    <td><?= !empty($scan) ? Html::encode($scan->metaKeywords) : '('.Loc::getMessage('answers.no').')' ?></td>
                                    <td><?= !empty($scan) ? Html::encode($scan->metaDescription) : '('.Loc::getMessage('answers.no').')' ?></td>
                                    <td><?= !empty($scan) ? Html::encode($scan->metaPageTitle) : '('.Loc::getMessage('answers.no').')' ?></td>
                                    <td><?= Html::encode($url->sort) ?></td>
                                </tr>
                                <?php $iCounter++ ?>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="12"><?= Loc::getMessage('fields.urls.messages.empty') ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
        <?php $form->EndCustomField('urls') ?>
<?php } ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('autofillRules', Loc::getMessage('autofillRules').':', false) ?>
    <tr>
        <td width="40%"><?= $condition->getAttributeLabel('iBlockId').':' ?></td>
        <td>
            <!-- ko if: !iblocks.refreshing() -->
            <select name="<?= $condition->formName().'[autofillIBlockId]' ?>" data-bind="{
                            options: autofillIblocks,
                            optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                            optionsText: function (iblock) {
                                return '[' + iblock.id + '] ' + iblock.name;
                            },
                            optionsValue: 'id',
                            value: autofillIblock.id
                        }"></select>
            <!-- /ko -->
            <!-- ko if: iblocks.refreshing() -->
            <select name="<?= $condition->formName().'[autofillIBlockId]' ?>">
                <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
            </select>
            <!-- /ko -->
        </td>
    </tr>
    <tr>
        <td width="40%"><?= Loc::getMessage('fields.sections').':' ?></td>
        <td>
            <!-- ko if: !iblock.refreshing() -->
            <select name="<?= $condition->formName().'[autofillSectionId][]' ?>" multiple="multiple" size="10" data-bind="{
                            options: autofillIblock.sections,
                            optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                            optionsText: function (section) {
                                var level = '';

                                for (var i = 0; i < section.level; i++)
                                    level = level + '. ';

                                return '[' + section.id + '] ' + level + section.name;
                            },
                            optionsValue: 'id',
                            selectedOptions: autofillIblock.sections.selected
                        }"></select>
            <!-- /ko -->
            <!-- ko if: iblock.refreshing() -->
            <select name="<?= $condition->formName().'[autofillSectionId][]' ?>" multiple="multiple" size="10">
                <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
            </select>
            <!-- /ko -->
        </td>
    </tr>
    <?php $form->EndCustomField('autofillRules') ?>
    <?php $form->BeginCustomField('autofillSelf', $condition->getAttributeLabel('autofillSelf').':', false) ?>
    <tr>
        <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
        <td colspan="2">
            <?= Html::hiddenInput($condition->formName().'[autofillSelf]', 0) ?>
            <?= Html::checkbox($condition->formName().'[autofillSelf]', $condition->autofillSelf, [
                'value' => 1
            ]) ?>
        </td>
    </tr>
    <?php $form->EndCustomField('autofillSelf') ?>
    <?php $form->BeginCustomField('autofillQuantity', $condition->getAttributeLabel('autofillQuantity').':', false) ?>
    <tr>
        <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
        <td colspan="2"><?= Html::textInput($condition->formName().'[autofillQuantity]', $condition->autofillQuantity) ?></td>
    </tr>
    <?php $form->EndCustomField('autofillQuantity') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('articles', Loc::getMessage('articles').':', false) ?>
    <tr>
        <td width="40%" class="adm-detail-valign-top"><?= Loc::getMessage('fields.articles') ?> :</td>
        <td width="60%" class="adm-detail-content-cell-r">
            <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb09cac5541d919086be354ca12e5b4567">
                <tbody>
                <?php
                if (empty($arArticlesItems))
                    $iCounter = 2;
                else
                    $iCounter = count($arArticlesItems);

                $sFormName = $condition->formName();

                for ($iCount = 0; $iCount < $iCounter; $iCount++) {

                    $sSetting = '/bitrix/admin/iblock_element_search.php?lang=ru&amp;IBLOCK_ID=0&amp;n='.$sFormName.'[articles]&amp;k='.$iCount;
                    ?>
                    <tr>
                        <td>
                            <input name="<?= $sFormName ?>[articles][<?= $iCount ?>]" id="<?= $sFormName ?>[articles][<?= $iCount ?>]" value="<?= $arArticlesItems[$iCount]['id']?>" size="5" type="text">
                            <input type="button" value="..." onclick="jsUtils.OpenWindow('<?= $sSetting ?>', 900, 700);">
                            &nbsp;
                            <span id="sp_1d230a1ad90af2ec7fdfb9ac29f849c1_<?= $iCount ?>"><?= $arArticlesItems[$iCount]['name']?></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>
                        <input type="button" value="<?= Loc::getMessage('fields.articles.add') ?> " onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=ru&amp;IBLOCK_ID=0&amp;n=<?= $sFormName ?>[articles]&amp;m=y&amp;k=<?= $iCounter ?>', 900, 700);">
                        <span id="sp_1d230a1ad90af2ec7fdfb9ac29f849c1_<?= $iCounter ?>"></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <script type="text/javascript">
                var nomber = <?= JavaScript::toObject($iCounter) ?>;
                function InS1d230a1ad90af2ec7fdfb9ac29f849c1(id, name){
                    oTbl=document.getElementById('tb09cac5541d919086be354ca12e5b4567');
                    oRow=oTbl.insertRow(oTbl.rows.length-1);
                    oCell=oRow.insertCell(-1);
                    oCell.innerHTML='<input name="<?= $sFormName ?>[articles]['+nomber+']" value="'+id+'" id="<?= $sFormName ?>[articles]['+nomber+']" size="5" type="text">'+
                        '<input type="button" value="..." '+
                        'onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=ru&amp;IBLOCK_ID=0&amp;n=<?= $sFormName ?>[articles]&amp;k='+nomber+'\', '+
                        ' 900, 700);">'+'&nbsp;<span id="sp_1d230a1ad90af2ec7fdfb9ac29f849c1_'+nomber+'" >'+name+'</span>';nomber++;}
            </script>
        </td>
    </tr>
    <?php $form->EndCustomField('articles') ?>

<?php

$buttons = [];

if (!$condition->getIsNewRecord() && $action !== 'copy') {
    $url = new Url($arUrlTemplates['filter.url']);
    $url->getQuery()->setRange([
        'set_filter' => 'Y',
        'filterConditionIdValue' => $condition->id
    ]);

    $buttons[] = Html::a(Loc::getMessage('panel.actions.showUrl'), $url->build(), [
        'class' => [
            'adm-btn'
        ],
        'target' => '_blank',
        'style' => [
            'margin' => '2px',
            'float' => 'right'
        ]
    ]);

    $url = new Url($request->getUrl());
    $url->getQuery()->set('action', 'generate');

    $buttons[] = Html::a(Loc::getMessage('panel.actions.generateUrl'), $url->build(), [
        'class' => [
            'adm-btn'
        ],
        'style' => [
            'margin' => '2px',
            'float' => 'right'
        ]
    ]);

    unset($url);
}

?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['filter.conditions']
], implode('', $buttons)) ?>
<div id="intec-seo-filter-conditions-edit">
    <?php $form->Show() ?>
    <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
        <div data-bind="{
            html: $root.iblock.popups
        }"></div>
    <!-- /ko -->
    <?php
        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-edit-urlName-menu',
            $createPopupItems([[
                'code' => 'iblock',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlName.macros.iblock.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.iblock.items.iblockId'),
                    'value' => '#IBLOCK_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.iblock.items.iblockCode'),
                    'value' => '#IBLOCK_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.iblock.items.iblockTypeId'),
                    'value' => '#IBLOCK_TYPE_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.iblock.items.iblockName'),
                    'value' => '#IBLOCK_NAME#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.iblock.items.iblockExternalId'),
                    'value' => '#IBLOCK_EXTERNAL_ID#'
                ]]
            ], [
                'code' => 'section',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlName.macros.section.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.section.items.sectionId'),
                    'value' => '#SECTION_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.section.items.sectionCode'),
                    'value' => '#SECTION_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.section.items.sectionCodePath'),
                    'value' => '#SECTION_CODE_PATH#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.section.items.sectionName'),
                    'value' => '#SECTION_NAME#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.section.items.sectionExternalId'),
                    'value' => '#SECTION_EXTERNAL_ID#'
                ]]
            ], [
                'code' => 'property',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlName.macros.property.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.property.items.propertiesId'),
                    'value' => '#PROPERTIES_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.property.items.propertiesCode'),
                    'value' => '#PROPERTIES_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.property.items.propertiesName'),
                    'value' => '#PROPERTIES_NAME#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlName.macros.property.items.propertiesCombination'),
                    'value' => '#PROPERTIES_COMBINATION#'
                ]]
            ]], '#intec-seo-filter-conditions-edit-urlName')
        );
    
        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-edit-urlSource-menu',
            $createPopupItems([[
                'code' => 'iblock',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlSource.macros.iblock.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.iblock.items.iblockId'),
                    'value' => '#IBLOCK_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.iblock.items.iblockCode'),
                    'value' => '#IBLOCK_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.iblock.items.iblockTypeId'),
                    'value' => '#IBLOCK_TYPE_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.iblock.items.iblockExternalId'),
                    'value' => '#IBLOCK_EXTERNAL_ID#'
                ]]
            ], [
                'code' => 'section',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlSource.macros.section.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.section.items.id'),
                    'value' => '#ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.section.items.sectionId'),
                    'value' => '#SECTION_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.section.items.code'),
                    'value' => '#CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.section.items.sectionCode'),
                    'value' => '#SECTION_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.section.items.sectionCodePath'),
                    'value' => '#SECTION_CODE_PATH#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlSource.macros.section.items.externalId'),
                    'value' => '#EXTERNAL_ID#'
                ]]
            ]], '#intec-seo-filter-conditions-edit-urlSource')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-edit-urlTarget-menu',
            $createPopupItems([[
                'code' => 'iblock',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlTarget.macros.iblock.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.iblock.items.iblockId'),
                    'value' => '#IBLOCK_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.iblock.items.iblockCode'),
                    'value' => '#IBLOCK_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.iblock.items.iblockTypeId'),
                    'value' => '#IBLOCK_TYPE_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.iblock.items.iblockExternalId'),
                    'value' => '#IBLOCK_EXTERNAL_ID#'
                ]]
            ], [
                'code' => 'section',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlTarget.macros.section.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.section.items.id'),
                    'value' => '#ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.section.items.sectionId'),
                    'value' => '#SECTION_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.section.items.code'),
                    'value' => '#CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.section.items.sectionCode'),
                    'value' => '#SECTION_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.section.items.sectionCodePath'),
                    'value' => '#SECTION_CODE_PATH#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.section.items.externalId'),
                    'value' => '#EXTERNAL_ID#'
                ]]
            ], [
                'code' => 'property',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlTarget.macros.property.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.property.items.propertyId'),
                    'value' => '#PROPERTY_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.property.items.propertyCode'),
                    'value' => '#PROPERTY_CODE#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.property.items.propertyValue'),
                    'value' => '#PROPERTY_VALUE#'
                ]]
            ], [
                'code' => 'additional',
                'type' => 'group',
                'name' => Loc::getMessage('fields.urlTarget.macros.additional.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.additional.items.ranges'),
                    'value' => '#RANGES#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.urlTarget.macros.additional.items.prices'),
                    'value' => '#PRICES#'
                ]]
            ]], '#intec-seo-filter-conditions-edit-urlTarget')
        );

        $popup->Show();
    ?>
    <?php include(__DIR__ . '/debug.php'); ?>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="width: 100%; box-sizing: border-box">
            <?= Loc::getMessage('notes.fields') ?>
        </div>
    </div>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="width: 100%; box-sizing: border-box">
            <?= Loc::getMessage('notes.macros') ?>
        </div>
    </div>
</div>
<?php

$data = [];
$data['iblock'] = $condition->iBlockId;
$data['autofillIblock'] = $condition->autofillIBlockId;
$data['sections'] = $conditionSections->asArray(function ($index, $section) {
    /** @var Section $section */

    return [
        'value' => $section->iBlockSectionId
    ];
});
$data['autofillSections'] = $conditionAutofillSections->asArray(function ($index, $section) {
    /** @var AutofillSection $section */

    return [
        'value' => $section->iBlockSectionId
    ];
});
$data['articles'] = $arArticlesItems;

$convert = function ($rule) use (&$convert) {
    $result = null;

    if ($rule instanceof GroupCondition) {
        $result = [
            'type' => 'group',
            'operator' => $rule->operator,
            'result' => $rule->result,
            'conditions' => []
        ];

        foreach ($rule->conditions as $condition) {
            $child = $convert($condition);

            if ($child !== null)
                $result['conditions'][] = $child;
        }
    } else if ($rule instanceof IBlockSectionCondition) {
        $result = [
            'type' => 'section',
            'operator' => $rule->operator,
            'value' => $rule->value
        ];
    } else if ($rule instanceof IBlockPropertyCondition) {
        $result = [
            'type' => 'property',
            'id' => $rule->id,
            'operator' => $rule->operator,
            'value' => $rule->value
        ];
    } else if ($rule instanceof CatalogPriceCondition) {
        $result = [
            'type' => 'price',
            'variety' => null,
            'id' => $rule->id,
            'operator' => $rule->operator,
            'value' => $rule->value
        ];

        if ($rule instanceof CatalogPriceMinimalCondition) {
            $result['variety'] = 'minimal';
        } else if ($rule instanceof CatalogPriceMaximalCondition) {
            $result['variety'] = 'maximal';
        } else if ($rule instanceof CatalogPriceFilteredMinimalCondition) {
            $result['variety'] = 'filteredMinimal';
        } else if ($rule instanceof CatalogPriceFilteredMaximalCondition) {
            $result['variety'] = 'filteredMaximal';
        }

        if (empty($result['variety']))
            $result = null;
    }

    return $result;
};

$data['rule'] = $convert($condition->getRules());

?>
<script type="text/javascript">
    (function ($, api) {
        window.page = {};
        window.page.insertMacroToField = function (selector, macro) {
            var field = $(selector);

            if (field.length === 0)
                return;

            var frame = field.find('iframe');
            var input = field.find('input[type=text]');
            var textArea = field.find('textarea');

            if (frame.length > 0) {
                frame.contents().find('body').append(macro);
                textArea.insertAtCaret(macro);
            } else {
                textArea.insertAtCaret(macro);
                input.insertAtCaret(macro);
            }
        };

        window.intecSeoFilterConditionsEditSectionsSelector = {
            'AddValue': function (value) {
                if (!ko.isObservable(root.iblock.sections.selector.link))
                    return;

                root.iblock.sections.selector.link(value);
                root.iblock.sections.selector.link = null;
            }
        };

        window.intecSeoFilterConditionsEditElementsSelector = {
            'AddValue': function (value) {
                if (!ko.isObservable(root.iblock.elements.selector.link))
                    return;

                root.iblock.elements.selector.link(value);
                root.iblock.elements.selector.link = null;
            }
        };

        <?php if (!empty($request->get('tab'))) { ?>
            $('#tab_cont_<?= $request->get('tab') ?>').trigger('click');
        <?php } ?>

        var multiplyUrlOperation = {
            'buttons': {
                'active': {
                    'button': $('[data-role="urls.activate.button"]'),
                    'url': $('[data-role="urls.activate.button"]').attr('href')
                },
                'deactivate': {
                    'button': $('[data-role="urls.deactivate.button"]'),
                    'url': $('[data-role="urls.deactivate.button"]').attr('href')
                },
                'delete': {
                    'button': $('[data-role="urls.delete.button"]'),
                    'url': $('[data-role="urls.delete.button"]').attr('href')
                }
            },
            'selectedId':[],
            'checkBoxes': $('[data-role="url.id.checkbox"]')
        };

        $('[data-role="url.id.checkbox.all"]').on('click', function () {
            if ($(this).prop('checked')) {
                multiplyUrlOperation.selectedId = [];

                multiplyUrlOperation.checkBoxes.each(function () {
                    multiplyUrlOperation.selectedId.push($(this).prop('value'));
                    $(this).attr('checked', true);
                });
            } else {
                multiplyUrlOperation.checkBoxes.each(function () {
                    multiplyUrlOperation.selectedId = [];
                    $(this).attr('checked', false);
                });
            }

            updateLinks();
        });

        multiplyUrlOperation.checkBoxes.on('click', function () {
            var thisValue = $(this).prop('value');

            if ($(this).prop('checked')) {
                multiplyUrlOperation.selectedId.push(thisValue);
            } else {
                multiplyUrlOperation.selectedId.forEach(function (item, i) {
                    if (thisValue === item)
                        multiplyUrlOperation.selectedId.splice(i, 1);
                });
            }

            updateLinks();
        });

        function updateLinks () {
            $.each(multiplyUrlOperation.buttons, function (key, value) {
                if (multiplyUrlOperation.selectedId.length > 0) {
                    var url = value.url;

                    multiplyUrlOperation.selectedId.forEach(function (id) {
                        url = url + '&ID[]=' + id;
                    });

                    value.button.attr('href', url);
                } else {
                    value.button.attr('href', value.url);
                }
            });
        }

        var root = {};
        var data = <?= JavaScript::toObject($data) ?>;

        var models = {};
        var request = function (action, data, callback) {
            data = api.extend({}, data, {
                'action': action
            });

            $.ajax({
                'async': true,
                'type': 'POST',
                'data': data,
                'dataType': 'json',
                'cache': false,
                'success': function (response) {
                    var data;

                    if (api.isObject(response)) {
                        if (response.status === 'success') {
                            data = api.isObject(response.data) ? response.data : {};

                            if (api.isFunction(callback))
                                callback(data);
                        } else {
                            if (response.message) {
                                console.error(response.message)
                            } else {
                                console.error('Error occurred while request.');
                            }
                        }
                    } else {
                        console.error(response);
                    }
                },
                'error': function (data) {
                    console.error(data);
                }
            });
        };

        models.Condition = function (data) {
            var self = this;

            self.type = ko.computed(function () { return data.type; });

            if (self.type() === 'group') {
                self.operator = ko.observable('and');
                self.operator.toggle = function () {
                    if (self.operator() === 'and') {
                        if (self.result()) {
                            self.result(false);
                        } else {
                            self.result(true);
                            self.operator('or');
                        }
                    } else {
                        if (self.result()) {
                            self.result(false);
                        } else {
                            self.result(true);
                            self.operator('and');
                        }
                    }
                };

                self.result = ko.observable(true);
                self.conditions = ko.observableArray();
                self.conditions.active = ko.computed(function () {
                    var result = [];

                    api.each(self.conditions(), function (index, condition) {
                        if (condition.type() === 'property') {
                            if (condition.property() !== null)
                                result.push(condition);
                        } else {
                            result.push(condition);
                        }
                    });

                    return result;
                });

                self.conditions.create = $.proxy(function (data) {
                    var condition = new models.Condition(api.extend({}, data));
                    this.push(condition);
                    return condition;
                }, self.conditions);

                if (data.operator === 'and' || data.operator === 'or')
                    self.operator(data.operator);

                if (!data.result)
                    self.result(false);

                api.each(data.conditions, function (index, condition) {
                    self.conditions.create(condition);
                });
            } else if (self.type() === 'section') {
                self.operator = ko.observable(data.operator);
                self.value = ko.observable(data.value);
                self.section = ko.computed(function () {
                    var result = null;

                    api.each(root.iblock.sections(), function (index, section) {
                        if (section.id === self.value()) {
                            result = section;
                            return false;
                        }
                    });

                    return result;
                });

                self.operators = ko.computed(function () {
                    return [{
                        'value': '=',
                        'text': '<?= Loc::getMessage('conditions.items.types.section.operators.equal') ?>'
                    }, {
                        'value': '!',
                        'text': '<?= Loc::getMessage('conditions.items.types.section.operators.notEqual') ?>'
                    }];
                });
            } else if (self.type() === 'property') {
                var object = ko.observable();

                object.update = function () {
                    var property = self.property();

                    if (!api.isDeclared(property))
                        return;

                    object(null);

                    if (property.values) {
                        api.each(property.values, function (index, value) {
                            if (value.value === self.value()) {
                                object(value);
                                return false;
                            }
                        });
                    } else if (property.type === 'G') {
                        if (self.value() > 0)
                            request('get.iblock.section', {
                                'section': self.value()
                            }, function (result) {
                                object(result);
                            });
                    } else if (property.type === 'E') {
                        if (self.value() > 0)
                            request('get.iblock.element', {
                                'element': self.value()
                            }, function (result) {
                                object(result);
                            });
                    }
                };

                self.id = ko.observable(data.id);
                self.operator = ko.observable(data.operator);
                self.value = ko.observable(data.value);

                self.property = ko.computed(function () {
                    var result = null;

                    api.each(root.iblock.properties(), function (index, property) {
                        if (property.id === self.id()) {
                            result = property;
                            return false;
                        }
                    });

                    return result;
                });

                self.value.object = ko.computed(function () {
                    return object();
                });

                self.value.text = ko.computed(function () {
                    var property = self.property();
                    var object = self.value.object();

                    if (api.isDeclared(property) && object) {
                        if (property.values) {
                            return object.text;
                        } else if (property.type === 'E' || property.type === 'G') {
                            return '[' + object.id + '] ' + object.name;
                        }
                    }

                    return self.value();
                });

                self.property.subscribe(object.update);
                self.value.subscribe(object.update);

                self.operators = ko.computed(function () {
                    var property = self.property();
                    var result = [];

                    if (!api.isDeclared(property))
                        return result;

                    result = [{
                        'value': '=',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.equal') ?>'
                    }, {
                        'value': '!',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.notEqual') ?>'
                    }];

                    if (property.type === 'N') {
                        result = [{
                            'value': '<',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.less') ?>'
                        }, {
                            'value': '<=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.lessOrEqual') ?>'
                        }, {
                            'value': '>',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.more') ?>'
                        }, {
                            'value': '>=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.moreOrEqual') ?>'
                        }];
                    }

                    if (!api.isDeclared(property.values) && property.type !== 'E' && property.type !== 'G' && property.type !== 'N') {
                        result.push({
                            'value': '*=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.contain') ?>'
                        });

                        result.push({
                            'value': '!*=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.notContain') ?>'
                        });
                    }

                    return result;
                });
            } else if (self.type() === 'price') {
                self.id = ko.observable(data.id);
                self.variety = ko.observable(data.variety);
                self.operator = ko.observable(data.operator);
                self.value = ko.observable(data.value);

                self.price = ko.computed(function () {
                    var result = null;

                    api.each(root.prices(), function (index, price) {
                        if (price.id === self.id()) {
                            result = price;
                            return false;
                        }
                    });

                    return result;
                });

                self.operators = ko.computed(function () {
                    return result = [{
                        'value': '=',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.equal') ?>'
                    }, {
                        'value': '!',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.notEqual') ?>'
                    }, {
                        'value': '<',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.less') ?>'
                    }, {
                        'value': '<=',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.lessOrEqual') ?>'
                    }, {
                        'value': '>',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.more') ?>'
                    }, {
                        'value': '>=',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.moreOrEqual') ?>'
                    }];
                });
            }
        };


        /*autofill tab begin*/
        root.autofillIblocks = ko.observableArray();
        root.autofillIblocks.refreshing = ko.observable(false);
        root.autofillIblocks.refresh = function () {
            var autofillIblocks = root.autofillIblocks;

            autofillIblocks.refreshing(true);
            autofillIblocks.removeAll();

            request('get.autofillIblocks', {}, function (data) {
                autofillIblocks(data.iblocks);
                autofillIblocks.refreshing(false);
            });
        };

        root.autofillIblocks.refresh();

        root.autofillIblock = {};
        root.autofillIblock.id = ko.observable();
        root.autofillIblock.sections = ko.observableArray();
        root.autofillIblock.sections.selected = ko.observableArray();
        root.autofillIblock.sections.selector = {};
        root.autofillIblock.sections.selector.link = null;
        root.autofillIblock.sections.selector.select = function (observable) {
            var url = '/bitrix/admin/iblock_section_search.php?lang=<?= LANGUAGE_ID?>';

            if (!ko.isObservable(observable))
                return;

            root.autofillIblock.sections.selector.link = observable;
            url += '&lookup=intecSeoMetadataTemplatesEditSectionsSelector';

            jsUtils.OpenWindow(url);
        };


        root.autofillIblock.popups = ko.observable();
        root.autofillIblock.refreshing = ko.observable(false);
        root.autofillIblock.refresh = function () {
            var autofillIblock = root.autofillIblock;
            autofillIblock.refreshing(true);

            if (this.id() > 0) {
                request('get.iblock.entities', {
                    'iblock': this.id()
                }, function (data) {
                    autofillIblock.sections(data.sections);
                    autofillIblock.popups(data.popups);
                    autofillIblock.refreshing(false);
                });
            } else {
                autofillIblock.refreshing(false);
            }
        };

        root.autofillIblock.id.subscribe(function () {
            root.autofillIblock.refresh();
        });

        if (data.sections.length < 1)
            data.sections[0] = 0;

        root.autofillIblock.id(data.autofillIblock);
        root.autofillIblock.sections.selected(data.autofillSections);
        /*autofill tab end*/

        root.iblocks = ko.observableArray();
        root.iblocks.refreshing = ko.observable(false);
        root.iblocks.refresh = function () {
            var iblocks = root.iblocks;

            iblocks.refreshing(true);
            iblocks.removeAll();

            request('get.iblocks', {}, function (data) {
                iblocks(data.iblocks);
                iblocks.refreshing(false);
            });
        };

        root.iblocks.refresh();

        root.iblock = {};
        root.iblock.id = ko.observable();
        root.iblock.sections = ko.observableArray();
        root.iblock.sections.selected = ko.observableArray();
        root.iblock.sections.selector = {};
        root.iblock.sections.selector.link = null;
        root.iblock.sections.selector.select = function (observable) {
            var url = '/bitrix/admin/iblock_section_search.php?lang=<?= LANGUAGE_ID?>';

            if (!ko.isObservable(observable))
                return;

            root.iblock.sections.selector.link = observable;
            url += '&lookup=intecSeoFilterConditionsEditSectionsSelector';

            jsUtils.OpenWindow(url);
        };

        root.iblock.elements = {};
        root.iblock.elements.selector = {};
        root.iblock.elements.selector.link = null;
        root.iblock.elements.selector.select = function (observable) {
            var url = '/bitrix/admin/iblock_element_search.php?lang=<?= LANGUAGE_ID?>';

            if (!ko.isObservable(observable))
                return;

            root.iblock.elements.selector.link = observable;
            url += '&lookup=intecSeoFilterConditionsEditElementsSelector';

            jsUtils.OpenWindow(url);
        };

        root.iblock.properties = ko.observableArray();
        root.iblock.popups = ko.observable();
        root.iblock.refreshing = ko.observable(false);
        root.iblock.refresh = function () {
            var iblock = root.iblock;

            iblock.refreshing(true);

            if (this.id() > 0) {
                request('get.iblock.entities', {
                    'iblock': this.id()
                }, function (data) {
                    iblock.sections(data.sections);
                    iblock.properties(data.properties);
                    iblock.popups(data.popups);
                    //console.log(iblock.popups());
                    iblock.refreshing(false);
                });
            } else {
                iblock.refreshing(false);
            }
        };

        root.iblock.id.subscribe(function () {
            root.iblock.refresh();
        });

        root.prices = ko.observableArray();
        root.prices.refreshing = ko.observable(false);
        root.prices.refresh = function () {
            var prices = root.prices;

            prices.refreshing(true);
            prices.removeAll();

            request('get.prices', {}, function (data) {
                prices(data.prices);
                prices.refreshing(false);
            });
        };

        root.prices.refresh();

        root.rule = ko.observable();
        root.iblock.id(data.iblock);
        root.iblock.sections.selected(data.sections);
        root.rule(new models.Condition(data.rule));



        ko.applyBindings(root, $('#intec-seo-filter-conditions-edit').get(0));

        /** Скрипты отладчика */
        $(function () {
            var debug;
            var request;
            var messages;

            messages = <?= JavaScript::toObject([
                'configuring' => Loc::getMessage('debug.panel.status.messages.configuring'),
                'progress' => Loc::getMessage('debug.panel.status.messages.progress'),
                'clearing' => Loc::getMessage('debug.panel.status.messages.clearing'),
                'complete' => Loc::getMessage('debug.panel.status.messages.complete'),
                'error' => Loc::getMessage('debug.panel.status.messages.error')
            ]) ?>;

            debug = {};
            debug.current = 0;
            debug.total = 0;
            debug.state = null;
            debug.nodes = {
                'message': {
                    'view': $('#debug-message-view')
                },
                'status': {
                    'view': $('#debug-status-view'),
                    'title': $('#debug-status-title'),
                    'bar': {
                        'root': $('#debug-status-bar'),
                        'progress': $('#debug-status-bar-progress'),
                        'text': $('#debug-status-bar-text')
                    },
                    'message': $('#debug-status-message')
                },
                'control': {
                    'view': $('#debug-control-view'),
                    'options': {
                        'count': $('#debug-control-option-count')
                    },
                    'buttons': {
                        'start': $('#debug-control-button-start'),
                        'clear': $('#debug-control-button-clear'),
                        'stop': $('#debug-control-button-stop')
                    }
                }
            };

            request = function (action, parameters, callback, error) {
                var data = $.extend({}, parameters, {
                    'action': action
                });

                $.ajax({
                    'cache': false,
                    'data': data,
                    'dataType': 'json',
                    'type': 'POST',
                    'success': function (result) {
                        if (result.status === 'success') {
                            if ($.isFunction(callback))
                                callback.call(window, result.data);
                        } else if (result.status === 'error') {
                            if ($.isFunction(error))
                                error.call(window, result.message ? result.message : null);
                        }
                        console.log(result);
                    },
                    'error': function (error) {
                        console.error(error);

                        if ($.isFunction(error))
                            error.call(window, null);
                    }
                })
            };

            debug.getProgress = function () {
                var result = 0;

                if (debug.total === 0) {
                    result = 100;
                } else {
                    result = Math.floor(debug.current / debug.total * 100);

                    if (result > 100)
                        result = 100;
                }

                return result;
            };

            debug.start = function () {
                if (debug.state !== null)
                    return;

                debug.state = 'running';
                debug.count = debug.nodes.control.options.count.val();
                debug.count = parseInt(debug.count);

                if (debug.count < 1 || isNaN(debug.count))
                    debug.count = 1;

                request('configure', {}, function (data) {
                    var handler;

                    debug.total = data.total;
                    debug.setMessage(messages.configuring);
                    debug.refresh();

                    if (debug.state !== 'running')
                        return;

                    if (debug.total === 0) {
                        debug.setMessage(messages.complete);
                        window.location.reload();

                        return;
                    }

                    handler = function () {
                        debug.setMessage(messages.progress.replace('#count#', debug.current).replace('#total#', debug.total));

                        if (debug.state !== 'running')
                            return;

                        request('run', {
                            'current': debug.current,
                            'count': debug.count
                        }, function (data) {
                            debug.total = data.total;
                            debug.current = data.current;

                            debug.refresh();

                            if (debug.current >= debug.total) {
                                debug.setMessage(messages.complete);
                                window.location.reload();

                                return;
                            }

                            handler();
                        }, debug.error);
                    };

                    handler();
                }, debug.error);
            };

            debug.clear = function () {
                if (debug.state !== null)
                    return;

                debug.state = 'clearing';
                debug.setMessage(messages.clearing);
                debug.current = 0;
                debug.total = 1;
                debug.refresh();

                request('clear', {}, function () {
                    debug.current = 0;
                    debug.total = 0;
                    debug.setMessage(messages.complete);
                    debug.refresh();
                    window.location.reload();
                }, debug.error)
            };

            debug.setMessage = function (text) {
                var node = debug.nodes.status.message;

                node.css({'display': text ? '' : 'none'});
                node.text(text);
            };

            debug.error = function (message) {
                debug.state = null;

                if (message === null)
                    message = messages.error;

                debug.setMessage(message);
                debug.refresh();
            };

            debug.refresh = function () {
                var progress = debug.getProgress();

                debug.nodes.status.bar.text.text(progress + '%');
                debug.nodes.status.bar.progress.css({
                    'width': Math.round(((debug.nodes.status.bar.root.width() - 4) * progress / 100)) + 'px'
                });

                if (debug.state === 'running') {
                    debug.nodes.control.options.count.prop('disabled', true);
                    debug.nodes.control.buttons.start.css({'display': 'none'});
                    debug.nodes.control.buttons.clear.css({'display': 'none'});
                    debug.nodes.control.buttons.stop.css({'display': ''});
                } else if (debug.state === 'clearing') {
                    debug.nodes.control.options.count.prop('disabled', true);
                    debug.nodes.control.buttons.start.css({'display': 'none'});
                    debug.nodes.control.buttons.clear.css({'display': 'none'});
                    debug.nodes.control.buttons.stop.css({'display': 'none'});
                } else {
                    debug.nodes.control.options.count.prop('disabled', false);
                    debug.nodes.control.buttons.start.css({'display': ''});
                    debug.nodes.control.buttons.clear.css({'display': ''});
                    debug.nodes.control.buttons.stop.css({'display': 'none'});
                }
            };

            debug.stop = function () {
                debug.state = null;
            };

            debug.nodes.control.buttons.start.on('click', debug.start);
            debug.nodes.control.buttons.stop.on('click', debug.stop);
            debug.nodes.control.buttons.clear.on('click', debug.clear);
        });
    })(jQuery, intec);
</script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>

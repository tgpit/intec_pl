<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\filter\Sitemap;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title'));

include(__DIR__.'/../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

$request = Core::$app->request;

$list = 'seo_filter_sitemap';
$listSort = [];
$listSort['variable'] = $list.'_by';
$listSort['value'] = $request->get($listSort['variable'], 'sort');
$listOrder = [];
$listOrder['variable'] = $list.'_order';
$listOrder['value'] = $request->get($listOrder['variable'], 'asc');

$sort = new CAdminSorting(
    $list,
    $listSort['value'],
    $listSort['variable'],
    $listOrder['value'],
    $listOrder['variable']
);

$list = new CAdminList($list, $sort);

$filter = $list->InitFilter([
    'filterIdValue',
    'filterSiteIdValue',
    'filterNameValue',
    'filterConfiguredValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.siteId'),
        Loc::getMessage('filter.fields.name'),
        Loc::getMessage('filter.fields.configured')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $sitemaps */
        $sitemaps = Sitemap::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Sitemap $sitemap */
            $sitemap = $sitemaps->get($id);

            if (empty($sitemap))
                continue;

            $sitemap->load($data, '');

            if (!$sitemap->validate()) {
                $error = $sitemap->getFirstErrors();
                $error = ArrayHelper::getFirstValue($error);
                $list->AddGroupError($error, $id);

                continue;
            }

            if (isset($data['active'])) {
                if ($data['active'] === 'Y') {
                    if (!$sitemap->setActive(true, true)) {
                        $list->AddGroupError(Loc::getMessage('list.errors.activate', [
                            '#name#' => $sitemap->name
                        ]), $sitemap->id);
                    }
                } else {
                    if (!$sitemap->setActive(false, true)) {
                        $list->AddGroupError(Loc::getMessage('list.errors.deactivate', [
                            '#name#' => $sitemap->name
                        ]), $sitemap->id);
                    }
                }
            }

            if (isset($data['configured']))
                $sitemap->configured = $sitemap->configured === 'Y';

            $sitemap->save(false);
        }

        unset($error);
        unset($data);
        unset($id);
    }
} else if ($id = $list->GroupAction()) {
    $action = $_REQUEST['action'];

    if (empty($id[0]) && count($id) === 1)
        $id = null;

    $sitemaps = Sitemap::find();

    if (!empty($id))
        $sitemaps->where(['id' => $id]);

    /** @var Sitemap[] $sitemaps */
    $sitemaps = $sitemaps->all();

    foreach ($sitemaps as $sitemap) {
        if ($action === 'activate') {
            if (!$sitemap->setActive(true)) {
                $list->AddGroupError(Loc::getMessage('list.errors.activate', [
                    '#name#' => $sitemap->name
                ]), $sitemap->id);
            }
        } else if ($action === 'deactivate') {
            if (!$sitemap->setActive(false)) {
                $list->AddGroupError(Loc::getMessage('list.errors.deactivate', [
                    '#name#' => $sitemap->name
                ]), $sitemap->id);
            }
        } else if ($action === 'generate') {
            if (!$sitemap->generateFile()) {
                $list->AddGroupError(Loc::getMessage('list.errors.generate', [
                    '#name#' => $sitemap->name
                ]), $sitemap->id);
            }
        } else if ($action === 'delete') {
            $sitemap->delete();
        }
    }
}

unset($sitemap);
unset($sitemaps);
unset($id);

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'))->indexBy('ID');
$sitemaps = Sitemap::find();

if (!empty($filterIdValue))
    $sitemaps->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterSiteIdValue) && Type::isArray($filterSiteIdValue) && !ArrayHelper::isEmpty($filterSiteIdValue, true))
    $sitemaps->andWhere(['in', 'siteId', $filterSiteIdValue]);

if (!empty($filterNameValue))
    $sitemaps->andWhere(['like', 'name', $filterNameValue]);

if (isset($filterConfiguredValue) && Type::isNumeric($filterConfiguredValue))
    $sitemaps->andWhere(['=', 'configured', $filterConfiguredValue > 0 ? 1 : 0]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($sitemaps->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$sitemaps->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $sitemaps->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $sitemaps */
$sitemaps = $sitemaps->all();

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'siteId',
    'content' => Loc::getMessage('list.headers.siteId'),
    'sort' => 'siteId',
    'default' => true
], [
    'id' => 'active',
    'content' => Loc::getMessage('list.headers.active'),
    'default' => true
], [
    'id' => 'generated',
    'content' => Loc::getMessage('list.headers.generated'),
    'default' => true
], [
    'id' => 'name',
    'content' => Loc::getMessage('list.headers.name'),
    'sort' => 'name',
    'default' => true
], [
    'id' => 'configured',
    'content' => Loc::getMessage('list.headers.configured'),
    'sort' => 'configured',
    'default' => true
], [
    'id' => 'sourceFile',
    'content' => Loc::getMessage('list.headers.sourceFile'),
    'sort' => 'sourceFile',
    'default' => true
], [
    'id' => 'targetFile',
    'content' => Loc::getMessage('list.headers.targetFile'),
    'sort' => 'targetFile',
    'default' => true
], [
    'id' => 'sort',
    'content' => Loc::getMessage('list.headers.sort'),
    'sort' => 'sort',
    'default' => true
]];

$list->AddHeaders($headers);

$menu = [[
    'TEXT' => Loc::getMessage('list.actions.add.selectSite'),
    'ACTION' => false
]];

foreach ($sites as $site) {
    $menu[] = [
        'TEXT' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME']),
        'LINK' => StringHelper::replaceMacros($arUrlTemplates['filter.sitemap.add'], [
            'site' => $site['ID']
        ])
    ];
}

$list->AddAdminContextMenu([[
    'TEXT' => Loc::getMessage('list.actions.add'),
    'ICON' => 'btn_new',
    'TITLE' => Loc::getMessage('list.actions.add'),
    'MENU' => $menu
]]);

unset($menu);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'activate' => Loc::getMessage('list.actions.activate'),
    'deactivate' => Loc::getMessage('list.actions.deactivate'),
    'generate' => Loc::getMessage('list.actions.generate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($sitemaps as $sitemap) {
    /** @var Sitemap $sitemap */
    $site = $sites->get($sitemap->siteId);
    $active = $sitemap->getActive();
    $exists = $sitemap->getIsFileExists();
    $sourceFile = $sitemap->getSourceFile(true);
    $sourceUrl = $sitemap->getSourceUrl();
    $targetFile = $sitemap->getTargetFile(true);
    $targetUrl = $sitemap->getTargetUrl();

    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.sitemap.edit'], [
            'sitemap' => $sitemap->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    if ($active) {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.deactivate'),
            'ACTION' => $list->ActionDoGroup($sitemap->id, 'deactivate')
        ];
    } else {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.activate'),
            'ACTION' => $list->ActionDoGroup($sitemap->id, 'activate')
        ];
    }

    $actions[] = [
        'TEXT' => Loc::getMessage('list.rows.actions.generate'),
        'ACTION' => $list->ActionDoGroup($sitemap->id, 'generate')
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $sitemap->id,
            'delete'
        )
    ];

    $row = $list->AddRow($sitemap->id, [
        'id' => $sitemap->id,
        'siteId' => !empty($site) ? '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME']) : null,
        'active' => $active ? 'Y' : 'N',
        'generated' => $exists ? Loc::getMessage('list.rows.answers.yes') : Loc::getMessage('list.rows.answers.no'),
        'name' => $sitemap->name,
        'configured' => $sitemap->configured ? 'Y' : 'N',
        'sourceFile' => !empty($sourceFile) ? $sourceFile : '('.Loc::getMessage('list.rows.answers.no').')',
        'targetFile' => !empty($targetFile) ? $targetFile : '('.Loc::getMessage('list.rows.answers.no').')',
        'sort' => $sitemap->sort
    ]);

    if (!empty($sourceFile) && !empty($sourceUrl))
        $row->AddViewField('sourceFile', Html::a($sourceFile, $sourceUrl, [
            'target' => '_blank'
        ]));

    if ($exists && !empty($targetFile) && !empty($targetUrl))
        $row->AddViewField('targetFile', Html::a($targetFile, $targetUrl, [
            'target' => '_blank'
        ]));

    $row->AddCheckField('active');
    $row->AddInputField('name');
    $row->AddCheckField('configured');
    $row->AddInputField('sort');
    $row->AddActions($actions);
}

$list->CheckListMode();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<form name="find_form" method="get" action="<?= '/'.$request->getPathInfo() ?>">
    <?php $filter->Begin() ?>
    <tr>
        <td><?= Loc::getMessage('filter.fields.id') ?>:</td>
        <td><?= Html::textInput('filterIdValue', !empty($filterIdValue) ? $filterIdValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.siteId') ?>:</td>
        <td>
            <?= Html::hiddenInput(
                'filterSiteIdValue',
                null
            ).Html::dropDownList(
            'filterSiteIdValue[]',
            !empty($filterSiteIdValue) && Type::isArray($filterSiteIdValue) ? $filterSiteIdValue : null,
            ArrayHelper::merge([
                '' => '('.Loc::getMessage('filter.fields.siteId.all').')'
            ],$sites->asArray(function ($index, $site) {
                return [
                    'key' => $site['ID'],
                    'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
                ];
            })), [
                'multiple' => 'multiple'
            ]) ?>
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.name') ?>:</td>
        <td><?= Html::textInput('filterNameValue', !empty($filterNameValue) ? $filterNameValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.configured') ?>:</td>
        <td>
            <div style="margin-top: 6px;">
                <div style="margin-bottom: 3px;">
                    <label>
                        <?= Html::radio('filterConfiguredValue', !isset($filterConfiguredValue) || !Type::isNumeric($filterConfiguredValue), [
                            'value' => null,
                            'style' => [
                                'margin' => 0
                            ]
                        ]) ?>
                        <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.configured.any') ?></span>
                    </label>
                </div>
                <div style="margin-bottom: 3px;">
                    <label>
                        <?= Html::radio('filterConfiguredValue', isset($filterConfiguredValue) && Type::isNumeric($filterConfiguredValue) ? $filterConfiguredValue == 0 : false, [
                            'value' => 0,
                            'style' => [
                                'margin' => 0
                            ]
                        ]) ?>
                        <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.configured.inactive') ?></span>
                    </label>
                </div>
                <div style="margin-bottom: 13px;">
                    <label>
                        <?= Html::radio('filterConfiguredValue', isset($filterConfiguredValue) && Type::isNumeric($filterConfiguredValue) ? $filterConfiguredValue > 0 : false, [
                            'value' => 1,
                            'style' => [
                                'margin' => 0
                            ]
                        ]) ?>
                        <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.configured.active') ?></span>
                    </label>
                </div>
            </div>
        </td>
    </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>

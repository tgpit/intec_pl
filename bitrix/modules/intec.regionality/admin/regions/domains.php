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
use intec\regionality\models\Region;
use intec\regionality\models\region\Domain;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$sites = Arrays::fromDBResult(CSite::GetList($by = 'order', $sort = 'asc'))->indexBy('ID');

/** @var Region $region */
$region = $request->get('region');
$region = Region::findOne($region);

if (empty($region))
    LocalRedirect($arUrlTemplates['regions']);

$APPLICATION->SetTitle(Loc::getMessage('title', ['#region#' => $region->name]));

$list = 'regionality_regions_domains';
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
    'filterActiveValue',
    'filterDefaultValue',
    'filterSitesValue',
    'filterValueValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.active'),
        Loc::getMessage('filter.fields.default'),
        Loc::getMessage('filter.fields.sites'),
        Loc::getMessage('filter.fields.value')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $domains */
        $domains = $region->getDomains(false)
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Domain $domain */
            $domain = $domains->get($id);

            if (empty($domain))
                continue;

            $domain->load($data, '');

            if (isset($data['active']))
                $domain->active = $domain->active === 'Y';

            if (isset($data['default']))
                $domain->default = $domain->default === 'Y';

            if (!$domain->save()) {
                $error = $domain->getFirstErrors();
                $error = ArrayHelper::getFirstValue($error);
                $list->AddGroupError($error, $id);
            }
        }

        unset($error);
        unset($data);
        unset($id);
    }
} else if ($id = $list->GroupAction()) {
    $action = $_REQUEST['action'];

    if (empty($id[0]) && count($id) === 1)
        $id = null;

    $domains = $region->getDomains(false);

    if (!empty($id))
        $domains->where(['id' => $id]);

    /** @var Region[] $domains */
    $domains = $domains->all();

    foreach ($domains as $domain) {
        if ($action === 'activate') {
            $domain->active = true;
            $domain->save();
        } else if ($action === 'deactivate') {
            $domain->active = false;
            $domain->save();
        }  else if ($action === 'default') {
            $domain->default = true;
            $domain->save();
        } else if ($action === 'delete') {
            $domain->delete();
        }
    }
}

unset($domain);
unset($domains);
unset($id);

$domains = $region->getDomains(false);

if (!empty($filterIdValue))
    $domains->andWhere(['=', 'id', $filterIdValue]);

if (isset($filterActiveValue) && Type::isNumeric($filterActiveValue))
    $domains->andWhere(['=', 'active', $filterActiveValue > 0 ? 1 : 0]);

if (isset($filterDefaultValue) && Type::isNumeric($filterDefaultValue))
    $domains->andWhere(['=', 'default', $filterDefaultValue > 0 ? 1 : 0]);

if (!empty($filterValueValue))
    $domains->andWhere(['like', 'value', $filterValueValue]);

if (!empty($filterSitesValue) && Type::isArray($filterSitesValue)) {
    $filterSitesValueTemporary = [];

    foreach ($filterSitesValue as $value)
        if (!empty($value))
            $filterSitesValueTemporary[] = $value;

    unset($value);

    $filterSitesValue = $filterSitesValueTemporary;

    if (!empty($filterSitesValue))
        $domains->andWhere(['in', 'siteId', $filterSitesValue]);

    unset($filterSitesValueTemporary);
}

$domains->distinct(true);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($domains->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$domains->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $domains->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $domains */
$domains = $domains->all();

$list->AddHeaders([[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'active',
    'content' => Loc::getMessage('list.headers.active'),
    'sort' => 'active',
    'default' => true
], [
    'id' => 'default',
    'content' => Loc::getMessage('list.headers.default'),
    'sort' => 'default',
    'default' => true
], [
    'id' => 'siteId',
    'content' => Loc::getMessage('list.headers.siteId'),
    'sort' => 'siteId',
    'default' => true
], [
    'id' => 'value',
    'content' => Loc::getMessage('list.headers.value'),
    'sort' => 'value',
    'default' => true
], [
    'id' => 'sort',
    'content' => Loc::getMessage('list.headers.sort'),
    'sort' => 'sort',
    'default' => true
]]);

$list->AddAdminContextMenu([[
    'TEXT' => Loc::getMessage('list.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => StringHelper::replaceMacros($arUrlTemplates['regions.domains.add'], [
        'region' => $region->id
    ]),
    'TITLE' => Loc::getMessage('list.actions.add')
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'activate' => Loc::getMessage('list.actions.activate'),
    'deactivate' => Loc::getMessage('list.actions.deactivate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($domains as $domain) {
    /** @var Domain $domain */
    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.domains.edit'], [
            'region' => $region->id,
            'domain' => $domain->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    if ($domain->active) {
        if (!$domain->default)
            $actions[] = [
                'TEXT' => Loc::getMessage('list.rows.actions.deactivate'),
                'ACTION' => $list->ActionDoGroup($domain->id, 'deactivate', 'region='.$region->id)
            ];
    } else {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.activate'),
            'ACTION' => $list->ActionDoGroup($domain->id, 'activate', 'region='.$region->id)
        ];
    }

    if (!$domain->default) {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.default'),
            'ACTION' => $list->ActionDoGroup($domain->id, 'default', 'region='.$region->id)
        ];
    }

    if (count($actions) > 2)
        $actions[] = [
            'SEPARATOR' => 'Y'
        ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $domain->id,
            'delete',
            'region='.$region->id
        )
    ];

    $row = $list->AddRow($domain->id, [
        'id' => $domain->id,
        'active' => $domain->active ? 'Y' : 'N',
        'default' => $domain->default ? 'Y' : 'N',
        'siteId' => $domain->siteId,
        'value' => $domain->value,
        'sort' => $domain->sort
    ]);

    $row->AddCheckField('active');
    $row->AddCheckField('default');
    $row->AddSelectField('siteId', $sites->asArray(function ($index, $site) {
        return [
            'key' => $site['ID'],
            'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
        ];
    }));

    $row->AddInputField('value');
    $row->AddInputField('sort');

    $row->AddViewField('value', Html::a($domain->value, '//'.$domain->value, [
        'target' => '_blank'
    ]));

    $row->AddActions($actions);
}

$list->CheckListMode();
$sections = include(__DIR__.'/sections.php');
$panel = include(__DIR__.'/panel.php');

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php $sections($region, 'domains', ['margin-bottom' => '10px']) ?>
<?php $panel->Show() ?>
<form name="find_form" method="get" action="<?= '/'.$request->getPathInfo() ?>">
    <?= Html::hiddenInput('region', $region->id) ?>
    <?php $filter->Begin(); ?>
        <tr>
            <td><?= Loc::getMessage('filter.fields.id') ?>:</td>
            <td><?= Html::textInput('filterIdValue', !empty($filterIdValue) ? $filterIdValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.active') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterActiveValue', !isset($filterActiveValue) || !Type::isNumeric($filterActiveValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.active.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterActiveValue', isset($filterActiveValue) && Type::isNumeric($filterActiveValue) ? $filterActiveValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.active.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterActiveValue', isset($filterActiveValue) && Type::isNumeric($filterActiveValue) ? $filterActiveValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.active.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.default') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterDefaultValue', !isset($filterDefaultValue) || !Type::isNumeric($filterDefaultValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.default.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterDefaultValue', isset($filterDefaultValue) && Type::isNumeric($filterDefaultValue) ? $filterDefaultValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.default.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterDefaultValue', isset($filterDefaultValue) && Type::isNumeric($filterDefaultValue) ? $filterDefaultValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.default.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.sites') ?>:</td>
            <td>
                <?= Html::hiddenInput(
                    'filterSitesValue',
                    null
                ).Html::dropDownList(
                    'filterSitesValue[]',
                    !empty($filterSitesValue) && Type::isArray($filterSitesValue) ? $filterSitesValue : null,
                    ArrayHelper::merge([
                        '' => '('.Loc::getMessage('filter.fields.sites.all').')'
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
            <td><?= Loc::getMessage('filter.fields.value') ?>:</td>
            <td><?= Html::textInput('filterValueValue', !empty($filterValueValue) ? $filterValueValue : null) ?></td>
        </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
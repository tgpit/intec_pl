<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\Url;
use intec\seo\models\filter\url\Scan;

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

$list = 'seo_filter_url';
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
    'filterConditionIdValue',
    'filterActiveValue',
    'filterNameValue',
    'filterSourceValue',
    'filterTargetValue',
    'filterMappingValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.conditionId'),
        Loc::getMessage('filter.fields.active'),
        Loc::getMessage('filter.fields.name'),
        Loc::getMessage('filter.fields.source'),
        Loc::getMessage('filter.fields.target'),
        Loc::getMessage('filter.fields.mapping')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $urls */
        $urls = Url::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Url $url */
            $url = $urls->get($id);

            if (empty($url))
                continue;

            $url->load($data, '');

            if (isset($data['active']))
                $url->active = $url->active === 'Y';

            if (isset($data['mapping']))
                $url->mapping = $url->mapping === 'Y';

            if (!$url->save()) {
                $error = $url->getFirstErrors();
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

    $urls = Url::find();

    if (!empty($id))
        $urls->where(['id' => $id]);

    /** @var Url[] $urls */
    $urls = $urls->all();

    foreach ($urls as $url) {
        if ($action === 'activate') {
            $url->active = true;
            $url->save();
        } else if ($action === 'deactivate') {
            $url->active = false;
            $url->save();
        } else if ($action === 'delete') {
            $url->delete();
        }
    }
}

unset($url);
unset($urls);
unset($id);

$conditions = Condition::find()->all()->indexBy('id');
$urls = Url::find();

if (!empty($filterIdValue))
    $urls->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterConditionIdValue))
    $urls->andWhere(['=', 'conditionId', $filterConditionIdValue]);

if (isset($filterActiveValue) && Type::isNumeric($filterActiveValue))
    $urls->andWhere(['=', 'active', $filterActiveValue > 0 ? 1 : 0]);

if (!empty($filterNameValue))
    $urls->andWhere(['like', 'name', $filterNameValue]);

if (!empty($filterSourceValue))
    $urls->andWhere(['like', 'source', $filterSourceValue]);

if (!empty($filterTargetValue))
    $urls->andWhere(['like', 'target', $filterTargetValue]);

if (isset($filterMappingValue) && Type::isNumeric($filterMappingValue))
    $urls->andWhere(['=', 'mapping', $filterMappingValue > 0 ? 1 : 0]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($urls->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$urls->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $urls->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $urls */
$urls = $urls->all();
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

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'conditionId',
    'content' => Loc::getMessage('list.headers.conditionId'),
    'sort' => 'conditionId',
    'default' => true
], [
    'id' => 'active',
    'content' => Loc::getMessage('list.headers.active'),
    'sort' => 'active',
    'default' => true
], [
    'id' => 'name',
    'content' => Loc::getMessage('list.headers.name'),
    'sort' => 'name',
    'default' => true
], [
    'id' => 'source',
    'content' => Loc::getMessage('list.headers.source'),
    'sort' => 'source',
    'default' => true
], [
    'id' => 'target',
    'content' => Loc::getMessage('list.headers.target'),
    'sort' => 'target',
    'default' => true
], [
    'id' => 'dateCreate',
    'content' => Loc::getMessage('list.headers.dateCreate'),
    'sort' => 'dateCreate',
    'default' => true
], [
    'id' => 'dateChange',
    'content' => Loc::getMessage('list.headers.dateChange'),
    'sort' => 'dateChange',
    'default' => false
], [
    'id' => 'mapping',
    'content' => Loc::getMessage('list.headers.mapping'),
    'sort' => 'mapping',
    'default' => true
], [
    'id' => 'iBlockElementsCount',
    'content' => Loc::getMessage('list.headers.iBlockElementsCount'),
    'sort' => 'iBlockElementsCount',
    'default' => true
], /*[
    'id' => 'metaTitle',
    'content' => Loc::getMessage('list.headers.metaTitle'),
    'sort' => 'metaTitle',
    'default' => false
], [
    'id' => 'metaKeywords',
    'content' => Loc::getMessage('list.headers.metaKeywords'),
    'sort' => 'metaKeywords',
    'default' => false
], [
    'id' => 'metaDescription',
    'content' => Loc::getMessage('list.headers.metaDescription'),
    'sort' => 'metaDescription',
    'default' => false
], [
    'id' => 'metaPageTitle',
    'content' => Loc::getMessage('list.headers.metaPageTitle'),
    'sort' => 'metaPageTitle',
    'default' => false
], [
    'id' => 'metaBreadcrumbName',
    'content' => Loc::getMessage('list.headers.metaBreadcrumbName'),
    'sort' => 'metaBreadcrumbName',
    'default' => false
], */[
    'id' => 'debugMetaTitle',
    'content' => Loc::getMessage('list.headers.debugMetaTitle'),
    'default' => true
], [
    'id' => 'debugMetaKeywords',
    'content' => Loc::getMessage('list.headers.debugMetaKeywords'),
    'default' => true
], [
    'id' => 'debugMetaDescription',
    'content' => Loc::getMessage('list.headers.debugMetaDescription'),
    'default' => true
], [
    'id' => 'debugMetaPageTitle',
    'content' => Loc::getMessage('list.headers.debugMetaPageTitle'),
    'default' => true
], [
    'id' => 'sort',
    'content' => Loc::getMessage('list.headers.sort'),
    'sort' => 'sort',
    'default' => true
]];

$list->AddHeaders($headers);

$list->AddAdminContextMenu([[
    'TEXT' => Loc::getMessage('list.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['filter.url.add'],
    'TITLE' => Loc::getMessage('list.actions.add')
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'activate' => Loc::getMessage('list.actions.activate'),
    'deactivate' => Loc::getMessage('list.actions.deactivate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($urls as $url) {
    /** @var Url $url */
    /** @var Condition $condition */
    /** @var Scan $scan */
    $condition = null;
    $scan = $scans->get($url->id);

    if ($url->conditionId)
        $condition = $conditions->get($url->conditionId);

    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.url.edit'], [
            'url' => $url->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    if ($url->active) {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.deactivate'),
            'ACTION' => $list->ActionDoGroup($url->id, 'deactivate')
        ];
    } else {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.activate'),
            'ACTION' => $list->ActionDoGroup($url->id, 'activate')
        ];
    }

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $url->id,
            'delete'
        )
    ];

    $row = $list->AddRow($url->id, [
        'id' => $url->id,
        'conditionId' => $url->conditionId,
        'active' => $url->active ? 'Y' : 'N',
        'name' => $url->name,
        'source' => $url->source,
        'target' => $url->target,
        'dateCreate' => !empty($url->dateCreate) ? Core::$app->formatter->asDate($url->dateCreate, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('list.rows.answers.no').')',
        'dateChange' => !empty($url->dateChange) ? Core::$app->formatter->asDate($url->dateChange, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('list.rows.answers.no').')',
        'mapping' => $url->mapping ? 'Y' : 'N',
        'iBlockElementsCount' => !empty($url->iBlockElementsCount) ? $url->iBlockElementsCount : '('.Loc::getMessage('list.rows.answers.no').')',
        /*'metaTitle' => $url->metaTitle,
        'metaKeywords' => $url->metaKeywords,
        'metaDescription' => $url->metaDescription,
        'metaPageTitle' => $url->metaPageTitle,
        'metaBreadcrumbName' => $url->metaBreadcrumbName,*/
        'debugMetaTitle' => !empty($scan) ? $scan->metaTitle : '('.Loc::getMessage('list.rows.answers.no').')',
        'debugMetaKeywords' => !empty($scan) ? $scan->metaKeywords : '('.Loc::getMessage('list.rows.answers.no').')',
        'debugMetaDescription' => !empty($scan) ? $scan->metaDescription : '('.Loc::getMessage('list.rows.answers.no').')',
        'debugMetaPageTitle' => !empty($scan) ? $scan->metaPageTitle : '('.Loc::getMessage('list.rows.answers.no').')',
        'sort' => $url->sort
    ]);

    $row->AddSelectField('conditionId', ArrayHelper::merge([
        '' => '('.Loc::getMessage('list.rows.answers.unset').')'
    ], $conditions->asArray(function ($id, $condition) {
        /** @var Condition $condition */

        return [
            'key' => $id,
            'value' => '['.$id.'] '.$condition->name
        ];
    })));

    if (!empty($condition))
        $row->AddViewField('conditionId', Html::a(
            '['.$condition->id.'] '.$condition->name,
            StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
                'condition' => $condition->id
            ])
        ));

    $row->AddViewField('source', Html::a($url->source, $url->source, [
        'target' => '_blank'
    ]));

    $row->AddViewField('target', Html::a($url->target, $url->target, [
        'target' => '_blank'
    ]));

    $row->AddCheckField('active');
    $row->AddInputField('name');
    $row->AddInputField('source');
    $row->AddInputField('target');
    $row->AddCheckField('mapping');
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
            <td><?= Loc::getMessage('filter.fields.conditionId') ?>:</td>
            <td>
                <?= Html::dropDownList('filterConditionIdValue', !empty($filterConditionIdValue) ? $filterConditionIdValue : null, ArrayHelper::merge([
                    '' => '('.Loc::getMessage('list.rows.answers.unset').')'
                ], $conditions->asArray(function ($id, $condition) {
                    /** @var Condition $condition */

                    return [
                        'key' => $id,
                        'value' => '['.$id.'] '.$condition->name
                    ];
                }))) ?>
            </td>
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
            <td><?= Loc::getMessage('filter.fields.name') ?>:</td>
            <td><?= Html::textInput('filterNameValue', !empty($filterNameValue) ? $filterNameValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.source') ?>:</td>
            <td><?= Html::textInput('filterSourceValue', !empty($filterSourceValue) ? $filterSourceValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.target') ?>:</td>
            <td><?= Html::textInput('filterTargetValue', !empty($filterTargetValue) ? $filterTargetValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.mapping') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterMappingValue', !isset($filterMappingValue) || !Type::isNumeric($filterMappingValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.mapping.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterMappingValue', isset($filterMappingValue) && Type::isNumeric($filterMappingValue) ? $filterMappingValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.mapping.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterMappingValue', isset($filterMappingValue) && Type::isNumeric($filterMappingValue) ? $filterMappingValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.mapping.active') ?></span>
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

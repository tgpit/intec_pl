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
use intec\core\net\Url;
use intec\seo\models\filter\Condition;

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

$list = 'seo_filter_conditions';
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
    'filterNameValue',
    'filterSearchableValue',
    'filterIndexingValue',
    'filterStrictValue',
    'filterRecursiveValue',
    'filterPriorityValue',
    'filterFrequencyValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.active'),
        Loc::getMessage('filter.fields.name'),
        Loc::getMessage('filter.fields.searchable'),
        Loc::getMessage('filter.fields.indexing'),
        Loc::getMessage('filter.fields.strict'),
        Loc::getMessage('filter.fields.recursive'),
        Loc::getMessage('filter.fields.priority'),
        Loc::getMessage('filter.fields.frequency')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $conditions */
        $conditions = Condition::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Condition $condition */
            $condition = $conditions->get($id);

            if (empty($condition))
                continue;

            $condition->load($data, '');

            if (isset($data['active']))
                $condition->active = $condition->active === 'Y';

            if (isset($data['searchable']))
                $condition->searchable = $condition->searchable === 'Y';

            if (isset($data['indexing']))
                $condition->indexing = $condition->indexing === 'Y';

            if (isset($data['strict']))
                $condition->strict = $condition->strict === 'Y';

            if (isset($data['recursive']))
                $condition->recursive = $condition->recursive === 'Y';

            if (!$condition->save()) {
                $error = $condition->getFirstErrors();
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

    $conditions = Condition::find();

    if (!empty($id))
        $conditions->where(['id' => $id]);

    /** @var Condition[] $conditions */
    $conditions = $conditions->all();

    foreach ($conditions as $condition) {
        if ($action === 'activate') {
            $condition->active = true;
            $condition->save();
        } else if ($action === 'deactivate') {
            $condition->active = false;
            $condition->save();
        } else if ($action === 'generate') {
            if (empty($condition->urlSource)) {
                $list->AddGroupError(Loc::getMessage('list.errors.generate.source', [
                    '#name#' => $condition->name
                ]), $condition->id);
            } else if (empty($condition->urlTarget)) {
                $list->AddGroupError(Loc::getMessage('list.errors.generate.target', [
                    '#name#' => $condition->name
                ]), $condition->id);
            } else {
                $urls = $condition->getUrl(true);

                foreach ($urls as $url)
                    $url->delete();

                $condition->generateUrl();
            }
        } else if ($action === 'delete') {
            $condition->delete();
        }
    }
}

unset($condition);
unset($conditions);
unset($id);

$conditions = Condition::find();

if (!empty($filterIdValue))
    $conditions->andWhere(['=', 'id', $filterIdValue]);

if (isset($filterActiveValue) && Type::isNumeric($filterActiveValue))
    $conditions->andWhere(['=', 'active', $filterActiveValue > 0 ? 1 : 0]);

if (!empty($filterNameValue))
    $conditions->andWhere(['like', 'name', $filterNameValue]);

if (isset($filterSearchableValue) && Type::isNumeric($filterSearchableValue))
    $conditions->andWhere(['=', 'searchable', $filterSearchableValue > 0 ? 1 : 0]);

if (isset($filterIndexingValue) && Type::isNumeric($filterIndexingValue))
    $conditions->andWhere(['=', 'indexing', $filterIndexingValue > 0 ? 1 : 0]);

if (isset($filterStrictValue) && Type::isNumeric($filterStrictValue))
    $conditions->andWhere(['=', 'strict', $filterStrictValue > 0 ? 1 : 0]);

if (isset($filterRecursiveValue) && Type::isNumeric($filterRecursiveValue))
    $conditions->andWhere(['=', 'recursive', $filterRecursiveValue > 0 ? 1 : 0]);

if (isset($filterPriorityValue) && Type::isNumeric($filterPriorityValue))
    $conditions->andWhere(['=', 'priority', Type::toFloat($filterPriorityValue)]);

if (!empty($filterFrequencyValue))
    $conditions->andWhere(['=', 'frequency', $filterFrequencyValue]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($conditions->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$conditions->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $conditions->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $conditions */
$conditions = $conditions->all();

$headers = [[
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
    'id' => 'name',
    'content' => Loc::getMessage('list.headers.name'),
    'sort' => 'name',
    'default' => true
], [
    'id' => 'searchable',
    'content' => Loc::getMessage('list.headers.searchable'),
    'sort' => 'searchable',
    'default' => true
], [
    'id' => 'indexing',
    'content' => Loc::getMessage('list.headers.indexing'),
    'sort' => 'indexing',
    'default' => true
], [
    'id' => 'strict',
    'content' => Loc::getMessage('list.headers.strict'),
    'sort' => 'strict',
    'default' => true
], [
    'id' => 'recursive',
    'content' => Loc::getMessage('list.headers.recursive'),
    'sort' => 'recursive',
    'default' => true
], [
    'id' => 'priority',
    'content' => Loc::getMessage('list.headers.priority'),
    'sort' => 'priority',
    'default' => true
], [
    'id' => 'frequency',
    'content' => Loc::getMessage('list.headers.frequency'),
    'sort' => 'frequency',
    'default' => true
], [
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
    'id' => 'metaSearchTitle',
    'content' => Loc::getMessage('list.headers.metaSearchTitle'),
    'sort' => 'metaSearchTitle',
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
], [
    'id' => 'metaDescriptionTop',
    'content' => Loc::getMessage('list.headers.metaDescriptionTop'),
    'sort' => 'metaDescriptionTop',
    'default' => false
], [
    'id' => 'metaDescriptionBottom',
    'content' => Loc::getMessage('list.headers.metaDescriptionBottom'),
    'sort' => 'metaDescriptionBottom',
    'default' => false
], [
    'id' => 'metaDescriptionAdditional',
    'content' => Loc::getMessage('list.headers.metaDescriptionAdditional'),
    'sort' => 'metaDescriptionAdditional',
    'default' => false
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
    'LINK' => $arUrlTemplates['filter.conditions.add'],
    'TITLE' => Loc::getMessage('list.actions.add')
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'activate' => Loc::getMessage('list.actions.activate'),
    'deactivate' => Loc::getMessage('list.actions.deactivate'),
    'generate' => Loc::getMessage('list.actions.generate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($conditions as $condition) {
    /** @var Condition $condition */
    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.edit'], [
            'condition' => $condition->id
        ]))
    ];

    $actions[] = [
        'ICON' => 'copy',
        'TEXT' => Loc::getMessage('list.rows.actions.copy'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.copy'], [
            'condition' => $condition->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    if ($condition->active) {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.deactivate'),
            'ACTION' => $list->ActionDoGroup($condition->id, 'deactivate')
        ];
    } else {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.activate'),
            'ACTION' => $list->ActionDoGroup($condition->id, 'activate')
        ];
    }

    $url = new Url($arUrlTemplates['filter.url']);
    $url->getQuery()->setRange([
        'set_filter' => 'Y',
        'filterConditionIdValue' => $condition->id
    ]);

    $actions[] = [
        'TEXT' => Loc::getMessage('list.rows.actions.show'),
        'LINK' => $url->build()
    ];

    unset($url);

    $actions[] = [
        'TEXT' => Loc::getMessage('list.rows.actions.generate'),
        'ACTION' => $list->ActionDoGroup($condition->id, 'generate')
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $condition->id,
            'delete'
        )
    ];

    $row = $list->AddRow($condition->id, [
        'id' => $condition->id,
        'active' => $condition->active ? 'Y' : 'N',
        'name' => $condition->name,
        'searchable' => $condition->searchable ? 'Y' : 'N',
        'indexing' => $condition->indexing ? 'Y' : 'N',
        'strict' => $condition->strict ? 'Y' : 'N',
        'recursive' => $condition->recursive ? 'Y' : 'N',
        'priority' => $condition->priority,
        'frequency' => $condition->frequency,
        'metaTitle' => $condition->metaTitle,
        'metaKeywords' => $condition->metaKeywords,
        'metaDescription' => $condition->metaDescription,
        'metaSearchTitle' => $condition->metaSearchTitle,
        'metaPageTitle' => $condition->metaPageTitle,
        'metaBreadcrumbName' => $condition->metaBreadcrumbName,
        'metaDescriptionTop' => $condition->metaDescriptionTop,
        'metaDescriptionBottom' => $condition->metaDescriptionBottom,
        'metaDescriptionAdditional' => $condition->metaDescriptionAdditional,
        'sort' => $condition->sort
    ]);

    $row->AddCheckField('active');
    $row->AddInputField('name');
    $row->AddCheckField('searchable');
    $row->AddCheckField('indexing');
    $row->AddCheckField('strict');
    $row->AddCheckField('recursive');
    $row->AddInputField('priority');
    $row->AddSelectField('frequency', Condition::getFrequencies());
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
            <td><?= Loc::getMessage('filter.fields.searchable') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterSearchableValue', !isset($filterSearchableValue) || !Type::isNumeric($filterSearchableValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.searchable.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterSearchableValue', isset($filterSearchableValue) && Type::isNumeric($filterSearchableValue) ? $filterSearchableValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.searchable.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterSearchableValue', isset($filterSearchableValue) && Type::isNumeric($filterSearchableValue) ? $filterSearchableValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.searchable.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.indexing') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterIndexingValue', !isset($filterIndexingValue) || !Type::isNumeric($filterIndexingValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.indexing.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterIndexingValue', isset($filterIndexingValue) && Type::isNumeric($filterIndexingValue) ? $filterIndexingValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.indexing.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterIndexingValue', isset($filterIndexingValue) && Type::isNumeric($filterIndexingValue) ? $filterIndexingValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.indexing.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.strict') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterStrictValue', !isset($filterStrictValue) || !Type::isNumeric($filterStrictValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.strict.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterStrictValue', isset($filterStrictValue) && Type::isNumeric($filterStrictValue) ? $filterStrictValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.strict.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterStrictValue', isset($filterStrictValue) && Type::isNumeric($filterStrictValue) ? $filterStrictValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.strict.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.recursive') ?>:</td>
            <td>
                <div style="margin-top: 6px;">
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterRecursiveValue', !isset($filterRecursiveValue) || !Type::isNumeric($filterRecursiveValue), [
                                'value' => null,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.recursive.any') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <label>
                            <?= Html::radio('filterRecursiveValue', isset($filterRecursiveValue) && Type::isNumeric($filterRecursiveValue) ? $filterRecursiveValue == 0 : false, [
                                'value' => 0,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.recursive.inactive') ?></span>
                        </label>
                    </div>
                    <div style="margin-bottom: 13px;">
                        <label>
                            <?= Html::radio('filterRecursiveValue', isset($filterRecursiveValue) && Type::isNumeric($filterRecursiveValue) ? $filterRecursiveValue > 0 : false, [
                                'value' => 1,
                                'style' => [
                                    'margin' => 0
                                ]
                            ]) ?>
                            <span style="margin-left: 5px; vertical-align: middle;"><?= Loc::getMessage('filter.fields.recursive.active') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.priority') ?>:</td>
            <td><?= Html::textInput('filterPriorityValue', !empty($filterPriorityValue) ? $filterPriorityValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.frequency') ?>:</td>
            <td><?= Html::dropDownList('filterFrequencyValue', !empty($filterFrequencyValue) ? $filterFrequencyValue : null, ArrayHelper::merge([
                '' => '('.Loc::getMessage('filter.fields.frequency.any').')'
            ], Condition::getFrequencies())) ?></td>
        </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>

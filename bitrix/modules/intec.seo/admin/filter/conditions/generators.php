<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\condition\Generator;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title'));

include(__DIR__.'/../../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

$request = Core::$app->request;

$list = 'seo_filter_conditions_generators';
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
    'filterNameValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.name')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $generators */
        $generators = Generator::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Generator $generator */
            $generator = $generators->get($id);

            if (empty($generator))
                continue;

            $generator->load($data, '');

            if (isset($data['conditionActive']))
                $generator->conditionActive = $generator->conditionActive === 'Y';

            if (isset($data['conditionSearchable']))
                $generator->conditionSearchable = $generator->conditionSearchable === 'Y';

            if (isset($data['conditionIndexing']))
                $generator->conditionIndexing = $generator->conditionIndexing === 'Y';

            if (isset($data['conditionStrict']))
                $generator->conditionStrict = $generator->conditionStrict === 'Y';

            if (isset($data['conditionRecursive']))
                $generator->conditionRecursive = $generator->conditionRecursive === 'Y';

            if (!$generator->save()) {
                $error = $generator->getFirstErrors();
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

    $generators = Generator::find();

    if (!empty($id))
        $generators->where(['id' => $id]);

    /** @var Generator[] $generators */
    $generators = $generators->all();

    foreach ($generators as $generator) {
        if ($action === 'generate') {
            if (!$generator->generate()) {
                $list->AddGroupError(Loc::getMessage('list.errors.generate', [
                    '#name#' => $generator->name
                ]), $generator->id);
            }
        } else if ($action === 'delete') {
            $generator->delete();
        }
    }
}

unset($generator);
unset($generators);
unset($id);

$generators = Generator::find();

if (!empty($filterIdValue))
    $generators->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterNameValue))
    $generators->andWhere(['like', 'name', $filterNameValue]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($generators->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$generators->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $generators->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $generators */
$generators = $generators->all();

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'name',
    'content' => Loc::getMessage('list.headers.name'),
    'sort' => 'name',
    'default' => true
], [
    'id' => 'conditionActive',
    'content' => Loc::getMessage('list.headers.conditionActive'),
    'sort' => 'conditionActive',
    'default' => true
], [
    'id' => 'conditionName',
    'content' => Loc::getMessage('list.headers.conditionName'),
    'sort' => 'conditionName',
    'default' => true
], [
    'id' => 'conditionSearchable',
    'content' => Loc::getMessage('list.headers.conditionSearchable'),
    'sort' => 'conditionSearchable',
    'default' => true
], [
    'id' => 'conditionIndexing',
    'content' => Loc::getMessage('list.headers.conditionIndexing'),
    'sort' => 'conditionIndexing',
    'default' => true
], [
    'id' => 'conditionStrict',
    'content' => Loc::getMessage('list.headers.conditionStrict'),
    'sort' => 'conditionStrict',
    'default' => true
], [
    'id' => 'conditionRecursive',
    'content' => Loc::getMessage('list.headers.conditionRecursive'),
    'sort' => 'conditionRecursive',
    'default' => true
], [
    'id' => 'conditionPriority',
    'content' => Loc::getMessage('list.headers.conditionPriority'),
    'sort' => 'conditionPriority',
    'default' => true
], [
    'id' => 'conditionFrequency',
    'content' => Loc::getMessage('list.headers.conditionFrequency'),
    'sort' => 'conditionFrequency',
    'default' => true
], [
    'id' => 'conditionMetaTitle',
    'content' => Loc::getMessage('list.headers.conditionMetaTitle'),
    'sort' => 'conditionMetaTitle',
    'default' => false
], [
    'id' => 'conditionMetaKeywords',
    'content' => Loc::getMessage('list.headers.conditionMetaKeywords'),
    'sort' => 'conditionMetaKeywords',
    'default' => false
], [
    'id' => 'conditionMetaDescription',
    'content' => Loc::getMessage('list.headers.conditionMetaDescription'),
    'sort' => 'conditionMetaDescription',
    'default' => false
], [
    'id' => 'conditionMetaSearchTitle',
    'content' => Loc::getMessage('list.headers.conditionMetaSearchTitle'),
    'sort' => 'conditionMetaSearchTitle',
    'default' => false
], [
    'id' => 'conditionMetaPageTitle',
    'content' => Loc::getMessage('list.headers.conditionMetaPageTitle'),
    'sort' => 'conditionMetaPageTitle',
    'default' => false
], [
    'id' => 'conditionMetaBreadcrumbName',
    'content' => Loc::getMessage('list.headers.conditionMetaBreadcrumbName'),
    'sort' => 'conditionMetaBreadcrumbName',
    'default' => false
], [
    'id' => 'conditionMetaDescriptionTop',
    'content' => Loc::getMessage('list.headers.conditionMetaDescriptionTop'),
    'sort' => 'conditionMetaDescriptionTop',
    'default' => false
], [
    'id' => 'conditionMetaDescriptionBottom',
    'content' => Loc::getMessage('list.headers.conditionMetaDescriptionBottom'),
    'sort' => 'conditionMetaDescriptionBottom',
    'default' => false
], [
    'id' => 'conditionMetaDescriptionAdditional',
    'content' => Loc::getMessage('list.headers.conditionMetaDescriptionAdditional'),
    'sort' => 'conditionMetaDescriptionAdditional',
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
    'LINK' => $arUrlTemplates['filter.conditions.generators.add'],
    'TITLE' => Loc::getMessage('list.actions.add')
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'generate' => Loc::getMessage('list.actions.generate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($generators as $generator) {
    /** @var Generator $generator */
    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.generators.edit'], [
            'generator' => $generator->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'TEXT' => Loc::getMessage('list.rows.actions.generate'),
        'ACTION' => $list->ActionDoGroup($generator->id, 'generate')
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $generator->id,
            'delete'
        )
    ];

    $row = $list->AddRow($generator->id, [
        'id' => $generator->id,
        'name' => $generator->name,
        'conditionActive' => $generator->conditionActive ? 'Y' : 'N',
        'conditionName' => $generator->conditionName,
        'conditionSearchable' => $generator->conditionSearchable ? 'Y' : 'N',
        'conditionIndexing' => $generator->conditionIndexing ? 'Y' : 'N',
        'conditionStrict' => $generator->conditionStrict ? 'Y' : 'N',
        'conditionRecursive' => $generator->conditionRecursive ? 'Y' : 'N',
        'conditionPriority' => $generator->conditionPriority,
        'conditionFrequency' => $generator->conditionFrequency,
        'conditionMetaTitle' => $generator->conditionMetaTitle,
        'conditionMetaKeywords' => $generator->conditionMetaKeywords,
        'conditionMetaDescription' => $generator->conditionMetaDescription,
        'conditionMetaSearchTitle' => $generator->conditionMetaSearchTitle,
        'conditionMetaPageTitle' => $generator->conditionMetaPageTitle,
        'conditionMetaBreadcrumbName' => $generator->conditionMetaBreadcrumbName,
        'conditionMetaDescriptionTop' => $generator->conditionMetaDescriptionTop,
        'conditionMetaDescriptionBottom' => $generator->conditionMetaDescriptionBottom,
        'conditionMetaDescriptionAdditional' => $generator->conditionMetaDescriptionAdditional,
        'sort' => $generator->sort
    ]);

    $row->AddInputField('name');
    $row->AddCheckField('conditionActive');
    $row->AddInputField('conditionName');
    $row->AddCheckField('conditionSearchable');
    $row->AddCheckField('conditionIndexing');
    $row->AddCheckField('conditionStrict');
    $row->AddCheckField('conditionRecursive');
    $row->AddInputField('conditionPriority');
    $row->AddSelectField('conditionFrequency', Condition::getFrequencies());
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
            <td><?= Loc::getMessage('filter.fields.name') ?>:</td>
            <td><?= Html::textInput('filterNameValue', !empty($filterNameValue) ? $filterNameValue : null) ?></td>
        </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>

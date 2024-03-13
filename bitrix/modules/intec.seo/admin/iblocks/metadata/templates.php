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
use intec\seo\models\iblocks\metadata\Template;

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

$list = 'seo_iblocks_metadata_templates';
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
    'filterCodeValue',
    'filterActiveValue',
    'filterNameValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.code'),
        Loc::getMessage('filter.fields.active'),
        Loc::getMessage('filter.fields.name')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $templates */
        $templates = Template::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Template $template */
            $template = $templates->get($id);

            if (empty($template))
                continue;

            $template->load($data, '');

            if (isset($data['active']))
                $template->active = $template->active === 'Y';

            if (!$template->save()) {
                $error = $template->getFirstErrors();
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

    $templates = Template::find();

    if (!empty($id))
        $templates->where(['id' => $id]);

    /** @var Template[] $templates */
    $templates = $templates->all();

    foreach ($templates as $template) {
        if ($action === 'activate') {
            $template->active = 1;
            $template->save();
        } else if ($action === 'deactivate') {
            $template->active = 0;
            $template->save();
        } else if ($action === 'delete') {
            $template->delete();
        }
    }
}

unset($template);
unset($templates);
unset($id);

$templates = Template::find();

if (!empty($filterIdValue))
    $templates->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterCodeValue))
    $templates->andWhere(['like', 'code', $filterCodeValue]);

if (isset($filterActiveValue) && Type::isNumeric($filterActiveValue))
    $templates->andWhere(['=', 'active', $filterActiveValue > 0 ? 1 : 0]);

if (!empty($filterNameValue))
    $templates->andWhere(['like', 'name', $filterNameValue]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($templates->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$templates->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $templates->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $templates */
$templates = $templates->all();

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'code',
    'content' => Loc::getMessage('list.headers.code'),
    'sort' => 'code',
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
    'id' => 'sort',
    'content' => Loc::getMessage('list.headers.sort'),
    'sort' => 'sort',
    'default' => true
]];

$list->AddHeaders($headers);

$list->AddAdminContextMenu([[
    'TEXT' => Loc::getMessage('list.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['iblocks.metadata.templates.add'],
    'TITLE' => Loc::getMessage('list.actions.add')
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'activate' => Loc::getMessage('list.actions.activate'),
    'deactivate' => Loc::getMessage('list.actions.deactivate')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($templates as $template) {
    /** @var Template $template */
    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['iblocks.metadata.templates.edit'], [
            'template' => $template->id
        ]))
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    if ($template->active) {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.deactivate'),
            'ACTION' => $list->ActionDoGroup($template->id, 'deactivate')
        ];
    } else {
        $actions[] = [
            'TEXT' => Loc::getMessage('list.rows.actions.activate'),
            'ACTION' => $list->ActionDoGroup($template->id, 'activate')
        ];
    }

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
                $template->id,
                'delete'
            )
    ];

    $row = $list->AddRow($template->id, [
        'id' => $template->id,
        'code' => $template->code,
        'active' => $template->active ? 'Y' : 'N',
        'name' => $template->name,
        'sort' => $template->sort
    ]);

    $row->AddInputField('code');
    $row->AddCheckField('active');
    $row->AddInputField('name');
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
            <td><?= Loc::getMessage('filter.fields.code') ?>:</td>
            <td><?= Html::textInput('filterCodeValue', !empty($filterCodeValue) ? $filterCodeValue : null) ?></td>
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
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>

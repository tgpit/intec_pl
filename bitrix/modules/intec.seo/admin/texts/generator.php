<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\text\Pattern;
use intec\seo\text\generator\ParserException;
use intec\seo\text\generators\IBlockElementGenerator;
use intec\seo\text\generators\IBlockGenerator;
use intec\seo\text\generators\IBlockSectionGenerator;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

Loader::includeModule('fileman');

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title'));

include(__DIR__.'/../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

Core::$app->web->js->loadExtensions(['knockout_extensions']);

$request = Core::$app->request;

if ($request->getIsAjax() && $request->getIsPost()) {
    $action = $request->post('action');
    $response = null;

    if ($action === 'get.iblocks') {
        $response = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], []));
        $response = $response->asArray(function ($index, $item) {
            return [
                'value' => [
                    'id' => $item['ID'],
                    'name' => '['.$item['ID'].'] '.$item['NAME']
                ]
            ];
        });
    } else if ($action === 'get.sections') {
        $iblocks = $request->post('iblocks');

        if (!empty($iblocks) && Type::isArray($iblocks)) {
            $response = Arrays::fromDBResult(CIBlockSection::GetList(['LEFT_MARGIN' => 'ASC'], [
                'IBLOCK_ID' => $iblocks
            ], false, [
                'ID',
                'NAME',
                'DEPTH_LEVEL'
            ]));

            $response = $response->asArray(function ($index, $item) {
                return [
                    'value' => [
                        'id' => $item['ID'],
                        'name' => str_repeat('.', $item['DEPTH_LEVEL']).' ['.$item['ID'].'] '.$item['NAME']
                    ]
                ];
            });
        } else {
            $response = [];
        }
    } else if ($action === 'get.elements') {
        $iblocks = $request->post('iblocks');
        $sections = $request->post('sections');

        if (!empty($iblocks) && Type::isArray($iblocks)) {
            $filter = [
                'IBLOCK_ID' => $iblocks
            ];

            if (!empty($sections))
                $filter['SECTION_ID'] = $sections;

            $response = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], $filter, false, false, [
                'ID',
                'NAME'
            ]));

            $response = $response->asArray(function ($index, $item) {
                return [
                    'value' => [
                        'id' => $item['ID'],
                        'name' => '['.$item['ID'].'] '.$item['NAME']
                    ]
                ];
            });
        } else {
            $response = [];
        }
    } else if ($action === 'process.prepare') {
        $entity = $request->post('entity');

        if ($entity === 'IBlock') {
            $iblocks = $request->post('iblocks');

            if (!empty($iblocks) && Type::isArray($iblocks)) {
                $response = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
                    'ID' => $iblocks
                ]));

                $response = [
                    'status' => 'success',
                    'items' => $response->asArray(function ($index, $item) {
                        return [
                            'value' => [
                                'id' => $item['ID'],
                                'name' => '['.$item['ID'].'] '.$item['NAME']
                            ]
                        ];
                    })
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Loc::getMessage('states.processing.errors.iblocks')
                ];
            }
        } else if ($entity === 'IBlockSection') {
            $iblocks = $request->post('iblocks');

            if (!empty($iblocks) && Type::isArray($iblocks)) {
                $sections = $request->post('sections');
                $filter = [
                    'IBLOCK_ID' => $iblocks
                ];

                if (!empty($sections))
                    $filter['ID'] = $sections;

                $response = Arrays::fromDBResult(CIBlockSection::GetList(['SORT' => 'ASC'], $filter));
                $response = [
                    'status' => 'success',
                    'items' => $response->asArray(function ($index, $item) {
                        return [
                            'value' => [
                                'id' => $item['ID'],
                                'name' => '['.$item['ID'].'] '.$item['NAME']
                            ]
                        ];
                    })
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Loc::getMessage('states.processing.errors.iblocks')
                ];
            }
        } else if ($entity === 'IBlockElement') {
            $iblocks = $request->post('iblocks');

            if (!empty($iblocks) && Type::isArray($iblocks)) {
                $sections = $request->post('sections');
                $elements = $request->post('elements');
                $filter = [
                    'IBLOCK_ID' => $iblocks
                ];

                if (!empty($sections))
                    $filter['SECTION_ID'] = $sections;

                if (!empty($elements))
                    $filter['ID'] = $elements;

                $response = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], $filter));
                $response = [
                    'status' => 'success',
                    'items' => $response->asArray(function ($index, $item) {
                        return [
                            'value' => [
                                'id' => $item['ID'],
                                'name' => '['.$item['ID'].'] '.$item['NAME']
                            ]
                        ];
                    })
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Loc::getMessage('states.processing.errors.iblocks')
                ];
            }
        }
    } else if ($action === 'process.handle') {
        $id = $request->post('id');
        $refill = $request->post('refill');
        $refill = Type::toBoolean($refill);
        $source = $request->post('source');
        $text = null;

        if (!empty($id)) {
            if ($source === 'textPattern') {
                $text = Pattern::findOne($request->post('textPattern'));

                if (!empty($text)) {
                    $text = $text->value;
                } else {
                    $text = null;
                }
            } else {
                $text = StringHelper::convert($request->post('text'), null, Encoding::UTF8);
            }

            if ($text !== null) {
                $generator = null;
                $handler = null;
                $field = null;

                if ($entity === 'IBlock') {
                    $entity = CIBlock::GetByID($id)->Fetch();
                    $field = 'DESCRIPTION';

                    if (!empty($entity)) {
                        if (empty($entity[$field]) || $refill) {
                            $generator = new IBlockGenerator();
                            $generator->setIBlock($id);
                            $handler = new CIBlock();
                        } else {
                            $response = [
                                'status' => 'success'
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => Loc::getMessage('states.processing.errors.iblock', [
                                '#ID#' => $id
                            ])
                        ];
                    }
                } else if ($entity === 'IBlockSection') {
                    $entity = CIBlockSection::GetByID($id)->Fetch();
                    $field = 'DESCRIPTION';

                    if (!empty($entity)) {
                        if (empty($entity[$field]) || $refill) {
                            $generator = new IBlockSectionGenerator();
                            $generator->setSection($id);
                            $handler = new CIBlockSection();
                        } else {
                            $response = [
                                'status' => 'success'
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => Loc::getMessage('states.processing.errors.section', [
                                '#ID#' => $id
                            ])
                        ];
                    }
                } else if ($entity === 'IBlockElement') {
                    $entity = CIBlockElement::GetByID($id)->Fetch();
                    $field = $request->post('field');
                    $field = ArrayHelper::fromRange([
                        'PREVIEW_TEXT',
                        'DETAIL_TEXT'
                    ], $field);

                    if (!empty($entity)) {
                        if (empty($entity[$field]) || $refill) {
                            $generator = new IBlockElementGenerator();
                            $generator->setElement($id);
                            $handler = new CIBlockElement();
                        } else {
                            $response = [
                                'status' => 'success'
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => Loc::getMessage('states.processing.errors.element', [
                                '#ID#' => $id
                            ])
                        ];
                    }
                }

                if (!empty($generator) && !empty($handler) && !empty($field)) {
                    try {
                        $handler->Update($id, [
                            $field => $generator->process($text)
                        ]);

                        $response = [
                            'status' => 'success'
                        ];
                    } catch (ParserException $exception) {
                        $response = [
                            'status' => 'error',
                            'message' => Loc::getMessage('states.processing.errors.parse') . ': ' . $exception->getMessage()
                        ];
                    }
                } else {
                    $response = [
                        'status' => 'success'
                    ];
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Loc::getMessage('states.processing.errors.source')
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'No entity identifier'
            ];
        }
    }

    echo Json::encode($response, 320, true);
    return;
}

$textPatterns = Pattern::find()->where([
    'active' => 1
])->all();

$tabs = new CAdminTabControl('textsGeneratorForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
]], false);

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<div id="page">
    <?php $tabs->Begin() ?>
        <?php $tabs->BeginNextTab() ?>
            <!-- ko if: state() === 'configuring' -->
                <tr>
                    <td width="40%"><?= Loc::getMessage('fields.entity') ?>:</td>
                    <td>
                        <select data-bind="{
                            value: entity
                        }">
                            <option value="IBlock"><?= Loc::getMessage('fields.entity.IBlock') ?></option>
                            <option value="IBlockSection"><?= Loc::getMessage('fields.entity.IBlockSection') ?></option>
                            <option value="IBlockElement"><?= Loc::getMessage('fields.entity.IBlockElement') ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="40%"><?= Loc::getMessage('fields.iblocks') ?></td>
                    <td>
                        <select data-bind="{
                            options: iblocks.list,
                            optionsValue: 'id',
                            optionsText: 'name',
                            optionsCaption: '(<?= Loc::getMessage('answers.unselected') ?>)',
                            selectedOptions: iblocks
                        }" multiple="multiple" size="10"></select>
                    </td>
                </tr>
                <!-- ko if: entity() === 'IBlockSection' || entity() === 'IBlockElement' -->
                    <tr>
                        <td width="40%"><?= Loc::getMessage('fields.sections') ?></td>
                        <td>
                            <select data-bind="{
                                options: sections.list,
                                optionsValue: 'id',
                                optionsText: 'name',
                                optionsCaption: '(<?= Loc::getMessage('answers.unselected') ?>)',
                                selectedOptions: sections
                            }" multiple="multiple" size="10"></select>
                        </td>
                    </tr>
                <!-- /ko -->
                <!-- ko if: entity() === 'IBlockElement' -->
                    <tr>
                        <td width="40%"><?= Loc::getMessage('fields.elements') ?></td>
                        <td>
                            <select data-bind="{
                                options: elements.list,
                                optionsValue: 'id',
                                optionsText: 'name',
                                optionsCaption: '(<?= Loc::getMessage('answers.unselected') ?>)',
                                selectedOptions: elements
                            }" multiple="multiple" size="10"></select>
                        </td>
                    </tr>
                    <tr>
                        <td width="40%"><?= Loc::getMessage('fields.field') ?></td>
                        <td>
                            <select data-bind="{
                                value: field
                            }">
                                <option value="PREVIEW_TEXT"><?= Loc::getMessage('fields.field.PREVIEW_TEXT') ?></option>
                                <option value="DETAIL_TEXT"><?= Loc::getMessage('fields.field.DETAIL_TEXT') ?></option>
                            </select>
                        </td>
                    </tr>
                <!-- /ko -->
                <tr>
                    <td width="40%"><?= Loc::getMessage('fields.refill') ?>:</td>
                    <td>
                        <input type="checkbox" data-bind="{
                            checked: refill
                        }"/>
                    </td>
                </tr>
                <tr>
                    <td width="40%"><?= Loc::getMessage('fields.source') ?>:</td>
                    <td>
                        <select data-bind="{
                            value: source
                        }">
                            <option value="text"><?= Loc::getMessage('fields.source.text') ?></option>
                            <option value="textPattern"><?= Loc::getMessage('fields.source.textPattern') ?></option>
                        </select>
                    </td>
                </tr>
                <!-- ko if: source() === 'textPattern' -->
                    <tr>
                        <td width="40%"><?= Loc::getMessage('fields.textPattern') ?>:</td>
                        <td>
                            <?= Html::dropDownList(null, null, ArrayHelper::merge([
                                '' => '('.Loc::getMessage('answers.unselected').')'
                            ], $textPatterns->asArray(function ($index, $textPattern) {
                                return [
                                    'key' => $textPattern->id,
                                    'value' => '['.$textPattern->code.'] '.$textPattern->name
                                ];
                            })), [
                                'data-bind' => '{
                                    value: textPattern
                                }'
                            ]) ?>
                        </td>
                    </tr>
                <!-- /ko -->
            <!-- /ko -->
            <tr data-bind="{
                visible: state() === 'configuring' && source() === 'text'
            }">
                <td width="40%"><?= Loc::getMessage('fields.text') ?>:</td>
                <td>
                    <?php CFileMan::AddHTMLEditorFrame(
                        'text',
                        $textPattern->value,
                        null,
                        'html',
                        [
                            'height' => 450,
                            'width' => '100%'
                        ],
                        'N',
                        0,
                        '',
                        '',
                        '',
                        true,
                        false,
                        [
                            'toolbarConfig' => 'admin',
                            'saveEditorState' => false,
                            'hideTypeSelector' => true
                        ]
                    ) ?>
                </td>
            </tr>
            <tr data-bind="{
                visible: state() === 'configuring' && source() === 'text'
            }">
                <td colspan="2">
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message">
                            <?= Loc::getMessage('fields.text.description') ?>
                        </div>
                    </div>
                </td>
            </tr>
            <!-- ko if: state() === 'processing' -->
                <tr>
                    <td colspan="2">
                        <div class="adm-info-message-wrap adm-info-message-gray">
                            <div class="adm-info-message">
                                <span data-bind="{
                                    text: '<?= Loc::getMessage('states.processing.handle') ?> ' + (items.position() + 1) + ' <?= Loc::getMessage('states.processing.from') ?> ' + items.total()
                                }"></span>
                                <div class="adm-progress-bar-outer" style="width: 600px">
                                    <div class="adm-progress-bar-inner"  data-bind="{
                                            style: {
                                                width: (((items.position() + 1) * 100 / items.total()).toFixed(0) - 1) + '%'
                                            }
                                        }">
                                        <div class="adm-progress-bar-inner-text" data-bind="{
                                            text: ((items.position() + 1) * 100 / items.total()).toFixed(2) + '%'
                                        }" style="width: 600px">0%</div>
                                    </div>
                                    <span data-bind="{
                                        text: ((items.position() + 1) * 100 / items.total()).toFixed(2) + '%'
                                    }">0%</span>
                                </div>
                                <div class="adm-info-message-buttons"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            <!-- /ko -->
            <!-- ko if: state() === 'complete' -->
                <tr>
                    <td colspan="2">
                        <div class="adm-info-message-wrap adm-info-message-gray">
                            <div class="adm-info-message">
                                <?= Loc::getMessage('states.complete.message') ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <!-- /ko -->
            <!-- ko if: state() === 'error' -->
                <tr>
                    <td colspan="2">
                        <div class="adm-info-message-wrap adm-info-message-gray">
                            <div class="adm-info-message">
                                <div><b><?= Loc::getMessage('states.error.message') ?>:</b></div>
                                <div data-bind="{
                                    text: error
                                }"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            <!-- /ko -->
        <?php $tabs->Buttons() ?>
        <input type="button" class="adm-btn-save" data-bind="{
            click: process,
            enable: state() === 'configuring' || state() === 'complete'
        }" value="<?= Loc::getMessage('actions.generate') ?>">
        <input type="button" class="adm-btn" data-bind="{
            click: reset,
            enable: state() === 'complete' || state() === 'error'
        }" value="<?= Loc::getMessage('actions.back') ?>">
    <?php $tabs->End() ?>
    <script type="text/javascript">
        (function ($, api) {
            var root = {};
            var state = ko.observable('configuring');

            root.entity = ko.observable();
            root.field = ko.observable();

            root.iblocks = ko.observableArray([]);
            root.iblocks.list = ko.observableArray([]);
            root.iblocks.list.refresh = function () {
                $.ajax({
                    'cache': false,
                    'type': 'POST',
                    'data': {
                        'action': 'get.iblocks'
                    },
                    'dataType': 'json',
                    'success': function (data) {
                        root.iblocks.list.removeAll();

                        api.each(data, function (index, item) {
                            root.iblocks.list.push(item);
                        });
                    },
                    'error': function (error) {
                        console.error(error);
                    }
                });
            };

            root.iblocks.subscribe(function () {
                root.sections.list.refresh();
                root.elements.list.refresh();
            });

            root.sections = ko.observableArray([]);
            root.sections.list = ko.observableArray([]);
            root.sections.list.refresh = function () {
                $.ajax({
                    'cache': false,
                    'type': 'POST',
                    'data': {
                        'action': 'get.sections',
                        'iblocks': root.iblocks()
                    },
                    'dataType': 'json',
                    'success': function (data) {
                        root.sections.list.removeAll();

                        api.each(data, function (index, item) {
                            root.sections.list.push(item);
                        });
                    },
                    'error': function (error) {
                        console.error(error);
                    }
                });
            };

            root.sections.subscribe(function () {
                root.elements.list.refresh();
            });

            root.elements = ko.observableArray([]);
            root.elements.list = ko.observableArray([]);
            root.elements.list.refresh = function () {
                $.ajax({
                    'cache': false,
                    'type': 'POST',
                    'data': {
                        'action': 'get.elements',
                        'iblocks': root.iblocks(),
                        'sections': root.sections()
                    },
                    'dataType': 'json',
                    'success': function (data) {
                        root.elements.list.removeAll();

                        api.each(data, function (index, item) {
                            root.elements.list.push(item);
                        });
                    },
                    'error': function (error) {
                        console.error(error);
                    }
                });
            };

            root.refill = ko.observable(false);
            root.source = ko.observable();
            root.textPattern = ko.observable();
            root.error = ko.observable();
            root.items = {
                'current': ko.observable(null),
                'position': ko.observable(0),
                'total': ko.observable(0)
            };

            root.state = ko.computed(function () {
                return state();
            });

            root.process = function () {
                var position = 0;
                var items = [];
                var prepare = function () {
                    $.ajax({
                        'cache': false,
                        'type': 'POST',
                        'data': {
                            'action': 'process.prepare',
                            'entity': root.entity(),
                            'iblocks': root.iblocks(),
                            'sections': root.sections(),
                            'elements': root.elements()
                        },
                        'dataType': 'json',
                        'success': function (data) {
                            if (data.status === 'success') {
                                items = data.items;
                                root.items.total(items.length);
                                handle();
                            } else if (data.status === 'error') {
                                root.error(data.message);
                                state('error');
                            }
                        },
                        'error': function (error) {
                            console.error(error);
                        }
                    });
                };

                var handle = function () {
                    var data;
                    var item;
                    var text = $('textarea[name="text"]').val();

                    if (position >= items.length) {
                        state('complete');
                    } else {
                        item = items[position];
                        root.items.current(item);
                        root.items.position(position);

                        data = {
                            'action': 'process.handle',
                            'entity': root.entity(),
                            'field': root.field(),
                            'id': item.id,
                            'refill': root.refill() ? 1 : 0,
                            'source': root.source()
                        };

                        if (data.source === 'text') {
                            data.text = text;
                        } else {
                            data.textPattern = root.textPattern();
                        }

                        $.ajax({
                            'cache': false,
                            'type': 'POST',
                            'data': data,
                            'dataType': 'json',
                            'success': function (data) {
                                if (data.status === 'success') {
                                    position++;
                                    handle();
                                } else if (data.status === 'error') {
                                    root.error(data.message);
                                    state('error');
                                }
                            },
                            'error': function (error) {
                                console.error(error);
                            }
                        });
                    }
                };

                state('processing');
                root.error(null);
                root.items.current(null);
                root.items.position(0);
                root.items.total(0);
                prepare();
            };

            root.reset = function () {
                state('configuring');
            };

            root.iblocks.list.refresh();

            ko.applyBindings(root, $('#page').get(0));
        })(jQuery, intec);
    </script>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>

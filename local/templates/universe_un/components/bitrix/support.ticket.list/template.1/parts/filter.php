<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arGet
 */

?>

<div class="support-ticket-list-filter">
    <div class="support-ticket-list-filter-title">
        <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER') ?>
    </div>
    <div class="support-ticket-list-filter-wrap">
        <form action="" method="get" class="support-ticket-list-filter-form" data-role="filter">
            <?php if (isset($arGet['SECTION'])) { ?>
                <input type="hidden" name="SECTION" value="<?= $arGet['SECTION'] ?>">
            <?php } ?>
            <div class="intec-grid intec-grid-i-8 intec-grid-wrap intec-grid-a-v-end">
                <?php foreach ($arResult['FILTER'] as $arFilter) { ?>
                    <div class="intec-grid-item-auto intec-grid-item-425-1" <?= $arFilter['id'] == 'MESSAGE' ? 'style="display: none"' : ''?>>
                        <label class="support-ticket-list-filter-form-label" for="<?= $arFilter['id']?>">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_'.StringHelper::toUpperCase($arFilter['id'])) ?>
                        </label>
                        <?php if (isset($arFilter['items'])) { ?>
                            <?= Html::beginTag('select', [
                                'class' => 'support-ticket-list-filter-form-select',
                                'id' => $arFilter['id'],
                                'name' => $arFilter['id']
                            ]) ?>
                            <?php if ($arFilter['id'] == 'LAMP') { ?>
                                <option value=""><?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_OPTION_ALL') ?></option>
                            <?php } ?>
                            <?php foreach ($arFilter['items'] as $keyOption => $arOption) { ?>
                                <option value="<?= $keyOption ?>" <?= isset($arGet[$arFilter['id']]) && $arGet[$arFilter['id']] == $keyOption ? 'selected=""' : null ?>><?= $arOption ?></option>
                            <?php } ?>
                            <?php unset($keyOption, $arOption) ?>
                            <?= Html::endTag('select') ?>
                        <?php } else { ?>
                            <?= Html::tag('input', '', [
                                'class' => [
                                    'support-ticket-list-filter-form-input',
                                    'intec-ui' => [
                                        '',
                                        'control-input',
                                        'view-1',
                                        'mod-round-2'
                                    ]
                                ],
                                'name' => $arFilter['id'],
                                'id' => $arFilter['id'],
                                'type' => $arFilter['id'] == 'MESSAGE' ? 'hidden' : 'text',
                                'pattern' => $arFilter['id'] != 'MESSAGE' ? '\d+' : null,
                                'value' => isset($arGet[$arFilter['id']]) && !empty($arGet[$arFilter['id']]) ? $arGet[$arFilter['id']] : ''
                            ]) ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php unset($arFilter) ?>
                <?php if (!empty($arParams['FILTER_USER_FIELD'])) { ?>
                    <div class="intec-grid-item-auto intec-grid-item-425-1">
                        <label class="support-ticket-list-filter-form-label" for="<?=$arParams['FILTER_USER_FIELD']?>">
                            <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_ORDER_ID') ?>
                        </label>
                        <?= Html::tag('input', '', [
                            'class' => [
                                'support-ticket-list-filter-form-input',
                                'intec-ui' => [
                                    '',
                                    'control-input',
                                    'view-1',
                                    'mod-round-2'
                                ]
                            ],
                            'name' => $arParams['FILTER_USER_FIELD'],
                            'id' => $arParams['FILTER_USER_FIELD'],
                            'type' => 'text',
                            'value' => isset($arGet[$arParams['FILTER_USER_FIELD']]) && !empty($arGet[$arParams['FILTER_USER_FIELD']]) ? $arGet[$arParams['FILTER_USER_FIELD']] : ''
                        ]) ?>
                    </div>
                <?php } ?>
                <div class="intec-grid-item-auto">
                    <button type="submit" class="support-ticket-list-filter-form-button-apply intec-ui intec-ui-control-button intec-ui-scheme-current">
                        <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_APPLY') ?>
                    </button>
                </div>
                <div class="intec-grid-item-auto">
                    <button class="support-ticket-list-filter-form-button-clear intec-cl-text-hover intec-ui intec-ui-control-button intec-ui-scheme-current" data-role="clear">
                        <?= Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_FILTER_CLEAR') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-search-page c-search-page-default">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="search-page-wrapper">
                <?php if ($arParams['SHOW_TAGS_CLOUD'] === 'Y') {
                    $arCloudParams = [
                        'SEARCH' => $arResult['REQUEST']['~QUERY'],
                        'TAGS' => $arResult['REQUEST']['~TAGS'],
                        'CHECK_DATES' => $arParams['CHECK_DATES'],
                        'arrFILTER' => $arParams['arrFILTER'],
                        'SORT' => $arParams['TAGS_SORT'],
                        'PAGE_ELEMENTS' => $arParams['TAGS_PAGE_ELEMENTS'],
                        'PERIOD' => $arParams['TAGS_PERIOD'],
                        'URL_SEARCH' => $arParams['TAGS_URL_SEARCH'],
                        'TAGS_INHERIT' => $arParams['TAGS_INHERIT'],
                        'FONT_MAX' => $arParams['FONT_MAX'],
                        'FONT_MIN' => $arParams['FONT_MIN'],
                        'COLOR_NEW' => $arParams['COLOR_NEW'],
                        'COLOR_OLD' => $arParams['COLOR_OLD'],
                        'PERIOD_NEW_TAGS' => $arParams['PERIOD_NEW_TAGS'],
                        'SHOW_CHAIN' => 'N',
                        'COLOR_TYPE' => $arParams['COLOR_TYPE'],
                        'WIDTH' => $arParams['WIDTH'],
                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'RESTART' => $arParams['RESTART'],
                    ];

                    if (is_array($arCloudParams['arrFILTER'])) {
                        foreach ($arCloudParams['arrFILTER'] as $strFILTER) {
                            if ($strFILTER == 'main') {
                                $arCloudParams['arrFILTER_main'] = $arParams['arrFILTER_main'];
                            } else if ($strFILTER == 'forum' && IsModuleInstalled('forum')) {
                                $arCloudParams['arrFILTER_forum'] = $arParams['arrFILTER_forum'];
                            } else if (strpos($strFILTER,'iblock_') === 0) {
                                foreach ($arParams['arrFILTER_'.$strFILTER] as $strIBlock)
                                    $arCloudParams['arrFILTER_'.$strFILTER] = $arParams['arrFILTER_'.$strFILTER];
                            } else if ($strFILTER == 'blog') {
                                $arCloudParams['arrFILTER_blog'] = $arParams['arrFILTER_blog'];
                            } else if ($strFILTER == 'socialnetwork') {
                                $arCloudParams['arrFILTER_socialnetwork'] = $arParams['arrFILTER_socialnetwork'];
                            }
                        }
                    }

                    $APPLICATION->IncludeComponent('bitrix:search.tags.cloud', '.default', $arCloudParams, $component, ['HIDE_ICONS' => 'Y']);
                } ?>
                <form action="" method="get">
                    <input type="hidden" name="tags" value="<?= $arResult['REQUEST']['TAGS']?>" />
                    <input type="hidden" name="how" value="<?= $arResult['REQUEST']['HOW'] == 'd' ? 'd' : 'r' ?>" />
                    <table class="search-page-form">
                        <tbody>
                            <tr>
                                <td class="search-page-form-input">
                                    <?php if ($arParams['USE_SUGGEST'] === 'Y') {
                                        if(strlen($arResult['REQUEST']['~QUERY']) && is_object($arResult['NAV_RESULT'])) {
                                            $arResult['FILTER_MD5'] = $arResult['NAV_RESULT']->GetFilterMD5();
                                            $obSearchSuggest = new CSearchSuggest($arResult['FILTER_MD5'], $arResult['REQUEST']['~QUERY']);
                                            $obSearchSuggest->SetResultCount($arResult['NAV_RESULT']->NavRecordCount);
                                        } ?>
                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:search.suggest.input',
                                            '',
                                            [
                                                'NAME' => "q",
                                                'VALUE' => $arResult['REQUEST']['~QUERY'],
                                                'INPUT_SIZE' => -1,
                                                'DROPDOWN_SIZE' => 10,
                                                'FILTER_MD5' => $arResult['FILTER_MD5'],
                                            ],
                                            $component, ['HIDE_ICONS' => 'Y']
                                        ) ?>
                                    <?php } else { ?>
                                        <input class="search-page-query intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-2 intec-ui-size-1" type="text" placeholder="<?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_PLACEHOLDER') ?>" name="q" value="<?= $arResult['REQUEST']['QUERY'] ?>" />
                                    <?php } ?>
                                </td>
                                <td class="search-page-form-button">
                                    <button class="search-page-button search-form-button intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current intec-ui-size-1" type="submit" value="<?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_GO') ?>">
                                        <span class="intec-ui-part-icon">
                                            <i class="far fa-search"></i>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--noindex-->
                    <div class="search-page-advanced">
                        <div class="search-page-advanced-result">
                            <?php if (is_object($arResult['NAV_RESULT'])) { ?>
                                <div class="search-page-result">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_FOUND') ?>: <?= $arResult['NAV_RESULT']->SelectedRowsCount() ?>
                                </div>
                            <?php } ?>
                            <?php
                            $arWhere = [];

                            if (!empty($arResult['TAGS_CHAIN'])) {
                                $tags_chain = '';

                                foreach ($arResult['TAGS_CHAIN'] as $arTag) {
                                    $tags_chain .= ' '.$arTag['TAG_NAME'].' [<a href="'.$arTag['TAG_WITHOUT'].'" class="search-page-tags-link" rel="nofollow">x</a>]';
                                }

                                $arWhere[] = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_TAGS').' &mdash; '.$tags_chain;
                            }

                            if ($arParams['SHOW_WHERE']) {
                                $where = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_EVERYWHERE');

                                foreach ($arResult['DROPDOWN'] as $key => $value)
                                    if ($arResult['REQUEST']['WHERE'] == $key)
                                        $where = $value;

                                $arWhere[] = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_WHERE').' &mdash; '.$where;
                            }

                            if ($arParams['SHOW_WHEN']) {
                                if ($arResult['REQUEST']['FROM'] && $arResult['REQUEST']['TO'])
                                    $when = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_DATES_FROM_TO', ['#FROM#' => $arResult['REQUEST']['FROM'], '#TO#' => $arResult['REQUEST']['TO']]);
                                else if ($arResult['REQUEST']['FROM'])
                                    $when = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_DATES_FROM', ['#FROM#' => $arResult['REQUEST']['FROM']]);
                                else if ($arResult['REQUEST']['TO'])
                                    $when = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_DATES_TO', ['#TO#' => $arResult['REQUEST']['TO']]);
                                else
                                    $when = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_DATES_ALL');

                                $arWhere[] = Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_WHEN').' &mdash; '.$when;
                            }

                            if (count($arWhere))
                                echo Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_WHERE_LABEL').': '.implode(', ', $arWhere);
                            ?>
                        </div>
                    </div>
                        <?php if ($arParams['SHOW_WHERE'] || $arParams['SHOW_WHEN']) { ?>
                            <script>
                                function switch_search_params() {
                                    var sp = document.getElementById('search_params');

                                    if(sp.style.display == 'none') {
                                        disable_search_input(sp, false);
                                        sp.style.display = 'block'
                                    } else {
                                        disable_search_input(sp, true);
                                        sp.style.display = 'none';
                                    }

                                    return false;
                                }

                                function disable_search_input(obj, flag) {
                                    var n = obj.childNodes.length;
                                    for(var j=0; j<n; j++) {
                                        var child = obj.childNodes[j];

                                        if(child.type) {
                                            switch(child.type.toLowerCase()) {
                                                case 'select-one':
                                                case 'file':
                                                case 'text':
                                                case 'textarea':
                                                case 'hidden':
                                                case 'radio':
                                                case 'checkbox':
                                                case 'select-multiple':
                                                    child.disabled = flag;
                                                    break;
                                                default:
                                                    break;
                                            }
                                        }

                                        disable_search_input(child, flag);
                                    }
                                }
                            </script>
                            <div class="search-page-advanced-filter">
                                <a href="#" onclick="return switch_search_params()">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ADVANCED_SEARCH') ?>
                                </a>
                            </div>
                            <div id="search-page_params" class="search-page-filter" style="display:<?echo $arResult['REQUEST']['FROM'] || $arResult['REQUEST']['TO'] || $arResult['REQUEST']['WHERE']? 'block': 'none'?>">
                                <h2>
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ADVANCED_SEARCH')?>
                                </h2>
                                <table class="search-page-filter" cellspacing="0">
                                    <tbody>
                                        <?php if ($arParams['SHOW_WHERE']) { ?>
                                            <tr>
                                                <td class="search-page-filter-name">
                                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_WHERE') ?>
                                                </td>
                                                <td class="search-page-filter-field">
                                                    <select class="select-field" name="where">
                                                        <option value="">
                                                            <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ALL') ?>
                                                        </option>
                                                        <?php foreach ($arResult['DROPDOWN'] as $key => $value) { ?>
                                                            <option value="<?= $key ?>" <?= $arResult['REQUEST']['WHERE'] == $key ? 'selected' : '' ?>>
                                                                <?= $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <?php if ($arParams['SHOW_WHEN']) { ?>
                                            <tr>
                                                <td class="search-page-filter-name">
                                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_WHEN') ?>
                                                </td>
                                                <td class="search-page-filter-field">
                                                    <?php $APPLICATION->IncludeComponent(
                                                        'bitrix:main.calendar',
                                                        '',
                                                        [
                                                            'SHOW_INPUT' => 'Y',
                                                            'INPUT_NAME' => 'from',
                                                            'INPUT_VALUE' => $arResult['REQUEST']['~FROM'],
                                                            'INPUT_NAME_FINISH' => 'to',
                                                            'INPUT_VALUE_FINISH' =>$arResult['REQUEST']['~TO'],
                                                            'INPUT_ADDITIONAL_ATTR' => 'class="input-field" size="10"',
                                                        ],
                                                        null,
                                                        ['HIDE_ICONS' => 'Y']
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td class="search-page-filter-name">&nbsp;</td>
                                            <td class="search-page-filter-field">
                                                <input class="search-page-button" value="<?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_GO') ?>" type="submit">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    <!--/noindex-->
                </form>
                <?php if (isset($arResult['REQUEST']['ORIGINAL_QUERY'])) { ?>
                    <div class="search-page-language-guess">
                        <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_KEYBOARD_WARNING', [
                            '#query#' => '<a href="'.$arResult['ORIGINAL_QUERY_URL'].'">'.$arResult['REQUEST']['ORIGINAL_QUERY'].'</a>'
                        ])?>
                    </div>
                    <br />
                <?php } ?>
                <div class="search-page-result">
                    <?php if ($arResult['REQUEST']['QUERY'] === false && $arResult['REQUEST']['TAGS'] === false) {

                    } else if ($arResult['ERROR_CODE'] != 0) { ?>
                        <p>
                            <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ERROR') ?>
                        </p>
                        <?php ShowError($arResult['ERROR_TEXT']) ?>
                        <p>
                            <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_CORRECT_AND_CONTINUE') ?>
                        </p>
                        <br /><br />
                        <p>
                            <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_SINTAX') ?><br />
                            <b>
                                <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_LOGIC') ?>
                            </b>
                        </p>
                        <table border="0" cellpadding="5">
                            <tr>
                                <td align="center" valign="top">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_OPERATOR') ?>
                                </td>
                                <td valign="top">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_SYNONIM') ?>
                                </td>
                                <td>
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_DESCRIPTION') ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_AND') ?>
                                </td>
                                <td valign="top">and, &amp;, +</td>
                                <td>
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_AND_ALT') ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_OR') ?>
                                </td>
                                <td valign="top">or, |</td>
                                <td>
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_OR_ALT') ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_NOT') ?>
                                </td>
                                <td valign="top">not, ~</td>
                                <td>
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_NOT_ALT') ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">( )</td>
                                <td valign="top">&nbsp;</td>
                                <td>
                                    <?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_BRACKETS_ALT') ?>
                                </td>
                            </tr>
                        </table>
                    <?php } else if (count($arResult['SEARCH']) > 0) { ?>
                        <?php if ($arParams['DISPLAY_TOP_PAGER'] != "N") { ?>
                            <?= $arResult['NAV_STRING'] ?>
                        <?php } ?>
                        <?php foreach($arResult['SEARCH'] as $arItem) { ?>
                            <div class="search-page-item">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-20">
                                    <?php if (!empty($arItem['PICTURE'])) { ?>
                                        <div class="intec-grid-item-5 intec-grid-item-768-1">
                                            <a href="<?= $arItem['URL'] ?>" class="search-page-item-img intec-ui-picture">
                                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arItem['PICTURE'], [
                                                    'loading' => 'lazy',
                                                    'alt' => $arItem['TITLE_FORMATED'],
                                                    'title' => $arItem['TITLE_FORMATED'],
                                                    'data' => [
                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arItem['PICTURE'] : null
                                                    ]
                                                ]) ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="intec-grid-item">
                                        <div class="search-page-item-data">
                                            <?= $arItem['DATE_CHANGE'] ?>
                                        </div>
                                        <h4 class="search-page-item-name-h4">
                                            <a href="<?= $arItem['URL'] ?>" class="search-page-item-name intec-cl-text">
                                                <?= $arItem['TITLE_FORMATED'] ?>
                                            </a>
                                        </h4>
                                        <div class="search-page-preview">
                                            <?= $arItem['BODY_FORMATED'] ?>
                                        </div>
                                        <?php if (
                                            ($arParams['SHOW_ITEM_DATE_CHANGE'] != 'N')
                                            || ($arParams['SHOW_ITEM_PATH'] == 'Y' && $arItem['CHAIN_PATH'])
                                            || ($arParams['SHOW_ITEM_TAGS'] != 'N' && !empty($arItem['TAGS']))
                                        ) { ?>
                                            <div class="search-page-item-meta">
                                                <?php if (
                                                    $arParams['SHOW_RATING'] == 'Y'
                                                    && strlen($arItem['RATING_TYPE_ID']) > 0
                                                    && $arItem['RATING_ENTITY_ID'] > 0
                                                ) { ?>
                                                    <div class="search-page-item-rate">
                                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:rating.vote',
                                                            $arParams['RATING_TYPE'],
                                                            [
                                                                'ENTITY_TYPE_ID' => $arItem['RATING_TYPE_ID'],
                                                                'ENTITY_ID' => $arItem['RATING_ENTITY_ID'],
                                                                'OWNER_ID' => $arItem['USER_ID'],
                                                                'USER_VOTE' => $arItem['RATING_USER_VOTE_VALUE'],
                                                                'USER_HAS_VOTED' => $arItem['RATING_USER_VOTE_VALUE'] == 0? 'N': 'Y',
                                                                'TOTAL_VOTES' => $arItem['RATING_TOTAL_VOTES'],
                                                                'TOTAL_POSITIVE_VOTES' => $arItem['RATING_TOTAL_POSITIVE_VOTES'],
                                                                'TOTAL_NEGATIVE_VOTES' => $arItem['RATING_TOTAL_NEGATIVE_VOTES'],
                                                                'TOTAL_VALUE' => $arItem['RATING_TOTAL_VALUE'],
                                                                'PATH_TO_USER_PROFILE' => $arParams['~PATH_TO_USER_PROFILE'],
                                                            ],
                                                            $component,
                                                            ['HIDE_ICONS' => 'Y']
                                                        ) ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($arParams['SHOW_ITEM_TAGS'] != 'N' && !empty($arItem['TAGS'])) { ?>
                                                    <div class="search-page-item-tags">
                                                        <label><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ITEM_TAGS') ?>: </label>
                                                        <?php foreach ($arItem['TAGS'] as $tags) { ?>
                                                            <a href="<?= $tags['URL'] ?>">
                                                                <?= $tags['TAG_NAME'] ?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($arParams['SHOW_ITEM_DATE_CHANGE'] != 'N') { ?>
                                                    <div class="search-page-item-date">
                                                        <label><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_DATE_CHANGE') ?>: </label>
                                                        <span><?= $arItem['DATE_CHANGE'] ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($arParams['DISPLAY_BOTTOM_PAGER'] != 'N') { ?>
                            <?= $arResult['NAV_STRING'] ?>
                        <?php } ?>
                        <?php if ($arParams['SHOW_ORDER_BY'] != "N") { ?>
                            <div class="search-page-sorting"><label><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ORDER') ?>:</label>&nbsp;
                            <?php if ($arResult['REQUEST']['HOW'] == 'd') { ?>
                                <a href="<?= $arResult['URL'] ?>&amp;how=r"><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ORDER_BY_RANK') ?></a>&nbsp;<b><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ORDER_BY_DATE') ?></b>
                            <?php } else { ?>
                                <b><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ORDER_BY_RANK') ?></b>&nbsp;<a href="<?= $arResult['URL'] ?>&amp;how=d"><?= Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_ORDER_BY_DATE') ?></a>
                            <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php ShowNote(Loc::getMessage('C_SEARCH_PAGE_TEMPLATE_DEFAULT_TEMPLATE_NOTHING_TO_FOUND')) ?>
                        <?php if ($arVisual['BLOCK_ON_EMPTY_RESULTS']['SHOW']) { ?>
                            <?php include(__DIR__.'/parts/elements.php') ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

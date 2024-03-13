<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */
if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$arParams = ArrayHelper::merge([
    'LIST_MENU_SHOW' => 'N'
], $arParams);

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/list.php');

$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['LIST_MENU_SHOW'] === 'Y';

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news',
        'c-news-vacancies-1',
        'p-news'
    ]
]) ?>
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => 'news-content',
                'data' => [
                    'role' => !$arColumns['SHOW'] ? 'content' : null
                ]
            ]) ?>
            <?php if ($arMenu['SHOW']) { ?>
            <div class="news-content-left intec-content-left">
                <div class="news-menu">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:menu',
                        $arMenu['TEMPLATE'],
                        $arMenu['PARAMETERS'],
                        $component
                    ) ?>
                </div>
            </div>
            <div class="news-content-right intec-content-right">
                <div class="news-content-right-wrapper intec-content-right-wrapper">
                    <?php } ?>
                    <?php $APPLICATION->IncludeComponent (
                        'bitrix:news.list',
                        $arList['TEMPLATE'],
                        $arList['PARAMETERS'],
                        $component
                    );
                    ?>
                    <?php if ($arMenu['SHOW']) { ?>
                </div>
            </div>
            <div class="intec-ui-clear"></div>
        <?php } ?>
        <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

$fDrawItem = function($arSocial) use (&$arResult) {?>
    <?=Html::beginTag('a', [
        'class' => [
            'widget-panel-social-item',
            'intec-image-effect',
            'intec-grid-item-auto'
        ],
        'rel' => 'nofollow',
        'target' => '_blank',
        'href' => $arSocial['LINK']
    ]);?>
    <?=Html::tag('div',"", [
        'class' => 'widget-panel-social-item-icon',
        'data' => [
            'grey' => $arResult['SOCIAL']['GREY'],
            'social-icon' => $arSocial['CODE'],
            'social-icon-square' => $arResult['SOCIAL']['SQUARE']
        ]
    ]);?>
    <?=Html::endTag('a');?>
<?php }?>

<?php
/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */
?>
<!--noindex-->
<div class="widget-panel-social-wrap intec-grid-item-auto">
    <div class="widget-panel-social">
        <?=Html::beginTag('div', [
            'class' => [
                'widget-panel-social-items',
                'intec-grid' => [
                    '',
                    'nowrap',
                    'i-h-6',
                    'a-v-center',
                ]
            ],
            'data-role' => "items"
        ]);?>
        <?php
        $iCount = 0;
        $iLimit = 4;
        $bHidden = false;
        if (Type::isArray($arResult['SOCIAL']['ITEMS'])) {
            $arResult['SOCIAL']['ITEMS'] = array_filter($arResult['SOCIAL']['ITEMS'], function ($k) {
                return $k['SHOW'];
            });
        }
        ?>
        <?php foreach ($arResult['SOCIAL']['ITEMS'] as $sKey => $arSocial) { ?>
            <?php if (++$iCount > $iLimit) {
                $bHidden = true;
                break;
            }?>
            <div class="widget-panel-social-item-wrap intec-grid-item-auto intec-grid">
                <?php $fDrawItem($arSocial, 'widget-panel-social-item');?>
            </div>
        <?php } ?>
        <?php if ($bHidden) {?>
            <div class="widget-panel-social-item-hidden-wrap intec-grid-item-auto" data-role="more">
                <div class="widget-panel-social-item-more intec-grid-item-auto">
                    ...
                </div>
                <div class="widget-panel-social-hidden-items intec-grid intec-grid-i-6 intec-grid-o-vertical" data-role="more">
                    <?php $arHiddenSocialItems = ArrayHelper::slice($arResult['SOCIAL']['ITEMS'], $iLimit);

                    foreach ($arHiddenSocialItems as $sKey => $arSocial) {
                        $fDrawItem($arSocial);

                    }?>
                </div>
            </div>
        <?php }?>
        <?=Html::endTag('div');?>
    </div>
</div>
<?php unset($fDrawItem);?>

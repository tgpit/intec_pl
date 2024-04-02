<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var boolean $bSearchApply
 * @var array $arGet
 * @var array $arSvg
 */

?>

<div class="support-ticket-list-search<?= $bSearchApply ? ' intec-cl-border' : null ?>">
    <form action="" method="get" class="support-ticket-list-search-form" data-role="search">
        <?php if (isset($arGet['SECTION'])) { ?>
            <input type="hidden" name="SECTION" value="<?= $arGet['SECTION'] ?>">
        <?php } ?>
        <input type="hidden" name="clear_filter" value="Y">
        <div class="intec-grid intec-grid-i-h-5 intec-grid-nowrap intec-grid-a-v-center">
            <div class="intec-grid-item">
                <input type="text" class="support-ticket-list-search-form-input" <?= $bSearchApply ? 'disabled=""' : null ?> name="MESSAGE" placeholder="<?= $bSearchApply ? $arGet['MESSAGE'] : Loc::getMessage('C_SUPPORT_TICKET_LIST_TEMPLATE_1_TEMPLATE_SEARCH') ?>">
            </div>
            <div class="intec-grid-item-auto">
                <?= Html::tag('button', $bSearchApply ? $arSvg['SEARCH_RESET'] : $arSvg['SEARCH'], [
                    'class' => 'support-ticket-list-search-form-button',
                    'type' => 'submit'
                ]) ?>
            </div>
        </div>
    </form>
</div>

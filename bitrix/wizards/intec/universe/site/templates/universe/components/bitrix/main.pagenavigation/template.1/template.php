<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$component = $this->getComponent();

?>

<div class="ns-bitrix c-main-pagenavigation c-main-pagenavigation-template-1">
    <div class="main-pagenavigation-items">
        <div class="main-pagenavigation-items-wrapper">
        <?php if ($arResult['REVERSED_PAGES'] === true) { ?>
            <?php if ($arResult['CURRENT_PAGE'] < $arResult['PAGE_COUNT']) { ?>
                <?php if (($arResult['CURRENT_PAGE'] + 1) == $arResult['PAGE_COUNT']) { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-previous">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($arResult['URL']) ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-previous">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult['CURRENT_PAGE'] + 1)) ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </div>
                <?php } ?>
                    <div class="main-pagenavigation-item">
                        <a class="main-pagenavigation-item-wrapper" href="<?=htmlspecialcharsbx($arResult["URL"])?>">1</a>
                    </div>
            <?php } else { ?>
                <div class="main-pagenavigation-item main-pagenavigation-item-previous main-pagenavigation-item-disabled">
                    <div class="main-pagenavigation-item-wrapper">
                        <i class="fas fa-angle-left"></i>
                    </div>
                </div>
                <div class="main-pagenavigation-item main-pagenavigation-item-active">
                    <div class="main-pagenavigation-item-wrapper intec-cl-background">1</div>
                </div>
            <?php } ?>
            <?php $page = $arResult['START_PAGE'] - 1 ?>
            <?php while ($page >= $arResult['END_PAGE'] + 1) { ?>
                <?php if ($page == $arResult['CURRENT_PAGE']) { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-active">
                        <div class="main-pagenavigation-item-wrapper intec-cl-background"><?= ($arResult['PAGE_COUNT'] - $page + 1) ?></div>
                    </div>
                <?php } else { ?>
                    <div class="main-pagenavigation-item">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($page)) ?>">
                            <?= ($arResult['PAGE_COUNT'] - $page + 1) ?>
                        </a>
                    </div>
                <?php } ?>
                <?php $page-- ?>
            <?php } ?>
            <?php if ($arResult['CURRENT_PAGE'] > 1) { ?>
                <?php if ($arResult['PAGE_COUNT'] > 1) { ?>
                    <div class="main-pagenavigation-item">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate(1)) ?>">
                            <?= $arResult['PAGE_COUNT'] ?>
                        </a>
                    </div>
                <?php } ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-next">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult['CURRENT_PAGE'] - 1)) ?>">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </div>
            <?php } else { ?>
                <?php if ($arResult['PAGE_COUNT'] > 1) { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-active">
                        <div class="main-pagenavigation-item-wrapper intec-cl-background"><?= $arResult['PAGE_COUNT'] ?></div>
                    </div>
                <?php } ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-next main-pagenavigation-item-disabled">
                        <div class="main-pagenavigation-item-wrapper">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
            <?php } ?>
        <?php } else { ?>
            <?php if ($arResult['CURRENT_PAGE'] > 1) { ?>
                <?php if ($arResult['CURRENT_PAGE'] > 2) { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-previous">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult['CURRENT_PAGE'] - 1)) ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-previous">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($arResult['URL']) ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </div>
                <?php } ?>
                    <div class="main-pagenavigation-item">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($arResult['URL']) ?>">1</a>
                    </div>
            <?php } else { ?>
                <div class="main-pagenavigation-item main-pagenavigation-item-previous main-pagenavigation-item-disabled">
                    <div class="main-pagenavigation-item-wrapper">
                        <i class="fas fa-angle-left"></i>
                    </div>
                </div>
                <div class="main-pagenavigation-item main-pagenavigation-item-active">
                    <div class="main-pagenavigation-item-wrapper intec-cl-background">1</div>
                </div>
            <?php } ?>
            <?php $page = $arResult['START_PAGE'] + 1 ?>
            <?php while ($page <= $arResult['END_PAGE'] - 1) { ?>
                <?php if ($page == $arResult['CURRENT_PAGE']) { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-active">
                        <div class="main-pagenavigation-item-wrapper intec-cl-background"><?= $page ?></div>
                    </div>
                <?php } else { ?>
                    <div class="main-pagenavigation-item">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($page)) ?>">
                            <?= $page ?>
                        </a>
                    </div>
                <?php } ?>
                <?php $page++ ?>
            <?php } ?>
            <?php if ($arResult['CURRENT_PAGE'] < $arResult['PAGE_COUNT']) { ?>
                <?php if ($arResult['PAGE_COUNT'] > 1) { ?>
                    <div class="main-pagenavigation-item">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult['PAGE_COUNT'])) ?>">
                            <?= $arResult['PAGE_COUNT'] ?>
                        </a>
                    </div>
                <?php } ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-next">
                        <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult['CURRENT_PAGE'] + 1)) ?>">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </div>
            <?php } else { ?>
                <?php if ($arResult["PAGE_COUNT"] > 1) { ?>
                    <div class="main-pagenavigation-item main-pagenavigation-item-active">
                        <div class="main-pagenavigation-item-wrapper intec-cl-background"><?= $arResult['PAGE_COUNT'] ?></div>
                    </div>
                <?php } ?>
                <div class="main-pagenavigation-item main-pagenavigation-item-next main-pagenavigation-item-disabled">
                    <div class="main-pagenavigation-item-wrapper">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if ($arResult['SHOW_ALL']) { ?>
            <?php if ($arResult['ALL_RECORDS']) { ?>
                <div class="main-pagenavigation-item main-pagenavigation-item-all">
                    <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($arResult['URL']) ?>" rel="nofollow">
                        <?= Loc::getMessage('C_MAIN_PAGENAVIGATION_TEMPLATE_1_PAGES') ?>
                    </a>
                </div>
            <?php } else { ?>
                <div class="main-pagenavigation-item main-pagenavigation-item-all">
                    <a class="main-pagenavigation-item-wrapper" href="<?= htmlspecialcharsbx($component->replaceUrlTemplate('all')) ?>" rel="nofollow">
                        <?= Loc::getMessage('C_MAIN_PAGENAVIGATION_TEMPLATE_1_ALL') ?>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>
        </div>
	</div>
</div>

<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CBitrixComponentTemplate $this
 */

?>
<?php return function (&$arSections) use (&$arVisual, &$sTemplateId) {

    $vRenderChildren = include(__DIR__.'/children.php');

?>
    <div class="widget-items" data-role="services.content">
        <?php $bFirst = true ?>
        <?php foreach ($arSections as $arSection) {

            $sId = $sTemplateId.'_'.$arSection['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arSection['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

            $arPicture = [
                'TYPE' => 'picture',
                'SOURCE' => null,
                'ALT' => null,
                'TITLE' => null
            ];

            if (!empty($arSection['PICTURE'])) {
                if ($arSection['PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                    $arPicture['TYPE'] = 'svg';
                    $arPicture['SOURCE'] = $arSection['PICTURE']['SRC'];
                } else {
                    $arPicture['SOURCE'] = CFile::ResizeImageGet($arSection['PICTURE'], [
                            'width' => 800,
                            'height' => 800
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                    );

                    if (!empty($arPicture['SOURCE']))
                        $arPicture['SOURCE'] = $arPicture['SOURCE']['src'];
                    else
                        $arPicture['SOURCE'] = null;
                }
            }

            if (empty($arPicture['SOURCE'])) {
                $arPicture['TYPE'] = 'picture';
                $arPicture['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
            } else {
                $arPicture['ALT'] = $arSection['PICTURE']['ALT'];
                $arPicture['TITLE'] = $arSection['PICTURE']['TITLE'];
            }

        ?>
            <?= Html::beginTag('div', [
                'class' => 'widget-item',
                'data' => [
                    'role' => 'services.content.item',
                    'id' => $arSection['ID'],
                    'active' => $bFirst ? 'true' : 'false'
                ]
            ]) ?>
                <div class="widget-item-container" id="<?= $sAreaId ?>">
                    <div class="widget-item-content">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid' => [
                                    '',
                                    'wrap',
                                    'a-v-start',
                                    'i-h-20',
                                    'i-v-12'
                                ]
                            ]
                        ]) ?>
                            <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                <div class="intec-grid-item-auto intec-grid-item-768-1">
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'widget-item-picture' => true,
                                            'intec-ui-picture' => $arPicture['TYPE'] === 'svg',
                                            'intec-cl-svg' => $arPicture['TYPE'] === 'svg',
                                            'intec-image-effect' => true
                                        ], true),
                                        'data-size' => $arPicture['TYPE'] === 'svg' ? 'svg' : $arVisual['PICTURE']['SIZE']
                                    ]) ?>
                                        <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                            <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                                        <?php } else { ?>
                                            <?= Html::tag('div', null, [
                                                'class' => 'widget-item-picture-content',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                                ],
                                                'style' => [
                                                    'background-image' => 'url(\''.(
                                                        $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPicture['SOURCE']
                                                    ).'\')'
                                                ]
                                            ]) ?>
                                        <?php } ?>
                                    <?= Html::endTag('div') ?>
                                </div>
                            <?php } ?>
                            <div class="intec-grid-item intec-grid-item-600-1">
                                <div class="widget-item-name">
                                    <?= $arSection['NAME'] ?>
                                </div>
                                <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arSection['DESCRIPTION'])) { ?>
                                    <div class="widget-item-description">
                                        <?= $arSection['DESCRIPTION'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['CHILDREN']['SHOW'] &&  !empty($arSection['ITEMS'])) { ?>
                                    <div class="widget-item-children">
                                        <?php $vRenderChildren($arSection['ITEMS']) ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    </div>
                    <?php if ($arVisual['LINK']['USE']) { ?>
                        <?= Html::tag('a', Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_BUTTON_MORE'), [
                            'class' => [
                                'widget-item-button',
                                'intec-cl-background-hover',
                                'intec-cl-border-hover'
                            ],
                            'href' => $arSection['SECTION_PAGE_URL'],
                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                        ]) ?>
                    <?php } ?>
                </div>
            <?= Html::endTag('div') ?>
            <?php if ($bFirst) $bFirst = false ?>
        <?php } ?>
    </div>
<?php } ?>
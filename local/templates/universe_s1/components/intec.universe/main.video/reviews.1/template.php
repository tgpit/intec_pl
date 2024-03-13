<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$sId = $sTemplateId.'_'.$arResult['ITEM']['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['ITEM']['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['ITEM']['DELETE_LINK']);

$arVisual = ArrayHelper::getValue($arResult, 'VISUAL');
$arCodes = ArrayHelper::getValue($arResult, 'PROPERTY_CODES');
$arLink = ArrayHelper::getValue($arResult, ['ITEM', 'PROPERTIES', $arCodes['LINK'], 'VALUE']);

$sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
if (!empty($arResult['PICTURE']['SRC'])) {
    $sPicture = $arResult['PICTURE']['SRC'];
}

$arSvg = [
    'VIDEO' => FileHelper::getFileData(__DIR__.'/svg/video.play.svg')
];

?>
<?php if ($arVisual['TEMPLATE_SHOW']) { ?>
    <div class="widget c-video c-video-template-1" id="<?= $sTemplateId ?>">
        <div class="widget-element-wrap">
            <?= Html::beginTag('div', [ /** Главный тег элемента */
                'class' => 'widget-element',
                'id' => $sAreaId,
                'data-src' => $arLink,
                'data-parallax-ratio' => $arVisual['PARALLAX']['USE'] ? $arVisual['PARALLAX']['RATIO'] : null,
                'style' => [
                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null,
                    'height' => $arVisual['HEIGHT']
                ],
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ],
            ]) ?>
            <div class="widget-video-button-wrapper">
                <?= $arSvg['VIDEO'] ?>
            </div>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            <?php if (!defined('EDITOR')) { ?>
                $('.widget-element-wrap', data.nodes).lightGallery();
            <?php } ?>
        }, {
            'name': '[Component] intec.universe:main.video (reviews.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } else { ?>
    <div style="text-align: center; color: red; padding: 30px;">
        <?= Loc::getMessage('C_VIDEO_TEMP1_TEMPLATE_SHOW') ?>
    </div>
<?php } ?>
<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$item) use (&$arVisual) {

    if (empty($arItem['DATA']['ANSWER']['NAME']))
        $arItem['DATA']['ANSWER']['NAME'] = Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_TEMPLATE_ANSWER_NAME_DEFAULT');

    if (empty($arItem['DATA']['ANSWER']['POSITION']['VALUE']))
        $arItem['DATA']['ANSWER']['POSITION']['VALUE'] = Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_TEMPLATE_ANSWER_POSITION_DEFAULT');

?>
    <div class="news-list-item-answer">
        <div class="news-list-item-answer-container">
            <div class="news-list-item-block">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-8 intec-grid-400-wrap">
                    <?php if ($item['DATA']['ANSWER']['PICTURE']['SHOW']) { ?>
                        <div class="intec-grid-item-auto intec-grid-item-400-1">
                            <div class="news-list-item-answer-portrait intec-ui-picture">
                                <?php $sPicture = null;

                                if (Type::isArray($item['DATA']['ANSWER']['PICTURE'])) {
                                    $sPicture = CFile::ResizeImageGet($item['DATA']['ANSWER']['PICTURE']['VALUE'], [
                                        'width' => 64,
                                        'height' => 64
                                    ], BX_RESIZE_IMAGE_EXACT);

                                    if (!empty($sPicture))
                                        $sPicture = $sPicture['src'];
                                } else {
                                    $sPicture = $item['DATA']['ANSWER']['PICTURE']['VALUE'];
                                }

                                if (empty($sPicture))
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                ?>
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                    'class' => 'intec-image-effect',
                                    'alt ' => $item['DATA']['ANSWER']['NAME'],
                                    'title' => $item['DATA']['ANSWER']['NAME'],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item-400-1">
                        <div class="news-list-item-answer-name">
                            <?= $item['DATA']['ANSWER']['NAME'] ?>
                        </div>
                        <?php if ($item['DATA']['ANSWER']['POSITION']['SHOW']) { ?>
                            <div class="news-list-item-answer-information">
                                <?= $item['DATA']['ANSWER']['POSITION']['VALUE'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="news-list-item-answer-text">
                <?= $item['DATA']['ANSWER']['TEXT'] ?>
            </div>
        </div>
    </div>
<?php };
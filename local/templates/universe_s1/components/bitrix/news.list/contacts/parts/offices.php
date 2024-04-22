<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<div class="contacts-offices">
    <?php if ($arResult['TITLE']['SHOW']) { ?>
        <div class="contacts-title">
            <?= $arResult['TITLE']['TEXT'] ?>
        </div>
    <?php } ?>
    <?php if ($arResult['DESCRIPTION']['SHOW']) { ?>
        <div class="contacts-description">
            <?= $arResult['DESCRIPTION']['TEXT'] ?>
			<? 
            function getSubDomain($data) {	
				$tmp = explode('.', $data);
				$tmp = array_slice($tmp, 0, -2);
				$str = implode(".", $tmp);
				if (empty($str)) {$str = 'moskva';}
				return $str;
			}
			$host = trim(getSubDomain($_SERVER['HTTP_HOST']));
			echo $host;
			?>
        </div>
    <?php } ?>
    <div class="contacts-sections">
        <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
            <?// if (count($arSection['ITEMS']) <= 0) continue; ?>
			<?// if (!(trim($arSection["CODE"]) == $host)) continue; ?>
		<? if (trim($arSection["CODE"])=='ekaterenburg'){
			//		var_dump($arResult);
				}
         ?>
            <div class="contacts-section">
                <div class="contacts-section-title">
                   <?= $arSection['NAME'] ?>
					<?= $arSection["CODE"] ?>
                </div>
                <div class="contacts-offices-list">
                    <div class="contacts-offices-list-wrapper">
                        <?php foreach ($arSection['ITEMS'] as $arItem) { ?>
                        <?php //echo $arItem['CODE'];
                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sTag = 'div';

                            if (!empty($arItem['DATA']['LINK']))
                                $sTag = 'a';

                            $sImage = $arItem['PREVIEW_PICTURE'];

                            if (empty($sImage))
                                $sImage = $arItem['DETAIL_PICTURE'];

                            if (!empty($sImage)) {
                                $sImage = CFile::ResizeImageGet($sImage, [
                                    'width' => 360,
                                    'height' => 245
                                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                if (!empty($sImage))
                                    $sImage = $sImage['src'];
                            }

                            if (empty($sImage))
                                $sImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        ?>
                            <div class="contacts-office">
                                <div class="contacts-office-wrapper" id="<?= $sAreaId ?>">
                                    <div class="intec-grid intec-grid-wrap">
                                        <div class="intec-grid-item-auto">
                                            <?= Html::tag($sTag, null, [
                                                'href' => $arItem['DATA']['LINK'],
                                                'class' => 'contacts-image',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                                                ],
                                                'style' => [
                                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sImage.'\')' : null
                                                ]
                                            ]) ?>
                                        </div>
                                        <div class="intec-grid-item">
                                            <div class="contacts-information">
                                                <div class="intec-grid intec-grid-wrap intec-grid-i-15">
                                                    <?php if (!empty($arItem['DATA']['ADDRESS'])) { ?>
                                                        <div class="intec-grid-item intec-grid-item-700-1">
                                                            <div class="contacts-information-section contacts-address">
                                                                <div class="contacts-information-title intec-cl-svg-path-stroke">
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.41797 6.69667V6.582C3.41797 4.05133 5.4693 2 7.99997 2V2C10.5306 2 12.582 4.05133 12.582 6.582V6.69667C12.582 9.004 9.66064 12.4773 8.4833 13.784C8.22397 14.072 7.77597 14.072 7.51664 13.784C6.3393 12.4773 3.41797 9.004 3.41797 6.69667Z" stroke="#0065FF" stroke-width="1.4468" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        <path d="M6.66669 6.63666C6.66669 7.37332 7.26335 7.96999 8.00002 7.96999V7.96999C8.73669 7.96999 9.33335 7.37332 9.33335 6.63666V6.61199C9.33335 5.87532 8.73669 5.27866 8.00002 5.27866V5.27866C7.26335 5.27866 6.66669 5.87532 6.66669 6.61199" stroke="#0065FF" stroke-width="1.4468" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                    <div class="contacts-information-text">
                                                                        <?= Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_OFFICES_ADDRESS') ?>:
                                                                    </div>
                                                                </div>
                                                                <div class="contacts-information-content">
                                                                    <?= Html::tag($sTag, $arItem['DATA']['ADDRESS'], [
                                                                        'class' => $sTag == 'a' ? 'intec-cl-text-hover' : null,
                                                                        'href' => $arItem['DATA']['LINK']
                                                                    ]) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($arItem['DATA']['WORK_TIME'])) { ?>
                                                        <div class="intec-grid-item intec-grid-item-700-1">
                                                            <div class="contacts-information-section contacts-work-time">
                                                                <div class="contacts-information-title">
                                                                    <i class="period-icon glyph-icon-clock intec-cl-text icon-contacts"></i>
                                                                    <div class="contacts-information-text">
                                                                        <?= Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_OFFICES_WORK_TIME') ?>:
                                                                    </div>
                                                                </div>
                                                                <div class="contacts-information-content">
                                                                    <?php foreach ($arItem['DATA']['WORK_TIME'] as $arValue) { ?>
                                                                        <div class="contacts-work-time">
                                                                            <?= !empty($arValue['RANGE']) ? $arValue['RANGE'].' '.$arValue['TIME'] : $arValue['TIME'] ?>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($arItem['DATA']['EMAIL']) || !empty($arItem['DATA']['PHONE'])) { ?>
                                                        <div class="intec-grid-item intec-grid-item-700-1">
                                                            <div class="contacts-information-section contacts-contacts">
                                                                <div class="contacts-information-title">
                                                                    <i class="glyph-icon-mail intec-cl-text icon-contacts"></i>
                                                                    <div class="contacts-information-text">
                                                                        <?= Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_OFFICES_CONTACTS') ?>:
                                                                    </div>
                                                                </div>
                                                                <div class="contacts-information-content">
                                                                    <?php if (!empty($arItem['DATA']['PHONE'])) { ?>
                                                                        <div class="contacts-phone">


													<? if (strpos($arItem['DATA']['PHONE']['DISPLAY'], '/') > 0) {
															$phonev = explode("/", $arItem['DATA']['PHONE']['VALUE']);
															$phones = explode("/", $arItem['DATA']['PHONE']['DISPLAY']);
															$n = count($phones);
															for ($i = 0; $i < $n; $i++) {
																$val = substr($phonev[$i],0,12);
																$vals= substr($phones[$i],0,18);
	                                                            echo Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_OFFICES_PHONE')." :";
				                                                echo '<a class="tel widget-part-item-text" href="tel:'.$val.'">';
                			                                    echo '<span class="value">';
															    echo $vals;
																echo '</span>';
				                                                echo '</a><br>';
															}
														}else {
                                                            echo Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_OFFICES_PHONE')." :";
															$val = substr($arItem['DATA']['PHONE']['VALUE'],0,12);
			                                                echo '<a class="tel widget-part-item-text" href="tel:'.$val.'">';
               			                                    echo '<span class="value">';
													   		echo $arItem['DATA']['PHONE']['DISPLAY'];
															echo '</span>';
			                                                echo '</a>';
														} ?>




																				<!--
                                                                            <a class="intec-cl-text-hover" href="tel:<?= $arItem['DATA']['PHONE']['VALUE'] ?>">
                                                                                <?= $arItem['DATA']['PHONE']['DISPLAY'] ?>
                                                                            </a>    -->

                                                                        </div>
                                                                    <?php } ?>
                                                                    <?php if (!empty($arItem['DATA']['EMAIL'])) { ?>
                                                                        <div class="contacts-email">
                                                                            <a href="mailto:<?= $arItem['DATA']['EMAIL'] ?>" class="contacts-email intec-cl-text-hover">
                                                                                <?= $arItem['DATA']['EMAIL'] ?>
                                                                            </a>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($arResult['MAP']['SHOW'] && !empty($arItem['DATA']['MAP'])) { ?>
                                            <div class="intec-grid-item-auto intec-grid-item-700-1">
                                                <?= Html::tag('a', Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_OFFICES_SHOW_ON_MAP'), [
                                                    'class' => Html::cssClassFromArray([
                                                        'contacts-information-on-map',
                                                        'intec-cl' => [
                                                            'text',
                                                            'text-light-hover',
                                                            'border-light-hover'
                                                        ]
                                                    ]),
                                                    'href' => '#'.$sTemplateId.'_map',
                                                    'data' => [
                                                        'latitude' => $arItem['DATA']['MAP']['LATITUDE'],
                                                        'longitude' => $arItem['DATA']['MAP']['LONGITUDE']
                                                    ]
                                                ]) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
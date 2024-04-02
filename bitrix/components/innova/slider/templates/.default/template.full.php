<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<?  if(!empty($arResult['ITEMS'])) : ?>
<? if(empty($arParams['HIDE_TEXT'])) $arParams['HIDE_TEXT'] = 'N' ?>
<? $id = 'innova_slider'.$this->randString(); ?>
<div class="innova_slider" id="<?=$id?>">
	<? foreach($arResult['ITEMS'] as $slide) : ?>
		<div class="innova_slider_item">
			<? if(!empty($slide['PREVIEW_PICTURE'])) $arFile = CFile::GetFileArray($slide['PREVIEW_PICTURE']);?>
			<div class="innova_slide_block" data-width="<?=((!empty($slide['PREVIEW_PICTURE'])) ? $arFile['WIDTH'] : '1' )?>" data-height="<?=((!empty($slide['PREVIEW_PICTURE'])) ? $arFile['HEIGHT'] : '1' )?>" style="<? if(!empty($slide['PREVIEW_PICTURE'])) : ?>background-image: url(<?=CFile::GetPath($slide['PREVIEW_PICTURE'])?>); <? endif; ?><?=((!empty($arParams['STRETCH_TYPE']) and $arParams['STRETCH_TYPE'] == 2) ? '    background-size: cover !important;' : '')?>">
				<? if(($arParams['HIDE_TEXT'] == 'Y' or $slide['PROPERTIES']['HIDE_TEXT']['VALUE'] == 'Y') and !empty($slide['PROPERTIES']['LINK']['VALUE'])) : ?>
				<a href="<?=((!empty($slide['PROPERTIES']['LINK']['VALUE'])) ? $slide['PROPERTIES']['LINK']['VALUE'] : '#')?>" class="innova_slider_whole_slider_link">
				<? endif; ?>
				<div class="innova_slider_inner" 
					<?
						if($slide['PROPERTIES']['TEXT_ALIGN']['VALUE'] == 'right') {
							echo 'style="align-items: flex-end;"';
						} elseif($slide['PROPERTIES']['TEXT_ALIGN']['VALUE'] == 'left') {
							echo 'style="align-items: flex-start;"';
						} elseif($slide['PROPERTIES']['TEXT_ALIGN']['VALUE'] == 'center') {
							echo 'style="align-items: center;"';
						} else {
							echo 'style="align-items: '.((!empty($arParams['TEXT_ALIGN'])) ? $arParams['TEXT_ALIGN'] : 'flex-start').';"';
						}
					?>
				>
					<div class="innova_slide_innerH1" style="<?=((!empty($slide['PROPERTIES']['TITLE_COLOR']['VALUE'])) ? 'color: '.$slide['PROPERTIES']['TITLE_COLOR']['VALUE'].' !important; ' : '')?>
					<?
						if($slide['PROPERTIES']['TEXT_ALIGN']['VALUE'] == 'right' or $arParams['TEXT_ALIGN'] == 'flex-end') {
							echo 'text-align: right; ';
						} elseif($slide['PROPERTIES']['TEXT_ALIGN']['VALUE'] == 'left' or $arParams['TEXT_ALIGN'] == 'flex-start') {
							echo 'text-align: left; ';
						} elseif($slide['PROPERTIES']['TEXT_ALIGN']['VALUE'] == 'center' or $arParams['TEXT_ALIGN'] == 'center') {
							echo 'text-align: center; ';
						} else {
							echo 'text-align: center; ';
						}
					?>	
					">
						<? if($arParams['HIDE_TEXT'] != 'Y' and $slide['PROPERTIES']['HIDE_TEXT']['VALUE'] != 'Y') : ?>
							<?=$slide['NAME']?>
						<? endif; ?>
					</div>
					<p class="innova_slide_innerP" style="<?=((!empty($slide['PROPERTIES']['DESCRIPTION_COLOR']['VALUE'])) ? 'color: '.$slide['PROPERTIES']['DESCRIPTION_COLOR']['VALUE'].' !important;' : '')?><?=((empty($slide['PREVIEW_TEXT'])) ? ' display: none !important' : '')?>">
						<? if($arParams['HIDE_TEXT'] != 'Y' and $slide['PROPERTIES']['HIDE_TEXT']['VALUE'] != 'Y') : ?>
							<?=$slide['PREVIEW_TEXT']?>
						<? endif; ?>
					</p>
					<?if(!empty($slide['PROPERTIES']['LINK']['VALUE']) and $arParams['HIDE_TEXT'] != 'Y' and $slide['PROPERTIES']['HIDE_TEXT']['VALUE'] != 'Y') :?>
						<a class="innova_slider_btn" href="<?=((!empty($slide['PROPERTIES']['LINK']['VALUE'])) ? $slide['PROPERTIES']['LINK']['VALUE'] : '#')?>" style="<?=((!empty($slide['PROPERTIES']['BTN_COLOR']['VALUE'])) ? 'color: '.$slide['PROPERTIES']['BTN_COLOR']['VALUE'].' !important; border-color: '.$slide['PROPERTIES']['BTN_COLOR']['VALUE'].';' : 'color: #fff !important')?>"><?=((!empty($slide['PROPERTIES']['BTN_TEXT']['VALUE'])) ? $slide['PROPERTIES']['BTN_TEXT']['VALUE'] : GetMessage("INNOVA_SLIDER_LEARN_MORE") )?></a>
					<? else : ?>
						<span class="innova_slider_btn"></span>
					<? endif; ?>
				</div>
				<? if(($arParams['HIDE_TEXT'] == 'Y' or $slide['PROPERTIES']['HIDE_TEXT']['VALUE'] == 'Y') and !empty($slide['PROPERTIES']['LINK']['VALUE'])) : ?>
				</a>
				<? endif; ?>
			</div>
		</div>
	<? endforeach; ?>
</div>

<script>
	var <?=$id?> = tns({
		container: '#<?=$id?>',
		items: 1,
		loop: true,
		rewind: false,
		nav: false,
		autoplay: <?=((!empty($arParams['AUTOPLAY'])) ? $arParams['AUTOPLAY'] : 'true')?>,
		autoplayTimeout: <?=((!empty($arParams['AUTOPLAY_SPEED'])) ? $arParams['AUTOPLAY_SPEED'] : '3000')?>,
		autoplayButtonOutput: false,
		autoplayHoverPause: true,
		speed: <?=((!empty($arParams['SPEED'])) ? $arParams['SPEED'] : '500')?>,
		slideBy: 'page',
		onInit: setSliderHeight_<?=$id?>,
		mouseDrag: true
	});
	function setSliderHeight_<?=$id?>() {
		setSliderFontSize_<?=$id?>();
		var x = document.getElementById('<?=$id?>').getElementsByClassName('innova_slide_block');
		var i;
		var maxHeight = 0;
		var tmpWidth = 0;
		var tmpHeight = 0;
		for (i = 0; i < x.length; i++) {
			tmpWidth = x[i].offsetWidth;
			tmpHeight = (tmpWidth / x[i].dataset.width) * x[i].dataset.height;
			if(tmpHeight > x[i].dataset.height) tmpHeight = x[i].dataset.height;
			if((x[i].getElementsByClassName('innova_slide_innerH1')[0].offsetHeight + x[i].getElementsByClassName('innova_slide_innerP')[0].offsetHeight + x[i].getElementsByClassName('innova_slider_btn')[0].offsetHeight + 100) > tmpHeight) tmpHeight = (x[i].getElementsByClassName('innova_slide_innerH1')[0].offsetHeight + x[i].getElementsByClassName('innova_slide_innerP')[0].offsetHeight + x[i].getElementsByClassName('innova_slider_btn')[0].offsetHeight + 100);

			if(maxHeight < tmpHeight) maxHeight = tmpHeight;
		}
		<?=((!empty($arParams['HEIGHT'])) ? 'maxHeight = '.$arParams['HEIGHT'].';' : '')?>
		<?=((!empty($arParams['HEIGHT_MOBILE'])) ? 'if(document.body.offsetWidth < 768) { maxHeight = '.$arParams['HEIGHT_MOBILE'].'; }' : '')?>
		for (i = 0; i < x.length; i++) {
			x[i].style.height = maxHeight+'px';
		}
	}
	
	function setSliderFontSize_<?=$id?>() {
		var x = document.getElementById('<?=$id?>').getElementsByClassName('innova_slide_block');
		var i;
		var fontSize, tmpWidth;
		for (i = 0; i < x.length; i++) {
			tmpWidth = x[i].offsetWidth;
			fontSize = tmpWidth * 0.0375;
			if(fontSize < 20) fontSize = 20;
			if(tmpWidth < 801) {
				x[i].getElementsByClassName('innova_slide_innerP')[0].style.margin = "15px 0 20px";
				x[i].getElementsByClassName('innova_slider_btn')[0].style.padding = "10px 20px";
			} else {
				x[i].getElementsByClassName('innova_slide_innerP')[0].style.margin = "";
				x[i].getElementsByClassName('innova_slider_btn')[0].style.padding = "";
			}
			x[i].getElementsByClassName('innova_slide_innerH1')[0].style.fontSize = fontSize+'px';
			x[i].getElementsByClassName('innova_slide_innerP')[0].style.fontSize = (fontSize*0.5)+'px';
			x[i].getElementsByClassName('innova_slider_btn')[0].style.fontSize = (fontSize*0.35)+'px';
		}
	}
	
	var <?=$id?>_resize = new ResizeSensor(document.getElementById('<?=$id?>'), function(){ 
		setSliderHeight_<?=$id?>();
	});

</script>

<? if(!empty($arParams['BTN_COLOR'])) : ?>
<style>
	.tns-controls button[aria-controls="<?=$id?>"]:before, 
	.tns-controls button[aria-controls="<?=$id?>"]:after {
		background: <?=$arParams['BTN_COLOR']?> !important;
	}
</style>
<? endif; ?>

<? if(!empty($arParams['SLIDER_COLOR'])) : ?>
<style>
	#<?=$id?> .innova_slide_block {
		background-color: <?=$arParams['SLIDER_COLOR']?> !important;
	}
</style>
<? endif; ?>

<? endif; ?>
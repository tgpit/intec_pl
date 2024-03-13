<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;
?>
<?=Helper::showNote(static::getMessage('PARAGRAPH_ABOUT_REQUIRED_PARAMS'));?>
<p><?=static::getMessage('IMAGES_MAX_COUNT', array(
	'#NAME#' => static::getMessage('CATEGORY_NAME'),
	'#COUNT#' => 20,
));?></p>
<h2><?=static::getMessage('USEFUL_LINKS');?></h2>
<ul>
	<li>
		<a href="http://autoload.avito.ru/format/mototsikly_i_mototehnika" target="_blank">
			<?=static::getMessage('DOCUMENTATION');?>
		</a>
	</li>
	<li>
		<a href="http://autoload.avito.ru/format/xmlcheck/" target="_blank">
			<?=static::getMessage('CHECK_XML');?>
		</a>
	</li>
	<li>
		<a href="http://autoload.avito.ru/format/faq/" target="_blank">
			<?=static::getMessage('FAQ');?>
		</a>
	</li>
</ul>
<p><br/></p>

<h2><?=static::getMessage('VIDEO_TARIFF');?></h2>
<div style="max-width:720px;">
	<div style="position:relative;height:0;padding-bottom:56.25%;">
		<div style="height:100%;left:0;position:absolute;top:0;width:100%;">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/Ng_mTFVCpms" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="border:0;height:100%;width:100%;"></iframe>
		</div>
	</div>
</div>

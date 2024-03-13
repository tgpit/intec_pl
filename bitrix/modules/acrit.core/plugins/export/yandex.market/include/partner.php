<?
use
	\Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$strImage = 'data:image/png;base64,'.base64_encode(file_get_contents(__DIR__.'/../images/tab1.png'));

?>
<style>
	.yandex_mnarket_partner_info {
		background:#fff;
		margin:10px 0;
		padding:20px;
		text-align:center;
	}
	.yandex_mnarket_partner_info_buttons {
		margin-bottom:20px;
	}
	.yandex_mnarket_partner_info_buttons a {
		background:#3e424c;
		border-radius:8px;
		color:#fff;
		display:inline-block;
		font:normal 20px "Helvetica", "Arial", sans-serif;
		line-height:48px;
		margin:10px;
		outline:0;
		text-decoration:none;
		white-space:nowrap;
		width:280px;
	}
	.yandex_mnarket_partner_info img {
		display:inline-block;
		max-width:100%;
	}
</style>
<div class="yandex_mnarket_partner_info">
	<div class="yandex_mnarket_partner_info_buttons">
		<a href="https://partner.market.yandex.ru/welcome/partners/?promocode=MARKET_2854080" target="_blank"><?=Loc::getMessage('ACRIT_YANDEX_MARKET_PARTNER_BUTTON_1');?></a>
		<a href="https://partner.market.yandex.ru/welcome/partners/?promocode=MARKET_2854080" target="_blank"><?=Loc::getMessage('ACRIT_YANDEX_MARKET_PARTNER_BUTTON_2');?></a>
	</div>
	<div class="yandex_mnarket_partner_info_image">
		<img src="<?=$strImage;?>" alt="" />
	</div>
</div>
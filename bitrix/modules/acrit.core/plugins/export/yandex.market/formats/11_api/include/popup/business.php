<ul class="acrit_exp_yandex_market_api_businesses">
	<?foreach($arCampaigns as $arCampaign):?>
		<li>
			<span data-business-id="<?=$arCampaign['business']['id'];?>">
				[<?=$arCampaign['business']['id'];?>]
				<b><?=$arCampaign['business']['name'];?></b>,
				<?=$arCampaign['placementType'];?>
			</span>
		</li>
	<?endforeach?>
</ul>
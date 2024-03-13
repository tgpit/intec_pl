<?
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\WildberriesV4Helpers\HistoryTable as History;

# More data
$arHistoryItems = [];
if($arTask['ID']){
	$arQuery = [
		'filter' => [
			'PROFILE_ID' => $this->intProfileId,
			'TASK_ID' => $arTask['ID'],
		],
		'limit' => 5000,
	];
	$resHistoryItems = History::getList($arQuery);
	while($arHistoryItem = $resHistoryItems->fetch()){
		$arHistoryItems[] = $arHistoryItem;
	}
}
if(!empty($arHistoryItems)){
	?>
		<a data-role="log-tasks-status-toggle" class="acrit-inline-link"><?=static::getMessage('STATUS_TOGGLE');?></a>
		<div data-role="log-tasks-status-details-table">
			<table>
				<thead>
					<tr>
						<th>ElementId</th>
						<th>vendorCode</th>
						<th>nmId</th>
						<th>barcode</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?foreach($arHistoryItems as $arItem):?>
						<tr>
							<td align="right">
								<?=$arItem['ELEMENT_ID'];?>
							</td>
							<td>
								<?=$arItem['VENDOR_CODE'];?>
							</td>
							<td>
								<?=$arItem['NM_ID'];?>
							</td>
							<td>
								<?=$arItem['BARCODE'];?>
							</td>
							<td>
								<a href="#" data-role="log-tasks-status-preview" data-id="<?=$arItem['ID'];?>"
									class="acrit-inline-link">JSON</a>
							</td>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
		</div>
	<?
}

?>
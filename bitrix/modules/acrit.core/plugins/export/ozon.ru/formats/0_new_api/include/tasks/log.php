<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\TaskTable as Task;

Helper::loadMessages(__FILE__);

$strLogCustomTitle = static::getMessage('LOG_TASKS_HEADING');

$arNavParams = array(
	'page' => is_numeric($arGet['page']) ? $arGet['page'] : 1,
	'size' => is_numeric($arGet['size']) ? $arGet['size'] : 10,
);

$arProfileTasksAll = array();
$arTasksFilter = array(
	'PROFILE_ID' => $this->intProfileId,
);
$arQuery = [
	'filter' => $arTasksFilter,
	'order' => array(
		'ID' => 'DESC',
	),
	'limit' => $arNavParams['size'],
	'offset' => ($arNavParams['page'] - 1) * $arNavParams['size']
];
$resTasks = Task::getList($arQuery);
while($arTask = $resTasks->fetch()){
	$arProfileTasksAll[] = $arTask;
}

# Nav object
$obNav = new \Bitrix\Main\UI\AdminPageNavigation('acrit-exp-nav-tasks');
$arQuery = [
	'filter' => $arTasksFilter,
	'select' => ['CNT'],
	'runtime' => array(
		new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
	)
];
$obNav->setRecordCount(Task::getList($arQuery)->fetch()['CNT']);
$obNav->setCurrentPage($arNavParams['page']);
$obNav->setPageSize($arNavParams['size']);
?>
<div data-role="ozon_tasks_log">
	<?=Helper::showNote(static::getMessage('LOG_TASKS_NOTE', ['#PER_STEP#' => $this->intExportPerStep]), true);?>
	<div>
		<input type="button" data-role="log-tasks-refresh"
			value="<?=static::getMessage('LOG_TASKS_REFRESH');?>" />
		<?if(!empty($arProfileTasksAll)):?>
			<input type="button" data-role="log-tasks-open-task-json" style="float:right;"
				value="<?=static::getMessage('LOG_TASKS_OPEN_TASK');?>"
				data-text="<?=static::getMessage('LOG_TASKS_OPEN_TASK_PROMPT');?>";/>
			<input type="button" data-role="log-tasks-open-offer-json" style="float:right;"
				value="<?=static::getMessage('LOG_TASKS_OPEN_OFFER');?>"
				data-text="<?=static::getMessage('LOG_TASKS_OPEN_OFFER_PROMPT');?>";/>
		<?endif?>
	</div>
	<br/>
	<?if(!empty($arProfileTasksAll)):?>
		<table class="adm-list-table acrit-exp-table-tasks">
			<thead>
				<tr class="adm-list-table-header">
					<?if($this->isStockAndPrice()):?>
						<td class="adm-list-table-cell" style="width:1px;">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=static::getMessage('LOG_ID');?>
							</div>
						</td>
					<?endif?>
					<?if(!$this->isStockAndPrice()):?>
						<td class="adm-list-table-cell" style="width:1px;">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=static::getMessage('LOG_TASKS_ID');?>
							</div>
						</td>
					<?endif?>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_PRODUCTS_COUNT');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_TIMESTAMP_X');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_JSON');?>
						</div>
					</td>
					<td class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner">
							<?=static::getMessage('LOG_TASKS_STATUS');?>
						</div>
					</td>
					<?if(!$this->isStockAndPrice()):?>
						<td class="adm-list-table-cell" style="width:1px;">
							<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
								<?=static::getMessage('LOG_TASKS_STATUS_DATETIME');?>
							</div>
						</td>
						<td class="adm-list-table-cell" style="width:1px;">
							
						</td>
					<?endif?>
				</tr>
			</thead>
			<tbody>
				<?foreach($arProfileTasksAll as $arTask):?>
					<tr class="adm-list-table-row" data-stopped="<?=$arTask['STOPPED'];?>" data-task-id="<?=$arTask['TASK_ID'];?>">
						<?if($this->isStockAndPrice()):?>
							<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
								<?=$arTask['ID'];?>
							</td>
						<?endif?>
						<?if(!$this->isStockAndPrice()):?>
							<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
								<?=$arTask['TASK_ID'];?>
							</td>
						<?endif?>
						<td class="adm-list-table-cell align-right">
							<?=$arTask['PRODUCTS_COUNT'];?>
						</td>
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?=is_object($arTask['TIMESTAMP_X']) ? $arTask['TIMESTAMP_X']->toString() : '';?>
						</td>
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?if(strlen($arTask['JSON'])):?>
								<a class="acrit-inline-link" data-role="log-task-json-preview" data-id="<?=$arTask['ID'];?>" data-task-id="<?=$arTask['TASK_ID'];?>">
									<?=static::getMessage('LOG_TASKS_JSON_VIEW');?>
								</a>
							<?endif?>
						</td>
						<td class="adm-list-table-cell align-right">
							<div data-role="log-tasks-item-status" style="text-align:left;">
								<?=(strlen($arTask['STATUS']) || $this->isStockAndPrice() ? $this->displayTaskStatus($arTask) : '&mdash;');?>
							</div>
						</td>
						<?if(!$this->isStockAndPrice()):?>
							<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
								<div data-role="log-tasks-item-status-datetime">
									<?=(is_object($arTask['STATUS_DATETIME']) ? $arTask['STATUS_DATETIME']->toString() : '&mdash;');?>
								</div>
							</td>
							<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
								<a class="acrit-inline-link" 
									data-role="log-tasks-item-update-status"><?=static::getMessage('LOG_TASKS_STATUS_UPDATE');?></a>
							</td>
						<?endif?>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
		<?/**/?>
		<script>
		AcritExpTasksTable = {
			GetAdminList: function(url){
				if(params = url.match(/page-(\d+)-size-(\d+)/)){
					$('input[data-role="log-tasks-refresh"]').trigger('click', {
						page: params[1],
						size: params[2]
					});
				}
			}
		}
		</script>
		<style>
		#tr_LOG_CUSTOM .adm-nav-pages-number-block {
			display:none!important;
		}
		</style>
		<?
		$_REQUEST['admin_history'] = '';
		$GLOBALS['APPLICATION']->IncludeComponent(
			"bitrix:main.pagenavigation",
			"admin",
			array(
				"SEF_MODE" => "N",
				"NAV_OBJECT" => $obNav,
				"TITLE" => "",
				"PAGE_WINDOW" => 10,
				"SHOW_ALWAYS" => "Y",
				"TABLE_ID" => "AcritExpTasksTable",
			),
			false,
			array(
				"HIDE_ICONS" => "Y",
			)
		);
		unset($_REQUEST['admin_history']);
		?>
		<div style="clear:both"></div>
	<?else:?>
		<p><?=static::getMessage('LOG_TASKS_EMPTY');?></p>
	<?endif?>
</div>
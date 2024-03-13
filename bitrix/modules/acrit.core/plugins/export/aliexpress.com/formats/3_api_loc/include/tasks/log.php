<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\AliexpressComApiLocalHelpers\TaskTable as Task;

Helper::loadMessages(__FILE__);

$strLogCustomTitle = static::getMessage('LOG_TASKS_HEADING');

$arNavParams = array(
	'page' => is_numeric($arGet['page']) ? $arGet['page'] : 1,
	'size' => is_numeric($arGet['size']) ? $arGet['size'] : 10,
);

// Get tasks
$arTasksFilter = array(
	'PROFILE_ID' => $this->intProfileId,
);
if ($_REQUEST['status']) {
	$arTasksFilter['STATUS_ID'] = (int)$_REQUEST['status'];
}
$arQuery = [
	'filter' => $arTasksFilter,
	'order' => array(
		'ID' => 'ASC',
	),
	'limit' => $arNavParams['size'],
	'offset' => ($arNavParams['page'] - 1) * $arNavParams['size']
];
$arProfileTasksAll = Task::getListData($arQuery);
// Update statuses of tasks
$arTasksId = [];
foreach ($arProfileTasksAll as $arTask) {
	$arTasksId[] = $arTask['ID'];
}
$this->updateSavedTasks($arTasksId);
// Get fresh data of tasks
$arProfileTasksAll = Task::getListData($arQuery);

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

$arTasksTypes = [
    false => static::getMessage('LOG_TASKS_TYPE_EMPTY'),
    Task::TYPE_ADD => static::getMessage('LOG_TASKS_TYPE_ADD'),
    Task::TYPE_UPDATE => static::getMessage('LOG_TASKS_TYPE_UPDATE'),
];
?>
<div data-role="aliloc_tasks_log">
	<?=Helper::showNote(static::getMessage('LOG_TASKS_NOTE', ['#PER_STEP#' => $this->intExportPerStep]), true);?>
	<div>
		<input type="button" data-role="log-tasks-refresh"
			value="<?=static::getMessage('LOG_TASKS_REFRESH');?>" />
        <select name="log_tasks_filter_status" class="log-tasks-filter-status" data-role="log-tasks-filter-status">
            <option value=""><?=static::getMessage('LOG_TASKS_FILTER_STATUS_ALL');?></option>
            <option value="1"<?=($_REQUEST['status']==1?' selected':'');?>><?=static::getMessage('LOG_TASKS_FILTER_STATUS_1');?></option>
            <option value="2"<?=($_REQUEST['status']==2?' selected':'');?>><?=static::getMessage('LOG_TASKS_FILTER_STATUS_2');?></option>
            <option value="3"<?=($_REQUEST['status']==3?' selected':'');?>><?=static::getMessage('LOG_TASKS_FILTER_STATUS_3');?></option>
            <option value="4"<?=($_REQUEST['status']==4?' selected':'');?>><?=static::getMessage('LOG_TASKS_FILTER_STATUS_4');?></option>
        </select>
	</div>
	<br/>
	<?//if(!empty($arProfileTasksAll)):?>
		<table class="adm-list-table acrit-exp-table-tasks">
			<thead>
				<tr class="adm-list-table-header">
                    <td class="adm-list-table-cell" style="width:1px;">
                        <div class="adm-list-table-cell-inner" style="white-space:nowrap;">
                            <?=static::getMessage('LOG_ID');?>
                        </div>
                    </td>
                    <td class="adm-list-table-cell" style="width:1px;">
                        <div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_TIMESTAMP_X');?>
                        </div>
                    </td>
                    <td class="adm-list-table-cell" style="width:1px;">
                        <div class="adm-list-table-cell-inner" style="white-space:nowrap;">
                            <?=static::getMessage('LOG_TASKS_PRODUCT');?>
                        </div>
                    </td>
                    <td class="adm-list-table-cell" style="width:1px;">
                        <div class="adm-list-table-cell-inner" style="white-space:nowrap;">
                            <?=static::getMessage('LOG_TASKS_TYPE');?>
                        </div>
                    </td>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_STATUS');?>
						</div>
					</td>
					<td class="adm-list-table-cell" style="width:1px;">
						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">
							<?=static::getMessage('LOG_TASKS_ERRORS');?>
						</div>
					</td>
<!--					<td class="adm-list-table-cell" style="width:1px;">-->
<!--						<div class="adm-list-table-cell-inner" style="white-space:nowrap;">-->
<!--							--><?//=static::getMessage('LOG_TASKS_STATUS_DATETIME');?>
<!--						</div>-->
<!--					</td>-->
                    <td class="adm-list-table-cell" style="width:1px;">

                    </td>
				</tr>
			</thead>
			<tbody>
				<?foreach($arProfileTasksAll as $arTask):?>
					<tr class="adm-list-table-row" data-stopped="<?=$arTask['STOPPED'];?>" data-task-id="<?=$arTask['ID'];?>">
						<td class="adm-list-table-cell align-right" style="white-space:nowrap;">
                            <?=$arTask['ID'];?>
                        </td>
                        <td class="adm-list-table-cell align-right" style="white-space:nowrap;">
							<?=is_object($arTask['TIMESTAMP_X']) ? $arTask['TIMESTAMP_X']->toString() : '';?>
                        </td>
                        <td class="adm-list-table-cell align-right" style="white-space:nowrap;">
                            <?=$arTask['PRODUCT_NAME'];?> [<?=$arTask['PRODUCT_ID'];?>]
                        </td>
                        <td class="adm-list-table-cell align-right" style="white-space:nowrap;">
                            <?=$arTasksTypes[$arTask['TYPE']];?>
                        </td>
						<td class="adm-list-table-cell align-right" data-role="log-tasks-item-status">
							<?=$arTask['STATUS_NAME'];?> [<?=$arTask['STATUS_ID'];?>]
						</td>
						<td class="adm-list-table-cell align-right" data-role="log-tasks-item-errors">
                            <?if(is_array($arTask['ERRORS']) && count($arTask['ERRORS']) == 2):?>
                            <?=($arTask['ERRORS']['message'] . ' [' . $arTask['ERRORS']['code'] . ']');?>
                            <?else:?>
                            <?print_r($arTask['ERRORS']);?>
                            <?endif;?>
						</td>
<!--                        <td class="adm-list-table-cell align-right" style="white-space:nowrap;">-->
<!--                            <div data-role="log-tasks-item-status-datetime">-->
<!--                                --><?//=(is_object($arTask['STATUS_DATETIME']) ? $arTask['STATUS_DATETIME']->toString() : '&mdash;');?>
<!--                            </div>-->
<!--                        </td>-->
                        <td class="adm-list-table-cell align-right" style="white-space:nowrap;">
	                        <?//if($arTask['STATUS_ID'] < 3):?><a class="acrit-inline-link"
                                data-role="log-tasks-item-update-status"><?=static::getMessage('LOG_TASKS_STATUS_UPDATE');?></a></td><?//endif;?>
                        </td>
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
						size: params[2],
						status: <?=(int)$_REQUEST['status'];?>,
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
	<?//else:?>
	<?if(empty($arProfileTasksAll)):?>
		<p><?=static::getMessage('LOG_TASKS_EMPTY');?></p>
	<?endif?>
</div>
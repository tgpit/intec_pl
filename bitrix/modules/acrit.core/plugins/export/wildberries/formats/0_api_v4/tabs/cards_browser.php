<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

// Это использовать для всех дополнительных параметров в плагинах, чтобы параметры профиля не пересекались
$strPluginParams = 'PROFILE[PARAMS][_PLUGINS]['.$obPlugin::getCode().']';
$arPluginParams = $arProfile['PARAMS']['_PLUGINS'][$obPlugin::getCode()];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// CARDS_BROWSER
$obTabControl->BeginCustomField($strPluginParams.'[CARDS_BROWSER]', $obPlugin::getMessage('CARDS_BROWSER'));
?>
	<tr>
		<td colspan="2" data-role="acrit_wb_card_browser_wrapper">
			<style>
				.acrit_wb_cards_explorer--param_field {display:inline-block; margin-right:6px; vertical-align:top;}
				.acrit_wb_cards_explorer--param_field_execute_cards {padding-top:15px;}
				.acrit_wb_cards_explorer--param_field_execute_filter {padding-top:15px;}
				.acrit_wb_cards_explorer--param_field_execute_errors {padding-top:15px;}
				.acrit_wb_cards_explorer--param_field_execute_attributes {padding-top:15px;}
				.acrit_wb_cards_explorer--param_field_execute_prices {padding-top:15px;}
				.acrit_wb_cards_explorer--param_field > div {margin-bottom:2px;}
				.acrit_wb_cards_explorer--param_field input[type=text],
				.acrit_wb_cards_explorer--param_field select {box-sizing:border-box; height:27px; min-width:100px; width:100%;}
				.acrit_wb_cards_explorer--param_field_execute_cards input[type=button],
				.acrit_wb_cards_explorer--param_field_execute_filter input[type=button] {height:27px!important; margin:0;}
				.acrit_wb_cards_explorer--param_field_execute_errors input[type=button] {height:27px!important; margin:0;}
				.acrit_wb_cards_explorer--param_field_execute_attributes input[type=button] {height:27px!important; margin:0;}
				.acrit_wb_cards_explorer_ajax_result {margin-top:15px;}
			</style>
			<?
			$arSubTabs = [
				[
					'DIV' => 'subtab_wb_cards',
					'TAB' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_CARDS_NAME'),
					'TITLE' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_CARDS_DESC'),
				],
				[
					'DIV' => 'subtab_wb_filter',
					'TAB' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_FILTER_NAME'),
					'TITLE' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_FILTER_DESC'),
				],
				[
					'DIV' => 'subtab_wb_errors',
					'TAB' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_ERRORS_NAME'),
					'TITLE' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_ERRORS_DESC'),
				],
				[
					'DIV' => 'subtab_wb_attributes',
					'TAB' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_ATTRIBUTES_NAME'),
					'TITLE' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_ATTRIBUTES_DESC'),
				],
				[
					'DIV' => 'subtab_wb_prices',
					'TAB' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_PRICES_NAME'),
					'TITLE' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_PRICES_DESC'),
				],
				[
					'DIV' => 'subtab_wb_set_price',
					'TAB' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_SET_PRICE_NAME'),
					'TITLE' => $obPlugin::getMessage('CARDS_BROWSER_SUBTAB_SET_PRICE_DESC'),
				],
			];
			$obSubTabControl = new \CAdminViewTabControl('WbCardBrowser', $arSubTabs);
			$obSubTabControl->Begin();
			// Cards list
			$obSubTabControl->BeginNextTab();
			?>
				<div class="acrit_wb_cards_explorer_wrapper">
					<div class="acrit_wb_cards_explorer_controls">
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_COUNT_PER_PAGE');?>:</div>
							<div>
								<?
								$intCardsBrowserCountPerPage = intVal($arPluginParams['CARDS_BROWSER_COUNT_PER_PAGE']);
								if($intCardsBrowserCountPerPage == 0){
									$intCardsBrowserCountPerPage = 1000;
								}
								?>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_COUNT_PER_PAGE]"
									value="<?=(max(1, $intCardsBrowserCountPerPage));?>" size="8"
									data-role="acrit_exp_wildberries_cards_browser_count_per_page" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_SORT_FIELD');?>:</div>
							<div>
								<?
									$arCardsBrowserSortField = [
										'updatedAt' => $obPlugin::getMessage('CARDS_BROWSER_SORT_FIELD_updatedAt'),
									];
									$arCardsBrowserSortField = array(
										'reference_id' => array_keys($arCardsBrowserSortField),
										'reference' => array_values($arCardsBrowserSortField),
									);
									$strCardsBrowserSortField = $arPluginParams['CARDS_BROWSER_SORT_FIELD'];
									print SelectBoxFromArray($strPluginParams.'[CARDS_BROWSER_SORT_FIELD]', $arCardsBrowserSortField,
										$strCardsBrowserSortField, '', 'data-role="acrit_exp_wildberries_cards_browser_sort_field"');
									?>
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_SORT_ORDER');?>:</div>
							<div>
								<?
								$arCardsBrowserSortOrder = [
									'asc' => $obPlugin::getMessage('CARDS_BROWSER_SORT_ORDER_ASC'),
									'desc' => $obPlugin::getMessage('CARDS_BROWSER_SORT_ORDER_DESC'),
								];
								$arCardsBrowserSortOrder = array(
									'reference_id' => array_keys($arCardsBrowserSortOrder),
									'reference' => array_values($arCardsBrowserSortOrder),
								);
								$strCardsBrowserSortOrder = $arPluginParams['CARDS_BROWSER_SORT_ORDER'];
								print SelectBoxFromArray($strPluginParams.'[CARDS_BROWSER_SORT_ORDER]', $arCardsBrowserSortOrder,
									$strCardsBrowserSortOrder, '', 'data-role="acrit_exp_wildberries_cards_browser_sort_order"');
								?>
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_WITH_PHOTO');?>:</div>
							<div>
								<?
								$arCardsBrowserSortOrder = [
									'-1' => $obPlugin::getMessage('CARDS_BROWSER_WITH_PHOTO_ALL'),
									'1' => $obPlugin::getMessage('CARDS_BROWSER_WITH_PHOTO_Y'),
									'0' => $obPlugin::getMessage('CARDS_BROWSER_WITH_PHOTO_N'),
								];
								$arCardsBrowserSortOrder = array(
									'reference_id' => array_keys($arCardsBrowserSortOrder),
									'reference' => array_values($arCardsBrowserSortOrder),
								);
								$strCardsBrowserSortOrder = $arPluginParams['CARDS_BROWSER_WITH_PHOTO'];
								print SelectBoxFromArray($strPluginParams.'[CARDS_BROWSER_WITH_PHOTO]', $arCardsBrowserSortOrder,
									$strCardsBrowserSortOrder, '', 'data-role="acrit_exp_wildberries_cards_browser_with_photo"');
								?>
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_TEXT_SEARCH');?>:</div>
							<div>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_TEXT_SEARCH]"
									value="<?=htmlspecialcharsbx($arPluginParams['CARDS_BROWSER_TEXT_SEARCH']);?>" size="20"
									data-role="acrit_exp_wildberries_cards_browser_text_search" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_UPDATED_AT');?>:</div>
							<div>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_UPDATED_AT]"
									value="<?=htmlspecialcharsbx($arPluginParams['CARDS_BROWSER_UPDATED_AT']);?>" size="20"
									data-role="acrit_exp_wildberries_cards_browser_updated_at" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_NM_ID');?>:</div>
							<div>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_NM_ID]"
									value="<?=htmlspecialcharsbx($arPluginParams['CARDS_BROWSER_NM_ID']);?>" size="20"
									data-role="acrit_exp_wildberries_cards_browser_nm_id" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div style="padding-top:21px;">
								<input type="hidden" name="<?=$strPluginParams;?>[CARDS_BROWSER_AUTO_NAV]" value="N" />
								<label>
									<input type="checkbox" name="<?=$strPluginParams;?>[CARDS_BROWSER_AUTO_NAV]"
										value="Y" data-role="acrit_exp_wildberries_cards_browser_auto_nav"
										<?if($arPluginParams['CARDS_BROWSER_AUTO_NAV'] == 'Y'):?>checked<?endif?> />
									<span><?=$obPlugin::getMessage('CARDS_BROWSER_AUTO_NAV');?></span>
								</label>
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field acrit_wb_cards_explorer--param_field_execute_cards">
							<div>
								<input type="button" value="<?=$obPlugin::getMessage('CARDS_BROWSER_EXECUTE_CARDS');?>"
									data-role="acrit_exp_wildberries_cards_browser_execute" data-type="cards" />
							</div>
						</div>
					</div>
				</div>
				<div class="acrit_wb_cards_explorer_ajax_result" data-role="acrit_exp_wildberries_cards_browser_ajax_result">
					<?/* AJAX RESPONSE WILL BE HERE */?>
				</div>
			<?
			// Cards by filter
			$obSubTabControl->BeginNextTab();
			?>
				<div class="acrit_wb_cards_explorer_wrapper">
					<div class="acrit_wb_cards_explorer_controls">
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_FILTER_VENDOR_CODE');?>:</div>
							<div>
								<?
								$strCardsBrowserFilverVendorCode = $arPluginParams['CARDS_BROWSER_FILTER_VENDOR_CODE'];
								?>
								<textarea name="<?=$strPluginParams;?>[CARDS_BROWSER_FILTER_VENDOR_CODE]"
									cols="60" rows="1" data-role="acrit_exp_wildberries_cards_browser_filter_vendor_code"
									><?=htmlspecialcharsbx($strCardsBrowserFilverVendorCode);?></textarea>
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field acrit_wb_cards_explorer--param_field_execute_filter">
							<div>
								<input type="button" value="<?=$obPlugin::getMessage('CARDS_BROWSER_EXECUTE_FILTER');?>"
									data-role="acrit_exp_wildberries_cards_browser_execute" data-type="filter" />
							</div>
						</div>
					</div>
				</div>
				<div class="acrit_wb_cards_explorer_ajax_result" data-role="acrit_exp_wildberries_cards_browser_ajax_result">
					<?/* AJAX RESPONSE WILL BE HERE */?>
				</div>
			<?
			// Errors list
			$obSubTabControl->BeginNextTab();
			?>
				<div class="acrit_wb_cards_explorer_wrapper">
					<div class="acrit_wb_cards_explorer_controls">
						<div class="acrit_wb_cards_explorer--param_field acrit_wb_cards_explorer--param_field_execute_errors">
							<div>
								<input type="button" value="<?=$obPlugin::getMessage('CARDS_BROWSER_EXECUTE_ERRORS');?>"
									data-role="acrit_exp_wildberries_cards_browser_execute" data-type="errors" />
							</div>
						</div>
					</div>
				</div>
				<div class="acrit_wb_cards_explorer_ajax_result" data-role="acrit_exp_wildberries_cards_browser_ajax_result">
					<?/* AJAX RESPONSE WILL BE HERE */?>
				</div>
				<br/>
				<?Helper::showNote($obPlugin::getMessage('CARDS_BROWSER_ERRORS_DELETE_NOTE'), true);?>
			<?
			// Attributes
			$obSubTabControl->BeginNextTab();
			?>
				<div class="acrit_wb_cards_explorer_wrapper">
					<div class="acrit_wb_cards_explorer_controls">
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_ATTRIBUTES_CATEGORY_NAME');?>:</div>
							<div>
								<?
								$strCardsBrowserFilverVendorCode = $arPluginParams['CARDS_BROWSER_ATTRIBUTES_CATEGORY_NAME'];
								?>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_ATTRIBUTES_CATEGORY_NAME]"
									value="<?=htmlspecialcharsbx($strCardsBrowserFilverVendorCode);?>" size="60"
									data-role="acrit_exp_wildberries_cards_browser_attributes_category_name" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field acrit_wb_cards_explorer--param_field_execute_attributes">
							<div>
								<input type="button" value="<?=$obPlugin::getMessage('CARDS_BROWSER_EXECUTE_ATTRIBUTES');?>"
									data-role="acrit_exp_wildberries_cards_browser_execute" data-type="attributes" />
							</div>
						</div>
					</div>
				</div>
				<div class="acrit_wb_cards_explorer_ajax_result" data-role="acrit_exp_wildberries_cards_browser_ajax_result">
					<?/* AJAX RESPONSE WILL BE HERE */?>
				</div>
			<?
			// Prices list
			$obSubTabControl->BeginNextTab();
			?>
				<div class="acrit_wb_cards_explorer_wrapper">
					<div class="acrit_wb_cards_explorer_controls">
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_PRICES_NM_ID');?>:</div>
							<div>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_PRICES_NM_ID]"
									value="<?=htmlspecialcharsbx($arPluginParams['CARDS_BROWSER_PRICES_NM_ID']);?>" size="30"
									data-role="acrit_exp_wildberries_cards_browser_prices_nm_id" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field acrit_wb_cards_explorer--param_field_execute_prices">
							<div>
								<input type="button" value="<?=$obPlugin::getMessage('CARDS_BROWSER_EXECUTE_PRICES');?>"
									data-role="acrit_exp_wildberries_cards_browser_execute" data-type="prices" />
							</div>
						</div>
					</div>
				</div>
				<div class="acrit_wb_cards_explorer_ajax_result" data-role="acrit_exp_wildberries_cards_browser_ajax_result">
					<?/* AJAX RESPONSE WILL BE HERE */?>
				</div>
			<?
			// Set price
			$obSubTabControl->BeginNextTab();
			?>
				<div class="acrit_wb_cards_explorer_wrapper">
					<div class="acrit_wb_cards_explorer_controls">
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_SET_PRICE_NM_ID');?>:</div>
							<div>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_SET_PRICE_NM_ID]"
									value="<?=htmlspecialcharsbx($arPluginParams['CARDS_BROWSER_SET_PRICE_NM_ID']);?>" size="30"
									data-role="acrit_exp_wildberries_cards_browser_set_price_nm_id" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field">
							<div><?=$obPlugin::getMessage('CARDS_BROWSER_SET_PRICE_VALUE');?>:</div>
							<div>
								<input type="text" name="<?=$strPluginParams;?>[CARDS_BROWSER_SET_PRICE_VALUE]"
									value="<?=htmlspecialcharsbx($arPluginParams['CARDS_BROWSER_SET_PRICE_VALUE']);?>" size="20"
									data-role="acrit_exp_wildberries_cards_browser_set_price_value" />
							</div>
						</div>
						<div class="acrit_wb_cards_explorer--param_field acrit_wb_cards_explorer--param_field_execute_prices">
							<div>
								<input type="button" value="<?=$obPlugin::getMessage('CARDS_BROWSER_EXECUTE_SET_PRICE');?>"
									data-role="acrit_exp_wildberries_cards_browser_execute" data-type="set_price" />
							</div>
						</div>
					</div>
				</div>
				<div class="acrit_wb_cards_explorer_ajax_result" data-role="acrit_exp_wildberries_cards_browser_ajax_result">
					<?/* AJAX RESPONSE WILL BE HERE */?>
				</div>
			<?
			$obSubTabControl->End();
			?>
		</td>
	</tr>
<?
$obTabControl->EndCustomField($strPluginParams.'[CARDS_BROWSER]');

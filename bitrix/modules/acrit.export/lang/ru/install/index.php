<?
$strLang = 'ACRIT_EXPORT_';
$MESS[$strLang.'MODULE_NAME'] = 'Экспорт в Yandex и Google';
$MESS[$strLang.'MODULE_DESC'] = 'Универсальная XML выгрузка товаров на торговые порталы Яндекс и Google Merchant';
$MESS[$strLang.'PARTNER_NAME'] = 'Веб-Студия АКРИТ';
$MESS[$strLang.'PARTNER_URI'] = 'http://acrit-studio.ru';
$MESS[$strLang.'RECOMMENDED'] = 'Рекомендации перед установкой модуля «'.$MESS[$strLang.'MODULE_NAME'].'»';
$MESS[$strLang.'PHP_REQUIRE'] = 'Для работы модуля требуется версия php >= 5.4';
$MESS[$strLang.'DEINSTALL'] = 'Деинсталляция модуля «'.$MESS[$strLang.'MODULE_NAME'].'»';
#
$MESS[$strLang.'NO_CORE'] = '
	<div class="acrit_core_not_installed_title">
		Для продолжения необходимо установить дополнительный служебный модуль
	</div>
	<div class="acrit_core_not_installed_details">
		<p>
			Модуль <b>'.$MESS[$strLang.'MODULE_NAME'].'</b> ('.htmlspecialcharsbx($_GET['id']).') требует для своей работы дополнительный модуль - <a href="/bitrix/admin/update_system_partner.php?addmodule=acrit.core&lang='.LANGUAGE_ID.'" target="_blank">модуль служебных инструментов АКРИТ</a>.
		</p>
		<p>
			После установки служебного модуля обновите данную страницу для продолжения установки.
		</p>
		<p>
			<a href="#" id="acrit_core_not_installed_more_toggle">Для чего нужен служебный модуль?</a>
		</p>
		<p id="acrit_core_not_installed_more" style="display:none; margin-left:20px; border-left:3px solid silver; padding-left:12px;">
			Служебный модуль содержит общий код, применяемый во всех наших <a href="https://marketplace.1c-bitrix.ru/partners/detail.php?ID=38811.php" target="_blank">модулях</a>.<br/>
			Чтобы избежать дублирования кода и обеспечить ускоренное развитие модулей, общий код собран в едином модуле, благодаря чему функции, добавленные в одном модуле, могут быть легко добавлены в другом модуле.<br/>
			Это позволяет нам оперативнее выпускать обновления, а Вам - быстрее получать необходимый функционал.
		</p>
		<form action="/bitrix/admin/update_system_partner.php" method="get" target="_blank">
			<input type="hidden" name="addmodule" value="acrit.core" />
			<input type="hidden" name="lang" value="'.LANGUAGE_ID.'" />
			<div style="margin-top:10px;">
				<input type="submit" class="adm-btn-green" value="Установить служебный модуль" />
			</div>
		</form>
	</div>
	<style>
		.acrit_core_not_installed_message .adm-info-message {margin:0 0 10px!important; animation:borderblink 1.5s infinite!important;}
		.acrit_core_not_installed_message .adm-info-message-title {display:none!important;}
		.acrit_core_not_installed_message .adm-info-message-icon {display:none!important;}
		.acrit_core_not_installed_title {color:#d10000; font-weight:bold; margin:0 0 10px;}
		.acrit_core_not_installed_details p {margin:0 0 4px;}
		#acrit_core_not_installed_more_toggle {border-bottom:1px dashed #2675d7; text-decoration:none;}
		#acrit_core_not_installed_more_toggle:hover {border-bottom:none;}
		@keyframes borderblink {50%{border-color:#d10000;}}
	</style>
	<script>
		(function(){
			let
				divMessage = document.getElementsByClassName("acrit_core_not_installed_title")[0].parentNode.parentNode;
			divMessage.classList.remove("adm-info-message-red");
			divMessage.classList.add("acrit_core_not_installed_message");
			//
			BX.bind(BX("acrit_core_not_installed_more_toggle"), "click", function(e){
				e.preventDefault();
				let
					p = BX("acrit_core_not_installed_more");
				p.style.display = p.style.display == "none" ? "block" : "none";
			});
		})();
	</script>';
?>
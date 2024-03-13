<p>Данный формат выгрузки предназначен для экспорта товаров в маркетплейс Ozon с помощью API.</p>

<div class="acrit-exp-note-compact" style="margin-bottom:15px;">
	<div class="adm-info-message-wrap">
		<div class="adm-info-message">
			<b>Внимание! Важная информация без которой Вы не сможете корректно настроить выгрузку!</b><br/>
			Выбор категорий Ozon - обязателен (два режима - стандартный и нестандартный)!<br/><br/>
			И <b style="color:maroon">для каждой категории Ozon Вам потребуется заполнить как минимум обязательные атрибуты</b> (они появляются в общем списке полей товаров, внизу, с разделением по категориям). Без этого корректная выгрузка <b style="color:maroon">невозможна</b>!<br/><br/>
			Больше информации - в <a href="#" data-role="acrit_exp_ozon_decscription_teacher" class="acrit-inline-link">кратком уроке по настройке профиля Ozon</a>.
			<script>
			$('a[data-role="acrit_exp_ozon_decscription_teacher"]').bind('click', function(e){
				e.preventDefault();
				if(typeof window.acritTeachers == 'object'){
					for(let i in window.acritTeachers){
						if(window.acritTeachers[i].code == 'EXPORT_OZON_NEW_API'){
							$("#adm-workarea").acritTeacher(window.acritTeachers[i]);
						}
					}
				}
			});
			</script>
		</div>
	</div>
</div>

<div data-role="ozon_description_hotwo">
	<h2>Как начать работу в Ozon?</h2>
	<ul>
		<li>Шаг 1. <a href="https://seller.ozon.ru/signup" target="_blank">Зарегистрируйтесь</a> и активируйте аккаунт.</li>
		<li>Шаг 2. Прочитайте и примите <a href="https://docs.ozon.ru/partners/dogovor-dlya-prodavtsov-na-platforme-ozon" target="_blank">оферту</a> (откроется при первом входе в личный кабинет).</li>
		<li>Шаг 3. Подключите электронный <a href="https://docs.ozon.ru/partners/nachalo-raboty/shag-3-podklyuchite-elektronnyj-dokumentooborot" target="_blank">документооборот</a>.</li>
		<li>Шаг 4. Загрузите <a href="https://seller.ozon.ru/products?filter=all" target="_blank">товары</a> - на этом шаге Вам поможет данный модуль.</li>
		<li>Шаг 5. Дождитесь результатов модерации (обычно не более 3х дней).</li>
		<li>Шаг 6. Выберите <a href="https://docs.ozon.ru/partners/nachalo-raboty/shag-6-vyberite-shemu-raboty-i-nachnite-prodavat" target="_blank">схему работы</a> (FBO или FBS) и начните продавать.</li>
	</ul>
</div>

<p><br/></p>

<div data-role="ozon_description_recommendations">
	<h2>Наши рекомендации для эффективной выгрузки</h2>
	<ol>
		<li style="margin-bottom:4px;"><b>Один профиль - для одной категории Ozon</b><br/>
		Для каждой категории Ozon свой набор характеристик для загрузки, поэтому все дополнительные поля (атрибуты) в настройках прикреплены к конкретным разделам.<br/>
		В некоторых случаях может быть удобно настроить несколько категорий, но имейте в виду что в некоторых категориях большой перечень полей, и чем больше категорий тем больше полей.</li>
		<li style="margin-bottom:4px;"><b>Выбор и сопоставление категорий</b><br/>
		После выбора категорий в списке обязательно настройте соответствия категорий сайта категориям на Ozon (кнопка «Настроить названия»).<br/>
		После этого сохраните изменения в профиле и затем запустите загрузку атрибутов категорий (процесс может занять длительное время).</li>
		<li style="margin-bottom:4px;"><b>Обязательные поля и справочники</b><br/>
		Обращайте внимание на обязаные поля (указаны жирным шрифтом), и поля-справочники, в которых возможны только определенные значения (желтый восклицательный знак).</li>
	</ol>
</div>

<p><br/></p>

<div data-role="ozon_description_nuances">
	<h2>Важные нюансы</h2>
	<ul>
		<li>Справочники для различных категорий используются общие, кроме справочников «Тип» и «Коммерческий тип».</li>
		<li>После изменения выбранной категории не забывайте настроить сопоставление категорий и после этого применить изменения в профиле. После этого необходимо загрузить атрибуты и значения справочников.</li>
		<li>Для ускорения работы, загрузка значений справочников осуществляется с шагом 5000, несмотря на то, что рекомендация техподдержки Ozon - 1000. В случае необходимости изменния значения используется конфигурационный параметр <b><code>ozon_new_api_step_size</code></b> нашего модуля.</li>
		<li>Для большинства атрибутов Ozon требует точного соответствия значений. Например, если Вы выгружаете футболку, в которой пол указан как «М», «Для мужчин», «Мужские» - это неправильно. Должно выгружаться строго «Мужской».</li>
		<li>Картинки для товаров в личном кабинете сразу после загрузки могут не показываться - иногда требуется достаточно много времени, чтобы они загрузились (несколько часов).</li>
		<li>Выгрузить более чем один товар с одинаковым offer_id невозможно. В таком случае в Ozon обновится существующий товар (это также относится и к архиву).</li>
		<li>Если Вы встретились с ошибкой «invalid value for int32 type», проверьте, что в полях с длиной, шириной, высотой и весом выгружаются только целочисленные значения (лучше всего в настройках значений добавить округление).</li>
		<li>Если текущая цена от 400 до 10000 включительно, разница между текущей ценой и ценой до скидки должна быть больше 5%. В противном случае выгрузка товара может завершиться ошибкой <code>failed</code>.</li>
		<li>Ошибка «<code><b>NOT_FOUND product not found</b></code>» появляется в случае, когда выгружаются цены/остатки для offer_id, которых нет в личном кабинете Ozon. Т.е. при первичной выгрузке товаров и цен данная ошибка будет практически всегда, т.к. на момент выгрузки цены только что загруженный товар ещё не успел обработаться Озоном.</li>
	</ul>
</div>

<p><br/></p>

<h2>Дополнительно</h2>
<ul>
	<li>
		<a href="#" data-role="acrit_exp_spoiler_toggle">Статусы обработки товаров</a>
		<div data-role="acrit_exp_spoiler_data">
			<ul type="square">
				<li><b>pending</b> - импорт товаров ожидает обработки,</li>
				<li><b>imported</b> - товары импортированы (но это еще не значит что у них нет ошибок),</li>
				<li><b>processing</b> - товар в очереди на обработку,</li>
				<li><b>moderating</b> - товар в очереди на модерацию,</li>
				<li><b>processed</b> - товар успешно прошел модерацию и может быть выставлен на продажу,</li>
				<li><b>failed_moderation</b> - товар не прошел модерацию,</li>
				<li><b>failed_validation</b> - товар не прошел валидацию (грубые нарушения при создании товара, которые требуется исправить),</li>
				<li><b>failed</b> - внутренняя ошибка при обработке товара, решается через <a href="https://seller.ozon.ru/app/products?helpCenter=create-issue" target="_blank">поддержку Ozon</a> (предварительно проверьте, что для товаров выгружаются корректные и правдоподобные значения габаритов - длина, ширина, высота, вес).</li>
			</ul>
		</div>
	</li>
	<li>
		<a href="#" data-role="acrit_exp_spoiler_toggle">Выгрузка остатков</a>
		<div data-role="acrit_exp_spoiler_data">
			<p>С помощью данного формата выгрузки Вы можете настроить выгрузку как общего остатка товара в Ozon (без привязки к складу), так и остатков по каждому из складов, так и отдельно цен - всё зависит от заполненности поля stock (для выгрузки общего остатка), галочка «Выгружать остатки по складам» (для выгрузки остатков по складам) и галочки «Режим выгрузки только остатков и цен» (для включения режима выгрузки только цен и остатков).</p>
			<p>Для выгрузки остатка товар должен быть предварительно успешно загружен на Ozon (если быть точнее - когда статус товара сменился на <b>processed</b> - статус выгрузки каждого товара можно проверять на вкладке «Лог и история», в самом низу). Это означает, что после настройки выгрузки первый запуск никак не сможет корректно выгрузить остатки - он сможет их корректно выгрузить только через некоторое время - это зависит от Ozon.</p>
			<p>Также в модуле стал доступен новый режим работы «<b>Режим выгрузки только остатков и цен</b>» - в этом режиме не показываются дополнительные поля для выгрузки, только указанные: <code><b>offer_id</b></code> (код товара), <code><b>stock</b></code> (общий остаток), <code><b>price</b></code> (цена), <code><b>old_price</b></code> (цена без скидки), <code><b>premium_price</b></code> (цена premium) и остатки по складам вида <code><b>stock_123</b></code>.</p>
			<p>Важный нюанс, который приводит к ошибке "Request validation error: invalid ProductsStocksRequest.Stocks[0]: embedded message failed validation | caused by: invalid ProductsStocksRequest_Stock.WarehouseId: value must be greater than 0": если у Вас в личном кабинете Ozon созданы склады, то общий остаток не нужно выгружать.</p>
		</div>
	</li>
	<li>
		<a href="#" data-role="acrit_exp_spoiler_toggle">Особенности настройки некоторых полей</a>
		<div data-role="acrit_exp_spoiler_data">
			<ul type="square">
				<li><b> Идентификатор товара (артикул), offer_id</b>. Данное поле является основным идентификатором на Ozon, по нему выполняются все привязки. Например, если Вы выгружаете товар с offer_id равным 123, Ozon попытается найти товар с таким кодом, и если он найден - то загрузка пройдёт в него (товар обновится), иначе будет создан новый товар.</li>
				<li><b>Изображения, images</b>. Данное поле должно выгружать множественное значение, содержащее полные ссылки на изображения - это при создании профиля задаётся автоматически, но при изменении значения поля сбрасывается. Проверьте, чтобы и в настройках значения, и в настройках поля была отмечена опция выгрузки «Оставить множественным».</li>
				<li><b>PDF-файлы, pdf_list</b>. Множественное поле, которое должно представлять собой полные ссылки на файлы в формате PDF. Проверьте, чтобы и в настройках значения, и в настройках поля была отмечена опция выгрузки «Оставить множественным».</li>
				<li><b>Видео для YouTube, video_youtube</b>. Множественное поле, которое должно содержать адреса ссылок на YouTube-видео. Поддерживаются четыре различных варианта определения ссылок, что на практике означает что можно использовать любой из используемых где-либо форматов. Например, <code>https://www.youtube.com/watch?v=ene4qDMdn6A</code> или <code>https://www.youtube.com/embed/ene4qDMdn6A</code>.</li>
			</ul>
		</div>
	</li>
</ul>

<p><br/></p>

<h2>Полезные ссылки:</h2>
<ul>
	<li>
		<a href="https://docs.ozon.ru/partners/nachalo-raboty" target="_blank">
			Начало работы
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/partners/" target="_blank">
			Инструкция по работе с маркетплейсом Ozon
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/partners/trebovaniya-k-tovaram/obyazatel-nye-harakteristiki" target="_blank">
			Обязательные характеристики
		</a>
	</li>
	<li>
		<a href="https://seller.ozon.ru/settings/api-keys" target="_blank">
			Получение API ключа
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/global/products/upload/upload-limit/?country=RU" target="_blank">
			Лимиты на загрузку и изменение товаров
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/global/products/upload/adding-content/image-requirements/?country=RU" target="_blank">
			Требования к изображениям
		</a>
	</li>
	<li>
		<a href="https://cb-api.ozonru.me/apiref/ru/#t-title_sandbox" target="_blank">
			Тестовая среда для проверки
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/api/seller/#operation/ProductAPI_ImportProductsV2" target="_blank">
			Документация
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/partners/zagruzka-tovarov/moderatsiya/kritichnye-oshibki" target="_blank">
			Частые ошибки
		</a>
	</li>
	<li>
		<a href="https://api-seller.ozon.ru/docs/#/CategoryAPI/CategoryAPI_GetCategoryAttributes" target="_blank">
			Swaggger (отладка)
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/su" target="_blank">
			Обучение
		</a>
	</li>
</ul>

<p><br/></p>

<h2>Техподдержка Ozon</h2>
<ul>
	<li>
		<a href="https://seller.ozon.ru/app/products?helpCenter=create-issue" target="_blank">
			Задать вопрос в службу поддержки Ozon
		</a>
	</li>
	<li>
		<a href="https://t.me/OZON_marketplace_bot" target="_blank">
			Чат в Telegram
		</a>
	</li>
</ul>

<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';

# General
$MESS[$strLang.'NAME'] = 'Яндекс.Маркет API (FBS, FBY, DBS, Экспресс)';

$MESS[$strLang.'STEP_RESET_OLD_STOCKS'] = 'Сброс старых остатков';

$MESS[$strLang.'EXTERNAL_REQUEST_CHECKBOX'] = 'Разрешить внешний запрос (для /stocks, и при схеме FBS - для /cart)';
	$MESS[$strLang.'EXTERNAL_REQUEST_HINT'] = 'Данная опция позволяет разрешить внешние запросы напрямую к данному профилю.<br/><br/>
	Прежде всего, в личном кабинете Яндекса («Настройки» - «Настройки API») придумайте адрес, на который Яндекс будет отправлять запросы на сайт. Например, <code><b>/yandex_market/api/</b></code><br/><br/>
	Далее, в профиле укажите этот же адрес, но без протокола и без домена, причём на конце адреса должно быть добавлено /stocks. Например, <code><b>/yandex_market/api/stocks</code></b> (обратите внимание: слеша на конце не должно быть)<br/><br/>
	Обратите внимание, что адрес, на который Яндекс будет отправлять запрос оканчивается на /stocks без завершающего слеша. При этом Ваш сайт не должен пытаться добавить слеш к этому адресу - иначе функционал не будет работать.<br/><br/>
	Если у Вас добавление слеша на конце настроено и работает через файл .htaccess (например, строка <code>RewriteRule ^(.*)$ /$1/ [L,R=301]</code>), следует внести изменение: перед строкой, которая непосредственно добавляет слеш на конце, добавить дополнительное условие в соответствии с ранее указанным адресом:
	<code><b>RewriteCond %{REQUEST_URI} !^/yandex_market/api/stocks</b></code>.<br/><br/>
	<b>Обратите внимание</b>: при наличии любой ошибки в файле .htaccess сайт полностью «падает», поэтому если не уверены в том, что делать - обратитесь к специалисту.<br/><br/>
	Информация по остаткам при выгрузке сохраняется отдельно, и именно она отдаётся при запросах от Яндекса. Другими словами, те значения остатков, которые были переданы в последний раз, будут также переданы в ответ на запрос Яндекс.<br/><br/>
	<b>Обратите внимание!</b> Если у Вас используется схема работы FBS, текущий профиль может также отвечать на запросы <code>/cart</code>. В таком случае в поле нужно указать регулярное выражение, например:<br/>
	<code>/yandex_market/api/(stocks|cart)</code> - так модуль будет отвечать и на <code>/stocks</code> и на <code>/cart</code>.
	';
	$MESS[$strLang.'EXTERNAL_REQUEST_URL'] = 'Страница сайта: например, /yandex_market/api/stocks';
	$MESS[$strLang.'EXTERNAL_REQUEST_OFF'] = 'Не забудьте включить опцию «Разрешить внешние обращения к профилям» в <a href="/bitrix/admin/settings.php?lang=#LANGUAGE_ID#&mid=#MODULE_ID#" target="_blank">настройках модуля</a>.';
	$MESS[$strLang.'EXTERNAL_REQUEST_LOG_REQUEST'] = 'Внешний запрос [#URL#]: #JSON#';
	$MESS[$strLang.'EXTERNAL_REQUEST_LOG_RESPONSE'] = 'Ответ на внешний запрос: #JSON#';


$MESS[$strLang.'ERROR_CHECK_CAMPAIGN_ID'] = 'Ошибка проверки идентификатора кампании (статус #STATUS#): #MESSAGE#, headers: #HEADERS#.';

# Popup: businesses
$MESS[$strLang.'POPUP_BUSINESSES_TITLE'] = 'Выберите кабинет';
$MESS[$strLang.'ERROR_GET_BUSINESSES_POPUP'] = 'Ошибка получения списка кабинетов';

# Fields: GENERAL
$MESS[$strHead.'HEADER_GENERAL'] = 'Общие данные';
$MESS[$strName.'shopSku'] = 'shopSku';
	$MESS[$strHint.'shopSku'] = 'SKU товара.<br/><br/>
	Уникальный код, который вы используете для идентификации товара (если не используете — придумайте). Ваш SKU может состоять из:
	<ul>
		<li>цифр;</li>
		<li>латинских букв;</li>
		<li>русских букв (кроме ё);</li>
		<li>символов . , \ / ( ) [ ] - =.</li>
	</ul>
	Максимальная длина — 80 символов. Должен быть уникальным для каждого товара.<br/>
	Обязательный параметр.';
$MESS[$strName.'name'] = 'Название товара';
	$MESS[$strHint.'name'] = 'Составляйте по схеме: что (тип товара) + кто (производитель или бренд) + товар (модель, название) + отличительные характеристики, если есть (например, размер, вес или цвет).<br/><br/>
	Примеры:
	<ul>	
		<li>Утюг Philips GC 2088</li>
		<li>Увелка Хлопья 5 злаков, 350 г</li>
		<li>Комбинезон LEO размер 62, розовый</li>
	</li>';
$MESS[$strName.'category'] = 'Категория товара';
	$MESS[$strHint.'category'] = 'Указывайте специфичную категорию товара, а не общую. Например, для туши для глаз указывайте «Декоративная косметика», а не просто «Косметика».<br/><br/>
	При указании категорий вы можете ориентироваться на каталог Маркета.<br/><br/>
	<b>Внимание!</b> Формально данный атрибут не является обязательным, при выгрузке модуль использует рекомендации Яндекса, пытаясь определить правильную категорию (этот механизм работает при заполнении всех данных товара), однако этот механизм не на 100% надёжен, поэтому указание категории является желательным.';
$MESS[$strName.'manufacturer'] = 'Изготовитель товара';
	$MESS[$strHint.'manufacturer'] = 'Изготовитель товара: компания, которая произвела товар, ее адрес и регистрационный номер (если есть).';
$MESS[$strName.'manufacturerCountries'] = 'Страна';
	$MESS[$strHint.'manufacturerCountries'] = 'Список стран, в которых произведен товар. Должен содержать хотя бы одну, но не больше 5 стран.';
$MESS[$strName.'urls'] = 'URL товара или фото';
	$MESS[$strHint.'urls'] = 'URL фотографии товара или страницы с описанием на вашем сайте.<br/><br/>
	Переданные данные не будут отображаться на витрине, но они помогут специалистам Маркета найти карточку для вашего товара.';
$MESS[$strName.'pictures'] = 'Ссылки на изображения';
	$MESS[$strHint.'pictures'] = 'Ссылки (URL) изображений товара в хорошем качестве.<br/><br/>
	Можно указать до 10 ссылок. При этом изображение по первой ссылке будет основным. Оно используется в качестве изображения товара в поиске Маркета и на карточке товара. Другие изображения товара доступны в режиме просмотра увеличенных изображений.<br/><br/>
	Должен содержать хотя бы один вложенный параметр picture.';
$MESS[$strName.'vendor'] = 'Бренд';
	$MESS[$strHint.'vendor'] = 'Бренд товара.';
$MESS[$strName.'vendorCode'] = 'Артикул';
	$MESS[$strHint.'vendorCode'] = 'Артикул товара от производителя.';
$MESS[$strName.'barcodes'] = 'Штрихкоды';
	$MESS[$strHint.'barcodes'] = 'Штрихкоды товара от производителя.<br/><br/>
	Указывайте в виде последовательности цифр. Подойдут коды:
	<ul>
		<li>EAN-13</li>
		<li>EAN-8</li>
		<li>EAN-8UPC-A</li>
		<li>UPC-E</li>
		<li>Code 128</li>
	</ul>
	Для книг указывайте ISBN. Для товаров <a href="https://yastatic.net/s3/doc-binary/src/support/market/ru/yandex-market-list-for-gtin.xlsx" target="_blank">определенных категорий</a> и торговых марок обязательно передавайте код <a href="https://ru.wikipedia.org/wiki/GTIN" target="_blank">GTIN</a>.<br/><br/>
	Примечание. Внутренние коды, которые начинаются на 2 или 02, не подходят.<br/><br/>
	Пример: <b>46012300000000<b><br/></br>
	Если штрихкодов несколько, напишите все через запятую. Если товар продается упаковками — нужен штрихкод упаковки. Если штрихкода от производителя нет, используйте внутренний штрихкод магазина.';
$MESS[$strName.'description'] = 'Описание товара';
	$MESS[$strHint.'description'] = 'Максимальная длина — 3000 символов.<br/><br/>
	В описании запрещено:
	<ul>
		<li>давать инструкции по применению, установке или сборке</li>
		<li>использовать слова «скидка», «распродажа», «дешевый», «подарок» (кроме подарочных категорий), «бесплатно», «акция», «специальная цена», «новинка», «new», «аналог», «заказ», «хит»</li>
		<li>указывать номера телефонов, адреса электронной почты, почтовые адреса, номера ICQ, логины мессенджеров, любые URL-ссылки</li>
	</ul>';
$MESS[$strName.'customsCommodityCodes'] = 'Код ТН ВЭД';
	$MESS[$strHint.'customsCommodityCodes'] = 'Список кодов товара в <a href="http://www.eurasiancommission.org/ru/act/trade/catr/ett/Pages/default.aspx" target="_blank">единой Товарной номенклатуре внешнеэкономической деятельности</a> (ТН ВЭД).<br/><br/>
	Обязательный параметр, если товар подлежит особому учету (например, в системе «Меркурий» как продукция животного происхождения или в системе «Честный ЗНАК»).<br/><br/>
	Может содержать только один вложенный код ТН ВЭД.';
$MESS[$strName.'certificate'] = 'Номер документа на товар';
	$MESS[$strHint.'certificate'] = 'Перед указанием номера документ нужно загрузить в личном кабинете магазина. Подробнее см. в разделе <a href="https://yandex.ru/support/marketplace/operations/certificates.html" target="_blank">Как загрузить документы на товары</a> Справки Маркета для моделей FBY, FBY+ и FBS.';
$MESS[$strName.'transportUnitSize'] = 'Количество товара в одной упаковке';
	$MESS[$strHint.'transportUnitSize'] = 'Количество единиц товара в одной упаковке, которую вы поставляете на склад.<br/>
	Например, если вы поставляете детское питание коробками по 6 баночек, укажите значение 6.';
$MESS[$strName.'minShipment'] = 'Минимальное количество единиц товара';
	$MESS[$strHint.'minShipment'] = 'Минимальное количество единиц товара, которое вы поставляете на склад.<br/><br/>
	Например, если вы поставляете детское питание партиями минимум по 10 коробок, а в каждой коробке по 6 баночек, укажите значение 60.';
$MESS[$strName.'quantumOfSupply'] = 'Добавочная партия';
	$MESS[$strHint.'quantumOfSupply'] = 'Добавочная партия: по сколько единиц товара можно добавлять к минимальному количеству <code>minShipment</code>.<br/><br/>
	Например, если вы поставляете детское питание партиями минимум по 10 коробок и хотите добавлять к минимальной партии по 2 коробки, а в каждой коробке по 6 баночек, укажите значение 12.';
$MESS[$strName.'supplyScheduleDays'] = 'Дни недели';
	$MESS[$strHint.'supplyScheduleDays'] = 'Дни недели, в которые вы поставляете товары на склад.';
$MESS[$strName.'deliveryDurationDays'] = 'Срок поставки, дн.';
	$MESS[$strHint.'deliveryDurationDays'] = 'Срок, за который вы поставляете товары на склад, в днях.';
$MESS[$strName.'boxCount'] = 'Сколько мест занимает товар';
	$MESS[$strHint.'boxCount'] = 'Сколько мест (если больше одного) занимает товар.<br/><br/>
	Параметр указывается, только если товар занимает больше одного места (например, кондиционер занимает два места: внешний и внутренний блоки в двух коробках). Если товар занимает одно место, не указывайте этот параметр.';

#
$MESS[$strHead.'HEADER_DIMENSIONS'] = 'Вес и габариты с упаковкой';
$MESS[$strName.'weightDimensions.length'] = 'Длина упаковки, см';
	$MESS[$strHint.'weightDimensions.length'] = 'Длина упаковки в см. Можно указывать с точностью до тысячных, разделитель целой и дробной части — точка. Пример: 65.55.';
$MESS[$strName.'weightDimensions.width'] = 'Ширина упаковки, см';
	$MESS[$strHint.'weightDimensions.width'] = 'Ширина упаковки в см. Можно указывать с точностью до тысячных, разделитель целой и дробной части — точка. Пример: 50.7.';
$MESS[$strName.'weightDimensions.height'] = 'Высота упаковки, см';
	$MESS[$strHint.'weightDimensions.height'] = 'Высота упаковки в см. Можно указывать с точностью до тысячных, разделитель целой и дробной части — точка. Пример: 20.0.';
$MESS[$strName.'weightDimensions.weight'] = 'Вес товара, кг';
	$MESS[$strHint.'weightDimensions.weight'] = 'Вес товара в кг с учетом упаковки (брутто). Можно указывать с точностью до тысячных, разделитель целой и дробной части — точка. Пример: 1.001.';

#
$MESS[$strHead.'HEADER_SHELF_LIFE'] = 'Срок годности';
$MESS[$strName.'shelfLife.timePeriod'] = 'Срок годности';
	$MESS[$strHint.'shelfLife.timePeriod'] = 'Срок годности в единицах, указанных в параметре timeUnit.';
$MESS[$strName.'shelfLife.timeUnit'] = 'Единица измерения срока годности:';
	$MESS[$strHint.'shelfLife.timeUnit'] = 'Единица измерения срока годности:
		<ul>
			<li><b>HOUR</b> — часы.</li>
			<li><b>DAY</b> — дни.</li>
			<li><b>WEEK</b> — недели.</li>
			<li><b>MONTH</b> — месяцы.</li>
			<li><b>YEAR</b> — годы.</li>
		</li>';
$MESS[$strName.'shelfLife.comment'] = 'Дополнительные условия';
	$MESS[$strHint.'shelfLife.comment'] = 'Дополнительные условия использования в течение срока годности. Например:<br/><br/>
	<code>Хранить в сухом помещении.</code>';

#
$MESS[$strHead.'HEADER_LIFE_LIFE'] = 'Срок службы';
$MESS[$strName.'lifeTime.timePeriod'] = 'Срок службы';
$MESS[$strHint.'lifeTime.timePeriod'] = 'Срок службы в единицах, указанных в параметре timeUnit.';
$MESS[$strName.'lifeTime.timeUnit'] = 'Единица измерения срока службы:';
$MESS[$strHint.'lifeTime.timeUnit'] = 'Единица измерения срока службы:
	<ul>
		<li><b>HOUR</b> — часы.</li>
		<li><b>DAY</b> — дни.</li>
		<li><b>WEEK</b> — недели.</li>
		<li><b>MONTH</b> — месяцы.</li>
		<li><b>YEAR</b> — годы.</li>
	</li>';
$MESS[$strName.'lifeTime.comment'] = 'Дополнительные условия';
$MESS[$strHint.'lifeTime.comment'] = 'Дополнительные условия использования в течение срока службы. Например:<br/><br/>
	<code>Использовать при температуре не ниже -10 градусов.</code>';

#
$MESS[$strHead.'HEADER_GUARANTEE_PERIOD'] = 'Гарантия и документы';
$MESS[$strName.'guaranteePeriod.timePeriod'] = 'Гарантийный срок';
$MESS[$strHint.'guaranteePeriod.timePeriod'] = 'Гарантийный срок в единицах, указанных в параметре timeUnit.';
$MESS[$strName.'guaranteePeriod.timeUnit'] = 'Единица измерения гарантийного срока';
$MESS[$strHint.'guaranteePeriod.timeUnit'] = 'Единица измерения гарантийного срока:
	<ul>
		<li><b>HOUR</b> — часы.</li>
		<li><b>DAY</b> — дни.</li>
		<li><b>WEEK</b> — недели.</li>
		<li><b>MONTH</b> — месяцы.</li>
		<li><b>YEAR</b> — годы.</li>
	</li>';
$MESS[$strName.'guaranteePeriod.comment'] = 'Дополнительные условия';
$MESS[$strHint.'guaranteePeriod.comment'] = 'Дополнительные условия гарантии. Например:<br/><br/>
	<code>Гарантия на аккумулятор — 6 месяцев.</code>';

#
$MESS[$strHead.'HEADER_MAPPING'] = 'Информация о карточке товара на Маркете';
$MESS[$strName.'mapping.marketSku'] = 'SKU на Маркете';
	$MESS[$strHint.'mapping.marketSku'] = 'SKU на Маркете — идентификатор (число) карточки товара на Маркете.<br/><br/>
		Используя данное поле, можно прямо указать Яндекс.Маркету с какой уже существующей карточкой следует связать данный товар.
		Вручную можно определить, например, по GET-параметру <code>sku</code> на странице товара в Яндекс.Маркете.<br/><br/>
		Если данное поле не заполнено, модуль использует рекомендации Яндекс.Маркета для его определения. Если поле не заполнено, и при определении рекомендации результата нет, выгруженный товар будет привязан к категории при модерации.';

#
$MESS[$strHead.'HEADER_BASE_PRICES'] = 'Базовые цены';
$MESS[$strName.'baseprice.value'] = 'Базовая цена на товар, руб';
	$MESS[$strHint.'baseprice.value'] = 'Базовая цена на товар. Передаваемое значение должно быть больше 0';
$MESS[$strName.'baseprice.discountBase'] = 'Базовая цена до скидки, руб';
	$MESS[$strHint.'baseprice.discountBase'] = 'Цена до скидки должна быть больше цены со скидкой. Подробнее см. <a href="https://yandex.ru/support/marketplace/promo/shop-sales.html#shop" target="_blank">требования к скидкам</a> в Справке Маркета для моделей FBY, FBY+ и FBS.';
#
$MESS[$strHead.'HEADER_PRICES'] = 'Цены магазина';
$MESS[$strName.'price.value'] = 'Цена на товар, руб';
	$MESS[$strHint.'price.value'] = 'Цена на товар. Передаваемое значение должно быть больше 0';
$MESS[$strName.'price.discountBase'] = 'Цена до скидки, руб';
	$MESS[$strHint.'price.discountBase'] = 'Цена до скидки должна быть больше цены со скидкой. Подробнее см. <a href="https://yandex.ru/support/marketplace/promo/shop-sales.html#shop" target="_blank">требования к скидкам</a> в Справке Маркета для моделей FBY, FBY+ и FBS.';
$MESS[$strName.'price.vat'] = 'Ставка НДС (идентификатор)';
	$MESS[$strHint.'price.vat'] = 'Идентификатор ставки НДС, применяемой для товара:
	<ul>
		<li><b>2</b> — 10%</li>
		<li><b>5</b> — 0%</li>
		<li><b>6</b> — не облагается НДС</li>
		<li><b>7</b> — 20%</li>
	</ul>
	Если параметр не указан, используется ставка НДС, установленная в личном кабинете магазина.<br/><br/>
	В качестве поля удобно выбрать:<br><code><b>Величина НДС в формате Яндекс.API (7, 2, 5, 6)</b></code>.';

# Stocks (custom values)
$MESS[$strLang.'HEADER_STOCKS'] = 'Остаток на складе [#ID#] #NAME#';
$MESS[$strLang.'STOCK_TYPE'] = 'Тип остатка';
$MESS[$strLang.'STOCK_COUNT'] = 'Остаток (число)';
$MESS[$strLang.'STOCK_UPDATET_AT'] = 'Дата обновления остатка';

# Messages
$MESS[$strLang.'EXPORT_CARDS_SUCCESS'] = 'Карточки успешно выгружены (всего #COUNT#; SKU=#SKUS#).';
$MESS[$strLang.'EXPORT_CARDS_ERROR_TITLE'] = 'Ошибка выгрузки карточек товаров';
$MESS[$strLang.'EXPORT_CARDS_ERROR'] = 'Карточки НЕ выгружены из-за ошибки (всего #COUNT#; SKU=#SKUS#; метод: #METHOD#): #TEXT#.';
#
$MESS[$strLang.'EXPORT_BASE_PRICES_SUCCESS'] = 'Базовые цены успешно выгружены (всего #COUNT#; SKU=#SKUS#; /businesses/{businessId}/offer-prices/updates).';
$MESS[$strLang.'EXPORT_BASE_PRICES_ERROR_TITLE'] = 'Ошибка выгрузки базовых цен (/businesses/{businessId}/offer-prices/updates)';
$MESS[$strLang.'EXPORT_BASE_PRICES_ERROR'] = 'Базовые цены НЕ выгружены из-за ошибки (всего #COUNT#; SKU=#SKUS#; /businesses/{businessId}/offer-prices/updates): #TEXT#.';
#
$MESS[$strLang.'EXPORT_SHOP_PRICES_SUCCESS'] = 'Цены магазина успешно выгружены (всего #COUNT#; SKU=#SKUS#; /v2/campaigns/{campaignId}/offer-prices/updates.json).';
$MESS[$strLang.'EXPORT_SHOP_PRICES_ERROR_TITLE'] = 'Ошибка выгрузки цен магазина (/v2/campaigns/{campaignId}/offer-prices/updates.json)';
$MESS[$strLang.'EXPORT_SHOP_PRICES_ERROR'] = 'Цены магазина НЕ выгружены из-за ошибки (всего #COUNT#; SKU=#SKUS#; /v2/campaigns/{campaignId}/offer-prices/updates.json): #TEXT#.';
#
$MESS[$strLang.'EXPORT_STOCKS_SUCCESS'] = 'Остатки успешно выгружены (всего #COUNT#; SKU=#SKUS#).';
$MESS[$strLang.'EXPORT_STOCKS_ERROR_TITLE'] = 'Ошибка выгрузки остатков';
$MESS[$strLang.'EXPORT_STOCKS_ERROR'] = 'Остатки НЕ выгружены из-за ошибки (всего #COUNT#; SKU=#SKUS#): #TEXT#';
#
$MESS[$strLang.'ERROR_JSON'] = 'Ошибка JSON: #ERROR#, #JSON#.';

# Misc
$MESS[$strLang.'DELAY_LOG'] = 'Задержка: #TIME#с';

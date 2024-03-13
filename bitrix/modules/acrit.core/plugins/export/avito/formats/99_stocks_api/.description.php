<h2>Выгрузка остатков на Avito (по API)</h2>
<p>Данный формат используется для выгрузки информации по остаткам на Avito с использованием API.</p>
<p><br/></p>

<h2>Начало работы</h2>
<p>Прежде всего, необходимо проверить, что в Вашем личном кабинете Avito подключён платный тариф, т.к. выгрузка остатков иначе не будет работать.</p>
<p>Далее, в личном кабинете необходимо <a href="https://developers.avito.ru/applications" target="_blank">создать приложение для авторизации</a> (если ещё не создано), и скопировать значения полей <code>Client_id</code> и <code>Сlient_secret</code> в соответствующие поля профиля в модуле экспорта, после этого проверьте корректность данных, нажав кнопку <code>Проверить доступ</code>.</p>
<p><br/></p>

<h2>Заполнение полей</h2>
<p>Документация предусматривает передачу одного из двух идентификаторов товара - произвольный (собственный) идентификатор товара на сайте (тот, который указывается при выгрузке объявлений в XML) - <code>external_id</code>, либо числовой номер объявления Авито - <code>item_id</code>.</p>
<p>Для корректной передачи остатка одно из них должно быть заполнено.</p>
<p>В случае, если заполнены оба, приоритет отдаётся второму, т.е. Авито будет искать товар по <code>item_id</code>.</p>
<p>В большинстве случаев наиболее удобный способ - по собственному идентификатору (это то значение, которое при XML-выгрузках передаёте в поле «Идентификатор объявления»), поэтому рекомендуем использовать его, а второй оставлять незаполненным.</p>
<p><br/></p>

<h2>Полезные ссылки:</h2>
<ul>
	<li>
		<a href="https://developers.avito.ru/api-catalog/auth/documentation" target="_blank">
			Документация: авторизация
		</a>
	</li>
	<li>
		<a href="https://developers.avito.ru/api-catalog/stock-management/documentation" target="_blank">
			Документация: выгрузка остатков
		</a>
	</li>
</ul>
<p><br/></p>

<h2>Как выбрать тариф для бизнеса на Авито</h2>
<div style="max-width:720px;">
	<div style="position:relative;height:0;padding-bottom:56.25%;">
		<div style="height:100%;left:0;position:absolute;top:0;width:100%;">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/Ng_mTFVCpms" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="border:0;height:100%;width:100%;"></iframe>
		</div>
	</div>
</div>
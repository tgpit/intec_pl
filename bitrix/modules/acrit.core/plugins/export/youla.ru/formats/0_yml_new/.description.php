<style>
#tr_PLUGIN_DESCRIPTION code {
  background:#ddd;
  border:1px solid #ccc;
  border-radius:4px;
	color:maroon;
  padding:0 4px;
}
</style>

<h2>Выгрузка объявлений на сайт Юла</h2>
<p>Данный формат используется для выгрузки объявлений на сайт Юла (<a href="youla.ru" target="_blank">youla.ru</a>). В одном файле можно выгружать объявления из любых категорий одновременно.</p>
<p>При этом данный формат, как и многие другие, имеет свои особенности, которые необходимо учитывать для успешной выгрузки.</p>
<p><br/></p>

<h2>Категории</h2>
<p>На Юле нестандартный подход к работе с категориями: здесь для каждого товара мы обязаны указывать сразу два числовых параметра: идентификатор категории и идентификатор подкатегории (значения идентификаторов можно увидеть либо в документации, либо при выборе категорий в модуле).</p>
<p>В профиле имеется два подхода к указанию категорий для товаров - обычный и альтернативный. Обычный лучше подойдёт для случаев, когда категории сайта примерно соответствуют категориям Юлы. В ином случае удобнее будет использовать альтернативный способ, в этом случае необходимо наличие указанной категории где-либо в товаре, либо непосредственно в поле профиля (можно использовать условия и мультиусловия). При этом для каждого товара в поле <code>categoryId</code> (это служебное поле, которое само по себе никак не выгружается) должно передаваться значения идентификатора категории в одном из двух вариантов: либо число, означающее идентификатор подкатегории, либо полное название категории, например "[1504] Компьютерная техника / Ноутбуки".</p>
<p>Если для товара категория указана правильно, модуль автоматически определяет значения для <code>youlaCategoryId</code> и <code>youlaSubcategoryId</code> и подставляет их в XML.</p>
<p><br/></p>

<h2>Характеристики и значения</h2>
<p>Выбор категорий осуществляется для привязки дополнительных характеристик (атрибутов) к каждому объявлению. Эти характеристики появляются в профиле после выбора категорий для выгрузки (в обычном режиме) либо после выбора категорий альтернативного выбора. Желательно заполнить все поля, которые могут быть полезны пользователям на сервисе.</p>
<p>Для каждого товара при выгрузке определяется категория, и на основе неё в XML товара добавляются дополнительные характеристики, относящиеся к этой категории.</p>
<p>Например, если для выгрузки настроено два товара - футболка и ноутбук, у них должна быть правильно указана категория, и после выгрузки в футболке будут основные поля и характеристики только футболок, в ноутбуке - основные поля и характеристики только ноутбуков.</p>
<p>Все характеристики имеют собственный список допустимых значений, которые должны выгружаться в модуль текстовом виде. При этом, Юла требует замены текстовых значений на соответствующие им числовые, эту работу выполняет сам модуль. Главное условие здесь - корректно переданные значения (список доступных значений можно посмотреть, если нажать по жёлтой иконке с восклицательным знаком напротив каждого поля).</p>
<p><br/></p>

<h2>Публикация</h2>
<p>После настройки профиля запустите выгрузку, проверьте полученные данные в XML, и если всё правильно - в <a href="https://youla.ru/user/" target="_blank">личном кабинете Юлы</a> следует загрузить данный файл, скопировав ссылку на него.</p>
<p><br/></p>

<h2>Полезные ссылки:</h2>
<ul>
	<li>
		<a href="https://youla.ru/user/" target="_blank">
			Личный кабинет
		</a>
	</li>
	<li>
		<a href="https://docs.google.com/document/d/1_zBRRCNoM7uxe6xPHn5ztTFi55ANqjKKDA3XM1MvLEc/edit" target="_blank">
			Документация по выгрузке
		</a>
	</li>
</ul>
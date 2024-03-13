<?php

use intec\core\bitrix\iblock\tags\MorphologyTag;

$MESS['title.add'] = 'Добавить условие фильтра';
$MESS['title.edit'] = 'Редактировать условие фильтра';
$MESS['title.copy'] = 'Копирование условия фильтра';
$MESS['errors.generate.source'] = 'Ошибка генерации адресов. Не задан шаблон исходного адреса Url';
$MESS['errors.generate.target'] = 'Ошибка генерации адресов. Не задан шаблон целевого адреса Url';
$MESS['tabs.common'] = 'Общее';
$MESS['tabs.meta'] = 'Meta-информация';
$MESS['tabs.tags'] = 'Тэги';
$MESS['tabs.generation'] = 'Генерация';
$MESS['tabs.urls'] = 'Адреса';
$MESS['tabs.autofill'] = 'Автонаполнение товарами';
$MESS['tabs.autofill.articles'] = 'Автонаполнение статьями';
$MESS['panel.actions.back'] = 'Список';
$MESS['panel.actions.add'] = 'Добавить';
$MESS['panel.actions.copy'] = 'Копировать';
$MESS['panel.actions.generateUrl'] = 'Генерировать адреса';
$MESS['panel.actions.showUrl'] = 'Просмотреть адреса';
$MESS['fields.name.copy'] = 'Копия';
$MESS['fields.sites'] = 'Сайты';
$MESS['fields.articles'] = 'Статьи';
$MESS['fields.articles.add'] = 'Добавить ...';
$MESS['fields.sections'] = 'Разделы';
$MESS['fields.tagRelinkingConditions'] = 'Условия, на которые идет перелинковка';
$MESS['fields.urlName.macros.iblock.name'] = 'Поля инфоблока';
$MESS['fields.urlName.macros.iblock.items.iblockId'] = 'Идентификатор инфоблока';
$MESS['fields.urlName.macros.iblock.items.iblockCode'] = 'Символьный код инфоблока';
$MESS['fields.urlName.macros.iblock.items.iblockTypeId'] = 'Тип инфоблока';
$MESS['fields.urlName.macros.iblock.items.iblockName'] = 'Наименование инфоблока';
$MESS['fields.urlName.macros.iblock.items.iblockExternalId'] = 'Внешний код инфоблока';
$MESS['fields.urlName.macros.section.name'] = 'Поля раздела';
$MESS['fields.urlName.macros.section.items.sectionId'] = 'Идентификатор раздела';
$MESS['fields.urlName.macros.section.items.sectionCode'] = 'Символьный код раздела';
$MESS['fields.urlName.macros.section.items.sectionCodePath'] = 'Путь из символьных кодов раздела';
$MESS['fields.urlName.macros.section.items.sectionName'] = 'Наименование раздела';
$MESS['fields.urlName.macros.section.items.sectionExternalId'] = 'Внешний код раздела';
$MESS['fields.urlName.macros.property.name'] = 'Поля свойств';
$MESS['fields.urlName.macros.property.items.propertiesId'] = 'Идентификаторы свойств';
$MESS['fields.urlName.macros.property.items.propertiesCode'] = 'Коды свойств';
$MESS['fields.urlName.macros.property.items.propertiesName'] = 'Наименования свойств';
$MESS['fields.urlName.macros.property.items.propertiesCombination'] = 'Комбинация имен и значений свойств';
$MESS['fields.urlSource.description'] = 'Шаблон исходного адреса Url используется для генерации исходного адреса Url. Должен совпадать с адресом фильтруемого раздела каталога.<br /><br />
В шаблон можно вставлять определенные макросы.<br /><br />
Пример для генератора <b>Битрикс (Параметры в запросе)</b>:<br /><b>/catalog/?SECTION_ID=#SECTION_ID#&#SMART_FILTER_PATH#</b><br /><br />
Пример для генератора <b>Битрикс (ЧПУ)</b>:<br /><b>/catalog/#SECTION_ID#/filter/#SMART_FILTER_PATH#/apply/</b>';
$MESS['fields.urlSource.macros.iblock.name'] = 'Поля инфоблока';
$MESS['fields.urlSource.macros.iblock.items.iblockId'] = 'Идентификатор инфоблока';
$MESS['fields.urlSource.macros.iblock.items.iblockCode'] = 'Символьный код инфоблока';
$MESS['fields.urlSource.macros.iblock.items.iblockTypeId'] = 'Тип инфоблока';
$MESS['fields.urlSource.macros.iblock.items.iblockName'] = 'Наименование инфоблока';
$MESS['fields.urlSource.macros.iblock.items.iblockExternalId'] = 'Внешний код инфоблока';
$MESS['fields.urlSource.macros.section.name'] = 'Поля раздела';
$MESS['fields.urlSource.macros.section.items.id'] = 'Идентификатор раздела';
$MESS['fields.urlSource.macros.section.items.sectionId'] = 'Идентификатор раздела (2)';
$MESS['fields.urlSource.macros.section.items.code'] = 'Символьный код раздела';
$MESS['fields.urlSource.macros.section.items.sectionCode'] = 'Символьный код раздела (2)';
$MESS['fields.urlSource.macros.section.items.sectionCodePath'] = 'Путь из символьных кодов раздела';
$MESS['fields.urlSource.macros.section.items.externalId'] = 'Внешний код раздела';
$MESS['fields.urlTarget.description'] = 'Шаблон целевого адреса Url используется для генерации целевого адреса Url. Исходная страница будет доступна по данному адресу. Клиента будет автоматически перенаправлять <b>(301 Moved Permanently)</b> с исходного адреса страницы на целевой.<br /><br />
В шаблон можно вставлять определенные макросы. Помимо макросов также можно вставить шаблон для генерации свойств. Макросы для свойств доступны только в шаблоне свойств. Шаблон свойств заключен в фигурные скобки <b>{Шаблон свойства:Разделитель свойств:Разделитель значений свойства}</b>, разделен на 3 секции с помощью символа <b>:</b>.
Первая секция шаблона свойств отвечает за шаблон одного свойства в адресе <b>(Пример: #PROPERTY_CODE#_#PROPERTY_VALUE#)</b>, вторая секция отвечает за то, как будут разделяться свойства <b>(Пример: /)</b> и третья секция отвечает за то, как будут разделяться значения свойства, если их несколько <b>(Пример: -)</b>.<br /><br />
Пример шаблона свойств:<br /><b>{#PROPERTY_CODE#_#PROPERTY_VALUE#:/:-}</b><br /><br />
Пример шаблона:<br /><b>/catalog/#SECTION_ID#/{#PROPERTY_CODE#_#PROPERTY_VALUE#:/:-}/</b>';
$MESS['fields.urlTarget.macros.iblock.name'] = 'Поля инфоблока';
$MESS['fields.urlTarget.macros.iblock.items.iblockId'] = 'Идентификатор инфоблока';
$MESS['fields.urlTarget.macros.iblock.items.iblockCode'] = 'Символьный код инфоблока';
$MESS['fields.urlTarget.macros.iblock.items.iblockTypeId'] = 'Тип инфоблока';
$MESS['fields.urlTarget.macros.iblock.items.iblockName'] = 'Наименование инфоблока';
$MESS['fields.urlTarget.macros.iblock.items.iblockExternalId'] = 'Внешний код инфоблока';
$MESS['fields.urlTarget.macros.section.name'] = 'Поля раздела';
$MESS['fields.urlTarget.macros.section.items.id'] = 'Идентификатор раздела';
$MESS['fields.urlTarget.macros.section.items.sectionId'] = 'Идентификатор раздела (2)';
$MESS['fields.urlTarget.macros.section.items.code'] = 'Символьный код раздела';
$MESS['fields.urlTarget.macros.section.items.sectionCode'] = 'Символьный код раздела (2)';
$MESS['fields.urlTarget.macros.section.items.sectionCodePath'] = 'Путь из символьных кодов раздела';
$MESS['fields.urlTarget.macros.section.items.externalId'] = 'Внешний код раздела';
$MESS['fields.urlTarget.macros.property.name'] = 'Поля свойств';
$MESS['fields.urlTarget.macros.property.items.propertyId'] = 'Идентификатор свойства';
$MESS['fields.urlTarget.macros.property.items.propertyCode'] = 'Код свойства';
$MESS['fields.urlTarget.macros.property.items.propertyValue'] = 'Значение(я) свойства';
$MESS['fields.urlTarget.macros.additional.name'] = 'Дополнительные поля';
$MESS['fields.urlTarget.macros.additional.items.ranges'] = 'Диапазоны чисел';
$MESS['fields.urlTarget.macros.additional.items.prices'] = 'Цены';
$MESS['fields.urls'] = 'Адреса';
$MESS['fields.urls.fields.id'] = 'ID';
$MESS['fields.urls.fields.active'] = 'Активность';
$MESS['fields.urls.fields.name'] = 'Наименование';
$MESS['fields.urls.fields.source'] = 'Оригинальный адрес';
$MESS['fields.urls.fields.target'] = 'Назначаемый адрес';
$MESS['fields.urls.fields.dateCreate'] = 'Дата создания';
$MESS['fields.urls.fields.iBlockElementsCount'] = 'Количество элементов инфоблока';
$MESS['fields.urls.fields.debugMetaTitle'] = 'Meta заголовок (Отладка)';
$MESS['fields.urls.fields.debugMetaKeywords'] = 'Meta ключевые слова (Отладка)';
$MESS['fields.urls.fields.debugMetaDescription'] = 'Meta описание (Отладка)';
$MESS['fields.urls.fields.debugMetaPageTitle'] = 'Заголовок страницы (Отладка)';
$MESS['fields.urls.fields.sort'] = 'Сортировка';
$MESS['fields.urls.buttons.activate'] = 'Активировать';
$MESS['fields.urls.buttons.deactivate'] = 'Деактивировать';
$MESS['fields.urls.buttons.delete'] = 'Удалить';
$MESS['fields.urls.buttons.show'] = 'Просмотреть';
$MESS['fields.urls.messages.empty'] = 'Адресов нет';
$MESS['fields.autofill'] = 'Автонаполнение';
$MESS['fields.autofill.filling'] = 'Заполняющие разделы';
$MESS['conditions.operators.and'] = 'И';
$MESS['conditions.operators.or'] = 'ИЛИ';
$MESS['conditions.results.not'] = 'НЕ';
$MESS['conditions.items.operators.and'] = 'Все условия';
$MESS['conditions.items.operators.or'] = 'Одно из условий';
$MESS['conditions.items.results.true'] = 'Выполнено(ы)';
$MESS['conditions.items.results.false'] = 'Не выполнено(ы)';
$MESS['conditions.items.add'] = 'Добавить условие';
$MESS['conditions.items.add.caption'] = 'Выберите условие ...';
$MESS['conditions.items.add.cancel'] = 'Отменить';
$MESS['conditions.items.remove'] = 'Удалить';
$MESS['conditions.items.types.group'] = 'Группа условий';
$MESS['conditions.items.types.section'] = 'Раздел';
$MESS['conditions.items.types.section.operators.equal'] = 'Равен';
$MESS['conditions.items.types.section.operators.notEqual'] = 'Не равен';
$MESS['conditions.items.types.section.select'] = 'Выберите раздел ...';
$MESS['conditions.items.types.property'] = 'Свойство';
$MESS['conditions.items.types.property.operators.equal'] = 'Равно';
$MESS['conditions.items.types.property.operators.notEqual'] = 'Не равно';
$MESS['conditions.items.types.property.operators.less'] = 'Меньше';
$MESS['conditions.items.types.property.operators.lessOrEqual'] = 'Меньше или равно';
$MESS['conditions.items.types.property.operators.more'] = 'Больше';
$MESS['conditions.items.types.property.operators.moreOrEqual'] = 'Больше или равно';
$MESS['conditions.items.types.property.operators.contain'] = 'Содержит';
$MESS['conditions.items.types.property.operators.notContain'] = 'Не содержит';
$MESS['conditions.items.types.price.minimal'] = 'Минимальное значение цены';
$MESS['conditions.items.types.price.maximal'] = 'Максимальное значение цены';
$MESS['conditions.items.types.price.filteredMinimal'] = 'Минимальное фильтруемое значение цены';
$MESS['conditions.items.types.price.filteredMaximal'] = 'Максимальное фильтруемое значение цены';
$MESS['answers.unset'] = 'Не выбрано';
$MESS['answers.yes'] = 'Да';
$MESS['answers.no'] = 'Нет';
$MESS['notes.fields'] = '<b><span class="required" style="vertical-align: super; font-size: smaller;">1</span></b> - Условие будет доступно для поиска, на каждую страницу фильтра будет сгенерировано название изходя из поля <b>Заголовок в поиске</b>, а если оно пустое, то из поля <b>Meta заголовок</b>. Телом поисковой записи служат поля: <b>Верхнее описание</b>, <b>Нижнее описание</b>, <b>Дополнительное описание</b>. По заголовку поисковой записи и телу поисковой записи модуль поиска при совпадении поискового запроса будет формировать результат.<br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">2</span></b> - Условие будет устанавливать мета-заголовок <b>robots</b> страницы фильтра для того, чтобы указать поисковым роботам необходимость индексировать данную страницу.<br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">3</span></b> - Условие будет выполняться только если в фильтре установлены значения свойств, которые указаны в правилах. Если будут установлены значения ещё и других свойств, то условие выполняться не будет.<br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">4</span></b> - Условие будет учитывать товары из подразделов раздела.<br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">5</span></b> - Используется в карте сайта для указания приоритета страницам по отношению к другим страницам.<br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">6</span></b> - Используется в карте сайта для указания частоты изменения страницы.<br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">7</span></b> - Определяет алгоритм работы тегов на странице фильтра.<br />
<ul>
    <li><b>Текущий раздел</b> - Генерирует все возможные комбинации фильтра для текущей страницы фильтра, без переходов на другие разделы.</li>
    <li><b>Текущий раздел и подразделы</b> - Генерирует все возможные комбинации фильтра для текущей страницы фильтра и подразделы текущего раздела.</li>
    <li><b>Все разделы условия</b> - Генерирует все возможные комбинации фильтра для всех разделов этого условия.</li>
    <li><b>Привязанные условия</b> - Генерирует все возможные комбинации фильтра для всех привязанных условий к этому условию.</li>
    <li><b>Все</b> - Генерирует все возможные комбинации фильтра для всех разделов этого условия и всех привязанных условий к этому условию.</li>
</ul><br />
<b><span class="required" style="vertical-align: super; font-size: smaller;">8</span></b> - Комбинации фильтра не будут генерироваться для текущего раздела (только другие разделы), если выполняется строгое условие.<br />';
$MESS['notes.macros'] = 'Пример для мета-информации:<br />
<br />
<b>{=this.Name} из {=morphology {=concat {=filterProperty "PROCREATOR"} ", "} "РД"}</b><br />
<br />
Дополнительные операторы, доступные в мета-информации и тегах:<br />
<ul>
    <li><b>{=lower argument1 argument2 ... argumentN}</b> - Оператор прведения к нижнему регистру. Приводит все регистрозависимые символы, передающиеся в аргументах, к нижнему регистру;</li>
    <li><b>{=upper argument1 argument2 ... argumentN}</b> - Оператор прведения к верхнему регистру. Приводит все регистрозависимые символы, передающиеся в аргументах, к верхнему регистру;</li>
    <li><b>{=concat argument1 argument2 ... argumentN "разделитель"}</b> - Оператор сложения строк с использованием разделителя. Объединяет все аргументы в строку, устанавливая между ними разделитель, указанный аргументом <b>"разделитель"</b>. Разделитель может иметь любую длину и содержать любые символы;</li>
    <li><b>{=limit argument1 argument2 ... argumentN "разделитель" "длина"}</b> - Оператор ограничения с использованием разделителя. Объединяет все аргументы в строку, устанавливая между ними разделитель, указанный аргументом <b>"разделитель"</b> и устанавливает максимальную длину из аргумента <b>"длина"</b>. Разделитель может иметь любую длину и содержать любые символы;</li>
    <li><b>{=translit argument1 argument2 ... argumentN}</b> - Оператор транслитерации. Производит транслитерацию аргументов;</li>
    <li><b>{=min argument1 argument2 ... argumentN}</b> - Оператор выборки минимального числа. Выбирает минимальное число из аргументов;</li>
    <li><b>{=max argument1 argument2 ... argumentN}</b> - Оператор выборки максимального числа. Выбирает максимальное число из аргументов;</li>
    <li><b>{=distinct argument1 argument2 ... argumentN}</b> - Оператор выборки уникальных значений. Выбирает уникальные значения из аргументов;</li>
    <li><b>{=morphology argument1 argument2 ... argumentN "модификаторы"}</b> - Оператор обработки морфологии. Конвертирует слова в зависимости от модификаторов, устанавливаемых аргументом <b>"модификаторы"</b>;</li>
</ul><br />
Оператор обработки морфологии (<b>morphology</b>)<br />
<br />
Оператор морфологии может иметь 3 типа модификаторов, которые указываются через пробел. При использовании всех типов модификаторы будут выглядеть следующим образом: <b>"род число падеж"</b>. Итоговые слова будут изменены в зависимости от модификаторов.<br />
<br />
Возможные значения модификатора <b>род</b>:
<ul>';

$kinds = MorphologyTag::getKinds();

foreach ($kinds as $key => $value)
    $MESS['notes.macros'] .= '<li><b>'.$key.'</b> - '.$value.'</li>';

$MESS['notes.macros'] .= '</ul>Возможные значения модификатора <b>число</b>:
<ul>';

$multipliers = MorphologyTag::getMultipliers();

foreach ($multipliers as $key => $value)
    $MESS['notes.macros'] .= '<li><b>'.$key.'</b> - '.$value.'</li>';

$MESS['notes.macros'] .= '</ul>
Возможные значения модификатора <b>падеж</b>:
<ul>';

$cases = MorphologyTag::getCases();

foreach ($cases as $key => $value)
    $MESS['notes.macros'] .= '<li><b>'.$key.'</b> - '.$value.'</li>';

$MESS['notes.macros'] .= '</ul>';


$MESS['debug.title'] = 'Отладчик фильтра';
$MESS['debug.panel.status.title'] = 'Прогресс';
$MESS['debug.panel.status.messages.configuring'] = 'Подготовка';
$MESS['debug.panel.status.messages.progress'] = 'Обработано #count# из #total#';
$MESS['debug.panel.status.messages.clearing'] = 'Очистка ...';
$MESS['debug.panel.status.messages.complete'] = 'Завершено';
$MESS['debug.panel.status.messages.error'] = 'В процессе возникла ошибка';
$MESS['debug.panel.control.option.count'] = 'Количество операций за раз';
$MESS['debug.panel.control.button.start'] = 'Запустить';
$MESS['debug.panel.control.button.clear'] = 'Очистить';
$MESS['debug.panel.control.button.stop'] = 'Остановить';
$MESS['debug.filter.fields.urlId'] = 'Адрес фильтра (Идентификатор)';
$MESS['debug.filter.fields.urlSource'] = 'Оригинальный адрес';
$MESS['debug.filter.fields.urlTarget'] = 'Назначаемый адрес';
$MESS['debug.filter.fields.status'] = 'Статус';
$MESS['debug.list.headers.urlId'] = 'Адрес фильтра';
$MESS['debug.list.headers.urlSource'] = 'Оригинальный адрес';
$MESS['debug.list.headers.urlTarget'] = 'Назначаемый адрес';
$MESS['debug.list.headers.date'] = 'Дата';
$MESS['debug.list.headers.status'] = 'Статус';
$MESS['debug.list.headers.metaTitle'] = 'Meta заголовок';
$MESS['debug.list.headers.metaKeywords'] = 'Meta ключевые слова';
$MESS['debug.list.headers.metaDescription'] = 'Meta описание';
$MESS['debug.list.headers.metaPageTitle'] = 'Заголовок страницы';
$MESS['debug.list.rows.actions.history'] = 'История';
$MESS['debug.list.navigation.title'] = 'Результаты сканирования';
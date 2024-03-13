<?php

use intec\core\bitrix\iblock\tags\MorphologyTag;

$MESS['title.add'] = 'Добавить генератор условий фильтра';
$MESS['title.edit'] = 'Редактировать генератор условий фильтра';
$MESS['errors.generate'] = 'Возникла ошибка при генерации условий.';
$MESS['tabs.common'] = 'Общее';
$MESS['tabs.meta'] = 'Meta-информация';
$MESS['tabs.tags'] = 'Тэги';
$MESS['tabs.generation'] = 'Генерация';
$MESS['panel.actions.back'] = 'Список';
$MESS['panel.actions.add'] = 'Добавить';
$MESS['panel.actions.generate'] = 'Генерировать условия';
$MESS['fields.sites'] = 'Сайты';
$MESS['fields.sections'] = 'Разделы';
$MESS['fields.conditionName.macros.sites.name'] = 'Сайты';
$MESS['fields.conditionName.macros.sites.items.id'] = 'Идентификаторы сайтов';
$MESS['fields.conditionName.macros.sites.items.name'] = 'Наименования сайтов';
$MESS['fields.conditionName.macros.sections.name'] = 'Разделы';
$MESS['fields.conditionName.macros.sections.items.id'] = 'Идентификаторы разделов';
$MESS['fields.conditionName.macros.sections.items.name'] = 'Наименования разделов';
$MESS['fields.conditionName.macros.properties.name'] = 'Свойства';
$MESS['fields.conditionName.macros.properties.items.id'] = 'Идентификаторы свойств';
$MESS['fields.conditionName.macros.properties.items.name'] = 'Наименования свойств';

$MESS['fields.conditionUrlName.macros.iblock.name'] = 'Поля инфоблока';
$MESS['fields.conditionUrlName.macros.iblock.items.iblockId'] = 'Идентификатор инфоблока';
$MESS['fields.conditionUrlName.macros.iblock.items.iblockCode'] = 'Символьный код инфоблока';
$MESS['fields.conditionUrlName.macros.iblock.items.iblockTypeId'] = 'Тип инфоблока';
$MESS['fields.conditionUrlName.macros.iblock.items.iblockName'] = 'Наименование инфоблока';
$MESS['fields.conditionUrlName.macros.iblock.items.iblockExternalId'] = 'Внешний код инфоблока';
$MESS['fields.conditionUrlName.macros.section.name'] = 'Поля раздела';
$MESS['fields.conditionUrlName.macros.section.items.sectionId'] = 'Идентификатор раздела';
$MESS['fields.conditionUrlName.macros.section.items.sectionCode'] = 'Символьный код раздела';
$MESS['fields.conditionUrlName.macros.section.items.sectionCodePath'] = 'Путь из символьных кодов раздела';
$MESS['fields.conditionUrlName.macros.section.items.sectionName'] = 'Наименование раздела';
$MESS['fields.conditionUrlName.macros.section.items.sectionExternalId'] = 'Внешний код раздела';
$MESS['fields.conditionUrlName.macros.property.name'] = 'Поля свойств';
$MESS['fields.conditionUrlName.macros.property.items.propertiesId'] = 'Идентификаторы свойств';
$MESS['fields.conditionUrlName.macros.property.items.propertiesCode'] = 'Коды свойств';
$MESS['fields.conditionUrlName.macros.property.items.propertiesName'] = 'Наименования свойств';
$MESS['fields.conditionUrlName.macros.property.items.propertiesCombination'] = 'Комбинация имен и значений свойств';
$MESS['fields.conditionUrlSource.description'] = 'Шаблон исходного адреса Url используется для генерации исходного адреса Url. Должен совпадать с адресом фильтруемого раздела каталога.<br /><br />
В шаблон можно вставлять определенные макросы.<br /><br />
Пример для генератора <b>Битрикс (Параметры в запросе)</b>:<br /><b>/catalog/?SECTION_ID=#SECTION_ID#&#SMART_FILTER_PATH#</b><br /><br />
Пример для генератора <b>Битрикс (ЧПУ)</b>:<br /><b>/catalog/#SECTION_ID#/filter/#SMART_FILTER_PATH#/apply/</b>';
$MESS['fields.conditionUrlSource.macros.iblock.name'] = 'Поля инфоблока';
$MESS['fields.conditionUrlSource.macros.iblock.items.iblockId'] = 'Идентификатор инфоблока';
$MESS['fields.conditionUrlSource.macros.iblock.items.iblockCode'] = 'Символьный код инфоблока';
$MESS['fields.conditionUrlSource.macros.iblock.items.iblockTypeId'] = 'Тип инфоблока';
$MESS['fields.conditionUrlSource.macros.iblock.items.iblockName'] = 'Наименование инфоблока';
$MESS['fields.conditionUrlSource.macros.iblock.items.iblockExternalId'] = 'Внешний код инфоблока';
$MESS['fields.conditionUrlSource.macros.section.name'] = 'Поля раздела';
$MESS['fields.conditionUrlSource.macros.section.items.id'] = 'Идентификатор раздела';
$MESS['fields.conditionUrlSource.macros.section.items.sectionId'] = 'Идентификатор раздела (2)';
$MESS['fields.conditionUrlSource.macros.section.items.code'] = 'Символьный код раздела';
$MESS['fields.conditionUrlSource.macros.section.items.sectionCode'] = 'Символьный код раздела (2)';
$MESS['fields.conditionUrlSource.macros.section.items.sectionCodePath'] = 'Путь из символьных кодов раздела';
$MESS['fields.conditionUrlSource.macros.section.items.externalId'] = 'Внешний код раздела';
$MESS['fields.conditionUrlTarget.description'] = 'Шаблон целевого адреса Url используется для генерации целевого адреса Url. Исходная страница будет доступна по данному адресу. Клиента будет автоматически перенаправлять <b>(301 Moved Permanently)</b> с исходного адреса страницы на целевой.<br /><br />
В шаблон можно вставлять определенные макросы. Помимо макросов также можно вставить шаблон для генерации свойств. Макросы для свойств доступны только в шаблоне свойств. Шаблон свойств заключен в фигурные скобки <b>{Шаблон свойства:Разделитель свойств:Разделитель значений свойства}</b>, разделен на 3 секции с помощью символа <b>:</b>.
Первая секция шаблона свойств отвечает за шаблон одного свойства в адресе <b>(Пример: #PROPERTY_CODE#_#PROPERTY_VALUE#)</b>, вторая секция отвечает за то, как будут разделяться свойства <b>(Пример: /)</b> и третья секция отвечает за то, как будут разделяться значения свойства, если их несколько <b>(Пример: -)</b>.<br /><br />
Пример шаблона свойств:<br /><b>{#PROPERTY_CODE#_#PROPERTY_VALUE#:/:-}</b><br /><br />
Пример шаблона:<br /><b>/catalog/#SECTION_ID#/{#PROPERTY_CODE#_#PROPERTY_VALUE#:/:-}/</b>';
$MESS['fields.conditionUrlTarget.macros.iblock.name'] = 'Поля инфоблока';
$MESS['fields.conditionUrlTarget.macros.iblock.items.iblockId'] = 'Идентификатор инфоблока';
$MESS['fields.conditionUrlTarget.macros.iblock.items.iblockCode'] = 'Символьный код инфоблока';
$MESS['fields.conditionUrlTarget.macros.iblock.items.iblockTypeId'] = 'Тип инфоблока';
$MESS['fields.conditionUrlTarget.macros.iblock.items.iblockName'] = 'Наименование инфоблока';
$MESS['fields.conditionUrlTarget.macros.iblock.items.iblockExternalId'] = 'Внешний код инфоблока';
$MESS['fields.conditionUrlTarget.macros.section.name'] = 'Поля раздела';
$MESS['fields.conditionUrlTarget.macros.section.items.id'] = 'Идентификатор раздела';
$MESS['fields.conditionUrlTarget.macros.section.items.sectionId'] = 'Идентификатор раздела (2)';
$MESS['fields.conditionUrlTarget.macros.section.items.code'] = 'Символьный код раздела';
$MESS['fields.conditionUrlTarget.macros.section.items.sectionCode'] = 'Символьный код раздела (2)';
$MESS['fields.conditionUrlTarget.macros.section.items.sectionCodePath'] = 'Путь из символьных кодов раздела';
$MESS['fields.conditionUrlTarget.macros.section.items.externalId'] = 'Внешний код раздела';
$MESS['fields.conditionUrlTarget.macros.property.name'] = 'Поля свойств';
$MESS['fields.conditionUrlTarget.macros.property.items.propertyId'] = 'Идентификатор свойства';
$MESS['fields.conditionUrlTarget.macros.property.items.propertyCode'] = 'Код свойства';
$MESS['fields.conditionUrlTarget.macros.property.items.propertyValue'] = 'Значение(я) свойства';
$MESS['fields.conditionUrlTarget.macros.additional.name'] = 'Дополнительные поля';
$MESS['fields.conditionUrlTarget.macros.additional.items.ranges'] = 'Диапазоны чисел';
$MESS['fields.conditionUrlTarget.macros.additional.items.prices'] = 'Цены';

$MESS['blocks.operators.and'] = 'И';
$MESS['blocks.operators.or'] = 'ИЛИ';
$MESS['blocks.items.add'] = 'Добавить блок';
$MESS['blocks.items.remove'] = 'Удалить блок';
$MESS['blocks.items.properties.add'] = 'Добавить свойство';
$MESS['blocks.items.properties.remove'] = 'Удалить свойство';
$MESS['answers.unset'] = 'Не выбрано';
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
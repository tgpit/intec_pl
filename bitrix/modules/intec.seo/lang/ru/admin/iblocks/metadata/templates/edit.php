<?php

use intec\core\bitrix\iblock\tags\MorphologyTag;

$MESS['title.add'] = 'Добавить шаблон метаданных инфоблока';
$MESS['title.edit'] = 'Редактировать шаблон метаданных инфоблока';
$MESS['tabs.common'] = 'Общее';
$MESS['tabs.sectionMeta'] = 'Meta-информация раздела';
$MESS['tabs.elementMeta'] = 'Meta-информация элемента';
$MESS['panel.actions.back'] = 'Список';
$MESS['panel.actions.add'] = 'Добавить';
$MESS['fields.sites'] = 'Сайты';
$MESS['fields.sections'] = 'Разделы';
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
$MESS['notes.macros'] = 'Пример для мета-информации:<br />
<br />
<b>{=this.Name} из {=morphology {=concat {=property "PROCREATOR"} ", "} "РД"}</b><br />
<br />
Дополнительные операторы, доступные в мета-информации:<br />
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
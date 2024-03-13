<?php

use intec\core\bitrix\iblock\tags\MorphologyTag;

$MESS['title.add'] = 'Добавить шаблон имен элементов';
$MESS['title.edit'] = 'Редактировать шаблон имен элементов';
$MESS['tabs.common'] = 'Общее';
$MESS['panel.actions.back'] = 'Список';
$MESS['panel.actions.add'] = 'Добавить';
$MESS['fields.rules'] = 'Правила';
$MESS['fields.sites'] = 'Сайты';
$MESS['fields.sections'] = 'Разделы';
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
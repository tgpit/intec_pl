<?php

$MESS['title'] = 'Генератор текстов';
$MESS['tabs.common'] = 'Генерация';
$MESS['fields.entity'] = 'Объект';
$MESS['fields.entity.IBlock'] = 'Инфоблок';
$MESS['fields.entity.IBlockSection'] = 'Раздел инфоблока';
$MESS['fields.entity.IBlockElement'] = 'Элемент инфоблока';
$MESS['fields.field'] = 'Поле';
$MESS['fields.field.PREVIEW_TEXT'] = 'Текст анонса';
$MESS['fields.field.DETAIL_TEXT'] = 'Детальный текст';
$MESS['fields.iblocks'] = 'Инфоблоки';
$MESS['fields.sections'] = 'Разделы';
$MESS['fields.elements'] = 'Элементы';
$MESS['fields.refill'] = 'Генерировать новый текст, даже если поле уже заполнено';
$MESS['fields.source'] = 'Источник';
$MESS['fields.source.text'] = 'Текст';
$MESS['fields.source.textPattern'] = 'Текстовый шаблон';
$MESS['fields.text'] = 'Текст';
$MESS['fields.text.description'] = '
<b>Синтаксис</b>
<p>Для генерации текста можно использовать различные операторы, чтобы для каждого элемента текст был разным. Все операторы поддерживают вложенность. Пример: <b>{Недорогой|Дешевый|Качественный} #NAME#[с мощностью #PROPERTY_POWER#Вт.] и отличной {динамикой|эргономикой}</b>.</p>
<b>Доступные операторы:</b>
<ul>
    <li><b>{Текст 1|Текст 2|Текст n} - Группа</b>. Выбирает один из возможных текстов в группе для каждого элемента. Группа начинается с символа <b>{</b> и заканчивается символом <b>}</b>. Элементы группы разделяются символом <b>|</b>;</li>
    <li><b>#MACROS# - Макрос</b>. Заменяется на содержимое элемента (у каждого элемента свои макросы). Макрос начинается с символа <b>#</b> и заканчивается символом <b>#</b>. Внутри оператора помещается название макроса;</li>
    <li><b>[Текст и операторы] - Условный оператор</b>. Помещает в себе текст и другие операторы. Текст условного оператора будет выведен только в том случае, если он содержит в себе другие операторы и один из этих операторов возвращает не пустой результат. Условный оператор начинается с символа <b>[</b> и заканчивается символом <b>]</b>;</li>
    <li><b>\ - Экранирование</b>. Ставится перед любым спец. символом операторов, для того чтобы спец. символ считался обычным и выводился в тексте.</li>
</ul>
<b>Доступные макросы инфоблока:</b>
<ul>
    <li><b>ID</b> - Идентификатор инфоблока;</li>
    <li><b>CODE</b> - Символьный код инфоблока;</li>
    <li><b>NAME</b> - Наименование инфоблока;</li>
    <li><b>DESCRIPTION</b> - Описание инфоблока;</li>
    <li><b>SECTIONS_NAME</b> - Наименование разделов инфоблока;</li>
    <li><b>SECTION_NAME</b> - Наименование раздела инфоблока;</li>
    <li><b>ELEMENTS_NAME</b> - Наименование элементов инфоблока;</li>
    <li><b>ELEMENT_NAME</b> - Наименование элемента инфоблока.</li>
</ul>
<b>Доступные макросы раздела инфоблока:</b>
<ul>
    <li><b>ID</b> - Идентификатор раздела;</li>
    <li><b>CODE</b> - Символьный код раздела;</li>
    <li><b>NAME</b> - Наименование раздела;</li>
    <li><b>DESCRIPTION</b> - Описание раздела;</li>
    <li><b>PROPERTY_&lt;Код свойства&gt;</b> - UF_ свойство раздела. Код свойства прописывается без приставки UF_;</li>
    <li><b>IBLOCK_ID</b> - Идентификатор инфоблока;</li>
    <li><b>IBLOCK_CODE</b> - Символьный код инфоблока;</li>
    <li><b>IBLOCK_NAME</b> - Наименование инфоблока;</li>
    <li><b>IBLOCK_DESCRIPTION</b> - Описание инфоблока;</li>
    <li><b>IBLOCK_SECTIONS_NAME</b> - Наименование разделов инфоблока;</li>
    <li><b>IBLOCK_SECTION_NAME</b> - Наименование раздела инфоблока;</li>
    <li><b>IBLOCK_ELEMENTS_NAME</b> - Наименование элементов инфоблока;</li>
    <li><b>IBLOCK_ELEMENT_NAME</b> - Наименование элемента инфоблока.</li>
</ul>
<b>Доступные макросы элемента инфоблока:</b>
<ul>
    <li><b>ID</b> - Идентификатор элемента;</li>
    <li><b>CODE</b> - Символьный код элемента;</li>
    <li><b>NAME</b> - Наименование элемента;</li>
    <li><b>DESCRIPTION_PREVIEW</b> - Описание анонса элемента;</li>
    <li><b>DESCRIPTION_DETAIL</b> - Детальное описание элемента;</li>
    <li><b>PROPERTY_&lt;Код свойства&gt;</b> - Свойство элемента;</li>
    <li><b>SECTION_ID</b> - Идентификатор раздела;</li>
    <li><b>SECTION_CODE</b> - Символьный код раздела;</li>
    <li><b>SECTION_NAME</b> - Наименование раздела;</li>
    <li><b>SECTION_DESCRIPTION</b> - Описание раздела;</li>
    <li><b>SECTION_PROPERTY_&lt;Код свойства&gt;</b> - UF_ свойство раздела. Код свойства прописывается без приставки UF_;</li>
    <li><b>IBLOCK_ID</b> - Идентификатор инфоблока;</li>
    <li><b>IBLOCK_CODE</b> - Символьный код инфоблока;</li>
    <li><b>IBLOCK_NAME</b> - Наименование инфоблока;</li>
    <li><b>IBLOCK_DESCRIPTION</b> - Описание инфоблока;</li>
    <li><b>IBLOCK_SECTIONS_NAME</b> - Наименование разделов инфоблока;</li>
    <li><b>IBLOCK_SECTION_NAME</b> - Наименование раздела инфоблока;</li>
    <li><b>IBLOCK_ELEMENTS_NAME</b> - Наименование элементов инфоблока;</li>
    <li><b>IBLOCK_ELEMENT_NAME</b> - Наименование элемента инфоблока.</li>
</ul>
';
$MESS['fields.textPattern'] = 'Текстовый шаблон';
$MESS['states.processing.handle'] = 'Обработано';
$MESS['states.processing.from'] = 'из';
$MESS['states.processing.errors.parse'] = 'Ошибка разбора текста';
$MESS['states.processing.errors.iblock'] = 'Инфоблок с идентификатором #ID# не найден';
$MESS['states.processing.errors.iblocks'] = 'Инфоблоки не выбраны';
$MESS['states.processing.errors.section'] = 'Раздел с идентификатором #ID# не найден';
$MESS['states.processing.errors.source'] = 'Не указан источник текста';
$MESS['states.error.message'] = 'В процессе возникла ошибка';
$MESS['states.complete.message'] = 'Генерация завершена';
$MESS['actions.generate'] = 'Генерировать';
$MESS['actions.back'] = 'Назад';
$MESS['answers.unselected'] = 'Не выбрано';
<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_XMLCHARS_';

$MESS[$strMessPrefix.'NAME'] = 'Удалить служебные символы';
$MESS[$strMessPrefix.'DESC'] = 'Удаление служебных ASCII-символов с символами от 0 до 31 (кроме символа табуляции, переноса строки и возврата каретки).<br/>
<p>Эта опция важна при выгрузке в XML в случае, если в выгружаемых значениях могут встречаться служебные символы ASCII (это не относится к HTML-сущностям, начинающимся с символа <code>&amp;</code> и собственно символу <code>&amp;</code> - для них есть отдельная опция «Преобразовать HTML-сущности»)</p>';
$MESS[$strMessPrefix.'ALSO'] = 'Также удалить: ';
$MESS[$strMessPrefix.'ALSO_TAB'] = '<code>\t</code>';
	$MESS[$strMessPrefix.'ALSO_TAB_HINT'] = 'Символ табуляции (\t).';
$MESS[$strMessPrefix.'ALSO_NEWLINE'] = '<code>\n</code>';
	$MESS[$strMessPrefix.'ALSO_NEWLINE_HINT'] = 'Символ переноса строки (\n).';
$MESS[$strMessPrefix.'ALSO_CARET'] = '<code>\r</code>';
	$MESS[$strMessPrefix.'ALSO_CARET_HINT'] = 'Символ возврата каретки (\r).';
$MESS[$strMessPrefix.'ALSO_SPACE'] = 'Пробел';
?>
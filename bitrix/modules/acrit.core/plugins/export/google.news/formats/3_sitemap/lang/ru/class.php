<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Google.News (Sitemap)';

// Settings
$MESS[$strLang.'SETTINGS_NAME_GOOGLE_SITEMAP_MAX_COUNT'] = 'Максимальное количество ссылок';
	$MESS[$strLang.'SETTINGS_HINT_GOOGLE_SITEMAP_MAX_COUNT'] = 'Укажите здесь максимально допустимое количество ссылок в одном файле. При большем количестве файла будет разделяться на несколько частей.';
$MESS[$strLang.'SETTINGS_NAME_GOOGLE_SITEMAP_MAX_SIZE'] = 'Максимальный размер файла (мегабайт)';
	$MESS[$strLang.'SETTINGS_HINT_GOOGLE_SITEMAP_MAX_SIZE'] = 'Укажите здесь максимально допустимый размер файла в мегабайтах. При большем размере файла он будет разделяться на несколько частей.';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Основные данные о товарах';
$MESS[$strName.'loc'] = 'Ссылка';
	$MESS[$strHint.'loc'] = 'URL новостной статьи.';
$MESS[$strName.'news:news.news:publication.news:name'] = 'Издание, в котором опубликована статья';
	$MESS[$strHint.'news:news.news:publication.news:name'] = 'Издание, в котором опубликована статья (название). В поле следует указать название издателя новостей, приведенное в статьях на сайте <a href="https://news.google.com" target="_blank">news.google.com</a>.';
$MESS[$strName.'news:news.news:publication.news:language'] = 'Язык издания';
	$MESS[$strHint.'news:news.news:publication.news:language'] = 'Код языкы новостной статьи (напр., ru или en).';
$MESS[$strName.'news:news.news:publication_date'] = 'Дата публикации';
	$MESS[$strHint.'news:news.news:publication_date'] = 'Дата публикации новостной статьи (формат 1997-07-16 или 1997-07-16T19:20+01:00).';
$MESS[$strName.'news:news.news:title'] = 'Название';
	$MESS[$strHint.'news:news.news:title'] = 'Название новостной статьи.';


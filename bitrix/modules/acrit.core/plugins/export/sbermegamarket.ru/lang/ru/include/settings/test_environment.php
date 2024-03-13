<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

# Settings
$MESS[$strLang.'SETTINGS_NAME_TEST_ENVIRONMENT'] = 'Среда';
	$MESS[$strLang.'SETTINGS_HINT_TEST_ENVIRONMENT'] = 'Выберите среду (окружение), в которую следует выгружать товары.<br/><br/>
	Общий алгоритм следующий: при настройке профиля следует использовать тестовую среду, после завершения настроек следует получить новый токен для production-среды (его необходимо запросить в техподдержке СберМегаМаркет), и переключить профиль на использование production-среду.';
$MESS[$strLang.'SETTINGS_NAME_ENVIRONMENT_TEST'] = 'Тестовая среда';
$MESS[$strLang.'SETTINGS_NAME_ENVIRONMENT_PROD'] = 'Продуктовая среда (production)';

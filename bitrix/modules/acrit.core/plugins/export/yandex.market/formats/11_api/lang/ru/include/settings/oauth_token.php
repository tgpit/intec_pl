<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN'] = 'Токен авторизации OAuth';
$MESS[$strLang.'SETTINGS_HINT_OAUTH_TOKEN'] = 'Для работы с API Яндекс.Маркета необходима авторизация через токен <a href="https://yandex.ru/dev/id/doc/dg/oauth/concepts/about.html" target="_blank">OAuth</a>.';
	$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_GET'] = 'Получить';
	$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_LABEL_CONFIRM_CODE'] = 'Код подтверждения:';
	$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_PLACEHOLDER_CONFIRM_CODE'] = 'Код (7 цифр)';
	$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_BUTTON_CONFIRM_CODE'] = 'Подтвердить';
	$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_NO_CLIENT_ID'] = 'Для получения токена необходимо указать ClientID для приложения OAuth';
	$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_NO_CLIENT_SECRET_ID'] = 'Для получения токена необходимо указать Client secret для приложения OAuth';
		$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_NEED_AUTH'] = 
		'Сейчас необходимо авторизоваться в Яндексе и подтвердить доступ к приложению.'."\n".
		'После этого необходимо будет ввести код подтверждения.';
		$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_CONFIRM_CODE'] = 'Код подтверждения (на странице Яндекс)';
		$MESS[$strLang.'SETTINGS_NAME_OAUTH_TOKEN_MESSAGE_GET_TOKEN'] = 'Ошибка получения токена';
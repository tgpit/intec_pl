<?php

$MESS['title'] = 'Cron';
$MESS['field.templates'] = 'Шаблоны экспорта';
$MESS['field.templates.placeholder'] = 'Выберите шаблон';
$MESS['field.frequency.type'] = 'Периодичность запуска';
$MESS['field.frequency.type.daily'] = 'ежедневно';
$MESS['field.frequency.type.daily.label'] = 'в';
$MESS['field.frequency.type.hours'] = 'каждые n часов';
$MESS['field.frequency.type.hours.label'] = 'периодичность в часах';
$MESS['field.frequency.type.minutes'] = 'каждые n минут';
$MESS['field.frequency.type.minutes.label'] = 'периодичность в минутах';
$MESS['field.frequency.type.expert'] = 'экспертные настройки';
$MESS['field.frequency.type.expert.label'] = 'время запуска';
$MESS['field.php.path'] = 'Путь к php';
$MESS['field.auto'] = 'Установить автоматически';
$MESS['btn.title.delete.from.cron'] = 'Удалить из cron';
$MESS['btn.title.add.to.cron'] = 'Добавить в cron';
$MESS['heading.cron.list'] = 'Записи в кроне';
$MESS['heading.cron.list.time'] = 'Время запуска';
$MESS['heading.cron.list.template'] = 'Шаблоны экспорта';
$MESS['hint.php.path'] = 'Путь к php зависит от настроек Вашего хостинга.<br> Обычно используется путь /usr/bin/php, но может быть и другой.<br> Вы можете уточнить путь к php у Вашего хостера.';
$MESS['hint.auto'] = 'Внимание!<br> Данная опция перезаписывает системный cron из файла /bitrix/crontab/crontab.cfg.<br> Записи, не внесенные в данный файл, будут удалены из cron.<br> Используйте эту опцию только на виртуальной машине 1С-Битрикс.';

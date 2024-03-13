<?php

$MESS['title'] = 'Шаблоны экспорта в excel';
$MESS['title.cron'] = 'Настройки крон';
$MESS['filter.fields.id'] = 'ID';
$MESS['filter.fields.name'] = 'Наименование';
$MESS['filter.fields.create.date'] = 'Дата создания';
$MESS['filter.fields.edit.date'] = 'Дата последнего изменения';
$MESS['list.actions.cron'] = 'Cron';
$MESS['list.actions.add'] = 'Добавить';
$MESS['list.actions.delete'] = 'Удалить';
$MESS['list.actions.activate'] = 'Активировать';
$MESS['list.actions.deactivate'] = 'Деактивировать';
$MESS['list.headers.id'] = 'ID';
$MESS['list.headers.name'] = 'Наименование';
$MESS['list.headers.create.date'] = 'Дата создания';
$MESS['list.headers.edit.date'] = 'Дата последнего изменения';
$MESS['list.rows.actions.edit'] = 'Редактировать';
$MESS['list.rows.actions.copy'] = 'Копировать';
$MESS['list.rows.actions.delete'] = 'Удалить';
$MESS['list.rows.actions.delete.confirm'] = 'Вы действительно хотите удалить шаблон?';
$MESS['list.rows.answers.unset'] = 'Не установлено';
$MESS['list.rows.answers.yes'] = 'Да';
$MESS['list.rows.answers.no'] = 'Нет';
$MESS['list.navigation.title'] = 'Шаблоны';

$MESS['cron.message.none.template'] = 'Не выбран шаблон';
$MESS['cron.add.message.success'] = '<div class="adm-info-message-title">Изменения успешно сохранены</div><br><br>

                                 Изменения внесены в файл #CRONTAB_PATH# <br>
                                 Для настройки cron через панель управления используйте запись <br><br>
                                 
                                 <i>#TIME# #PHP_PATH# -d default_charset=windows-1251 -d short_open_tag=on -d memory_limit=1024M -f #CRON_FRAME_PATH# #TEMPLATE# > #LOG_PATH# </i><br><br>
                                 
                                 Где:<br>
                                 <i>#TIME#</i> - время запуска скрипта импорта<br>
                                 <i>#PHP_PATH#</i> - путь к php<br>
                                 <i>#CRON_FRAME_PATH#</i> - путь к исполняемому файлу<br>
                                 <i>#TEMPLATE#</i> - ID профиля импорта<br>
                                 <i>#LOG_PATH#</i> - путь к файлу логов<br>
';
$MESS['cron.current.delete.message.success'] = '<div class="adm-info-message-title">Изменения успешно сохранены</div><br>

                                 Изменения внесены в файл #CRONTAB_PATH# <br>
                                 Шаблон #TEMPLATE# и временем #TIME# удален из файла <br>
';
$MESS['cron.list.delete.message.success'] = '<div class="adm-info-message-title">Изменения успешно сохранены</div><br>

                                 Изменения внесены в файл #CRONTAB_PATH# <br>
                                 Шаблон #TEMPLATE# удален из файла <br>
';
$MESS['cron.error'] = '<div class="adm-info-message-title">Произошла ошибка</div>';
$MESS['cron.non.template.error'] = '<div class="adm-info-message-title">Не выбран шаблон</div>';

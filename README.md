## Модуль интеграции с CMS Bitrix
Данный модуль обеспечивает взаимодействие между интернет-магазином на базе CMS Bitrix (с модулем электронной комерции __sale__) и сервисом платежей [ХуткiГрош](https://hutkigrosh.by)
  
### Требования ###
1. PHP 5.6 и выше
1. Библиотека Curl

## Инструкция по установке:
### Автоматическая установка (через Marketplace) 
1. Перейти на страницу _Marketplace > Каталог решений_
1. Введите в поиске _Hutkigrosh_
1. Установите решение
### Ручная установка
1. Загрузите архив модуля [esas.hutkigrosh.zip](https://github.com/esasby/hutkigrosh-bitrix-module/blob/master/esas.hutkigrosh.zip)
(кодировка cp-1251) 
1. Распакуйте архив в папку 
```/bitrix/modules/```
После распаковки должна появиться папка 
```/bitrix/modules/esasby.hutkigrosh```
1. Перейти на страницу _Marketplace > Установленные решения_ (/bitrix/admin/partner_modules.php)
1. В контекстном меню решения esasby.hutkigrosh выбрать "Установить".

## Инструкция по настройке
1. Перейти на страницу _Магазин > Настройки > Платежные системы_ (/bitrix/admin/sale_pay_system.php)
1. В контекстном меню платежной системы "ХуткiГрош" выбрать "Изменить". 
1. Загрузить логотип для платежной системы ```hgrosh.png``` (находится в архиве с модулем)
1. В секции _"Настройка обработчика ПС"_ укажите обязательные параметры
    * Уникальный идентификатор услуги ЕРИП – ID ЕРИП услуги
    * Логин интернет-магазина – логин в системе ХуткiГрош.
    * Пароль интернет-магазина – пароль в системе ХуткiГрош.
    * Тестовый режим (0 - режим реальных платежей, 1 - режим тестирвоания)
    * Sms оповещение - включить информирование клиента по смс при успешном выставлении счета (выполняется шлюзом Хуткiгрош)
    * Email оповещение - включить информирование клиента по email при успешном выставлении счета (выполняется шлюзом Хуткiгрош)
    * Кнопка Alfaclick - Если включена, то на итоговом экране клиенту отобразится кнопка для выставления счета в Alfaclick
    * Кнопка Webpay - Если включена, то на итоговом экране клиенту отобразится кнопка для оплаты счета картой (переход на Webpay)
    * Статус при выставлении счета  - Какой статус выставить заказу при успешном выставлении счета в ЕРИП (идентификатор существующего статуса из Магазин > Настройки > Статусы)
    * Статус при успешной оплате счета - Какой статус выставить заказу при успешной оплате выставленного счета (идентификатор существующего статуса)
    * Статус при отмене оплаты счета - Какой статус выставить заказу при отмене оплаты счета (идентификатор существующего статуса)
    * Статус при ошибке оплаты счета - Какой статус выставить заказу при ошибке выставленния счета (идентификатор существующего статуса)
    * Путь в дереве ЕРИП - путь для оплаты счета в дереве ЕРИП, который будет показан клиенту после оформления заказа (например, Платежи > Магазин > Заказы)
    * Срок действия счета - как долго счет, будет доступен в ЕРИП для оплаты

## Удаление модуля
1. Для удаления модуля перейти на страницу _Marketplace > Установленные решения_ (/bitrix/admin/partner_modules.php)
В контекстном меню решения esasby.hutkigrosh выбрать "Удалить". Затем "Стереть"
1. Сохраните изменения

### Внимание!
1. Для автоматического обновления статуса заказа (после оплаты клиентом выставленного в ЕРИП счета) необходимо сообщить в службу технической поддержки сервиса «Хуткi Грош» адрес обработчика:
```
http://mydomen.my/bitrix/tools/sale_ps_result.php?handler=hutkigrosh
```
2. Для корректной работы модуля необходимо включить библиотеку curl. Для подключения curl в bitrix копируем 20-curl.ini.disabled в 20-curl.ini

### Тестовые данные
Для настрой оплаты в тестовом режиме:
 * воспользуйтесь данными для подключения к тестовой системе, полученными при регистрации в ХуткiГрош
 * включите в настройках модуля тестовый режим 
 * для эмуляции оплаты клиентом выставленного счета воспльзуйтесь личным кабинетом [тестовой системы](https://trial.hgrosh.by) (меню _Тест оплаты ЕРИП_)

_Разработано и протестировано с 1С-Битрикс: Управление сайтом 17.0.9_



## **DCMS** - система управления сайтом ##

### Версии (ветки): ###
* Версия 7.4 (ветка 7.4) - для стабилизации. Правим только критичные баги.
* Версия 7.5 с PDO (ветка DEV). Доработка существующего функционала, исправление багов. Подготовка к релизу.
* Версия 7.6 (пока нет ветки). Разработка нового функционала.

### Список модулей по-умолчанию: ###

* **Управление пользователями**: регистрация, авторизация, блокировка пользователя и т.д.
* **Мини-чат**: позволяет общаться с другими пользователями в онлайне или высказать свое мнение осайте.
* **Новости**: позволяют делать рассылку по email адресам из профиля пользователей.
* **Форум**: 2-х уровневый форум с возможностью разделения прав на просмотр, создание и редактирование тем, разделов и категорий.
* Многоуровневая система управления файлами: **обменник**, **загруз-центр**, хранилище файлов для форума и т.д.
* Понимает ID3 теги у большинства популярных форматов.
* Также имеется возможность создания скриншотов к изображениям и видео (нужен php-ffmpeg).
* Автоматическое определение типа браузера пользователя позволяет использовать 3 разновидности тем оформления (Light, Mobile, Full), адаптированные под WEB, Touch, PDA или WAP устройства.

### Системные требования ###

* Apache с модулем mod_rewrite.
* PHP >= 5.2
* - GD2
* - mbstring и(или) Iconv
* - php-ffmpeg (опционально)
* MySQL (PDO)
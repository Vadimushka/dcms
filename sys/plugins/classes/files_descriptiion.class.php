<?php
class files_descriptiion {

    const FILES = [
        'FILES' => [
            'runame' => 'Все файлы',
            'group_edit' => 4,
            'id_user' => 1,
            'sort_default' => "time_add:desc",
        ],
        '.AVATARS' => [
            'runame' => '.avatars',
            'group_show' => 0,
            'group_write' => 3,
        ],
        '.BBCODE' => [
            'runame' => 'BBCODE IMG',
            'group_show' => 6,
            'group_write' => 6,
            'sort_default' => "time_add:desc",
        ],
        '.DOWNLOADS' => [
            'runame' => 'Загрузки',
            'group_show' => 0,
            'group_write' => 4,
            'group_edit' => 5,
            'sort_default' => "time_add:desc",
        ],
        '.FORUM' => [
            'runame' => 'Файлы форума',
            'group_show' => 0,
            'group_write' => 2,
            'group_edit' => 3,
        ],
        '.OBMEN' => [
            'runame' => 'Обменник',
            'group_show' => 0,
            'group_write' => 1,
            'group_edit' => 4,
            'description' => 'Файлы разрешено выгружать только авторам скриптов!\r\nК файлу обязательно должен прилагаться скриншот и подробное описание.\r\nВ конце описания необходимо указать, что автором являетесь вы!\r\n[b]Файлы, не соответствующие описанию или оформленные некорректно, будут удаляться без предупреждения[/b]',
            'sort_default' => "time_add:desc",
        ],
        '.PHOTOS' => [
            'runame' => 'Фотографии',
            'group_show' => 1,
            'group_write' => 6,
        ],
    ];
    const ADDKEYS = [
        '.BBCODE' => [
            'width:desc' => 'Разрешение',
        ],
    ];

    public static function defaultDescriptions($type)
    {
        return self::FILES[$type] ?? [];
    }

    public static function defaultAddKeys($type)
    {
        return self::ADDKEYS[$type] ?? [];
    }
}
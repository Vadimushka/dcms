<?php

/**
 * Класс отвечает за отображение конкретного файла в группе
 */
class files_file {

    protected $_data = array(); // информация о файле
    protected $_screens = array(); // скриншоты (имена файлов)
    protected $_need_save = false; // необходимость пересохранения сведений о файле
    var $ratings = array();

    /**
     * files_file::__construct()
     *
     * @param string $path_dir_abs
     * @param string $filename
     */
    function __construct($path_dir_abs, $filename) {


        $this->ratings = array(
            -2 => __('Ужасный файл'),
            -1 => __('Плохой файл'),
            0  => __('Без оценки'),
            1  => __('Хороший файл'),
            2  => __('Отличный файл')
        );



        $this->_data['id'] = 0;
        $this->_data['runame'] = convert::to_utf8($filename); // имя на русском (чтобы небыло пустым)
        $this->_data['id_user'] = 0; // создатель файла
        $this->_data['downloads'] = 0; // кол-во скачиваний файла
        $this->_data['description'] = ''; // описание файла (задается вручную)
        $this->_data['description_small'] = ''; // короткое описание файла (задается вручную)
        $this->_data['rating'] = 0; // рейтинг файла
        $this->_data['rating_count'] = 0; // кол-во проголосовавших
        $this->_data['comments'] = 0; // кол-во комментариев
        if ($cfg_ini = ini::read($path_dir_abs . '/.' . $filename . '.ini', true)) {
            // загружаем конфиг
            $this->_data = array_merge($this->_data, (array) @$cfg_ini['CONFIG']);
            $this->_screens = array_merge($this->_screens, (array) @$cfg_ini['SCREENS']);
        } else {
            $dir = new files($path_dir_abs);
            $this->_data['group_show'] = $dir->group_show; // группа, которой доступен файл
            $this->_data['group_edit'] = max($dir->group_write, 2); // группа, которая имеет право на изменение параметров файла
            // если конфиг не загрузился, то предполагаем что файл грузился не средствами движка,
            // поэтому задаем ему время добавления
            $this->_data['time_add'] = TIME; // дата добавления файла
            $this->_need_save = true; // обязательно сохраняем сведения о файле
        }
        $this->_data['name'] = $filename;

        // расширение файла
        $pinfo = pathinfo(strtolower($filename));
        $this->_data['ext'] = $pinfo['extension'];

        $this->_setPathes($path_dir_abs);
        // получение дополнительных сведений о файле
        $this->_getPropertiesAuto();

        if (!$this->_data['id']) {
            $this->_baseAdd();
        }
    }

    public function rename($runame, $name) {
        if ($this->_data['name'] {0} == '.') {
            return false;
        }

        if ($name {0} == '.') {
            return false;
        }


        if (@rename($this->_data['path_file_abs'], $this->_data['path_dir_abs'] . '/' . $name)) {
            // переименовываем скрины
            foreach ($this->_screens as $scr_key => $scr_file) {
                if (@rename($this->_data['path_dir_abs'] . '/' . $scr_file, $this->_data['path_dir_abs'] . '/.' . $name . '.' . $scr_key . '.jpg'))
                    $this->_screens[$scr_key] = '.' . $name . '.' . $scr_key . '.jpg';
            }
            // переименовываем конфиг
            @rename($this->_data['path_dir_abs'] . '/.' . $this->_data['name'] . '.ini', $this->_data['path_dir_abs'] . '/.' . $name . '.ini');
            $this->_data['name'] = $name;
            // вычисление дополнительных путей
            $this->_setPathes($this->_data['path_dir_abs']);
            $this->_data['runame'] = $runame;
            // обновление инфы в кэше (в базе)
            $this->_baseUpdate();
            $dir = new files($this->_data['path_dir_abs']);
            $dir->cacheClear();
            $this->_need_save = true;
            return true;
        }
    }

    public function moveTo($path_dir_abs) {
        global $user;

        $dir = new files($path_dir_abs);


        if ($dir->group_show > $user->group || $dir->group_write > $user->group) {
            // если нет прав на просмотр или запись в папку
            return false;
        }


        if (@rename($this->_data['path_file_abs'], $dir->path_abs . '/' . $this->_data['name'])) {
            // переименовываем скрины
            foreach ($this->_screens as $scr_key => $scr_file) {
                if (@rename($this->_data['path_dir_abs'] . '/' . $scr_file, $dir->path_abs . '/.' . $this->_data['name'] . '.' . $scr_key . '.jpg'))
                    $this->_screens[$scr_key] = '.' . $this->_data['name'] . '.' . $scr_key . '.jpg';
            }
            // переименовываем конфиг
            @rename($this->_data['path_dir_abs'] . '/.' . $this->_data['name'] . '.ini', $dir->path_abs . '/.' . $this->_data['name'] . '.ini');


            $dir_old = new files($this->_data['path_dir_abs']);
            $dir_old->cacheClear();

            // вычисление дополнительных путей
            $this->_setPathes($dir->path_abs);

            // обновление инфы в кэше (в базе)
            $this->_baseUpdate();
            $dir->cacheClear();
            $this->_need_save = true;
            return true;
        }
    }

    // получение списка доступных ключей для сортировки
    public function getKeys() {
        $keys = array();
        if (!empty($this->_data['time_create']))
            $keys['time_create:desc'] = __('Время создания');
        if (!empty($this->_data['downloads']))
            $keys['downloads:desc'] = __('Кол-во скачиваний');
        if (!empty($this->_data['comments']))
            $keys['comments:desc'] = __('Кол-во комментариев');
        if (!empty($this->_data['id_user']))
            $keys['id_user:desc'] = __('Автор');
        if (!empty($this->_data['rating']))
            $keys['rating:desc'] = __('Рейтинг');
        if (!empty($this->_data['title']))
            $keys['title:asc'] = __('Заголовок');
        if (!empty($this->_data['frames']))
            $keys['frames:desc'] = __('Кол-во кадров');
        if (!empty($this->_data['width']))
            $keys['width:desc'] = __('Разрешение');
        if (!empty($this->_data['video_codec']))
            $keys['video_codec:asc'] = __('Видео кодек');
        if (!empty($this->_data['audio_codec']))
            $keys['audio_codec:asc'] = __('Аудио кодек');
        if (!empty($this->_data['playtime_seconds']))
            $keys['playtime_seconds:desc'] = __('Продолжительность');
        if (!empty($this->_data['artist']))
            $keys['artist:asc'] = __('Исполнители');
        if (!empty($this->_data['band']))
            $keys['band:asc'] = __('Группа');
        if (!empty($this->_data['album']))
            $keys['album:asc'] = __('Альбом');
        if (!empty($this->_data['genre']))
            $keys['genre:asc'] = __('Жанр');
        if (!empty($this->_data['track_number']))
            $keys['track_number:asc'] = __('Номер трека');
        if (!empty($this->_data['vendor']))
            $keys['vendor:asc'] = __('Производитель');

        return $keys;
    }

    // удаление данного файла и всех файлов, относящихся к нему
    public function delete() {
        if (!file_exists($this->_data['path_file_abs']) || @unlink($this->_data['path_file_abs'])) {
            // удаляем скрины
            if ($this->_screens) {
                foreach ($this->_screens as $num => $scr_file) {
                    $this->screenDelete($num);
                }
            }

            // удаляем конфиг
            @unlink($this->_data['path_dir_abs'] . '/.' . $this->_data['name'] . '.ini');

            $this->_baseDelete();
            $dir = new files($this->_data['path_dir_abs']);
            $dir->cacheClear();
            $this->_need_save = false;
            $this->__destruct();
            return true;
        }
    }

    // проверяем, можно ли голосавать
    public function rating_my($set = false) {
        global $user;
        $q = mysql_query("SELECT `rating` FROM `files_rating` WHERE `id_file` = '{$this->_data['id']}' AND `id_user` = '{$user->id}'");
        if (mysql_num_rows($q)) {
            $my_rating = mysql_result($q, 0);
        } else {
            $my_rating = 0;
        }

        if ($set !== false && isset($this->ratings[$set])) {
            if ($set && $my_rating) {
                // Изменяем запись
                $my_rating = (int) $set;
                mysql_query("UPDATE `files_rating` SET `rating` = '$my_rating', `time` = '" . TIME . "' WHERE `id_file` = '{$this->_data['id']}' AND `id_user` = '{$user->id}' LIMIT 1");
            } elseif ($set) {
                // Вносим запись
                $my_rating = (int) $set;
                mysql_query("INSERT INTO `files_rating` (`id_file`, `id_user`, `time`, `rating`) VALUES ('{$this->_data['id']}', '{$user->id}', '" . TIME . "', '$my_rating')");
            } elseif ($my_rating) {
                // Удаляем запись
                $my_rating = 0;
                mysql_query("DELETE FROM `files_rating` WHERE `id_file` = '{$this->_data['id']}' AND `id_user` = '{$user->id}'");
            }

            $this->rating_update();
        }


        return $my_rating;
    }

    public function rating_update() {
        $q = mysql_query("SELECT AVG(`rating`) AS `rating`, COUNT(`id_user`) AS `rating_count` FROM `files_rating` WHERE `id_file` = '{$this->_data['id']}'");
        $data = mysql_fetch_assoc($q);

        $this->_data['rating'] = $data['rating'];
        $this->_data['rating_count'] = $data['rating_count'];
        $this->_need_save = true;
    }

    // получение дополнительных сведений о файле
    protected function _getPropertiesAuto() {
        if (!empty($this->_data['properties_auto_comlete']))
            return false;
        if ($desc = files_types::getPropertiesType($this->_data['path_file_abs'])) {
            if (@function_exists('set_time_limit')) {
                @set_time_limit(30);
            }
            $propert = "files_properties_$desc";
            $prop_obj = new $propert($this->_data['path_file_abs']);
            if ($prop = $prop_obj->getProperties()) {
                $this->_data = array_merge((array) $prop, $this->_data);
            }
        }
        $this->_data['properties_auto_comlete'] = 1;
        $dir = new files($this->_data['path_dir_abs']);
        $dir->cacheClear();
        $this->_need_save = true;
    }

    // получение кол-ва скриншотов
    public function getScreensCount() {
        $this->_createScreensAuto();
        return count($this->_screens);
    }

    // получение скриншота определенного размера
    public function getScreen($img_max_width, $num = 0) {
        $this->_createScreensAuto();
        if (!empty($this->_screens[$num])) {
            $screen_path_rel = '/sys/tmp/public.' . md5($this->_data['path_file_rel']) . '.num' . $num . '.width' . $img_max_width . '.jpg';

            if (file_exists(H . $screen_path_rel))
                return $screen_path_rel;
            if (!$img = @imagecreatefromjpeg($this->_data['path_dir_abs'] . '/' . $this->_screens[$num]))
                return false;
            $img_screen = imaging::to_screen($img, $img_max_width);

            if ($img_max_width > 48)
                imaging::add_copyright($img_screen);

            if (imagejpeg($img_screen, H . $screen_path_rel, 85))
                return $screen_path_rel;
        }
    }

    public function screenAdd($img) {
        sort($this->_screens);
        $key = count($this->_screens);
        $scr = '.' . $this->_data['name'] . '.' . $key . '.jpg';
        if (!@imagejpeg($img, $this->_data['path_dir_abs'] . '/' . $scr, 85))
            return false;
        $this->_screens[$key] = $scr;
        $this->_need_save = true;
        return true;
    }

    public function screenDelete($num) {
        if (empty($this->_screens[$num]))
            return false;

        if (@unlink($this->_data['path_dir_abs'] . '/' . $this->_screens[$num]) || !file_exists($this->_data['path_dir_abs'] . '/' . $this->_screens[$num])) {
            // удаление уменьшенных копий скриншотов
            $screen_path_tmp = (array) glob(H . '/sys/tmp/public.' . md5($this->_data['path_file_rel']) . '.num' . $num . '.width*.jpg');
            foreach ($screen_path_tmp as $path_to_delete) {
                @unlink($path_to_delete);
            }

            unset($this->_screens[$num]);
            sort($this->_screens);
            $this->_need_save = true;
            return true;
        }
    }

    protected function _createScreensAuto() {
        if (!empty($this->_data['screens_auto_comlete']))
            return false;
        if ($screen = files_types::getScreenType($this->_data['path_file_abs'])) {
            $screener = "files_screen_$screen";
            $scr_obj = new $screener($this->_data['path_file_abs']);

            if (@function_exists('set_time_limit')) {
                @set_time_limit(30);
            }
            if ($imgs = $scr_obj->getScreen()) {
                $imgs = (array) $imgs;

                foreach ($imgs as $img) {
                    $this->screenAdd($img);
                }
            }
        }
        $this->_data['screens_auto_comlete'] = 1;
        $dir = new files($this->_data['path_dir_abs']);
        $dir->cacheClear();
        $this->_need_save = true;
    }

    public function icon() {
        return files_types::getIconType($this->_data['path_file_abs']);
    }

    public function image($size = 48) {
        if ($screen = $this->getScreen($size)) {
            return $screen;
        }
    }

    // установка путей
    protected function _setPathes($path_dir_abs) {
        // полный путь к папке
        $this->_data['path_dir_abs'] = filesystem::unixpath($path_dir_abs);
        // полный путь к файлу
        $this->_data['path_file_abs'] = $this->_data['path_dir_abs'] . '/' . $this->_data['name'];
        // относительный путь к папке
        $this->_data['path_dir_rel'] = str_replace(filesystem::unixpath(FILES), '', $this->_data['path_dir_abs']);
        // относительный путь к файлу
        $this->_data['path_file_rel'] = $this->_data['path_dir_rel'] . '/' . $this->_data['name'];
    }

    // заносим сведения о файле в базу
    protected function _baseAdd() {
        if ($this->_data['id'])
            return false;
        if ($this->_data['name'] {
                0} == '.')
            return false;

        mysql_query("INSERT INTO `files_cache` (`path_file_rel`, `time_add`, `group_show`, `runame`)
VALUES ('" . my_esc(convert::to_utf8($this->_data['path_file_rel'])) . "', '" . intval($this->_data['time_add']) . "', '" . intval($this->_data['group_show']) . "', '" . my_esc($this->_data['runame']) . "')");
        $this->_data['id'] = mysql_insert_id();
        $this->_need_save = true;
    }

    // обновляем сведения о файле в базе
    protected function _baseUpdate() {
        mysql_query("UPDATE `files_cache`
SET `path_file_rel` = '" . my_esc(convert::to_utf8($this->_data['path_file_rel'])) . "',
`time_add` = '" . intval($this->_data['time_add']) . "',
`group_show` = '" . intval($this->_data['group_show']) . "',
`runame` = '" . my_esc($this->_data['runame']) . "'
WHERE `id` = '" . intval($this->_data['id']) . "' LIMIT 1");
    }

    // удаляем сведения о файле в базе
    protected function _baseDelete() {
        // удаление файла из кэша базы
        mysql_query("DELETE FROM `files_cache` WHERE `id` = '" . intval($this->_data['id']) . "' LIMIT 1");
        // удаление комментов к файлу
        mysql_query("DELETE FROM `files_comments` WHERE `id_file` = '" . intval($this->_data['id']) . "'");
        // удаление рейтингов файла
        mysql_query("DELETE FROM `files_rating` WHERE `id_file` = '" . intval($this->_data['id']) . "'");
        return true;
    }

    // получение пути к папке для ссылки
    public function getPath() {
        $path_rel = preg_split('#/+#', $this->_data['path_dir_rel']);
        foreach ($path_rel as $key => $value) {
            $path_rel[$key] = urlencode($value);
        }
        return implode('/', $path_rel) . '/' . urlencode($this->_data['name']);
    }

    // получение размера файла
    protected function _getSize() {
        $size = @filesize($this->_data['path_file_abs']);
        $this->_data['size'] = $size;
        $this->_need_save = true;
        return $size;
    }

    // получение даты и времени создания файла
    protected function _getTimeCreate() {
        $time = @filemtime($this->_data['path_file_abs']);
        $this->_data['time_create'] = $time;
        $this->_need_save = true;
        return $time;
    }

    function __get($n) {
        global $dcms;
        switch ($n) {
            case 'rating_name':return $this->ratings[round($this->_data['rating'])];
            case 'description_small': return empty($this->_data[$n]) ? text::substr($this->_data['description'], $dcms->browser_type == 'web' ? 512 : 256) : $this->_data[$n];
            case 'time_create': return isset($this->_data[$n]) ? $this->_data[$n] : $this->_getTimeCreate();
            case 'size': return isset($this->_data[$n]) ? $this->_data[$n] : $this->_getSize();
            default: return isset($this->_data[$n]) ? $this->_data[$n] : false;
        }
    }

    function __set($n, $v) {
        if (!isset($this->_data[$n]))
            return false;
        $this->_data[$n] = $v;
        $this->_need_save = true;

        if ($n == 'path_dir_abs') {
            $this->_setPathes($v);
            $this->_baseUpdate();
        }

        if ($n == 'group_show' || $n == 'time_add' || $n == 'path_file_rel' || $n == 'runame')
            $this->_baseUpdate();
    }

    public function save_data() {
        if ($this->_data['name'] {0} !== '.') {
            ini::save($this->_data['path_dir_abs'] . '/.' . $this->_data['name'] . '.ini', array('CONFIG'  => $this->_data, 'SCREENS' => $this->_screens), true);
        }
    }

    function __destruct() {
        if ($this->_need_save) {
            $this->save_data();
        }
    }

}

?>

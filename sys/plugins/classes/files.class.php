<?php

class files {

    protected $_config_file_name = '.!config.dir.ini'; // название конфиг-файла
    protected $_data = array(); // параметры директории
    protected $_need_save = false; // необходимость сохранения параметров директории
    protected $_screens = array(); // список скриншотов
    protected $_keys = array(); // ключи, доступные для сортировки
    var $user_sort = 'position';
    var $error; // последняя ошибка

    function __construct($path_abs) {
        $path_abs = realpath($path_abs);
        $this->_data ['type'] = 'folder'; // тип содержимого для иконки (по-умолчанию: папка)
        $this->_data ['runame'] = convert::to_utf8(basename($path_abs)); // отображаемое название папки
        $this->_data ['group_show'] = 2; // группа пользователей, которой разрешен просмотр папки
        $this->_data ['group_write'] = 3; // группа пользователей, которой разрешено создание вложенных файлов и папок
        $this->_data ['group_edit'] = groups::max(); // группа пользователей, которой разрешено изменение параметров данной папки
        $this->_data ['position'] = 1; // позиция. Учитывается при сортировке
        $this->_data ['id_user'] = 0; // создатель папки
        $this->_data ['description'] = ''; // описание папки
        $this->_data ['time_last'] = 0; // время последних действий
        $this->_data ['sort_default'] = 'runame::asc'; // сортировка по-умолчанию

        if ($cfg_ini = ini::read($path_abs . '/' . $this->_config_file_name, true)) {
            // загружаем конфиг
            $this->_data = array_merge($this->_data, (array) @$cfg_ini ['CONFIG']);
            $this->_screens = array_merge($this->_screens, (array) @$cfg_ini ['SCREENS']);
            $this->_keys = array_merge($this->_keys, (array) @$cfg_ini ['ADDKEYS']);
        } else {
            $this->_data ['time_create'] = TIME; // время создания
            $this->_need_save = true;
        }
        // настоящее имя папки
        $this->_data ['name'] = basename($path_abs);
        // получение путей на основе абсолютного пути
        $this->_setPathes($path_abs);
    }

    public function fileImport($url) {
        if (function_exists('set_time_limit')) {
            set_time_limit(30);
        }
        // Импортировать файл
        $this->error = false;

        misc::log($url, 'loads.import');

        $purl = parse_url($url);

        if (empty($purl ['path'])) {
            misc::log('Путь: ERR', 'loads.import');
            $this->error = __('Путь к файлу не распознан');
            return false;
        } else {
            misc::log('Путь: OK', 'loads.import');
        }


        if (!$fname = basename($purl ['path'])) {
            misc::log('Имя файла: ERR', 'loads.import');
            $this->error = __('Не удалось получить имя файла из пути');
            return false;
        } else {
            misc::log('Имя файла: OK', 'loads.import');
        }

        $curl = new http_client($url);
        $name = text::for_filename($fname);

        if (!$headers = $curl->get_headers()) {
            misc::log('Headers: ERR', 'loads.import');
            $this->error = __('Не удалось получить заголовок');
            return false;
        } else {
            misc::log('Headers: OK', 'loads.import');
        }

        if (!preg_match('#^Content-Length: ([0-9]+)#uim', $headers)) {
            misc::log('Размер файла: ERR', 'loads.import');
            $this->error = __('В заголовке не указан размер');
            return false;
        } else {
            misc::log('Размер файла: OK', 'loads.import');
        }

        if (file_exists($this->_data ['path_abs'] . '/' . $name)) {
            misc::log('Проверка на существование файла: ERR (файл с таким названием уже существует)', 'loads.import');
            $this->error = __('В данной папке уже имеется файл с именем %s', $name);
            return false;
        } else {
            misc::log('Проверка на существование файла: OK', 'loads.import');
        }

        if (!$curl->save_content($this->_data ['path_abs'] . '/' . $name)) {
            misc::log('Сохранение файла: ERR', 'loads.import');
            $this->error = __('Не удалось сохранить файл %s', $name);
            return false;
        } else {
            misc::log('Сохранение файла: OK', 'loads.import');
        }



        @chmod($this->_data ['path_abs'] . '/' . $name, filesystem::getChmodToWrite());
        $f_obj = new files_file($this->_data ['path_abs'], $name);
        $f_obj->time_add = TIME;

        if (!$f_obj->size) {
            $f_obj->delete();
            misc::log('Размер файла = 0: ERR (файл будет удален)', 'loads.import');
            $this->error = __('Скачан файл с нулевым размером');
            return false;
        } else {
            misc::log('Проверка файла: OK', 'loads.import');
        }

        // очистка кэша
        $this->cacheClear();
        misc::log('Очистка кэша: OK', 'loads.import');
        misc::log('Импорт успешно завершен', 'loads.import');
        return $f_obj;
    }

    public function filesAdd($files) {
        // добавление файлов в папку
        $ok = array();
        $files = (array) $files;
        foreach ($files as $path => $runame) {
            $name = text::for_filename($runame);

            if (@move_uploaded_file($path, $this->_data ['path_abs'] . '/' . $name)) {
                @chmod($this->_data ['path_abs'] . '/' . $name, filesystem::getChmodToWrite());

                $ok [$path] = $f_obj = new files_file($this->_data ['path_abs'], $name);
                $f_obj->runame = $runame;
                $f_obj->time_add = TIME;
            } elseif (@copy($path, $this->_data ['path_abs'] . '/' . $name)) {
                @chmod($this->_data ['path_abs'] . '/' . $name, filesystem::getChmodToWrite());

                $ok [$path] = $f_obj = new files_file($this->_data ['path_abs'], $name);
                $f_obj->runame = $runame;
                $f_obj->time_add = TIME;
            }
        }
        // очистка кэша


        $this->cacheClear();
        return $ok;
    }

    public function cacheClear() {
        // очистка кэша директории (а также проверка соответствия записей в базе реальным файлам)
        $path_rel_ru = convert::to_utf8($this->_data ['path_rel']);
        $q = mysql_query("SELECT * FROM `files_cache` WHERE `path_file_rel` LIKE '" . my_esc($path_rel_ru) . "/%'");
        while ($files = @mysql_fetch_assoc($q)) {
            $abs_path = FILES . convert::of_utf8($files ['path_file_rel']);
            if (is_file($abs_path)) {
                continue;
                // если файл существует, то все ОК, пропускаем
            }
            // НО!!! Если файла нет, то это лишняя запись в базе, которую необходимо "похерить"
            // удаление файла из кэша базы
            mysql_query("DELETE FROM `files_cache` WHERE `id` = '" . intval($files ['id']) . "' LIMIT 1");
            // удаление комментов к файлу
            mysql_query("DELETE FROM `files_comments` WHERE `id_file` = '" . intval($files ['id']) . "'");
            // удаление рейтингов файла
            mysql_query("DELETE FROM `files_rating` WHERE `id_file` = '" . intval($files ['id']) . "'");
        }
        // а так же очистка кэша содержимого папки

        cache::set('files.' . $this->_data ['path_rel'], false, 1);
        $this->_need_save = true;
    }

    public function mkdir($runame, $name = false) {
        // создание папки
        if ($name)
            $name = text::for_filename($name);
        else
            $name = text::for_filename($runame);

        if (!filesystem::mkdir($this->_data ['path_abs'] . '/' . $name)) {
            return false;
        }
        $new_dir = new files($this->_data ['path_abs'] . '/' . $name);
        $new_dir->runame = $runame;
        $new_dir->group_show = $this->_data ['group_show'];
        $new_dir->group_write = $this->_data ['group_write'];
        $new_dir->group_edit = $this->_data ['group_edit'];
        $new_dir->time_create = TIME;

        $this->cacheClear();
        return $new_dir;
    }

    public function delete() {
        // удаление папки со всем содержимым
        // если папки не существует, то и удалять ее не можем.
        if (!is_dir($this->_data ['path_abs']))
            return false;
        // папки и файлы с точкой являются системными и их случайное удаление крайне нежелательно
        if ($this->_data ['name'] {0} === '.')
            return false;
        $od = opendir($this->_data ['path_abs']);
        while ($rd = readdir($od)) {
            if ($rd {0} == '.')
                continue;

            if (is_dir($this->_data ['path_abs'] . '/' . $rd)) {
                if (function_exists('set_time_limit')) {
                    set_time_limit(30);
                }

                $dir = new files($this->_data ['path_abs'] . '/' . $rd);
                $dir->delete(); // "Правильное" рекурсивное удаление папки
            } elseif (is_file($this->_data ['path_abs'] . '/' . $rd)) {
                $file = new files_file($this->_data ['path_abs'], $rd);
                $file->delete(); // "Правильное" удаление файла
            }
        }

        closedir($od);

        if (function_exists('set_time_limit'))
            set_time_limit(30);
        if (filesystem::rmdir($this->_data ['path_abs'])) {
            $ld = pathinfo($this->_data ['path_abs']);
            $last_dir = new files($ld ['dirname']);
            $last_dir->cacheClear();

            $this->__destruct();
            return true;
        }
    }

    // получение новых файлов
    public function getNewFiles() {
        $time = NEW_TIME;
        global $user;
        $content = array('dirs' => array(), 'files' => array());
        $path_rel_ru = convert::to_utf8($this->_data ['path_rel']);
        $q = mysql_query("SELECT * FROM `files_cache` WHERE `group_show` <= '" . intval($user->group) . "' AND `path_file_rel` LIKE '" . my_esc($path_rel_ru) . "/%' AND `path_file_rel` NOT LIKE '" . my_esc($path_rel_ru) . "/.%' AND `time_add` > '$time' ORDER BY `time_add` DESC");
        while ($files = mysql_fetch_assoc($q)) {
            $abs_path = FILES . convert::of_utf8($files ['path_file_rel']);
            $pathinfo = pathinfo($abs_path);
            $file = new files_file($pathinfo ['dirname'], $pathinfo ['basename']);

            if (!is_file($abs_path)) {
                $file->delete(); // если файл не существует, то удаляем всю информацию о нем
                continue;
            }
            $content ['files'] [] = $file;
        }

        return $content;
    }

    // поиск файлов в папке и во всех вложенных папках
    protected function _search($search) {
        global $user;
        $content = array('dirs' => array(), 'files' => array());
        $path_rel_ru = convert::to_utf8($this->_data ['path_rel']);
        $q = mysql_query("SELECT * FROM `files_cache` WHERE `group_show` <= '" . intval($user->group) . "' AND `path_file_rel` LIKE '" . my_esc($path_rel_ru) . "/%' AND `path_file_rel` NOT LIKE '" . my_esc($path_rel_ru) . "/.%' AND `runame` LIKE '%" . my_esc($search) . "%'");
        while ($files = mysql_fetch_assoc($q)) {
            $abs_path = FILES . convert::of_utf8($files ['path_file_rel']);
            $pathinfo = pathinfo($abs_path);
            $file = new files_file($pathinfo ['dirname'], $pathinfo ['basename']);

            if (!is_file($abs_path)) {
                $file->delete(); // если файл не существует, то удаляем всю информацию о нем
                continue;
            }
            $content ['files'] [] = $file;
        }

        return $content;
    }

    public function count($is_new = false) {
        if ($is_new) {
            $time = NEW_TIME;
        } else {
            $time = 0;
        }

        global $user;
        $group = (int) $user->group;
        if ($count = cache_counters::get('files.' . $this->_data ['path_rel'] . '.' . (int) $is_new . '.' . $group)) {
            return $count;
        }

        $path_rel_ru = convert::to_utf8($this->_data ['path_rel']);
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `files_cache` WHERE `group_show` <= '$group' AND `time_add` > '$time' AND `path_file_rel` LIKE '" . my_esc($path_rel_ru) . "/%' AND `path_file_rel` NOT LIKE '" . my_esc($path_rel_ru) . "/.%'"), 0);

        cache_counters::set('files.' . $this->_data ['path_rel'] . '.' . (int) $is_new . '.' . $group, $count, 600);
        return $count;
    }

    public function getKeys() {
        // получение возможных ключей для сортировки папки
        $keys = array();
        $keys ['runame:asc'] = __('Название');
        $keys ['size:desc'] = __('Размер');
        $keys ['time_add:desc'] = __('Время добавления');

        return array_merge($keys, $this->_keys);
    }

    // получаем содержимое директории
    protected function _getListFull() {

        if ($content = cache::get('files.' . $this->_data ['path_rel'])) {
            return $content;
        }
        $this->_keys = array();
        $content = array('dirs' => array(), 'files' => array());
        $od = opendir($this->_data ['path_abs']);
        while ($rd = readdir($od)) {
            if ($rd {0} == '.')
                continue; // все файлы и папки начинающиеся с точки пропускаем
            if (is_dir($this->_data ['path_abs'] . '/' . $rd)) {
                $content ['dirs'] [] = new files($this->_data ['path_abs'] . '/' . $rd);
            } elseif (is_file($this->_data ['path_abs'] . '/' . $rd)) {
                $content ['files'] [] = $file = new files_file($this->_data ['path_abs'], $rd);
                $this->_keys = array_merge($this->_keys, $file->getKeys());
            }
        }
        // если файлов и папок мало, то нет необходимости в испозовании кэша
        if (count($content ['dirs']) + count($content ['files']) > 30) {
            cache::set('files.' . $this->_data ['path_rel'], $content, 60);
        }
        $this->_need_save = true;
        closedir($od);
        return $content;
    }

    function _sort_cmp_dir($f1, $f2) {
        if ($f1->position == $f2->position) {
            return strcmp($f1->runame, $f2->runame);
        }
        return ($f1->position < $f2->position) ? -1 : 1;
    }

    protected function _sort_cmp_files($f1, $f2) {
        $sn = $this->user_sort;
        if ($f1->$sn == $f2->$sn) {
            return 0;
        }
        return ($f1->$sn < $f2->$sn) ? - 1 : 1;
    }

    protected function _listSort($list, $sort) {
        usort($list ['dirs'], array($this, '_sort_cmp_dir'));


        if (!$sort) {
            $sort = $this->sort_default;
        }
        @list ($this->user_sort, $order ) = @explode(':', $sort);
        usort($list ['files'], array($this, '_sort_cmp_files'));
        if ($order == 'desc') {
            $list ['files'] = array_reverse($list ['files']);
        }



        return $list;
    }

    protected function _listFilter($list) {
        // исключение из списка всех недоступных пользователю папок и файлов
        global $user;
        $list2 = array();
        $c = count($list);
        for ($i = 0; $i < $c; $i++) {
            if ($list [$i]->group_show <= $user->group)
                $list2 [] = $list [$i];
        }
        return $list2;
    }

    // проверка существования папки
    public function is_dir($name) {
        $list = $this->getList();

        foreach ($list ['dirs'] as $dir) {
            if ($dir->name === $name)
                return true;
        }

        return false;
    }

    // проверка существования файла
    public function is_file($name) {
        $list = $this->getList();

        foreach ($list ['files'] as $file) {
            if ($file->name === $name)
                return true;
        }

        return false;
    }

    // получаем список содержимого директории в отфильтрованном и отсортированном виде
    public function getList($sort = false, $search = false) {
        if ($search) {
            $list = $this->_search($search); // получение списка файлов по запросу
        } else {
            $list = $this->_getListFull(); // получение списка содержимого
        }

        $list ['dirs'] = $this->_listFilter($list ['dirs']); // отсеиваем недоступные папки
        $list ['files'] = $this->_listFilter($list ['files']); // отсеиваем недоступные файлы
        $list = $this->_listSort($list, $sort); // сортируем
        return $list;
    }

    public function getPathRu() {
        // получение пути на русском языке
        $return = array();
        $all_path = array();
        if ($this->_data ['path_rel']) {
            $path_rel = preg_split('#/+#', $this->_data ['path_rel']);
            if ($path_rel) {
                // $all_path[]='/';
                foreach ($path_rel as $key => $value) {
                    $path = '';
                    for ($i = 0; $i < $key; $i++) {
                        $path .= $path_rel [$i] . '/';
                    }
                    if ($path)
                        $all_path [] = $path;
                }
            }

            if ($all_path) {
                for ($i = 0; $i < count($all_path); $i++) {
                    $cnf = new files(FILES . '/' . $all_path [$i]);
                    $return [] = $cnf->runame;
                }
            }
        }

        return ($return ? implode('/', $return) . '/' : '') . $this->_data ['runame'];
    }

    public function getPath() {
        // получение пути к папке для ссылки
        $path_rel = preg_split('#/+#', $this->_data ['path_rel']);
        foreach ($path_rel as $key => $value) {
            $path_rel [$key] = urlencode($value);
        }
        return implode('/', $path_rel) . '/';
    }

    public function getScreens() {
        // получение скриншотов папки
        $screens = array();
        foreach ($this->_screens as $key => $value) {
            if (is_file($this->_data ['path_abs'] . '/' . $value))
                $screens [] = $this->_data ['path_rel'] . '/' . $value;
        }
        return $screens;
    }

    public function ret($k = 5) {
        // вывод массива путей для возврата
        global $user;
        $return = array();
        $all_path = array();
        if ($this->_data ['path_rel']) {
            $path_rel = preg_split('#/+#', $this->_data ['path_rel']);
            if ($path_rel) {
                // $all_path[]='/';
                foreach ($path_rel as $key => $value) {
                    $path = '';
                    for ($i = 0; $i < $key; $i++) {
                        $path .= $path_rel [$i] . '/';
                    }
                    if ($path)
                        $all_path [] = $path;
                }
            }

            if ($all_path) {
                $all_path = array_reverse($all_path);
                for ($i = 0; $i < $k && $i < count($all_path); $i++) {
                    $cnf = new files(FILES . '/' . $all_path [$i]);

                    if ($cnf->group_show > $user->group) {
                        // если пользователь не сможет зайти в папку, то и ссылку показывать не будем
                        $k++;
                        continue;
                    }
                    $return [] = array('path'   => $cnf->getPath(), 'runame' => $cnf->runame);
                }
            }
        }
        return $return;
    }

    public function icon() {
        // получение иконки
        return $this->_data ['type'];
    }

    public function move($new_path_abs) {
        // перемещение папки
        $new_path_abs = filesystem::unixpath($new_path_abs);
        // если новое расположение выходит за рамки Папки загрузок
        if (strpos($new_path_abs, filesystem::unixpath(FILES)) !== 0) {
            return false;
        }
        // новое расположение не может находиться внутри текущего
        if (strpos($new_path_abs, $this->_data ['path_abs']) === 0) {
            return false;
        }
        // если нихрена не прокатило
        if (!rename($this->_data ['path_abs'], $new_path_abs)) {
            return false;
        }
        $path_rel_ru_old = convert::to_utf8($this->_data ['path_rel']);
        $this->_setPathes($new_path_abs);
        $path_rel_ru_new = convert::to_utf8($this->_data ['path_rel']);
        // не забываем и в базе изменить путь вложенных файлов
        mysql_query("UPDATE `files_cache` SET `path_file_rel` = REPLACE(`path_file_rel`, '" . my_esc($path_rel_ru_old) . "', '" . my_esc($path_rel_ru_new) . "') WHERE `path_file_rel` LIKE '" . my_esc($path_rel_ru_old) . "/%'");
        $np = pathinfo($new_path_abs);
        $to_dir = new files($np ['dirname']);
        $to_dir->cacheClear();
        $this->_need_save = true;
        return true;
    }

    protected function _setPathes($path_dir_abs) {
        // установка путей
        // полный путь к папке
        $this->_data ['path_abs'] = filesystem::unixpath($path_dir_abs);
        // относительный путь к папке
        $this->_data ['path_rel'] = str_replace(filesystem::unixpath(FILES), '', $this->_data ['path_abs']);
    }

    public function rename($runame, $realname) {
        // переименование папки
        if ($this->_data ['path_rel'] && $this->_data ['name'] {0} !== '.') {
            $path_new = preg_replace('#[^\/\\\]+$#u', $realname, $this->_data ['path_rel']);

            if (!@rename($this->_data ['path_abs'], FILES . $path_new))
                return false;
            else {
                $this->_data ['name'] = basename($path_new);
            }
            $path_rel_ru_old = convert::to_utf8($this->_data ['path_rel']);
            $this->_setPathes(FILES . $path_new);
            $path_rel_ru_new = convert::to_utf8($this->_data ['path_rel']);
            // не забываем и в базе изменить путь вложенных файлов
            mysql_query("UPDATE `files_cache` SET `path_file_rel` = REPLACE(`path_file_rel`, '" . my_esc($path_rel_ru_old) . "', '" . my_esc($path_rel_ru_new) . "') WHERE `path_file_rel` LIKE '" . my_esc($path_rel_ru_old) . "/%'");
        }
        $np = pathinfo($this->_data ['path_abs']);
        $to_dir = new files($np ['dirname']);
        $to_dir->cacheClear();
        $this->_data ['runame'] = $runame;
        $this->_need_save = true;
        return true;
    }

    public function getPathesRecurse($exclude = false) {
        // получение всех объектов папок (рекурсивно)
        $dirs = array();
        $od = opendir($this->_data ['path_abs']);
        while ($rd = readdir($od)) {
            if ($rd == '.' || $rd == '..')
                continue;
            if (is_dir($this->_data ['path_abs'] . '/' . $rd)) {
                if (function_exists('set_time_limit')) {
                    set_time_limit(30);
                }
                $dir = new files($this->_data ['path_abs'] . '/' . $rd);

                $dirs [] = $dir;
                // обработка исключений
                if ($exclude && strpos($dir->path_abs, $exclude->path_abs) === 0) {
                    continue;
                }
                $dirs = array_merge($dirs, $dir->getPathesRecurse($exclude));
            }
        }

        closedir($od);
        return $dirs;
    }

    public function setGroupShowRecurse($group_show) {
        // установка группы пользователей, которым разрешено просматривать директорию
        // данный параметр будет рекурсивно применен ко всем вложенным объектам
        $od = @opendir($this->_data ['path_abs']);
        while ($rd = @readdir($od)) {
            if ($rd {0} == '.')
                continue;
            if (is_dir($this->_data ['path_abs'] . '/' . $rd)) {
                if (function_exists('set_time_limit'))
                    set_time_limit(30);
                $dir = new files($this->_data ['path_abs'] . '/' . $rd);
                $dir->setGroupShowRecurse($group_show);
            } elseif (is_file($this->_data ['path_abs'] . '/' . $rd)) {
                $file = new files_file($this->_data ['path_abs'], $rd);
                $file->group_show = $group_show;
            }
        }

        @closedir($od);
        $this->cacheClear();
        $this->_data ['group_show'] = $group_show;
        $this->_need_save = true;
    }

    function __get($n) {
        if (isset($this->_data [$n]))
            return $this->_data [$n];
        else
            return false;
    }

    function __set($n, $v) {
        if (!isset($this->_data [$n]))
            return false;
        $this->_data [$n] = $v;
        $this->_need_save = true;
    }

    function __destruct() {
        if ($this->_need_save) {
            $this->_data ['time_last'] = TIME; // время последних действий


            ini::save($this->_data ['path_abs'] . '/' . $this->_config_file_name, array('CONFIG'  => $this->_data, 'SCREENS' => $this->_screens, 'ADDKEYS' => $this->_keys), true);
        }
    }

}

?>

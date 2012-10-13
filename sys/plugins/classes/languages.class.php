<?php

abstract class languages {

    static public function exists($code) {
        // проверка на существование языка
        $list = self::getList();
        return isset($list[$code]);
    }

    static public function getConfig($code) {
        if (!self::exists($code)) {
            return false;
        }
        $list = self::getList();
        return $list[$code];
    }

    static public function getList() {
        static $list;

        if (isset($list)) {
            return $list;
        }

        // получение списка языковых пакетов
        if ($list = cache::get('languages')) {
            return $list;
        }

        $list = self::getRealList();
        cache::set('languages', $list, 60);

        return $list;
    }

    static public function getRealList() {
        $list = array();

        // получение списка языковых пакетов минуя кэш
        $lpath = H . '/sys/languages';
        $od = opendir(H . '/sys/languages');
        while ($rd = readdir($od)) {

            if ($rd {0} == '.') {
                continue; // все файлы и папки начинающиеся с точки пропускаем
            }
            if (is_dir($lpath . '/' . $rd)) {
                if (!file_exists($lpath . '/' . $rd . '/config.ini')) {
                    // если нет конфига, то языковой пакет тоже пропускаем
                    continue;
                }
                $config = ini::read($lpath . '/' . $rd . '/config.ini');
                $enname = empty($config['enname']) ? $rd : $config['enname']; // название языка на английском
                $name = empty($config['name']) ? $rd : $config['name']; // название языка на местном языке
                $xml_lang = empty($config['xml_lang']) ? $rd : $config['xml_lang'];

                $icon = file_exists($lpath . '/' . $rd . '/icon.png') ? '/sys/languages/' . $rd . '/icon.png' : false;

                $list[$rd] = array('enname' => $enname, 'name' => $name, 'icon' => $icon, 'xml_lang' => $xml_lang);
            }
        }
        closedir($od);

        ksort($list);
        reset($list);

        return $list;
    }

    static public function clearCache() {
        cache::set('languages', false);
    }

}

?>

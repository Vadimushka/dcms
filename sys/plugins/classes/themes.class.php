<?php

abstract class themes {

    static public function exists($code, $type = 'all') {
        // проверка на существование языка
        $list = self::getList($type);
        return isset($list[$code]);
    }

    static public function getConfig($code) {
        if (!self::exists($code)) {
            return false;
        }
        $list = self::getList();
        return $list[$code];
    }

    static public function getList($type = 'all') {
        static $list = false;

        if ($list !== false) {
            return self::filterType($list, $type);
        }

        // получение списка языковых пакетов
        if ($list = cache::get('themes'))           
            return self::filterType($list, $type);        

        $list = self::getRealList();
        cache::set('themes', $list, 60);

        return self::filterType($list, $type);
    }

    static protected function filterType($list, $type = 'all') {
        if ($type != 'all') {
            foreach ($list as $dir => $conf) {
                if ($conf['browsers'] && !in_array($type, $conf['browsers'])) {
                    unset($list[$dir]);
                }
            }
        }

        return $list;
    }

    static public function getRealList() {
        global $dcms;
        $list = array();

        // получение списка тем оформления минуя кэш
        $lpath = H . '/sys/themes';
        $od = opendir($lpath);
        while ($rd = readdir($od)) {

            if ($rd {0} == '.') {
                continue; // все файлы и папки начинающиеся с точки пропускаем
            }
            if (is_dir($lpath . '/' . $rd)) {
                if (!file_exists($lpath . '/' . $rd . '/config.ini')) {
                    // если нет конфига, то тему оформления тоже пропускаем
                    continue;
                }

                $config = ini::read($lpath . '/' . $rd . '/config.ini', true);

                if (empty($config['CONFIG'])) {
                    // нет конфигурации
                    continue;
                }

                if (empty($config['CONFIG']['version']) || $config['CONFIG']['version'] != $dcms->theme_version) {
                    // тема оформления не соответствует версии
                    continue;
                }

                $list[$rd] = self::properties($config, $rd);
            }
        }
        closedir($od);

        ksort($list);
        reset($list);

        return $list;
    }

    static protected function properties($config, $dir) {
        if (empty($config['VARS'])) {
            $vars = array();
        } else {
            $vars = $config['VARS'];
        }

        $info = $config['CONFIG'];
        $info['vars'] = &$vars;
        $info['dir'] = $dir;

        if (empty($info['name'])) {
            $info['name'] = $dir;
        }

        if (empty($info['img_width_max'])) {
            $info['img_width_max'] = 300;
        }

        if (empty($info['browsers'])) {
            $info['browsers'] = array();
        } else {
            $info['browsers'] = preg_split('/[\|\,\:\^]/', $info['browsers']);
        }

        if (empty($info['content'])) {
            $info['content'] = 'html';
        }

        if (empty($info['icons'])) {
            $info['icons'] = '/sys/images/icons';
        } else {
            $info['icons'] = '/sys/themes/' . $dir . '/' . $info['icons'];
        }

        return $info;
    }

    static public function clearCache() {
        cache::set('themes', false);
    }

}

?>

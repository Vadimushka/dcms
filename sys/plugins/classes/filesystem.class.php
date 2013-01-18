<?php

abstract class filesystem {

    static function fileToArray($path) {
        $array = array();
        $array2 = array();
        if (file_exists($path)) {
            $array2 = file($path);
        }
        foreach ($array2 as $value) {
            $value = trim($value);
            if (!$value) {
                continue;
            }

            $array [] = $value;
        }
        return $array;
    }

    static function getRelPath($path) {
        $is_array = false;
        if (is_array($path)) {
            $is_array = true;
        } else {
            $path = (array) $path;
        }

        $replace = self::unixpath(H . '/');

        foreach ($path as $k => $p) {
            $p = self::unixpath($p);
            $path[$k] = str_replace($replace, '', $p);
        }

        return $is_array ? $path : $path[0];
    }

    // получение оптимального CHMOD, разрешающего запись
    static function getChmodToWrite($is_dir = false) {
        if ($is_dir) {
            return 0700;
        } else {
            return 0600;
        }
    }

    // получение оптимального CHMOD, разрешающего чтение файла
    static function getChmodToRead($is_dir = false) {
        if ($is_dir) {
            return 0500;
        } else {
            return 0400;
        }
    }

    
    /**
     * Заменяет разделитель директорий на указанный
     * Удаляет повторные разделители
     * @param string $path путь
     * @param string $sep разделитель
     * @return string 
     */
    public static function setPathSeparator($path, $sep = '/') {
        return preg_replace('/[\\\\\/]+/', $sep, $path);
    }
    
    // получаем путь в стиле *UNIX
    static function unixpath($path) {
        return str_replace('\\', '/', $path);
    }

    static function systempath($path) {
        return str_replace(array('\\', '/'), IS_WINDOWS ? '\\' : '/', $path);
    }

    /**
     * Создание директории с установкой прав на запись
     * @param string $p путь
     * @return boolean
     */
    static function mkdir($p) {
        $p = self::systempath($p);
        if (@mkdir($p, filesystem::getChmodToWrite(true), true)) {
            @chmod($p, filesystem::getChmodToWrite(true));
            return true;
        }
    }

    /**
     * Рекурсивное удаление директории
     * @param string $dir
     * @param boolean $delete_this_dir
     * @return boolean
     */
    static function rmdir($dir, $delete_this_dir = true) {
        $dir = realpath($dir);

        if (!$dir)
            return false;

        $od = opendir($dir);
        while ($rd = readdir($od)) {
            if ($rd == '.' || $rd == '..')
                continue;
            if (is_dir($dir . '/' . $rd)) {
                self::rmdir($dir . '/' . $rd);
            } else {
                chmod($dir . '/' . $rd, filesystem::getChmodToWrite());
                unlink($dir . '/' . $rd);
            }
        }
        closedir($od);


        if ($delete_this_dir) {
            chmod($dir, filesystem::getChmodToWrite(1));
            if (!@rmdir($dir)) {
                // бывает, что с первого раза папка не удаляется, но мы попробуем еще раз с секундной задержкой
                clearstatcache();
                sleep(1);
                return @rmdir($dir);
            }
            return true;
        } else {
            return true;
        }
    }

    static function getAllDirs($dir) {
        $list = array();

        $dir = realpath($dir);
        $od = opendir($dir);
        while ($rd = readdir($od)) {
            if ($rd == '.' || $rd == '..') {
                continue;
            }
            if (is_dir($dir . '/' . $rd)) {
                $list[] = self::unixpath($dir . '/' . $rd);
                $list_n = self::getAllDirs($dir . '/' . $rd);
                foreach ($list_n as $path) {
                    $list[] = $path;
                }
            }
        }
        closedir($od);
        return $list;
    }

    static function getAllFiles($dir) {
        $list = array();
        $list_n = array();
        $dir = realpath($dir);
        $od = opendir($dir);
        while ($rd = readdir($od)) {
            if ($rd == '.' || $rd == '..') {
                continue;
            }
            if (is_dir($dir . '/' . $rd)) {
                $list_n[] = self::getAllFiles($dir . '/' . $rd);
            } else {
                $list[] = self::unixpath($dir . '/' . $rd);
            }
        }
        closedir($od);

        foreach ($list_n as $lists) {
            foreach ($lists as $path) {
                $list[] = $path;
            }
        }


        return $list;
    }

    // получаем общий размер устаревших временных файлов
    static function getOldTmpFilesSize() {
        $files = self::getOldTmpFiles();
        $size = 0;
        foreach ($files as $path) {
            $size += @filesize($path);
        }
        return $size;
    }

    // удаляем временные файлы, которые не обновлялись более суток
    static function deleteOldTmpFiles() {
        if (@function_exists('set_time_limit')) {
            @set_time_limit(300); // ставим ограничение на 5 минут
        }


        $yesterday = TIME - 86400;

        // получаем список папок, в которых могут содержаться временные файлы
        $dirs = array(H . '/sys/tmp');

        $th_o = opendir(H . '/sys/themes');
        while ($th_r = readdir($th_o)) {
            if ($th_r {0} === '.') {
                continue;
            }
            if (!is_dir(H . '/sys/themes/' . $th_r . '/tpl_cache')) {
                continue;
            }
            $dirs [] = H . '/sys/themes/' . $th_r . '/tpl_cache';
        }
        closedir($th_o);


        $count = count($dirs);
        for ($i = 0; $i < $count; $i++) {
            $od = opendir($dirs [$i]);
            while ($rd = readdir($od)) {
                if ($rd {0} === '.') {
                    // файлы, начинающиеся с точки пропускаем
                    continue;
                }
                if (filemtime($dirs [$i] . '/' . $rd) > $yesterday) {
                    // файл еще не старый
                    continue;
                }
                @unlink($dirs [$i] . '/' . $rd);
            }
            closedir($od);
        }
    }
    
    public static function getFilesByPattern($path_abs, $pattern = '/.*/', $recursive = false) {
        $list = array();
        $paths = (array) glob(realpath($path_abs) . '/*');

        foreach ($paths as $path) {
            if (is_file($path) && preg_match($pattern, basename($path)))
                $list[] = self::setPathSeparator($path);
            elseif ($recursive)
                $list = array_merge($list, self::getFilesByPattern($path, $pattern, $recursive));
        }

        return $list;
    }

}

?>
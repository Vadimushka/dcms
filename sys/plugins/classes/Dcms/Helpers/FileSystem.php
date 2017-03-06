<?php

namespace Dcms\Helpers;

/**
 * Упрощение работы с файловой системой
 * Class FileSystem
 * @package Dcms\Helpers
 */
abstract class FileSystem
{

    /**
     * Возвращает путь к сайту относительно корневой папки сайта
     * Если движок установлен в корне ( http://example.com/ ), то вернет пустую строку.
     * Если движок установлен в подпапку ( http://example.com/site/ ), то вернет строку "/site"
     */
    public static function getSiteRelPath()
    {
        return self::getRelPath(H);
    }

    /**
     * Возвращает путь относительно корневой директории сайта.
     * Пригодится для получения url к файлу для клиента (браузера) по его пути в файловой системе.
     * !!! Не является противоположностью для метода getAbsPath, в случае, если движок установлен не в корневую директорию сайта
     * @param string $abs_path
     * @return string
     */
    public static function getRelPath($abs_path)
    {
        return str_replace(self::getUnixPath($_SERVER['DOCUMENT_ROOT']), '', self::getUnixPath($abs_path));
    }

    /**
     * Возвращает абсолютный путь к файлу (или папке) в файловой системе по пути, относительно папки с установленным движком.
     * !!! Не является противоположностью для метода getRelPath, в случае, если движок установлен не в корневую директорию сайта
     * @param string $rel_path
     * @return string
     */
    public static function getAbsPath($rel_path)
    {
        if ($rel_path{0} !== '/' && $rel_path{0} !== '\\') {
            $rel_path = '/' . $rel_path;
        }

        return H . self::getSystemPath($rel_path);
    }

    /**
     * Возвращает путь в unix стиле
     * @param $path
     * @return string
     */
    public static function getUnixPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Возвращает путь в системном стиле
     * @param string $path
     * @return string
     */
    public static function getSystemPath($path)
    {
        return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Сохраниение файла
     * @param $path
     * @param $content
     * @return bool
     * @throws \Exception
     */
    static function saveFileContent($path, $content)
    {
        $tmp_path = $path . '.tmp';

        // сохраняем во временный файл
        if (!@file_put_contents($tmp_path, $content)) {
            throw new \Exception(__('Не удалось сохранить временный файл "%s"', $tmp_path));
        }
//        @chmod($tmp_file, filesystem::getChmodToWrite());

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // в винде файл перед заменой нужно удалить
            if (@file_exists($path) && !@unlink($path)) {
                throw new \Exception(__('Не удалось удалить целевой файл "%s"', $path));
            }
        }
        // переименовываем временный файл в нужный нам
        if (!@rename($tmp_path, $path)) {
            throw new \Exception(__('Не переименовать временный файл "%s" в целевой "%s"', $tmp_path, $path));
        }
//        @chmod($file, filesystem::getChmodToWrite());

        return true;
    }

    static function appendFileContent($path, $content)
    {
        if (!@file_put_contents($path, $content, FILE_APPEND | LOCK_EX)) {
            throw new \Exception(__('Не удалось добавить содержимое в файл "%s"', $path));
        }

        return true;
    }
}
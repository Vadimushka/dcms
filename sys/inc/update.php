<?php

class update {

    protected $_tmp_path = false, $_backup_path = false, $_zip = false, $_checked = false, $_skip = array();
    var $version = false;
    var $err = array();

    public function __construct($zip = false) {
        $this->_tmp_path = TMP . '/' . passgen(); // папка с временными файлами

        if ($zip) {
            $this->_zip = $zip;
        } elseif ($zip = $this->_downloadLatestVersion()) {
            $this->_zip = $zip;
        } else {
            $this->_zip = false;
        }

        if ($this->_zip) {
            if (!filesystem::mkdir($this->_tmp_path)) {
                $this->err[] = 'Не удалось создать временную папку';
                $this->log('Не удалось создать временную папку');
            } elseif (!$this->_extract()) {
                $this->err[] = 'Не удалось распаковать архив с обновлением';
                $this->log('Не удалось распаковать архив с обновлением');
            } elseif (!$this->_check()) {
                $this->err[] = 'Ошибка целостности пакета обновления';
                $this->log('Ошибка целостности пакета обновления');
            } elseif (!$this->_check_version()) {
                $this->err[] = 'Пакет обновления не предназначен для данной версии';
                $this->log('Пакет обновления не предназначен для данной версии');
            } else {
                $this->_checked = true;
            }
        }
    }

    public function getLatestVersion() {
        // последняя версия DCMS
        $curl = new http_client('http://dcms.su/build/config.ini');
        $config_content = $curl->getContent();

        if (!$config_content) {
            $this->log('Не удалось получить информацию о последней версии');
            return false;
        }



        if (!$conf = ini::openString($config_content)) {
            $this->log('Не удалось прочитать информацию о последней версии');
            return false;
        }

        if (empty($conf['version_last']) || empty($conf['build_num'])) {
            $this->log('В информации о последней версии присутствуют не все данные');
            return false;
        }


        return $conf['version_last'] . '.' . $conf['build_num'];
    }

    protected function _downloadLatestVersion() {
        // скачивание последней версии      

        global $dcms;
        if (!$newversion = $this->getLatestVersion()) {
            return false;
        }

        if ($dcms->version === $newversion) {
            $this->err[] = 'Обновление не требуется';
            $this->log('Обновление не требуется');
            return false;
        }


        $curl = new http_client('http://dcms.su/build/updates.php?from=' . $dcms->version . '&to=' . $newversion);
        $tmp_file = TMP . '/' . passgen() . '.zip';
        if ($curl->save_content($tmp_file)) {
            $this->log('Не удалось загрузить пакет обновления');
            return $tmp_file;
        } else {
            return false;
        }
    }

    protected function _extract() {
        $zip = new PclZip($this->_zip);
        if ($zip->extract(PCLZIP_OPT_PATH, $this->_tmp_path . '/')) {
            return true;
        } else {

            //echo file_get_contents($this->_zip);
            return false;
        }
    }

    protected function _check() {

        // проверка содержимого пакета обновления
        if (!file_exists($this->_tmp_path . '/to_delete.ini')) {
            return false;
        }
        if (!file_exists($this->_tmp_path . '/to_update.ini')) {
            return false;
        }
        if (!file_exists($this->_tmp_path . '/version.ini')) {
            return false;
        }

        $to_update = keyvalue::read($this->_tmp_path . '/to_update.ini');

        foreach ($to_update as $fname) {
            if (!file_exists($this->_tmp_path . '/' . $fname)) {
                // отсутствует заявленный в обновлении файл
                return false;
            }
        }
        return true;
    }

    protected function _check_version() {
        global $dcms;
        // проверка соответствия обновления текущей версии движка
        if (!$version = keyvalue::read($this->_tmp_path . '/version.ini')) {
            return false;
        }

        if (empty($version['from']) || $dcms->version !== $version['from']) {
            // пакет обновления не предназначен для этой версии
            return false;
        }

        $this->version = $version['to'];
        return true;
    }

    public function is_updateble() {
        if (!$this->_checked) {
            return false;
        }
        return $this->version;
    }

    public function getUpdatebleFiles() {
        return keyvalue::read($this->_tmp_path . '/to_update.ini');
    }

    public function setSkipFiles($files) {
        $this->_skip = (array) $files;
    }

    public function start() {
        if (!$this->is_updateble()) {
            return false;
        }

        cache_events::set('system.update.work', true, 600);
        $return = $this->_start();
        cache_events::set('system.update.work', false);
        return $return;
    }

    protected function _start() {
        // запуск обновления
        set_time_limit(600); // время на обновление движка ставим 10 минут. Этого более чем достаточно для выполнения всех действий.
        ignore_user_abort(); // нельзя прерывать процесс обновления даже если пользователем он отменен.

        $this->log('Начинаем процесс обновления');
        $this->log('Файл обновления: ' . $this->_zip);



        $to_delete = (array) keyvalue::read($this->_tmp_path . '/to_delete.ini');
        $files_to_backup = $to_update = (array) keyvalue::read($this->_tmp_path . '/to_update.ini');

        foreach ($to_delete as $file => $hash) {
            $files_to_backup[] = $file;
        }



        foreach ($this->_skip as $file) {
            if ($key = array_search($file, $to_update)) {
                unset($to_update[$key]);
            }
        }




        $this->log('Создаем резервную копию обновляемых файлов');
        if (!$this->_backup_path = $this->_backup_create($files_to_backup)) {
            // если бэкап не создали, то обновлять опасно.
            $this->log('При создании резервной копии возникли ошибки. Обновление отменено.');
            return false;
        }
        $this->log('Резервная копия успешно создана');



        $this->log('Удаляем старые файлы системы');
        if (!$this->_delete($to_delete)) {
            // если во время удаления файлов возникла ошибка, то продолжать не будем, а лучше восстановим как было
            $this->log('При удалении файлов возникли ошибки. Восстанавливаем данные из резервной копии');
            if ($this->_recovery()) {
                $this->log('Данные из резервной копии восстановлены. Обновление отменено.');
            } else {
                $this->log('При восстановлении данных из резервной копии произошли ошибки');
            }
            return false;
        }
        $this->log('Файлы успешно удалены');


        $this->log('Начинаем процесс обновления');
        if (!$this->_update_files($to_update)) {
            // если какой-то файл обновить не удалось, то восстанавливаемся из бэкапа
            $this->log('Не удалось обновить некоторые файлы. Восстанавливаем данные из резервной копии');
            if ($this->_recovery()) {
                $this->log('Данные из резервной копии восстановлены. Обновление отменено.');
            } else {
                $this->log('При восстановлении данных из резервной копии произошли ошибки');
            }

            return false;
        }
        $this->log('Обновление файлов движка произведено успешно');

        // обновляем Базу данных
        $this->log('Обновляем структуру базы данных');
        $this->_sql();
        $this->log('Структура базы данных успешно обновлена');

        $this->log('Обновление движка успешно завершено');
        return true;
    }

    public function log($text) {
        //echo $text . "<br />";
        misc::log($text, 'system.update');
    }

    protected function _backup_create($files) {
        // создание бэкапа обновляемых и удаляемых файлов
        global $dcms;

        $to_backup = array();
        foreach ($files as $key => $value) {
            if (!file_exists(H . '/' . $value)) {
                continue;
            }
            $to_backup[] = H . '/' . $value;
        }

        $zip_file = TMP . '/backup.' . $dcms->version . '.' . TIME . '.zip';

        $zip = new PclZip($zip_file);
        if (!$zip->create($to_backup, PCLZIP_OPT_REMOVE_PATH, H . '/')) {
            $this->log('Не удалось создать архив с резервной копией. Ошибка: ' . $zip->errorInfo(true));
            @unlink($zip_file);
            return false;
        }


        return $zip_file;
    }

    protected function _recovery() {
        // восстановление бэкапа при неудаче
        $zip = new PclZip($this->_backup_path);
        return $zip->extract(PCLZIP_OPT_PATH, H . '/');
    }

    protected function _delete($to_delete) {
        // удаление файлов, заданных обновлением
        foreach ($to_delete as $path => $hash) {
            $file = H . '/' . $path;
            if (!file_exists($file)) {
                continue;
            }

            if (!@unlink($file)) {
                return false;
            }
        }
        return true;
    }

    protected function _update_files($to_update) {
        // замена файлов
        foreach ($to_update as $file) {
            $dirname = dirname(H . '/' . $file);

            if (!@is_dir($dirname)) {

                $this->log('Папка ' . $dirname . ' не обнаружена на сервере');
                if (filesystem::mkdir($dirname)) {
                    $this->log('Папка ' . $dirname . ' успешно создана');
                } else {
                    $this->log('Не удалось создать папку ' . $dirname);
                }
            }
            if (!@copy($this->_tmp_path . '/' . $file, H . '/' . $file)) {

                $this->log('Не удалось обновить (сохранить) файл ' . $file);
                return false;
            }
            @chmod(H . '/' . $file, filesystem::getChmodToWrite());
        }
        return true;
    }

    protected function _sql() {
        // обновление структуры таблиц в базе данных


        $tables_exists = new tables();
        $table_files = (array) glob(H . '/sys/preinstall/base.create.*.ini');
        $tables = array();
        foreach ($table_files as $table_file) {
            preg_match('#base.create\.(.+)\.ini#ui', $table_file, $m);
            $tables[] = $m[1];
        }


        foreach ($tables as $table) {

            $tab = new table_structure(H . '/sys/preinstall/base.create.' . $table . '.ini');
            // если такая таблица уже существует, то получаем запрос на ее изменение
            if (in_array($table, $tables_exists->tables)) {
                $tab_old = new table_structure();
                $tab_old->loadFromBase($table);
                $sql = $tab_old->getSQLQueryChange($tab);
            } else {
                $sql = $tab->getSQLQueryCreate();
            }

            mysql_query($sql);
        }
    }

    function __destruct() {

        filesystem::rmdir($this->_tmp_path, true);

        //@unlink($this->_backup_path);
        //@unlink($this->_zip);
    }

}

?>

<?php

class ban_by_ip {

    protected $_list_long = array(); // список забаненых IP в формате long
    protected $_list_pattern = array(); // список забаненых IP в формате шаблона (192.168.1.*)
    protected $_need_save = false; // необходимость сохранения

    function __construct() {
        if ($ini = ini::read(H . '/sys/ini/ban_by_ip.ini', true)) {
            if (!empty($ini['LONG'])) {
                $this->_list_long = (array) $ini['LONG'];
            }

            if (!empty($ini['PATTERN'])) {
                $this->_list_pattern = (array) $ini['PATTERN'];
            }
        }
    }

    // добавляем IP в бан в виде шаблона 192.168.1.*
    function addPattern($pattern, $msg, $time = 0) {
        if (!preg_match('#^([0-9]{1,3}|\*)\.([0-9]{1,3}|\*)\.([0-9]{1,3}|\*)\.([0-9]{1,3}|\*)$#', $pattern))
            return false;
        // кодируем шаблон в формат, принимаемый INI файлом
        $pattern_encoded = str_replace(array('\.', '*'), array('d', 'n'), $pattern);
        $time_to = $time;

        if (isset($this->_list_pattern[$pattern_encoded])) {
            @list($time_old, $msg) = @explode(':', $this->_list_pattern[$pattern_encoded], 2);
            // если предыдущее время бана бессрочно или больше устанавливаемого, то его и оставляем
            if (!$time_old || $time < $time_old)
                $time_to = $time_old;
        }
        $this->_list_pattern[$pattern_encoded] = $time_to . ':' . $msg;
        $this->_need_save = true;
        return true;
    }

    // добавляем IP в бан в виде LONG представления
    function addIpLong($ip_long, $msg, $time = 0) {
        $time_to = $time;

        if (isset($this->_list_long[$ip_long])) {
            @list($time_old, $msg) = @explode(':', $this->_list_long[$ip_long], 2);
            // если предыдущее время бана бессрочно или больше устанавливаемого, то его и оставляем
            if (!$time_old || $time < $time_old)
                $time_to = $time_old;
        }
        $this->_list_long[$ip_long] = $time_to . ':' . $msg;
        $this->_need_save = true;
        return true;
    }

    function is_ban($ip_long) {
        // проверка IP адреса на бан
        $ip_string = long2ip($ip_long);
        // поиск в списке IP long
        if (isset($this->_list_long[$ip_long])) {
            @list($time, $msg) = @explode(':', $this->_list_long[$ip_long], 2);
            if (!$msg)
                $msg = '[Без описания]';

            if (!$time)
                return $msg; // неограниченное время бана
            if ($time > TIME)
                return $msg; // время бана еще не вышло

                
// если запись есть, но бан не активен, то удаляем эту запись к буям собачьим
            unset($this->_list_long[$ip_long]);
            $this->_need_save = true;
        }
        // поиск в шаблонах
        foreach ($this->_list_pattern as $pattern_encoded => $data) {
            $pattern = str_replace(array('d', 'n'), array('\.', '[0-9]+'), $pattern_encoded);

            if (preg_match('#^' . $pattern . '$#', $ip_string)) {
                // только если совпал шаблон
                @list($time, $msg) = @explode(':', $data, 2);
                if (!$msg)
                    $msg = '[Без описания]';

                if (!$time)
                    return $msg; // неограниченное время бана
                if ($time > TIME)
                    return $msg; // время бана еще не вышло

                    
// если шаблон есть, но бан не активен, то удаляем эту запись к буям собачьим
                unset($this->_list_pattern[$pattern_encoded]);
                $this->_need_save = true;
            }
        }
    }

    function __destruct() {
        if (!$this->_need_save) {
            return false;
        }

        ini::save(H . '/sys/ini/ban_by_ip.ini', array('LONG' => $this->_list_long, 'PATTERN' => $this->_list_pattern), true);
    }

}

?>

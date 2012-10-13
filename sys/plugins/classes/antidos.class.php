<?php
class antidos {
    protected $_log; // список переходов
    protected $_settings = array();

    function __construct()
    {
        global $dcms;

        if ($dcms->antidos_period)
            $this->_settings['period'] = (int)$dcms->antidos_period;

        if ($dcms->antidos_max_visits_per_second)
            $this->_settings['max_visits_per_second'] = (int)$dcms->antidos_max_visits_per_second;

        if ($dcms->antidos_visits_per_second_for_ban)
            $this->_settings['visits_per_second_for_ban'] = (int)$dcms->antidos_visits_per_second_for_ban;

        $this->_process();
    }

    protected function _process()
    {
        global $dcms;
        $this->_log = (array)cache::get('antidos.' . $dcms->ip_long);
        // удаление устаревших переходов
        foreach ($this->_log as $id => $log) {
            if ($log['time'] < TIME - $this->_settings['period'])
                unset($this->_log[$id]);
        }
        $this->_log[] = array('time' => TIME);
        cache::set('antidos.' . $dcms->ip_long, $this->_log, $this->_settings['period']);
        $count_for_period = count($this->_log);
        $count_per_seconds = intval($count_for_period / $this->_settings['period']);

        if ($count_per_seconds > $this->_settings['visits_per_second_for_ban']) {
            global $ban_by_ip;

            $ban_by_ip->addIpLong($dcms->ip_long, 'Превышение разрешенного кол-ва обращений к сайту (antidos)', $this->_settings['period'] * 10);
            exit('Превышение разрешенного кол-ва обращений к сайту (antidos). Ваш IP забанен');
        }

        if ($count_per_seconds > $this->_settings['max_visits_per_second'])
            exit('Превышение разрешенного кол-ва обращений к сайту (antidos). Зайдите на сайт через ' . $this->_settings['period'] . ' сек');
    }
}

?>

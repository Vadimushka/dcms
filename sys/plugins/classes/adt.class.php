<?php

class adt extends menu {

    function __construct() {
        parent::__construct();
        $this->menu_arr = array('Под заголовком' => array('id' => 'top', 'url' => '?id=top', 'icon' => 'adt.png'),
            'Низ сайта' => array('id' => 'bottom', 'url' => '?id=bottom', 'icon' => 'adt.png'));
        //return $this->menu('adt');
    }

    function __get($id) {
        return $this->getArrayAdtForId($id);
    }

    // проверка id на существование? получение названия площадки
    function getNameById($id) {
        foreach ($this->menu_arr as $key => $value) {
            if (isset($value['id']) && $value['id'] == $id)
                return $key;
        }
        // в случае неудачи возвращаем false
        return false;
    }

    // получаем список рекламы в виде массива для определенного места
    private function getArrayAdtForId($id) {
        global $dcms;
        $return = array();
        if ($this->getNameById($id)) {
            $target = $dcms->browser_type == 'web' ? ' target="_blank"' : '';
            $q = mysql_query("SELECT * FROM `advertising` WHERE `space` = '" . my_esc($id) . "' AND `" . (IS_MAIN ? 'page_main' : 'page_other') . "` = '1' AND (`time_start` < '" . TIME . "' OR `time_start` = '0') AND (`time_end` > '" . TIME . "' OR `time_end` = '0') ORDER BY `time_start` ASC");
            while ($adt = mysql_fetch_assoc($q)) {
                if ($adt['url_img']) {
                    $return[] = '<a rel="nofollow" href="http://' . $_SERVER['HTTP_HOST'] . '/link.ext.php?url=' . urlencode($adt['url_link']) . '"' . $target . '><img src="' . $adt['url_img'] . '" alt="' . for_value($adt['name']) . '" /></a>';
                } else {
                    $return[] = ($adt['bold'] ? '<b>' : '') . '<a rel="nofollow" href="http://' . $_SERVER['HTTP_HOST'] . '/link.ext.php?url=' . urlencode($adt['url_link']) . '"' . $target . '>' . for_value($adt['name']) . '</a>' . ($adt['bold'] ? '</b>' : '');
                }
            }
            if (!isset($_SESSION['adt'][$id]['time_show']) || $_SESSION['adt'][$id]['time_show'] < TIME - 10) {
                // показ рекламы засчитывается один раз в 10 секунд
                $_SESSION['adt'][$id]['time_show'] = TIME;
                mysql_query("UPDATE `advertising` SET `count_show_" . $dcms->browser_type . "` = `count_show_" . $dcms->browser_type . "` + 1 WHERE `space` = '" . my_esc($id) . "' AND `" . (IS_MAIN ? 'page_main' : 'page_other') . "` = '1' AND (`time_start` < '" . TIME . "' OR `time_start` = '0') AND (`time_end` > '" . TIME . "' OR `time_end` = '0')");
            }
        }
        return $return;
    }

}

// END class adt
?>
<?php

/**
 * Запись посещений
 */
class log_of_visits {

    private $db;

    function __construct() {
        global $dcms;
        $this->db = DB::me();
        if (!cache_log_of_visits::get($dcms->ip_long)) {
            $res = $this->db->prepare("INSERT INTO `log_of_visits_today` (`time`, `browser_type`, `id_browser`, `iplong`) VALUES (?, ?, ?, ?)");
            $res->execute(Array(DAY_TIME, $dcms->browser_type, $dcms->browser_id, $dcms->ip_long));
            cache_log_of_visits::set($dcms->ip_long, true, 1);
        }
    }

    // подведение итогов посещений по дням
    function tally() {
     //   $res = $this->db->query("LOCK TABLES `log_of_visits_today` WRITE READ, `log_of_visits_for_days` WRITE READ");
        // запрашиваем дни, которые есть в базе исключая текущий
        $q = $this->db->prepare("SELECT DISTINCT `time`  FROM `log_of_visits_today` WHERE `time` <> ?");
        $q->execute(Array(DAY_TIME));
        $res_hits = $this->db->prepare("SELECT COUNT(*) AS cnt FROM `log_of_visits_today` WHERE `time` = ? AND `browser_type` = ?");
        $res_hosts = $this->db->prepare("SELECT COUNT(DISTINCT `iplong` , `id_browser`) AS cnt FROM `log_of_visits_today` WHERE `time` = ? AND `browser_type` = ?");
        $res_insert = $this->db->prepare("INSERT INTO `log_of_visits_for_days` (`time_day`, `hits_web`,`hosts_web`,`hits_wap`,`hosts_wap`,`hits_pda`,`hosts_pda`,`hits_itouch`,`hosts_itouch`) VALUES (?,?,?,?,?,?,?,?,?)");
        while ($day = $q->fetch()) {
            $res_hits->execute(Array($day['time'], 'wap'));
            $hits['wap'] = ($row = $res_hits->fetch()) ? $row['cnt'] : 0;
            $res_hits->execute(Array($day['time'], 'pda'));
            $hits['pda'] = ($row = $res_hits->fetch()) ? $row['cnt'] : 0;
            $res_hits->execute(Array($day['time'], 'itouch'));
            $hits['itouch'] = ($row = $res_hits->fetch()) ? $row['cnt'] : 0;
            $res_hits->execute(Array($day['time'], 'web'));
            $hits['web'] = ($row = $res_hits->fetch()) ? $row['cnt'] : 0;

            $res_hosts->execute(Array($day['time'], 'wap'));
            $hosts['wap'] = ($row = $res_hosts->fetch()) ? $row['cnt'] : 0;
            $res_hosts->execute(Array($day['time'], 'pda'));
            $hosts['pda'] = ($row = $res_hosts->fetch()) ? $row['cnt'] : 0;
            $res_hosts->execute(Array($day['time'], 'itouch'));
            $hosts['itouch'] = ($row = $res_hosts->fetch()) ? $row['cnt'] : 0;
            $res_hosts->execute(Array($day['time'], 'web'));
            $hosts['web'] = ($row = $res_hosts->fetch()) ? $row['cnt'] : 0;

            $res_insert->execute(Array($day['time'], $hits['web'], $hosts['web'], $hits['wap'], $hosts['wap'], $hits['pda'], $hosts['pda'], $hits['itouch'], $hosts['itouch']));
        }
        $res = $this->db->prepare("DELETE FROM `log_of_visits_today` WHERE `time` <> ?");
        $res->execute(Array(DAY_TIME));
        // оптимизация таблиц после удаления данных
        $this->db->query("OPTIMIZE TABLE `log_of_visits_today`");
        // разблокируем таблицы
        $this->db->query("UNLOCK TABLES");
    }

}

?>
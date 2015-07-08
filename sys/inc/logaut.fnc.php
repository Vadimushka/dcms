<?php /* Авторизация 2.5 for DCMS 7.5.1.151+ by S1S13AF7 */
/* придумайте пожалуйста где лучше ее разместить. 
 * как вариант в самом login.php, 
 * но ведь и для ВК тоже надо...
 * Ваши предложения в коменты */
if(!function_exists('logaut')) {
function logaut($id, $method, $status) {
        global $db, $dcms; /* будем получать IP, ID браузера, домен, делать запросы */
        $ua = (string) @$_SERVER['HTTP_USER_AGENT']; // (string) USER_AGENT
        
        $q = $db->prepare("SELECT * FROM `log_of_user_aut` WHERE `id_user` = :id AND `iplong` = :ip_long AND `browser_ua` = :ua AND `domain` = :domain AND `method` = :method AND `status` = :status ORDER BY `time` DESC LIMIT 1");
        $q->execute(Array(':id' => $id, ':ip_long' => $dcms->ip_long, ':ua' => $ua, ':domain' => $dcms->subdomain_main, ':method' => $method, ':status' => $status));

        if(!$row = $q->fetch()) { /* якщо записи з такими параметрами відсутні, то додаємо у базу */
            $res = $db->prepare("INSERT INTO `log_of_user_aut` (`id_user`,`method`,`iplong`, `time`, `id_browser`,`browser`,`browser_ua`,`domain`,`status`) VALUES (:id,:method,:ip_long,:time,:br_id,:br_name,:ua,:domain,:status)");
            $res->execute(Array(':id' => $id, ':ip_long' => $dcms->ip_long, ':ua' => $ua, ':domain' => $dcms->subdomain_main, ':method' => $method, ':status' => $status, ':br_id' => $dcms->browser_id, ':br_name' => $dcms->browser_name, ':time' => TIME));
        } else {
            $res = $db->prepare("UPDATE `log_of_user_aut` SET `time` = :time, `id_browser` = :br_id, `count` = `count` + 1 WHERE `id_user` = :id AND `iplong` = :ip_long AND `browser_ua` = :ua AND `domain` = :domain AND `method` = :method AND `status` = :status LIMIT 1");
            $res->execute(Array(':id' => $id, ':ip_long' => $dcms->ip_long, ':ua' => $ua, ':domain' => $dcms->subdomain_main, ':method' => $method, ':status' => $status, ':br_id' => $dcms->browser_id, ':time' => TIME));
        }
    }
}

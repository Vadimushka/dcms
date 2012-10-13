<?php

class browser {

    var $browser_type_auto;
    var $browser;
    var $ip_long;

    function __construct() {
        $this->browser_info();
        $this->ip();
    }

    private function ip() {
        $this->ip_long = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
        // $this->ip=1827459359;
        // IP адрес, получаемый из HTTP_X_FORWARDED_FOR передается клиентом, поэтому 100% доверять ему нельзя
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('#Opera#i', $_SERVER['HTTP_USER_AGENT'])) {
                $this->ip_proxy = sprintf("%u", ip2long($_SERVER['HTTP_X_FORWARDED_FOR']));
            }
        }
    }

    private function browser_info() {
        $user_agent = @$_SERVER['HTTP_USER_AGENT'];
        $this->browser_type_auto = 'wap';
        $this->browser = 'Нет данных';
        // определение названия браузера
        if (preg_match('#^([a-z0-9\-\_ ]+)/([0-9]+\.[0-9]+)#i', $user_agent, $b)) {
            $this->browser = $b[1] . (!empty($b[2]) ? ' ' . $b[2] : '');
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#MSIE#ui', $user_agent)) {
            $this->browser = 'Microsoft Internet Explorer';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#America Online Browser#i', $user_agent)) {
            $this->browser = 'AOL Explorer';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#(Avant|Advanced) Browser#i', $user_agent)) {
            $this->browser = 'Avant Browser';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Camino/([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            $this->browser = 'Camino ' . $v[1];
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#ELinks#i', $user_agent)) {
            $this->browser = 'ELinks';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Epiphany#i', $user_agent)) {
            $this->browser = 'Epiphany';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Flock#i', $user_agent)) {
            $this->browser = 'Flock';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#IceWeasel#i', $user_agent)) {
            $this->browser = 'GNU IceWeasel';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#IceCat#i', $user_agent)) {
            $this->browser = 'GNU IceCat';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Microsoft Pocket Internet Explorer#i', $user_agent)) {
            $this->browser = 'Internet Explorer Mobile';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#MSPIE#i', $user_agent)) {
            $this->browser = 'Internet Explorer Mobile';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#Windows.+Smartphone#i', $user_agent)) {
            $this->browser = 'Internet Explorer Mobile';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#Konqueror#i', $user_agent)) {
            $this->browser = 'Konqueror';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Links#i', $user_agent)) {
            $this->browser = 'Links';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Lynx#i', $user_agent)) {
            $this->browser = 'Lynx';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Minimo#i', $user_agent)) {
            $this->browser = 'Minimo';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#(Firebird|Phoenix|Firefox)/([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            $this->browser = 'Mozilla Firefox ' . $v[2];
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#NetPositive#i', $user_agent)) {
            $this->browser = 'NetPositive';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Opera/([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            $ver = $this->browser_version($user_agent);
            $this->browser = 'Opera ' . ($ver ? $ver : $v[1]);
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Opera Mini/([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            $this->browser = 'Opera Mini ' . $v[1];
            $this->browser_type_auto = 'wap';
        }

        if (preg_match('#Opera Mobi#i', $user_agent)) {
            $ver = $this->browser_version($user_agent);
            if ($tel = $this->phone_model($user_agent))
                $this->browser = 'Opera Mobile' . ($ver ? ' ' . $ver : '') . ' (' . $tel[0] . ')';
            else
                $this->browser = 'Opera Mobile' . ($ver ? ' ' . $ver : '');
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#(SymbOS|Symbian).+Opera ([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            if ($tel = $this->phone_model($user_agent))
                $this->browser = 'Opera Mobile ' . $v[2] . ' (' . $tel[0] . ')';
            else
                $this->browser = 'Opera Mobile ' . $v[2] . ' (Symbian)';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#Windows CE.+Opera ([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            if ($tel = $this->phone_model($user_agent))
                $this->browser = 'Opera Mobile ' . $v[1] . ' (' . $tel[0] . ')';
            else
                $this->browser = 'Opera Mobile ' . $v[1] . ' (Win)';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#PlayStation Portable#i', $user_agent)) {
            $this->browser = 'PlayStation Portable';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Safari#i', $user_agent)) {
            $this->browser = 'Safari';
            $this->browser_type_auto = 'web';
        }





        if (preg_match('#SeaMonkey#i', $user_agent)) {
            $this->browser = 'SeaMonkey';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Shiira#i', $user_agent)) {
            $this->browser = 'Shiira';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#w3m#i', $user_agent)) {
            $this->browser = 'w3m';
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#Chrome/([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            $this->browser = 'Google Chrome ' . $v[1];
            $this->browser_type_auto = 'web';
        }

        if (preg_match('#SONY/COM#i', $user_agent)) {
            $this->browser = 'Sony mylo';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#Nitro#i', $user_agent)) {
            $this->browser = 'Nintendo DS';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#^Openwave#i', $user_agent)) {
            $this->browser = 'Openwave';
            $this->browser_type_auto = 'pda';
        }

        if (preg_match('#UCWEB#i', $user_agent)) {
            $this->browser = 'UCWEB';
            $this->browser_type_auto = 'wap';
        }

        if (preg_match('#BOLT/([0-9]+\.[0-9]+)#i', $user_agent, $m)) {
            $this->browser = 'BOLT ' . $m[1];
            $this->browser_type_auto = 'wap';
        }
        if ($tel = $this->phone_model($user_agent)) {
            // определение модели телефона
            $this->browser = $tel[0];
            $this->browser_type_auto = $tel[1];
        }





        if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && preg_match('#Opera Mini/([0-9]+\.[0-9]+)#i', $user_agent, $v)) {
            $user_agent_opera = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
            if ($tel = $this->phone_model($user_agent_opera)) {
                $this->browser = 'Opera Mini ' . $v[1] . ' (' . $tel[0] . ')';
                $this->browser_type_auto = version_compare($v[1], 4, '>=') ? 'pda' : $tel[1];
            }
        }




        if (preg_match('#iPhone#i', $user_agent)) {
            $this->browser = 'iPhone';
            $this->browser_type_auto = 'itouch';
        }

        if (preg_match('#iPod#i', $user_agent)) {
            $this->browser = 'iPod';
            $this->browser_type_auto = 'itouch';
        }

        if (preg_match('#iPad#i', $user_agent)) {
            $this->browser = 'iPad';
            $this->browser_type_auto = 'itouch';
        }
        if (preg_match('#Bada#i', $user_agent)) {
            // $this->browser = 'Samsung Bada';
            $this->browser_type_auto = 'itouch';
        }

        if (preg_match('#Android#i', $user_agent)) {
            // $this->browser = 'Samsung Bada';
            $this->browser_type_auto = 'itouch';
        }

        if (preg_match('#Windows Phone#i', $user_agent)) {
           // $this->browser = 'Windows Phone 7';
            $this->browser_type_auto = 'itouch';
        }
    }

    protected function browser_version($user_agent) {
        // определение версии браузера
        if (preg_match('#Version/([0-9]+(\.[0-9]+)?)#i', $user_agent, $v))
            return $v[1];
    }

    protected function phone_model($ua) {
        // определение модели устройства
        if (preg_match('#SonyEricsson([0-9a-z]+)#i', $ua, $b)) {
            return array('SonyEricsson ' . $b[1], 'wap');
        }

        if (preg_match('#Nokia([0-9a-z]+)#i', $ua, $b)) {
            return array('Nokia ' . $b[1], 'wap');
        }

        if (preg_match('#LG-([0-9a-z]+)#i', $ua, $b)) {
            return array('LG ' . $b[1], 'wap');
        }

        if (preg_match('#FLY( |\-)([0-9a-z]+)#i', $ua, $b)) {
            return array('FLY ' . $b[2], 'wap');
        }

        if (preg_match('#MOT-([0-9a-z]+)#i', $ua, $b)) {
            return array('Motorola ' . $b[1], 'wap');
        }

        if (preg_match('#SAMSUNG(-SGH|-GT)?-([0-9a-z]+)#i', $ua, $b)) {
            return array('Samsung ' . $b[2], 'wap');
        }

        if (preg_match('#SEC-SGH([0-9a-z]+)#i', $ua, $b)) {
            return array('Samsung ' . $b[1], 'wap');
        }

        if (preg_match('#SIE-([0-9a-z]+)#i', $ua, $b)) {
            return array('Siemens ' . $b[1], 'wap');
        }

        return false;
    }

    protected function browser_id() {
        static $browser_id = false;

        if ($browser_id === false) {
            $q = mysql_query("SELECT * FROM `browsers` WHERE `name` = '" . my_esc($this->browser) . "' LIMIT 1");
            if (mysql_num_rows($q)) {
                $browser_id = mysql_result($q, 0);
            } else {
                $q = mysql_query("INSERT INTO `browsers` (`type`, `name`) VALUES ('$this->browser_type_auto','" . my_esc($this->browser) . "')");
                $browser_id = mysql_insert_id();
            }
        }
        return $browser_id;
    }

}

?>
<?php

abstract class crypt {

    static protected function init() {
        // инициализация шифрования
        global $dcms;
        // функция шифрования недоступна
        if (!function_exists('mcrypt_module_open'))
            return false;
        // если вектор шифрования уже есть, то все OK
        if (!empty($dcms->iv))
            return true;
        // генерируем и сохраняем вектор шифрования
        $td = @mcrypt_module_open('rijndael-256', '', 'ofb', '');
        $dcms->iv = base64_encode(base64_encode(@mcrypt_create_iv(@mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM)));
        $dcms->save_settings();
    }

    // делаем хэш пароля с наложением соли (покажем большой куй всем сервисам с md5 базами)
    static function hash($pass) {
        global $dcms;
        return md5($dcms->salt . md5((string) $pass) . md5($dcms->salt) . $dcms->salt);
    }

    // шифруем куки
    static function cookie_encrypt($str) {
        if (self::init()) {
            global $dcms;
            $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
            $ks = @mcrypt_enc_get_key_size($td);
            $key = substr(md5($dcms->salt . @$_SERVER ['HTTP_USER_AGENT']), 0, $ks);
            @mcrypt_generic_init($td, $key, base64_decode(base64_decode($dcms->iv)));
            $str = @mcrypt_generic($td, $str);
            @mcrypt_generic_deinit($td);
            @mcrypt_module_close($td);
        }

        return base64_encode(base64_encode($str));
    }

    // расшифровываем куки
    static function cookie_decrypt($str) {
        $str = base64_decode(base64_decode($str));

        if (self::init()) {
            global $dcms;
            $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
            $ks = @mcrypt_enc_get_key_size($td);
            $key = substr(md5($dcms->salt . @$_SERVER ['HTTP_USER_AGENT']), 0, $ks);
            @mcrypt_generic_init($td, $key, base64_decode(base64_decode($dcms->iv)));
            $str = @mdecrypt_generic($td, $str);
            @mcrypt_generic_deinit($td);
            @mcrypt_module_close($td);
        }

        return $str;
    }

}

?>
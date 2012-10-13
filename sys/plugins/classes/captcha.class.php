<?php
abstract class captcha {
    // генерация проверочного кода и сессии
    static function gen()
    {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= mt_rand(0, 9);
        }
        $sess = passgen();
        $_SESSION['captcha_session'][$sess] = (string)$code;
        return $sess;
    }
    // получение кода по сессии (для отображения капчи)
    static function getCode($sess)
    {
        return (!empty($_SESSION['captcha_session'][$sess]))?$_SESSION['captcha_session'][$sess]:false;
    }
    // проверка, введенного пользователем, кода по сессии и последующее удаление сессии
    static function check($code, $sess)
    {
        if (empty($_SESSION['captcha_session'][$sess]))return false;
        $return = (bool)($_SESSION['captcha_session'][$sess] === (string)$code);

        unset($_SESSION['captcha_session'][$sess]);
        return $return;
    }
}

?>

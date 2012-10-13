<?php
$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once 'sys/inc/start.php';
$doc = new document();
$doc->title = __('Регистрация');

if ($user->group) {
    $doc->access_denied(__('Вы уже зарегистрированы'));
}

if (!$dcms->reg_open) {
    $doc->access_denied(__('Регистрация временно закрыта'));
}
// пригласительный
$inv = &$_SESSION['reg']['invite'];
if (!$inv && isset($_GET['invite']) && $_GET['invite']) {
    $q = mysql_query("SELECT * FROM `invations` WHERE `code` = '" . my_esc($_GET['invite']) . "' AND `id_invite` IS NULL AND `email` IS NOT NULL LIMIT 1");
    // echo mysql_error();exit;
    if (mysql_num_rows($q)) {
        // $doc->msg('Пригласительный учтен');
        $inv = mysql_fetch_assoc($q);
        mysql_query("UPDATE `invations` SET `code` = null WHERE `id` = '$inv[id]' LIMIT 1");
    } else {
        $doc->err(__('Пригласительный недействителен'));
    }
}

if (!isset($inv))
    $inv = false;

if (!$inv && $dcms->reg_with_invite) {
    $doc->access_denied(__('Регистрация возможна только по приглашению'));
}

$step = &$_SESSION['reg']['step'];
if (!isset($step)) {
    $step = $dcms->reg_with_rules ? 0 : 1;
}
$login = &$_SESSION['reg']['login'];
// принимаем правила
if ($step == 0 && isset($_GET['rules'])) {
    if ($_POST['ok']) {
        $step = 1;
        $doc->msg(__('Очень хорошо, надеемся на их соблюдение'));
    } elseif ($_POST['no']) {
        $doc->err(__('Для продолжения регистрации необходимо принять правила сайта'));
    }
}
// выбираем ник
if ($step == 1 && isset($_GET['nick']) && isset($_POST['login'])) {
    if (is_valid::nick($_POST['login'])) {
        $login = $_POST['login'];
        if (!mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `login` = '" . my_esc($login) . "'"), 0)) {
            if ($_POST['login'] != my_esc($_POST['login']))
                $doc->msg(__('В нике содержатся запрещенные символы'));
            else {
                $step = 2;
                $_SESSION['reg']['login'] = $_POST['login'];
                $doc->msg(__('Ник может быть успешно зарегистрирован'));
            }
        } else {
            $doc->err(__('Ник занят другим пользователем'));
        }
    } else {
        $doc->err(__('Недопустимый ник'));
    }
}
// выбираем ник
if ($step == 2 && isset($_GET['final']) && isset($_POST['sex'])) {
    $sex = $_POST['sex'] ? 1 : 0;

    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } elseif ($dcms->reg_with_mail && !$inv) {
        if (empty($_POST['mail'])) {
            $doc->err(__('Необходимо указать E-mail'));
        } elseif (!is_valid::mail($_POST['mail'])) {
            $doc->err(__('Указан не корректный E-mail'));
        } elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `reg_mail` = '" . my_esc($_POST['mail']) . "'"), 0)) {
            $doc->err(__('Пользователь с таким e-mail уже зарегистрирован'));
        } elseif (empty($_POST['password'])) {
            $doc->err(__('Необходимо указать пароль'));
        } elseif (empty($_POST['password_retry'])) {
            $doc->err(__('Необходимо подтвердить пароль'));
        } elseif ($_POST['password_retry'] != $_POST['password']) {
            $doc->err(__('Введенные пароли не совпадают'));
        } elseif (!is_valid::password($_POST['password'])) {
            $doc->err(__('Не корректный пароль'));
        } else {
            $a_code = md5(passgen());

            mysql_query("INSERT INTO `users` (`reg_date`, `login`, `password`, `sex`, `a_code`, `reg_mail`)
values('" . TIME . "', '" . my_esc($_SESSION['reg']['login']) . "', '" . crypt::hash($_POST['password']) . "', '$sex', '$a_code', '" . my_esc($_POST['mail']) . "')");
            $id_user = mysql_insert_id();
            if ($id_user && is_numeric($id_user)) {


                if ($susp = is_valid::suspicion($inv['email'] . ' ' . $_SESSION['reg']['login'])) {
                    // подозрительный e-mail или логин
                    mysql_query("INSERT INTO `users_suspicion` (`id_user`, `text`) VALUES ('$id_user', '" . my_esc($susp) . "')");
                    $dcms->distribution("Пользователь [user]{$id_user}[/user] сочтен подозрительным, так как в нике или адресе email была обнаружена несвязная комбинация символов: {$susp}\n[url=/dpanel/users.suspicious.php]Список подозрительных пользователей[/url]", 4);
                }


                $t = new design();
                $t->assign('title', __('Успешная регистрация'));
                $t->assign('login', $_SESSION['reg']['login']);
                $t->assign('password', $_POST['password']);
                $t->assign('site', $dcms->sitename);
                $t->assign('url', 'http://' . $_SERVER['HTTP_HOST'] . '/activation.php?id=' . $id_user . '&amp;code=' . $a_code . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
                if (mail::send($_POST['mail'], 'Регистрация', $t->fetch('file:' . H . '/sys/templates/mail.activation.tpl'))) {
                    $step = 3;
                    $doc->msg(__('На Ваш E-mail отправлено письмо с ссылкой для активации аккаунта'));
                } else
                    $doc->err(__('Ошибка при отправке email, попробуйте позже'));
            } else {
                $doc->err(__('Ошибка при регистрации. Попробуйте позже'));
                $step = 1;
            }
        }
    } elseif ($inv) {
        if (empty($_POST['password']))
            $doc->err(__('Необходимо указать пароль'));
        elseif (!isset($_POST['password_retry']))
            $doc->err(__('Необходимо подтвердить пароль'));
        elseif ($_POST['password_retry'] != $_POST['password'])
            $doc->err(__('Введенные пароли не совпадают'));
        elseif (!is_valid::password($_POST['password']))
            $doc->err(__('Не корректный пароль'));
        elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `reg_mail` = '" . my_esc($inv['email']) . "'"), 0))
            $doc->err(__('Пользователь с таким e-mail уже зарегистрирован'));
        else {
            mysql_query("INSERT INTO `users` (`reg_date`, `login`, `password`, `sex`, `reg_mail`)
values('" . TIME . "', '" . my_esc($_SESSION['reg']['login']) . "', '" . crypt::hash($_POST['password']) . "', '$sex', '" . my_esc($inv['email']) . "')");
            $id_user = mysql_insert_id();


            if ($id_user && is_numeric($id_user)) {

                if ($susp = is_valid::suspicion($inv['email'] . ' ' . $_SESSION['reg']['login'])) {
                    // подозрительный e-mail или логин
                    mysql_query("INSERT INTO `users_suspicion` (`id_user`, `text`) VALUES ('$id_user', '" . my_esc($susp) . "')");
                    $dcms->distribution("Пользователь [user]{$id_user}[/user] сочтен подозрительным, так как в нике или адресе email была обнаружена несвязная комбинация символов: {$susp}\n[url=/dpanel/users.suspicious.php]Список подозрительных пользователей[/url]", 4);
                }



                mysql_query("UPDATE `invations` SET `id_invite` = '$id_user', `time_reg` = '" . TIME . "' WHERE `id` = '$inv[id]' LIMIT 1");
                mysql_query("UPDATE `users` SET `balls` = `balls` * '1.1' WHERE `id` = '$inv[id_user]' LIMIT 1");
                $step = 3;
            }
        }
    } else {
        mysql_query("INSERT INTO `users` (`reg_date`, `login`, `password`, `sex`) values('" . TIME . "', '" . my_esc($_SESSION['reg']['login']) . "', '" . crypt::hash($_POST['password']) . "', '$sex')");

        $id_user = mysql_insert_id();
        if ($id_user && is_numeric($id_user)) {

            if ($susp = is_valid::suspicion($_SESSION['reg']['login'])) {
                // подозрительный логин
                mysql_query("INSERT INTO `users_suspicion` (`id_user`, `text`) VALUES ('$id_user', '" . my_esc($susp) . "')");
                $dcms->distribution("Пользователь [user]{$id_user}[/user] сочтен подозрительным, так как в нике была обнаружена несвязная комбинация символов: {$susp}\n[url=/dpanel/users.suspicious.php]Список подозрительных пользователей[/url]", 4);
            }


            $step = 3;
        } else {
            $doc->err(__('Ошибка при регистрации. Попробуйте позже'));
            $step = 1;
        }
    }
}

if ($step == 3) {
    $doc->msg(__('Вы успешно зарегистрированы'));

    if ($dcms->reg_with_mail && !$inv) {
        echo __("На ваш E-mail отправлено письмо с ссылкой для активации аккаунта");
    } else {
        echo __("Теперь Вы можете <a href='%s'>Авторизоваться</a>", '/login.php' . (isset($_GET['return']) ? '?return=' . urlencode($_GET['return']) : null));
    }

    unset($_SESSION['reg']);
    exit;
}

if ($step == 2) {
    $doc->title = __('Завершение регистрации'); // заголовок страницы
    $form = new design();
    $form->assign('method', 'post');
    $form->assign('action', '/reg.php?final&amp;' . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
    $elements = array();
    $elements[] = array('type' => 'text', 'value' => __('Ваш ник: %s', $login), 'br' => 1);
    $elements[] = array('type' => 'password', 'title' => __('Пароль') . ' [6-32]', 'br' => 1, 'info' => array('name' => 'password'));
    $elements[] = array('type' => 'password', 'title' => __('Повторите пароль'), 'br' => 1, 'info' => array('name' => 'password_retry'));

    $elements[] = array('type' => 'select', 'title' => __('Ваш пол'), 'br' => 1, 'info' => array('name' => 'sex', 'options' => array(array(1, __('Мужской')), array(0, __('Женский')))));
    $elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
    if ($dcms->reg_with_mail && !$inv)
        $elements[] = array('type' => 'input_text', 'title' => __('Ваш E-mail'), 'br' => 1, 'info' => array('name' => 'mail'));
    if ($dcms->reg_with_mail && !$inv)
        $elements[] = array('type' => 'text', 'value' => '* ' . __('На Ваш E-mail придет письмо с ссылкой для активации аккаунта'), 'br' => 1);

    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'post', 'value' => __('Зарегистрироваться'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');
    exit;
}

if ($step == 1) {
    $doc->title = __('Подбор ника'); // заголовок страницы

    $form = new design();
    $form->assign('method', 'post');
    $form->assign('action', '/reg.php?nick&amp;' . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
    $elements = array();
    $elements[] = array('type' => 'input_text', 'title' => __('Выберите ник') . ' [A-zА-я0-9 -_]', 'br' => 1, 'info' => array('name' => 'login'));
    $elements[] = array('type' => 'text', 'value' => '* ' . __('Сочетание русского и английского алфавитов запрещено'), 'br' => 1);
    $elements[] = array('type' => 'text', 'value' => '** ' . __('Использование пробелов вначале и конце строк запрещено'), 'br' => 1);
    $elements[] = array('type' => 'text', 'value' => '*** ' . __('Ник не должен начинаться с цифр'), 'br' => 1);
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'post', 'value' => __('Продолжить'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');
    exit;
}

if ($step == 0) {
    $doc->title = __('Соглашение'); // заголовок страницы
    $form = new design();

    $form->assign('method', 'post');
    $form->assign('action', '/reg.php?rules&amp;' . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
    $elements = array();

    $bb = new bb(H . '/sys/docs/rules.txt');
    $elements[] = array('type' => 'text', 'value' => $bb->fetch()); // правила
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'ok', 'value' => __('Принимаю'))); // кнопка
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'no', 'value' => __('Не принимаю'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');
    exit;
}
?>

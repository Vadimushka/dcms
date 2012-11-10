<?php

include '../sys/inc/start.php';
include 'inc/func.php';
set_time_limit(600);
ignore_user_abort();
$doc = new document(groups::max());
$doc->title = 'Сборка движка';

$conf = ini::read('config.beta.ini'); // основной конфиг




if (!empty($_POST['start'])) {
    if (empty($_POST['version'])) {
        $doc->err('Не указана версия движка');
    } else {
        $version = $_POST['version'] . '.' . ($conf['build_num'] + 1);

        $dirs = filesystem::getRelPath(filesystem::getAllDirs('../'));
        $files = filesystem::getRelPath(filesystem::getAllFiles('../'));

        $skips = filesystem::fileToArray('skip.txt'); // список пропускаемых путей
        $noskips = filesystem::fileToArray('noskip.txt');

        $dirs = skip_files($dirs, $skips, $noskips);
        $files = skip_files($files, $skips, $noskips);

        // обновляем информацию о версии
        $defaults = ini::read(H . '/sys/inc/settings.default.ini', true);
        $defaults['REPLACE']['version'] = $version;
        ini::save(H . '/sys/inc/settings.default.ini', $defaults, true);

        // создаем временную папку с релизом
        $tmp_dir = 'tmp/' . passgen();
        filesystem::mkdir($tmp_dir);



        // echo nl2br(print_r($noskips, 1));
        // echo nl2br(print_r($skips, 1));
        // echo nl2br(print_r($dirs, 1));
        // echo nl2br(print_r($files, 1));
        // создаем папки
        foreach ($dirs as $path) {
            //echo "<b>mkdir</b> $path<br /><br />";
            filesystem::mkdir($tmp_dir . '/' . $path);
        }

        // копируем файлы
        foreach ($files as $path) {
            //echo "<b>copy(</b>/$path, $tmp_dir/$path<b>)</b><br /><br />";
            copy(H . '/' . $path, $tmp_dir . '/' . $path);
        }




        // предустановлчные параметры
        $preset = ini::read($tmp_dir . '/sys/preinstall/settings.ini');
        $preset['version'] = $version;
        $preset['salt'] = '';
        $preset['iv'] = '';
        $preset['mysql_user'] = 'root';
        $preset['mysql_pass'] = '';
        $preset['mysql_base'] = '';
        ini::save($tmp_dir . '/sys/preinstall/settings.ini', $preset);


        $content = "[title]{$version}[/title]\r\n\r\n" . file_get_contents(H . '/changelog.txt');

        file_put_contents(H . '/sys/docs/changelog/' . $version . '.txt', $content);
        file_put_contents($tmp_dir . '/sys/docs/changelog/' . $version . '.txt', $content);
        file_put_contents(H . '/changelog.txt', '');
        $files[] = 'sys/docs/changelog/' . $version . '.txt';



        // получение хэшей файлов (необходимо для создания пакета обновления)
        $hashes = array();
        foreach ($files as $path) {
            $hashes[$path] = md5_file($tmp_dir . '/' . $path);
        }

        keyvalue::save('hashes/' . $version . '.ini', $hashes);


        $build_file = 'builds/' . $version . '.zip';

        $zip = new PclZip($build_file);
        $zip->create($tmp_dir, PCLZIP_OPT_REMOVE_PATH, $tmp_dir);

        // удаляем временную папку
        @filesystem::rmdir($tmp_dir);
        @filesystem::rmdir($tmp_dir);

        $conf['build_num']++;
        $conf['version_last'] = $_POST['version'];
        $conf['time'] = TIME;
        ini::save('config.beta.ini', $conf);
        $doc->msg('Версия ' . $version . ' успешно собрана');

        $doc->ret(__('Вернуться'), '?' . passgen());
        exit;
    }
}


$changelog = @file_get_contents(H . '/changelog.txt');

if ($changelog) {
    echo output_text($changelog);
} else {
    $doc->err('Список изменений пуст');
}



$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', '?' . passgen());
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => 'Версия (build #' . ($conf['build_num'] + 1) . ')', 'br' => 1, 'info' => array('name' => 'version', 'value' => $conf['version_last']));
if ($changelog) {
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'start', 'value' => 'Собрать')); // кнопка
} else {
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'refresh', 'value' => 'Обновить данные')); // кнопка
}

$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');
?>
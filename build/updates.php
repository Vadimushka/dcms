<?php

include '../sys/inc/start.php';
include 'inc/func.php';
set_time_limit(600);
ignore_user_abort();
$doc = new document();
$doc->title = 'Создание обновления';

function createUpdate($from, $to) {
    $to_update = array(); // файлы для обновления или создания
    $to_delete = array(); // файлы для удаления
    $hashes_from = keyvalue::read('hashes/' . $from . '.ini');
    $hashes_to = keyvalue::read('hashes/' . $to . '.ini');


    foreach ($hashes_to as $file => $hash) {
        if (isset($hashes_from[$file]) && $hashes_from[$file] === $hash) {
            // если файл существует и его хэш совпадает, то пропускаем
            continue;
        }
        $to_update[] = $file;
    }

    foreach ($hashes_from as $file => $hash) {
        if (!isset($hashes_from[$file])) {
            // если в новой сборке отсутствует файл, который был в старой версии, то заносим в список на удаление
            $to_delete[$file] = $hash;
        }
    }

    $skips = filesystem::fileToArray('update_skip.txt');
    $to_update = skip_files($to_update, $skips);


    $tmp_dir = 'tmp/' . passgen();
    filesystem::mkdir($tmp_dir);

    $build_file = 'builds/' . $to . '.zip';




    $zip = new PclZip($build_file);

    $zip->extract(PCLZIP_OPT_PATH, $tmp_dir, PCLZIP_OPT_BY_NAME, $to_update);

    keyvalue::save($tmp_dir . '/to_delete.ini', $to_delete);
    keyvalue::save($tmp_dir . '/to_update.ini', $to_update);
    keyvalue::save($tmp_dir . '/version.ini', array('from' => $from, 'to' => $to));

    $update_file = 'updates/' . $from . '-' . $to . '.zip';

    $zip = new PclZip($update_file);
    $zip->create($tmp_dir, PCLZIP_OPT_REMOVE_PATH, $tmp_dir);


// удаляем временную папку
    filesystem::rmdir($tmp_dir);

    return true;
}

$builds = getBuildList();

$versions = array();
foreach ($builds as $build) {
    $versions [] = substr($build, 0, strrpos($build, '.'));
}
$versions = array_unique($versions);


if (!empty($_GET['from'])) {
    $from = (string) $_GET['from'];
    if (!in_array($from, $builds)) {
        $doc->err('Запрашиваемая версия не обнаружена');
    } else {


        if (!empty($_GET['to'])) {
            $to = (string) $_GET['to'];
            if (!in_array($to, $builds)) {
                $doc->err('Запрашиваемая версия не обнаружена');
            } elseif (version_compare($to, $from, '<=')) {
                $doc->err('Порядок обновления нарушен');
            } elseif (!file_exists('builds/' . $from . '.zip')) {
                $doc->err('Не найден архив исходной версии');
            } elseif (!file_exists('builds/' . $to . '.zip')) {
                $doc->err('Не найден архив обновляемой версии версии');
            } elseif (isset($_GET['info'])) {
                $doc->title = __('Пакет обновления с %s по %s', $from, $to);
                $bb = new bb('description_update.txt');
                $bb->display();

                $smarty = new design();
                $smarty->assign('method', 'post');
                $smarty->assign('action', '?from=' . urlencode($from) . '&amp;to=' . urlencode($to));
                $elements = array();
                $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Скачать выбранное обновление'))); // кнопка
                $smarty->assign('el', $elements);
                $smarty->display('input.form.tpl');

                exit;
            } else {
                $update_file = 'updates/' . $from . '-' . $to . '.zip';
                if (!file_exists($update_file)) {
                    createUpdate($from, $to);
                }


                $doc->clean();
                $f = new download('dcms_update-' . $from . '-' . $to . '.zip', $update_file);
                $f->output();
                exit;
            }
        }








        if (isset($_GET['to_version']) && in_array($_GET['to_version'], $versions)) {

            $doc->title = __('Сборка версии, до которой обновляемся');

            rsort($builds);
            $listing = new listing();
            foreach ($builds as $build) {


                if (false === strpos($build, $_GET['to_version'])) {
                    continue;
                }

                if (version_compare($build, $from, '<=')) {
                    continue;
                }


                $post = $listing->post();
                $post->url = '?from=' . urlencode($from) . '&amp;to=' . urlencode($build) . '&amp;info';
                $post->title = for_value($from . ' > ' . $build) ;
                $post->icon('cms');
            }

            $listing->display('По всей видимости, у Вас последняя версия');


            $doc->ret(__('Выбрать другую версию'), '?from=' . $from);
            $doc->ret(__('Выбрать исходную версию'), '?');
            exit;
        }


        $doc->title = __('Версия, до которой обновляемся');

        rsort($versions);
        $listing = new listing();

        foreach ($versions AS $version) {
            if (version_compare($version . '.99999', $from, '<')) {
                continue;
            }


            $post = $listing->post();
            $post->url = '?from=' . urlencode($from) . '&amp;to_version=' . $version;
            $post->title = for_value($from . ' > ' . $version) ;
            $post->icon('cms');
        }

        $listing->display('По всей видимости, у Вас последняя версия');
        $doc->ret(__('Выбрать исходную версию'), '?');
        exit;
    }
}






if (isset($_GET['version']) && in_array($_GET['version'], $versions)) {
    $doc->title = __('Выберите сборку Вашей версии');
    $listing = new listing();

    foreach ($builds AS $build) {

        if (false === strpos($build, $_GET['version'])) {
            continue;
        }



        $post = $listing->post();
        $post->url = '?from=' . $build;
        $post->title = $build;
        $post->icon('cms');
    }

    $listing->display('Не найдено ни одной сборки данной версии');
    $doc->ret(__('Выбрать исходную версию'), '?');
    exit;
}







$doc->title = __('Выберите Вашу версию');

$listing = new listing();

foreach ($versions AS $version) {
    $post = $listing->post();
    $post->url = '?version=' . $version;
    $post->title = $version ;
    $post->icon('cms');
}

$listing->display('Не найдено ни одной версии');
?>
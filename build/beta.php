<?php

include '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Скачать DCMS (BETA)';

$listing = new listing();

$post = $listing->post();
$post->title = __('Описание');
$post->icon('info');
$bb = new bb('description.txt');
$post->content = $bb->fetch();

$conf_release = ini::read('config.ini');
$conf = ini::read('config.beta.ini');


if ($conf_release['build_num'] == $conf['build_num']) {
    $doc->err(__('BETA версии отсутствуют'));
    $doc->ret(__('Скачать DCMS'), './');
    exit;
}


$doc->title = 'Скачать DCMS (BETA #' . $conf['build_num'] . ')';

$ch_files = (array) glob(H . '/sys/docs/changelog/' . $conf['version_last'] . '.*.txt');


$post = $listing->post();
$post->title = __('Список изменений');
$post->icon('changelog');

foreach ($ch_files as $ch_file) {
    $post->content[] = '[b]' . basename($ch_file, '.txt') . "[/b]";
    $bb = new bb($ch_file);
    $post->content[] = trim($bb->getText());
}


$info = ini::read('builds/' . $conf['version_last'] . '.' . $conf['build_num'] . '.ini');

if (empty($info['dcount'])) {
    $info['dcount'] = 0;
}

$post = $listing->post();
$post->title = __('Кол-во скачиваний');
$post->content = $info['dcount'] . ' ' . __(misc::number($info['dcount'], 'раз', 'раза', 'раз'));

$listing->display();


$form = new form('download/' . $conf['version_last'] . '.' . $conf['build_num'] . '.zip', false);
$form->button(__('Скачать %s', 'DCMS ' . $conf['version_last'] . ' BETA'));
$form->display();

if (groups::max() == $user->group) {
    if (isset($_POST['to_release'])) {
        if (copy('config.beta.ini', 'config.ini'))
            $doc->msg(__('BETA версия успешно сделана релизом'));
    }


    $form = new form('?' . passgen());
    $form->button(__('Сделать релизной версией'), 'to_release');
    $form->display();
}
?>
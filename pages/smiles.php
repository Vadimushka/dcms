<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Смайлы');
$smiles = smiles::get_ini();
$smiles_a = array();
// загружаем список смайлов
$smiles_gl = (array) glob(H . '/sys/images/smiles/*.gif');

foreach ($smiles_gl as $path) {
    if (preg_match('#/([^/]+)\.gif$#', $path, $m))
        $smiles_a[$m[1]] = $path;
}

$listing = new listing();
foreach ($smiles_a as $name => $path) {
    $post = $listing->post();
    $post->title = text::toValue($name);
    $post->image = '/sys/images/smiles/' . $name . '.gif';
    $post->content = __('Варианты') . ': *' . implode('*, *', array_keys($smiles, $name)) . '*';
}

$listing->display(__('Смайлы отсутствуют'));

if (!empty($_GET['return']))
    $doc->ret(__('Вернуться'), text::toValue($_GET['return']));

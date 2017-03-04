<?php
/**
 * @var $this document
 */
?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang->xml_lang ?>">
<head>
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/theme_light.css" type="text/css"/>
    <link rel="stylesheet" href="<?= $path ?>/style.css" type="text/css"/>
    <meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1,width=device-width" />
    <meta name="generator" content="DCMS <?= $dcms->version ?>"/>
    <?php if ($description) { ?>
        <meta name="description" content="<?= $description ?>" /><?php } ?>
    <?php if ($keywords) { ?>
        <meta name="keywords" content="<?= $keywords ?>" /><?php } ?>
    <style type="text/css">
        .hide {
            display: none !important;
        }
    </style>
</head>
<body class="theme_light theme_light_light">
<div>
    <?php $this->display('inc.title.tpl') ?>
    <?php $this->displaySection('after_title')?>
    <div id="tabs">
        <?= $this->section($tabs, '<a class="tab sel{selected}" href="{url}">{name}</a>', true); ?>
    </div>
    <?php $this->display('inc.user.tpl') ?>
    <?php $this->displaySection('before_content')?>
    <div id="content">
        <div id="messages">
            <?= $this->section($err, '<div class="err">{text}</div>'); ?>
            <?= $this->section($msg, '<div class="msg">{text}</div>'); ?>
        </div>
        <?php $this->displaySection('content') ?>
    </div>
    <?php $this->displaySection('after_content')?>
    <?php $this->display('inc.foot.tpl') ?>
    <div id="foot">
		<?= __("Язык") ?>: <a href='/pages/language.php?return=<?= URL ?>' id="language"><?= $lang->name ?></a><br/>
        <?= __("Время генерации страницы: %s сек", $document_generation_time) ?><br/>
        <?= $copyright ?>
    </div>
</div>
</body>
</html>
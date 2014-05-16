<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang->xml_lang ?>">
<head>
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/theme_light.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?= $path ?>/style.css"/>
    <script charset="utf-8" src="/sys/javascript/dcms.js" type="text/javascript"></script>
    <script charset="utf-8" src="<?= $path ?>/user.js" type="text/javascript"></script>
    <meta http-equiv="Сontent-Type" content="application/xhtml+xml; charset=utf-8"/>
    <meta name="generator" content="DCMS <?= $dcms->version ?>"/>
    <? if ($description) { ?>
        <meta name="description" content="<?= $description ?>" />
    <? } ?>
    <? if ($keywords) { ?>
        <meta name="keywords" content="<?= $keywords ?>" />
    <? } ?>
    <style>
        .hide {
            display: none !important;
        }
    </style>
    <script>
        LANG = {
            bbcode_b: '<?= __('Текст жирным шрифтом') ?>',
            bbcode_i: '<?= __('Текст курсивом') ?>',
            bbcode_u: '<?= __('Подчеркнутый текст') ?>',
            bbcode_img: '<?= __('Вставка изображения') ?>',
            bbcode_php: '<?= __('Выделение PHP-кода') ?>',
            bbcode_big: '<?= __('Увеличенный размер шрифта') ?>',
            bbcode_small: '<?= __('Уменьшенный размер шрифта') ?>',
            bbcode_gradient: '<?= __('Цветовой градиент') ?>',
            bbcode_hide: '<?= __('Скрытый текст') ?>',
            bbcode_spoiler: '<?= __('Свернутый текст') ?>',
            smiles: '<?= __('Смайлы') ?>',
            form_submit_error: '<?= __('Ошибка связи...') ?>'
        };
    </script>
</head>
<?
$class_fix = $dcms->ie_ver ? 'ie ie' . $dcms->ie_ver : '';
?>
<body class="theme_light_web theme_light <?= $class_fix ?>">
<div id="main">
    <div id="top_part">
        <div id="header" class="gradient_blue">
            <div class="body_width_limit">
                <h1 id="title"><?= $title ?></h1>

                <div id="navigation">
                    <? if (!IS_MAIN) { ?>
                        <a class="gradient_blue invert border radius padding" href='/'><?= __("На главную") ?></a>
                    <? } ?>
                    <?= $this->section($returns, '<a class="gradient_blue invert border radius padding" href="{1}">{0}</a>', true); ?>
                </div>
                <div class="body_width_limit">
                    <div id="menu">
                        <? if ($adt->top) { ?>
                            <div class="listing">
                                <div id="adt_top" class="post">
                                    <?= $this->section($adt->top, '{0}') ?>
                                </div>
                            </div>
                        <? } ?>
                        <?
                        $menu = new menu('main');
                        $menu->display();
                        ?>
                        <? if ($adt->bottom) { ?>
                            <div id="adt_bottom">
                                <?= $this->section($adt->bottom, '{0}') ?>
                            </div>
                        <? } ?>
                    </div>
                    <div id="content">
                        <div id="messages">
                            <?= $this->section($err, '<div class="gradient_red border radius">{text}</div>'); ?>
                            <?= $this->section($msg, '<div class="gradient_green border radius">{text}</div>'); ?>
                        </div>
                        <?= $content ?>
                    </div>

                </div>
                <div id="empty"></div>
            </div>
            <div id="footer" class="gradient_grey">
                <div class="body_width_limit">
                    <span id="copyright">
                        <?= $copyright ?>
                    </span>
                    <span id="language">
                        <?= __("Язык") ?>:<a href='/language.php?return=<?= URL ?>'
                            style='background-image: url(<?= $lang->icon ?>); background-repeat: no-repeat; background-position: 5px 2px; padding-left: 23px;'><?= $lang->name ?></a>
                    </span>
                    <span id="generation">
                        <?= __("Время генерации страницы: %s сек. ", $document_generation_time) ?> 
                        <?= __("SQL запросов: %s, ", $sql_count) ?> 
                        <?= __("SQL time: %s сек.", $sql_time) ?>
                    </span>                    
                </div>
            </div>
        </div>
        </div>
    </body>
</html>
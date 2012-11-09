<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang->xml_lang}">
    <head>
        <title>{$title|for_value}</title>
        <link rel="shortcut icon" href="/favicon.ico" />
        <script charset="utf-8" src="/sys/themes/system.js" type="text/javascript"></script>
        <script charset="utf-8" src="{$path}/user.js" type="text/javascript"></script>
        <link rel="stylesheet" href="/sys/themes/system.css" type="text/css" />
        <link rel="stylesheet" href="{$path}/style.css" type="text/css" />
        <meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8" />
    {if $description}<meta name="description" content="{$description|for_value}" />{/if}
{if $keywords}<meta name="keywords" content="{$keywords|for_value}" />{/if}

<style>
    .hide {
        display: none !important;
    }
</style>
</head>
<body>
    <div>
        {include file="inc.title.tpl"}   
        {include file="inc.user.tpl"}

        <div class="content">
            {include file="inc.adt.top.tpl"} 
            {$content}

        </div>
        {include file="inc.foot.tpl"}   
        {include file="inc.adt.bottom.tpl"}
        <div class="foot">
            {$lang->getString("Время генерации страницы")}: {$document_generation_time} сек<br />
            {$dcms->copyright|output_text}
        </div>

    </div>
</body>
</html>
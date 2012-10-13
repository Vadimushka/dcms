<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang->xml_lang}">
<head>
<title>{$title|for_value}</title>
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" href="/sys/themes/style.css" type="text/css" />
<link rel="stylesheet" href="{$path}/style.css" type="text/css" />
<meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8" />
{if $description}<meta name="description" content="{$description|for_value}" />{/if}
{if $keywords}<meta name="keywords" content="{$keywords|for_value}" />{/if}
</head>
<body>
<div>
{include file="inc.title.tpl"}
{include file="inc.adt.top.tpl"}
{include file="inc.user.tpl"}
{$content}
{include file="inc.foot.tpl"}
{include file="inc.adt.bottom.tpl"}

{$lang->getString("Время генерации страницы")}: {$document_generation_time} сек<br />
{$dcms->copyright|output_text}
</div>
</body>
</html>
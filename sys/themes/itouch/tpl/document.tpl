<!DOCTYPE html>
<html>
    <head>
        <title>{$title|for_value}</title>
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="stylesheet" href="/sys/themes/style.css" type="text/css" />
        <link rel="stylesheet" href="{$path}/style.css" type="text/css" />
        <link rel="stylesheet" href="{$path}/jquery.mobile-1.1.0.css" type="text/css" />
        <script charset="utf-8" src="{$path}/jquery-1.7.1.min.js" type="text/javascript"></script>
        <script charset="utf-8" src="{$path}/jquery.mobile-1.1.0.js" type="text/javascript"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    {if $description}<meta name="description" content="{$description|for_value}" />{/if}
{if $keywords}<meta name="keywords" content="{$keywords|for_value}" />{/if}
</head>
<body>
    <div data-role="page">
        {include file="inc.title.tpl"}        
        {include file="inc.user.tpl"}
        {include file="inc.adt.top.tpl"}
        <div data-role="content">
            {$content}
        </div>

        {include file="inc.foot.tpl"}   
        
{include file="inc.adt.bottom.tpl"}
    </div>
</body>
</html>
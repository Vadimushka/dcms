<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang->xml_lang}">
    <head>
        <title>{$title|for_value}</title>
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="stylesheet" href="/sys/themes/system.css" type="text/css" />        
        <link rel="stylesheet" type="text/css" href="{$path}/style.css" />
        <meta http-equiv="Сontent-Type" content="application/xhtml+xml; charset=utf-8" />
    {if $description}<meta name="description" content="{$description|for_value}" />{/if}

{if $keywords}<meta name="keywords" content="{$keywords|for_value}" />{/if}
<script charset="utf-8" src="{$path}/jquery-1.7.2.min.js" type="text/javascript"></script>
<script charset="utf-8" src="{$path}/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
<script charset="utf-8" src="{$path}/jquery.sound.js" type="text/javascript"></script>
<script charset="utf-8" src="{$path}/custom.js" type="text/javascript"></script>        
<script>
    
    page_time = {$smarty.now};
    // фразы, используемые в JavaScript
    lang_smiles = '{$lang->getString("Смайлы")}';
    lang_smiles_hide = '{$lang->getString("Скрыть смайлы")}';
    lang_smiles_show = '{$lang->getString("Показать смайлы")}';
    lang_smiles_load = '{$lang->getString("Загрузка смайлов")}';
    lang_smiles_load_err = '{$lang->getString("Не удалось загрузить смайлы")}';
    lang_mail = '{$lang->getString("Почта")}';
    lang_friends = '{$lang->getString("Друзья")}';
    lang_server_return_ok = '{$lang->getString("Данные успешно сохранены")}';
    lang_server_return_err = '{$lang->getString("Сервер вернул ошибку")}';    
</script>    
<script>
    // спойлеры скрываем с помощью JavaScript, чтобы в случае его отключения они оказались открытыми
        <![CDATA[
            $('head').append('<style type="text/css">div.spoiler div.spoiler_content{ display: none; }</style>');
        ]]>
</script>
</head>
<body> 
    <div class="head">
        <div class="body">
            <div class="title">
                <h1>
                    {$title|for_value}
                </h1>
                <div class="user">

                    {section name=i loop=$actions}<a href="{$actions[i].1}">{$actions[i].0}</a>
                    {/section}    

                    {if $user->id}
                        <script>
                            USER.message_file_path = '{$path}/sound.swf';
                            USER.id = {$user->id};
                            USER.count_mail = {$user->mail_new_count};
                            USER.count_friends = {$user->friend_new_count};
                            USER.updateBar(false);
                            USER.getData();
                        </script>
                        <a id='friends' style='font-weight: bold; display: none' href='/my.friends.php'>{$lang->getString("Друзья")}</a>
                        <a id='mail' style='font-weight: bold; display: none' href='/my.mail.php?only_unreaded'>{$lang->getString("Почта")}</a>
                        <a id='menu_user' style='font-weight: bold;' href="/menu.user.php">{$user->login}</a>
                    {else}
                        <a href="/login.php?return={$URL}">{$lang->getString("Авторизация")}</a>
                        <a href="/reg.php?return={$URL}">{$lang->getString("Регистрация")}</a>
                    {/if}
                </div>
            </div>
            {if $smarty.server.SCRIPT_NAME != '/index.php'}
                <div class="returns"> 
                    <span><span><a href='/'>{$lang->getString("На главную")}</a></span></span>    
                    {section name=i loop=$returns step=-1}
                        <span><span><a href="{$returns[i].1}">{$returns[i].0}</a></span></span>
                    {/section}
                </div>
            {/if}
        </div>
    </div>

    <div class="body">
        <div class="messages">
            {section name=i loop=$err}
                <div class="err">{$err[i].text|output_text}{if $err[i].help} [<a title="Помощь" href="/faq.php?info={$err[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
            {/section}
            {section name=i loop=$msg}
                <div class="msg">{$msg[i].text|output_text}{if $msg[i].help} [<a title="Помощь" href="/faq.php?info={$msg[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
            {/section}
        </div>

        <table>
            <tr>
                <td class="menu">
                    <div class="menu"> 
                        {if $adt->top}
                            <div class="adt">
                                <div class="post_hightlight">{$lang->getString("Партнеры")}</div>
                                {section name=i loop=$adt->top}
                                    {$adt->top[i]}
                                {/section}
                            </div>
                        {/if}


                        {php}
$menu = new menu('main');
$menu -> display();
                        {/php}

                        {if $adt->bottom}
                            <div class="post_hightlight">{$lang->getString("Разное")}</div>
                            {section name=i loop=$adt->bottom}
                                {$adt->bottom[i]}
                            {/section}
                        {/if}
                    </div>
                </td>
                <td class="content">
                    <div class="content">
                        {$content}
                    </div>
                </td>
            </tr>
        </table>



        <div class="foot">
            <span class="copyright">
                {$dcms->copyright|output_text}
            </span>
            <span class="generation">
                {$lang->getString("Время генерации страницы")}: {$document_generation_time}
                <a href='/language.php?return={$URL}' style='background-image: url({$lang->icon}); background-repeat: no-repeat; background-position: 5px 2px; padding-left: 23px;'>{$lang->name}</a>
            </span>

        </div>

    </div>

</body>
</html>
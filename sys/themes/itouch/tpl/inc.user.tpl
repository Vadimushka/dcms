{section name=i loop=$err}
    <div data-role="header" data-position="inline" data-theme="e">     
        <h1>{$err[i].text|output_text}</h1>
        {if $err[i].help}
            <a data-icon="info" data-iconpos="notext" class="ui-btn-right" href="/faq.php?info={$err[i].help}&amp;return={$return|urlencode}">{$lang->getString("Помощь")}</a>
        {/if}
    </div>
{/section}

{section name=i loop=$msg}
    <div data-role="header" data-position="inline" data-theme="e">     
        <h1>{$msg[i].text|output_text}</h1>
        {if $msg[i].help}
            <a data-icon="info" data-iconpos="notext" class="ui-btn-right" href="/faq.php?info={$msg[i].help}&amp;return={$return|urlencode}">{$lang->getString("Помощь")}</a>
        {/if}
    </div>
{/section}



<div data-role="navbar" data-iconpos="left">
    <ul>{if $user->id}
        <li><a data-icon="grid" data-theme="b" href="/menu.user.php">{$user->login}</a></li>
    {if $user->mail_new_count}<li><a data-theme="b" href='/my.mail.php?only_unreaded'>{$lang->getString("Почта")} +{$user->mail_new_count}</a></li>{/if}
{if $user->friend_new_count}<li><a data-theme="b" href='/my.friends.php'>{$lang->getString("Друзья")} +{$user->friend_new_count}</a></li>{/if}

{else}
    <li><a data-theme="b" href="/login.php?return={$URL}">{$lang->getString("Авторизация")}</a></li>
    <li><a data-theme="b" href="/reg.php?return={$URL}">{$lang->getString("Регистрация")}</a></li>
    {/if}
    </ul>
</div>

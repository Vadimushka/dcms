{if $actions}
    <div class="actions">
        {section name=i loop=$actions}
            <a href="{$actions[i].1}">{$actions[i].0}</a><br />
        {/section}
    </div>
{/if}

<div class="page_foot">
    {if $returns}
        <div class="returns">
            {section name=i loop=$returns}
                <a href="{$returns[i].1}">{$returns[i].0}</a><br />
            {/section}
        </div>
    {/if}
    {if $smarty.server.SCRIPT_NAME != '/index.php'}
        <img src="{$path}/for_css/home.png" alt="" /> <a href='/'>{$lang->getString("На главную")}</a><br />
    {/if}
</div>

{if $smarty.server.SCRIPT_NAME != '/index.php'}
<div class="returns"> 
    <span><span><a href='/'>{$lang->getString("На главную")}</a></span></span>    
    {section name=i loop=$returns step=-1}
        <span><span><a href="{$returns[i].1}">{$returns[i].0}</a></span></span>
    {/section}
</div>
{/if}
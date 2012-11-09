{if $actions}
<div class="actions">
{section name=i loop=$actions}
<a href="{$actions[i].1}"><div>{$actions[i].0}</div></a>
{/section}
</div>
{/if}

{if $returns OR $smarty.server.SCRIPT_NAME != '/index.php'}
<div class="returns">
{section name=i loop=$returns}
<a href="{$returns[i].1}"><div>{$returns[i].0}</div></a>
{/section}
{if $smarty.server.SCRIPT_NAME != '/index.php'}
<a href='/'><div>{$lang->getString("На главную")}</div></a>
{/if}
</div>
{/if}



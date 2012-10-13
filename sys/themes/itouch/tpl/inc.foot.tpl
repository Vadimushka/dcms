{if $actions}
    <div data-role="controlgroup" data-iconpos="left" data-mini="true">
        {section name=i loop=$actions}
            <a data-role="button" data-icon="gear" href="{$actions[i].1}">{$actions[i].0}</a>
        {/section}
    </div>
{/if}




{if $returns}
    <div data-role="navbar" data-iconpos="left">
        <ul>
            {section name=i loop=$returns step=-1}
                <li><a data-icon="back" href="{$returns[i].1}">{$returns[i].0}</a></li>
            {/section}
        </ul>
    </div>
{/if}


<div data-role="navbar" data-iconpos="left">
    <ul>
        {if $smarty.server.SCRIPT_NAME != '/index.php'}
            <li><a data-icon="home" href='/'>{$lang->getString("На главную")}</a></li>
        {/if}
    </ul>
</div>
    
<div data-role="footer" class="ui-bar">
    <h4>{$dcms->copyright|output_text}</h4>
</div>

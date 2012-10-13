<div data-role="navbar" data-iconpos="left">
{section name=i loop=$select}
    <ul>
{if $select[i][2]}
 <span>{$select[i][1]}</span>
{else}
 <li><a href='{$select[i][0]}'>{$select[i][1]}</a></li>
{/if}
</ul>
{/section}
</div>

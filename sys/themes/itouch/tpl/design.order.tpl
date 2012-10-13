<div data-role="controlgroup" data-type="horizontal" data-mini="true">
{section name=i loop=$order}
 
{if $order[i][2]}
 <a data-role="button">{$order[i][1]}</a>
{else}
 <a data-role="button" href='{$order[i][0]}'>{$order[i][1]}</a>
{/if}
 
{/section}
</div>
<br />

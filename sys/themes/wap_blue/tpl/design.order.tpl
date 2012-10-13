<div class="order">
{section name=i loop=$order}
{if $order[i][2]}
 [{$order[i][1]}]
{else}
 <a href='{$order[i][0]}'>{$order[i][1]}</a>
{/if}
{/section}
</div>

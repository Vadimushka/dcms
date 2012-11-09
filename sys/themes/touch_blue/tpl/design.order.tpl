<div class="select_bar">
{section name=i loop=$order}{if $order[i][2]}<span>{$order[i][1]}</span>{else}<a href='{$order[i][0]}'>{$order[i][1]}</a>{/if}{/section}
</div>

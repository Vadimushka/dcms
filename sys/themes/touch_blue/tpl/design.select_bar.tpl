<div class="select_bar">
{section name=i loop=$select}{if $select[i][2]}<span>{$select[i][1]}</span>{else}<a href='{$select[i][0]}'>{$select[i][1]}</a>{/if}{/section}
</div>

<div class="form">
<select onchange="location = this.options[this.selectedIndex].value;">
{section name=i loop=$order}
{if $order[i][2]}
 <option value="{$order[i][0]}" selected="selected">{$order[i][1]}</option>
{else}
 <option value="{$order[i][0]}">{$order[i][1]}</option>
{/if}
{/section}
</select>
</div>
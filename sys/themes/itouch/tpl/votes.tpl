<div class="vote">
<span class="vote_name">{$name|output_text}</span>



<table border="0" cellpadding="0" cellspacing="0" width="100%">
{section name=i loop=$votes}
<tr>
<td colspan="2">
{$votes[i].name|for_value} {if $votes[i].count}({$votes[i].count}){/if}
</td>
</tr>
<tr>
<td class="votes">
<div>
<div class="votes" style=" width:{$votes[i].pc}%; max-width:99%">
{$votes[i].pc}%
</div>

</div>
</td>
{if $is_add}
<td class="votes_add">
<div>
<a href="{$votes[i].url}">+</a>
</div>
</td>
{/if}
</tr>
{/section}
</table>

</div>
<br />
<div class="vote">
<div class="vote_name">
{$name|output_text}
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
{section name=i loop=$votes}
<tr>
<td colspan="2">
{$votes[i].name|for_value} {if $votes[i].count}({$votes[i].count}){/if}
</td>
</tr>
<tr>
<td class="votes">
<div class="votes" style="width:{$votes[i].pc}%;max-width:95%">
{$votes[i].pc}%
</div>
</td>
<td class="votes_add">
{if $is_add}
<a href="{$votes[i].url}">+</a>
{/if}
</td>

</tr>
{/section}
</table>

</div>

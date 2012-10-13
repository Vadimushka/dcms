{section name=i loop=$post}
<div class="post_{if $smarty.section.i.iteration is even}0{else}1{/if}">
{if $post[i].new}<div class="post_new">{/if}

<table width="100%">
{if $post[i].icon.size eq 'big'}
<tr>
<td class="icon_big" rowspan="3"><img src="{$post[i].icon.src}" alt="" /></td><td>
{$post[i].title}
{elseif $post[i].icon.size eq 'small'}
<tr class="post_title"><td>
<img class="icon_small" src="{$post[i].icon.src}" alt="" />
{$post[i].title}
{elseif $post[i].checkbox}
<tr class="post_title"><td>
<label><input type="checkbox"{if $post[i].checkbox.checked} checked="checked"{/if} name="{$post[i].checkbox.name}" value="1" />
{$post[i].title}</label>
{else}
<tr class="post_title"><td>{$post[i].title}
{/if}
</td>
</tr>
{if $post[i].post}
<tr>
<td colspan="10">
{if $post[i].hide}<div class="post_hide">{/if}
{$post[i].post}
{if $post[i].edit}<div class="post_edit">{$post[i].edit}</div>{/if}
{if $post[i].hide}</div>{/if}
</td>
</tr>
{/if}
</table>
{if $post[i].new}</div>{/if}

{if $post[i].act}
{section name=z loop=$post[i].act}
[<a href="{$post[i].act[z].1}">{$post[i].act[z].0}</a>] 
{/section}
{/if}

</div>
{/section}
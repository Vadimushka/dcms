
{capture assign="post_time"}
    {if $time}
        <span class="post_time">({$time})</span>
    {/if}
{/capture}

{capture assign="post_counter"}
    {if $counter}
    <span class="ui-li-count">{$counter}</span>
    {/if}
{/capture}

{capture assign="post_actions"}
    {section name=i loop=$actions}
        <span class="post_action"><a href="{$actions[i].url}"><img src="{$actions[i].icon}" alt="" /></a></span>
    {/section}
{/capture}

<li data-role="list-divider">
{if $url}<a href="{$url}">{/if}


{if !$image AND $icon}<img src="{$icon}" alt="" class="ui-li-icon">
{/if}

{$title} {$post_time} {$post_counter}

{if $url}</a>{/if}
</li>


{if $content OR $bottom OR $image}
<li>
{if $image}<img src="{$image}" alt="">{/if}
<p>{$content}</p>
<p class="ui-li-aside">{$bottom}</p>
</li>
{/if}